<?php

namespace Modules\FollowUser\Http\Services;

use App\Config\ps_constant;
use App\Http\Services\PsService;
use App\Models\User;
use Cassandra\Custom;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\FollowUser\Entities\FollowUser;
use Modules\Core\Constants\Constants;
use Modules\Core\Entities\CustomizeUi;
use Modules\Core\Http\Services\UserService;
use Modules\Core\Http\Services\ItemService;
use Modules\Core\Http\Services\PushNotificationTokenService;
use Modules\Core\Http\Services\CoreKeyService;
use Modules\Core\Entities\UserInfo;
use Modules\Core\Http\Services\UserAccessApiTokenService;
use Modules\Core\Transformers\Api\App\V1_0\Product\ProductApiResource;
use Modules\Core\Transformers\Api\App\V1_0\User\UserApiResource;

class FollowUserService extends PsService
{
    protected $followerReturnType, $followingReturnType, $noContentStatusCode, $successStatus, $coreKeyService, $usrFollowerCount, $usrFollowingCount, $userService, $createdStatusCode, $internalServerErrorStatusCode, $okStatusCode, $userId, $followedUserId, $deviceTokenParaApi, $loginUserIdParaApi, $userAccessApiTokenService, $forbiddenStatusCode;

    public function __construct(CoreKeyService $coreKeyService, UserService $userService, ItemService $itemService, PushNotificationTokenService $pushNotificationTokenService, UserAccessApiTokenService $userAccessApiTokenService)
    {
        $this->userService = $userService;
        $this->itemService = $itemService;
        $this->pushNotificationTokenService = $pushNotificationTokenService;
        $this->coreKeyService = $coreKeyService;

        $this->createdStatusCode = Constants::createdStatusCode;
        $this->forbiddenStatusCode = Constants::forbiddenStatusCode;
        $this->okStatusCode = Constants::okStatusCode;
        $this->internalServerErrorStatusCode = Constants::internalServerErrorStatusCode;
        $this->usrFollowerCount = Constants::usrFollowerCount;
        $this->usrFollowingCount = Constants::usrFollowingCount;

        $this->noContentStatusCode = Constants::noContentStatusCode;
        $this->successStatus = Constants::successStatus;

        $this->userId = FollowUser::userId;
        $this->followedUserId = FollowUser::followedUserId;

        $this->followerReturnType = Constants::followerReturnType;
        $this->followingReturnType = Constants::followingReturnType;

        $this->userAccessApiTokenService = $userAccessApiTokenService;
        $this->loginUserIdParaApi = ps_constant::loginUserIdParaFromApi;
        $this->deviceTokenParaApi = ps_constant::deviceTokenKeyFromApi;

        $this->userIdCol = User::id;
        $this->userNameCol = User::name;
        $this->userTableName = User::tableName;

        $this->customUiCoreKeysIdCol = CustomizeUi::coreKeysId;

    }

    public function create($request)
    {
        $follow = new FollowUser();

        if (isset($request->user_id) && !empty($request->user_id))
            $follow->user_id = $request->user_id;

        if (isset($request->followed_user_id) && !empty($request->followed_user_id))
            $follow->followed_user_id = $request->followed_user_id;


        if (isset($request->added_user_id) && !empty($request->added_user_id))
            $follow->added_user_id = $request->added_user_id;
        else{
            if(Auth::check()){
                $follow->added_user_id = Auth::user()->id;
            }else{
                $follow->added_user_id = 0;
            }
        }

        if($follow->save())
        {
            return true;
        }else{
            return false;
        }

    }

    public function followUserFromApi($request)
    {
        /// check permission start
        $deviceToken = $request->header($this->deviceTokenParaApi);
        $userId = $request->login_user_id;

        // user token layer permission start
        $userAccessApiToken = $this->userAccessApiTokenService->getUserAccessApiToken($userId, $deviceToken);
        // user token layer permission end

        /// check permission end

        if (empty($userAccessApiToken)){
            $msg = __('followUser__api_follow_no_permission',[],$request->language_symbol);
            return responseMsgApi($msg, $this->forbiddenStatusCode);
        }else{
            $data[$this->userId] = $request->user_id;
            $data[$this->followedUserId] = $request->followed_user_id;

            $data_exist = FollowUser::where($data)->first();

            if ($data_exist) {
                if($data_exist->delete()){
                    // update user follower count
                    $this->updateUserFollowerCountWithId($request->user_id);
                    $this->updateUserFollowerCountWithId($request->followed_user_id);
                }else{
                    return responseMsgApi(__('followUser__api_db_error',[],$request->language_symbol), $this->internalServerErrorStatusCode);
                }
            }else{

                if($this->create($request)){
                    // update user follower count
                    $this->updateUserFollowerCountWithId($request->user_id);
                    $this->updateUserFollowerCountWithId($request->followed_user_id);
                }else{
                    return responseMsgApi(__('followUser__api_db_error',[],$request->language_symbol), $this->internalServerErrorStatusCode);
                }
            }

            // start noti send to to_user_id when reviewed
            $conds['user_id'] = $request->followed_user_id;
            $notiTokens = $this->pushNotificationTokenService->getNotiTokens(null, $conds);
            $device_ids = [];
            $platform_names = [];
            foreach($notiTokens as $token){
                $device_ids[] = $token->device_token;
                $platform_names[] = $token->platform_name;
            }

            $cond[$this->userIdCol] = $request->user_id;
            $user = User::where($cond)->first();

            $data['message'] = $user->name . ' ' . __('followUser__api_followed_you',[],$request->language_symbol);
            $data['flag'] = Constants::followNotiFlag;
            send_android_fcm($device_ids, $data, $platform_names);
            // end noti send to followed_user when followed

            $followed_cond[$this->userIdCol] = $request->followed_user_id;
            $followed_user = User::where($followed_cond)->first();
            $followed_user = new UserApiResource($followed_user);

            return responseDataApi($followed_user);
        }
    }

