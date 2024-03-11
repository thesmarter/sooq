<?php

namespace Modules\BlockUser\Http\Services;

use App\Config\ps_constant;
use App\Http\Services\PsService;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\BlockUser\Entities\BlockUser;
use Modules\Core\Constants\Constants;
use Modules\Core\Http\Services\UserAccessApiTokenService;
use Modules\Core\Http\Services\UserService;
use Modules\Core\Transformers\Api\App\V1_0\User\UserApiResource;

class BlockUserService extends PsService
{
    protected $userService, $createdStatusCode, $successStatus, $dangerStatus, $blockUserIdCol, $blockUserFromBlockUserIdCol, $blockUserToBlockUserIdCol, $noContentStatusCode, $userId,  $deviceTokenParaApi, $loginUserIdParaApi, $userAccessApiTokenService, $forbiddenStatusCode ;

    public function __construct(UserService $userService, UserAccessApiTokenService $userAccessApiTokenService)
    {
        $this->userService = $userService;
        $this->createdStatusCode = Constants::createdStatusCode;
        $this->okStatusCode = Constants::okStatusCode;
        $this->successStatus = Constants::successStatus;
        $this->dangerStatus = Constants::errorStatus;
        $this->forbiddenStatusCode = Constants::forbiddenStatusCode;

        $this->blockUserIdCol = BlockUser::id;
        $this->blockUserFromBlockUserIdCol = BlockUser::fromBlockUserId;
        $this->blockUserToBlockUserIdCol = BlockUser::toBlockUserId;

        $this->userId = User::id;

        $this->noContentStatusCode = Constants::noContentStatusCode;

        $this->userAccessApiTokenService = $userAccessApiTokenService;
        $this->loginUserIdParaApi = ps_constant::loginUserIdParaFromApi;
        $this->deviceTokenParaApi = ps_constant::deviceTokenKeyFromApi;
    }

    public function create($request)
    {
        DB::beginTransaction();
        try {
            $block = new BlockUser();

            if (isset($request->from_block_user_id) && !empty($request->from_block_user_id)) {
                $block->from_block_user_id = $request->from_block_user_id;
            }

            if (isset($request->to_block_user_id) && !empty($request->to_block_user_id)) {
                $block->to_block_user_id = $request->to_block_user_id;
            }

            $block->added_user_id = Auth::user()->id;
            $block->save();

            DB::commit();
            return $block;
        } catch (\Throwable $e) {
            DB::rollBack();
            return ['error' => $e->getMessage()];
        }
    }

    public function deleteAllBy($conds = null)
    {
        DB::beginTransaction();
        try {
            BlockUser::when($conds, function ($query, $conds) {
                $query->where($conds);

            })->delete();

            DB::commit();
            return true;
        } catch (\Throwable $e) {
            DB::rollBack();
            return ['error' => $e->getMessage()];
        }
    }

    public function blockUserFromApi($request)
    {
        /// check permission start
        $deviceToken = $request->header($this->deviceTokenParaApi);
        $userId = $request->login_user_id;

        // user token layer permission start
        $userAccessApiToken = $this->userAccessApiTokenService->getUserAccessApiToken($userId, $deviceToken);
        // user token layer permission end

        /// check permission end

        if (empty($userAccessApiToken)){
            $msg = __('blockUser__api_block_no_permission',[],$request->language_symbol);
            return responseMsgApi($msg, $this->forbiddenStatusCode);
        }else{
            $data[$this->blockUserFromBlockUserIdCol] = $request->from_block_user_id;
            $data[$this->blockUserToBlockUserIdCol] = $request->to_block_user_id;

            $block_data[$this->blockUserFromBlockUserIdCol] = $request->to_block_user_id;
            $block_data[$this->blockUserToBlockUserIdCol] = $request->from_block_user_id;

            $data_exist = BlockUser::where($data)->first();
            $block_data_exist = BlockUser::where($block_data)->first();

            if ($data_exist) {
                $data_exist->delete();
                $block_data_exist->delete();
            }

            $data_create = (object) $data;
            $block_data_create = (object) $block_data;

            $data_save = $this->create($data_create);
            $block_save = $this->create($block_data_create);

            if(isset($data_save['error']) || isset($block_save['error'])){
                $data = [
                    "msg" => __('blockUser__api_block_fail',[],$request->language_symbol),
                    "statusCode" => $this->internalServerErrorStatusCode,
                    "flag" => $this->dangerStatus
                ];
            }else{
                $data = [
                    "msg" => __('blockUser__api_block_success',[],$request->language_symbol),
                    "statusCode" => $this->createdStatusCode,
                    "flag" => $this->successStatus
                ];
            }
            return $data;
        }
    }