    public function getFollowList( $limit = null, $offset = null, $conds = null){
        $follow = FollowUser::when($limit, function ($query, $limit) {
                                    $query->limit($limit);
                                })
                                ->when($offset, function ($query, $offset) {
                                    $query->offset($offset);
                                })
                                ->when($conds, function ($query, $conds) {
                                    $query->where($conds);
                                })
                                ->latest()->get();
        return $follow;
    }

    public function deleteAllBy($conds = null)
    {
        DB::beginTransaction();
        try {
            FollowUser::when($conds, function ($query, $conds) {
                $query->where($conds);

            })->delete();

            DB::commit();
            return true;
        } catch (\Throwable $e) {
            DB::rollBack();
            return ['error' => $e->getMessage()];
        }
    }

    public function updateUserFollowerCountWithId($user_id){
        $userId = $user_id;

        $follower = $this->userService->getUserInfo($this->usrFollowerCount, $userId);
        $following = $this->userService->getUserInfo($this->usrFollowingCount, $userId);

        $followUserCond['followed_user_id'] = $userId;
        $followercount = $this->countBy(null, $followUserCond);

        $followingUserCond['user_id'] = $userId;
        $followingcount = $this->countBy(null, $followingUserCond);

        if($follower){
            //if data already exist for follower, update
            $follower->value = $followercount;
            $follower->update();

        }else{

            //if data not exist for follower
            $saveFollower = new UserInfo();
            $saveFollower->value = $followercount;
            $saveFollower->core_keys_id = $this->usrFollowerCount;
            $saveFollower->user_id = $userId;
            $saveFollower->ui_type_id = CustomizeUi::where($this->customUiCoreKeysIdCol, $this->usrFollowerCount)->first()->ui_type_id;
            $saveFollower->added_user_id = $userId;
            $saveFollower->save();
        }

        if($following){
            //if data already exist for follower, update
            $following->value = $followingcount;
            $following->update();

        }else{
            //if data not exist for follower
            $saveFollowing = new UserInfo();
            $saveFollowing->value = $followingcount;
            $saveFollowing->core_keys_id = $this->usrFollowingCount;
            $saveFollowing->user_id = $userId;
            $saveFollowing->ui_type_id = CustomizeUi::where($this->customUiCoreKeysIdCol, $this->usrFollowingCount)->first()->ui_type_id;
            $saveFollowing->added_user_id = $userId;
            $saveFollowing->save();
        }
    }

    public function countBy($relation = null, $conds = null)
    {
        $count = FollowUser::when($conds, function ($query, $conds) {
                $query->where($conds);
            })->count();

        return $count;
    }

    public function getItemListFromApi($request)
    {
        $limit = $request->limit;
        $offset = $request->offset;

        /// check permission start
        $deviceToken = $request->header($this->deviceTokenParaApi);
        $userId = $request->login_user_id;

        // user token layer permission start
        $userAccessApiToken = $this->userAccessApiTokenService->getUserAccessApiToken($userId, $deviceToken);
        // user token layer permission end

        /// check permission end

        if (empty($userAccessApiToken)){
            $msg = __('followUser__api_no_permission',[],$request->language_symbol);
            return responseMsgApi($msg, $this->forbiddenStatusCode);
        }else{
            $dataConds = [];
            $dataConds['status'] = 1;
            if(isset($request->item_location_id) && !empty($request->item_location_id)){
                $dataConds['location_city_id'] = $request->item_location_id;
            }
            if(isset($request->item_location_township_id) && !empty($request->item_location_township_id)){
                $dataConds['location_township_id'] = $request->item_location_township_id;
            }

            $data['user_id'] = $userId;

            $data_exist = $this->getFollowList(null, null, $data);

            if ($data_exist->isEmpty()) {
                // no data db
                return responseMsgApi(__('followUser__api_no_data',[],$request->language_symbol), $this->noContentStatusCode, $this->successStatus);
            } else {

                $conds = [];
                foreach ($data_exist as $cond){
                    array_push($conds, $cond->followed_user_id);
                }
                $conds_in['added_user_ids'] = $conds;
                // return $conds;
                $itemApiRelation = ['category', 'subcategory', 'city', 'township', 'currency', 'owner','itemRelation'];
                $items = $this->itemService->getItems($itemApiRelation, true, null, $limit, $offset, $dataConds, $conds_in);
                // $items = Item::where($dataConds)->whereIn('added_user_id',  $conds)
                // ->with($itemApiRelation)->get();

                if ($items->isEmpty()){
                    // no data db
                    return responseMsgApi(__('followUser__api_no_data',[],$request->language_symbol), $this->noContentStatusCode, $this->successStatus);
                } else {
                    // return responseDataApi($items);
                    $items = ProductApiResource::collection($items);
                    return responseDataApi($items, $this->okStatusCode, $this->successStatus);
                }
            }
        }
    }

    public function getFollowedUsersFromApi($request)
   {
        $offset = $request->offset;
        $limit = $request->limit;

        /// check permission start
        $deviceToken = $request->header($this->deviceTokenParaApi);
        $userId = $request->login_user_id;

        // user token layer permission start
        $userAccessApiToken = $this->userAccessApiTokenService->getUserAccessApiToken($userId, $deviceToken);
        // user token layer permission end

        /// check permission end

        if (empty($userAccessApiToken)){
            $msg = __('followUser__api_no_permission',[],$request->language_symbol);
            return responseMsgApi($msg, $this->forbiddenStatusCode);
        }else{
            $data[$this->userId] = $userId;

            $data_exist = $this->getFollowList($limit, $offset, $data);

            $conds = [];
            foreach ($data_exist as $cond){
                array_push($conds, $cond->followed_user_id);
            }

            $users = User::whereIn('id',  $conds)->with('userRelation')->get();

            $users = UserApiResource::collection($users);

            if ($offset > 0 && $users->isEmpty()) {
                // no paginate data
                $data = [];
                return responseDataApi($data);

            } else if($users->isEmpty()) {
                // no data db
                return responseMsgApi(__('followUser__api_no_data'), $this->noContentStatusCode, $this->successStatus);
            } else {
                return responseDataApi($users, $this->okStatusCode, $this->successStatus);
            }
        }
   }

   public function searchFollowedUsersFromApi($request)
   {
        $offset = $request->offset;
        $limit = $request->limit;

        /// check permission start
        // $deviceToken = $request->header($this->deviceTokenParaApi);
        $userId = $request->login_user_id;

        // user token layer permission start
        // $userAccessApiToken = $this->userAccessApiTokenService->getUserAccessApiToken($userId, $deviceToken);
        // user token layer permission end

        /// check permission end

        // if (empty($userAccessApiToken)){
        //     $msg = __('followUser__api_no_permission',[],$request->language_symbol);
        //     return responseMsgApi($msg, $this->forbiddenStatusCode);
        // }else{
            $user_name =  $request->user_name;;
            $overall_rating = $request->overall_rating;
            $return_types = $request->return_types;
            $id = $request->id;
            $login_user_id = $request->login_user_id;

            $data = [];
            if($return_types == $this->followingReturnType){
                $data['user_id'] = $userId;
            }else if($return_types == $this->followerReturnType){
                $data['followed_user_id'] = $userId;
            }

            $followData = $this->getFollowList($limit, $offset, $data);

            if(count($followData) >0 && !empty($return_types)){
                // dd($offset);
                $idconds = [];
                if($return_types == $this->followingReturnType){
                    foreach ($followData as $cond){
                        array_push($idconds, $cond->followed_user_id);
                    }
                }else if($return_types == $this->followerReturnType){
                    foreach ($followData as $cond){
                        array_push($idconds, $cond->user_id);
                    }
                }

                $conds_in['ids'] = $idconds;

                $conds['user_name'] = $request->user_name;
                $conds['overall_rating'] = $request->overall_rating;

                $relations = ['userRelation'];
                $users = $this->userService->getUsers($relations, true, false, $conds, $limit, $offset = 0, $conds_in );
                $users = UserApiResource::collection($users);

                if ($offset > 0 && $users->isEmpty()) {
                    // no paginate data
                    $data = [];
                    return responseDataApi($data);
                } else if($users->isEmpty()) {
                    // no data db
                    return responseMsgApi(__('followUser__api_no_data',[],$request->language_symbol), $this->noContentStatusCode, $this->successStatus);
                } else {
                    return responseDataApi($users, $this->okStatusCode, $this->successStatus);
                }
            }else if($id != ''){
                $relations = ['userRelation'];
                $user = $this->userService->getUser($id, null, $relations);
                $user->following_user_id = $login_user_id;
                $user->followed_user_id = $id;
                $user = new UserApiResource($user);
                return responseDataApi($user, $this->okStatusCode, $this->successStatus);
            }else{
                return responseMsgApi(__('followUser__api_record_not_found',[],$request->language_symbol), $this->noContentStatusCode, $this->successStatus);
            }

            $idconds = [];
            if($return_types == $this->followerReturnType){
                foreach ($followData as $cond){
                    array_push($idconds, $cond->user_id);
                }
            }else if($return_types == $this->followingReturnType){
                foreach ($followData as $cond){
                    array_push($idconds, $cond->followed_user_id);
                }
            }
        // }
    }
}