    public function unblockUserFromApi($request)
    {
        /// check permission start
        $deviceToken = $request->header($this->deviceTokenParaApi);
        $userId = $request->login_user_id;

        // user token layer permission start
        $userAccessApiToken = $this->userAccessApiTokenService->getUserAccessApiToken($userId, $deviceToken);
        // user token layer permission end

        /// check permission end

        if (empty($userAccessApiToken)){
            $msg = __('blockUser__api_unblock_no_permission',[],$request->language_symbol);
            return responseMsgApi($msg, $this->forbiddenStatusCode);
        }else{
            $data[$this->blockUserFromBlockUserIdCol] = $request->from_block_user_id;
            $data[$this->blockUserToBlockUserIdCol] = $request->to_block_user_id;

            $block_data[$this->blockUserFromBlockUserIdCol] = $request->to_block_user_id;
            $block_data[$this->blockUserToBlockUserIdCol] = $request->from_block_user_id;

            $data_exist = BlockUser::where($data)->first();
            $block_data_exist = BlockUser::where($block_data)->first();

            if ($data_exist) {
                $data_exist->delete();
                $block_data_exist->delete();
            } else {
                $data = [
                    "msg" => __('blockUser__api_not_block_yet',[],$request->language_symbol),
                    "statusCode" => $this->okStatusCode,
                    "flag" => $this->successStatus
                ];
            }
            $data = [
                "msg" => __('blockUser___api_unblock_success',[],$request->language_symbol),
                "statusCode" => $this->createdStatusCode,
                "flag" => $this->successStatus
            ];
            return $data;
        }
    }

    public function getBlockList($limit = null, $offset = null, $conds = null)
    {
        $block = BlockUser::when($limit, function ($query, $limit) {
            $query->limit($limit);
        })
            ->when($offset, function ($query, $offset) {
                $query->offset($offset);
            })
            ->when($conds, function ($query, $conds) {

                $query->where($conds);

            })
            ->latest()->get();
        return $block;
    }

    public function getBlockedUsersFromApi($request)
    {
        /// check permission start
        $deviceToken = $request->header($this->deviceTokenParaApi);
        $userId = $request->login_user_id;

        // user token layer permission start
        $userAccessApiToken = $this->userAccessApiTokenService->getUserAccessApiToken($userId, $deviceToken);
        // user token layer permission end

        /// check permission end

        if (empty($userAccessApiToken)){
            $msg = __('blockUser__api_no_permission',[],$request->language_symbol);
            return responseMsgApi($msg, $this->forbiddenStatusCode);
        }else{
            $offset = $request->offset;
            $limit = $request->limit;

            $data[$this->blockUserFromBlockUserIdCol] = $userId;

            $blocks = $this->getBlockList($limit, $offset, $data);

            if ($offset > 0 && $blocks->isEmpty()) {
                // no paginate data
                $data = [];
                return responseDataApi($data);

            } else if($blocks->isEmpty()) {
                // no data db
                return responseMsgApi(__('blockUser__api_no_data',[],$request->language_symbol), $this->noContentStatusCode, $this->successStatus);
            } else {

                $conds = [];
                foreach ($blocks as $cond) {
                    array_push($conds, $cond->to_block_user_id);
                }

                $users = User::whereIn($this->userId, $conds)->with('userRelation')->get();
                $users = UserApiResource::collection($users);
                return responseDataApi($users);
            }
        }
    }

    public function getBlockUsers($relation = null, $limit = null, $offset = null, $conds = null)
    {
        $blockUsers = BlockUser::when($relation, function ($q, $relation) {
            $q->with($relation);
        })
            ->when($limit, function ($query, $limit) {
                $query->limit($limit);
            })
            ->when($offset, function ($query, $offset) {
                $query->offset($offset);
            })
            ->when($conds, function ($query, $conds) {
                $query->where($conds);
            })
            ->latest()->get();

        return $blockUsers;
    }

    public function getBlockUser($id = null, $relation = null)
    {
        $blockUser = BlockUser::when($relation, function ($q, $relation) {
            $q->with($relation);
        })
            ->when($id, function ($q, $id) {
                $q->where($this->blockUserIdCol, $id);
            })
            ->latest()
            ->first();

        return $blockUser;
    }

    public function getBlockUserIds($user_id)
    {
        // get_favourite_get api
        // get_item_by_followuser_post api

        // get all block users by user_id
        $conds_block[$this->blockUserFromBlockUserIdCol] = $user_id;
        $blockUsers = $this->getBlockUsers(null, null, null, $conds_block);
        $blockUserIds = [];

        // block users existed
        if (!empty($blockUsers)) {

            // get all the block user ids
            $i = 0;
            foreach ($blockUsers as $blockUser) {
                if (!in_array($blockUser[$this->blockUserToBlockUserIdCol], $blockUserIds)) {
                    $blockUserIds[$i++] = $blockUser[$this->blockUserToBlockUserIdCol];
                }
            }
        }

        return $blockUserIds;
    }
}
