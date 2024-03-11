<?php

namespace Modules\Core\Http\Services;

use App\Http\Services\PsService;
use Illuminate\Support\Facades\DB;
use Modules\Core\Constants\Constants;
use Modules\Core\Entities\PushNotificationToken;
use Modules\Core\Transformers\Api\App\V1_0\Currency\CurrencyApiResource;
use Modules\Core\Http\Services\UserAccessApiTokenService;
use App\Config\ps_constant;

class PushNotificationTokenService extends PsService
{
    protected $userAccessApiTokenService, $currencyService, $publish, $default, $forbiddenStatusCode, $noContentStatusCode, $successStatus;
    public function __construct(CurrencyService $currencyService,UserAccessApiTokenService $userAccessApiTokenService)
    {
        $this->successStatus = Constants::successStatus;
        $this->createdStatusCode = Constants::createdStatusCode;
        $this->okStatusCode = Constants::okStatusCode;
        $this->internalServerErrorStatusCode = Constants::internalServerErrorStatusCode;
        $this->noContentStatusCode = Constants::noContentStatusCode;
        $this->notFoundStatusCode = Constants::notFoundStatusCode;
        $this->badRequestStatusCode = Constants::badRequestStatusCode;

        $this->notiIdCol = PushNotificationToken::id;
        $this->notiDeviceTokenCol = PushNotificationToken::device_token;
        $this->notiUserIdCol = PushNotificationToken::user_id;
        $this->notiPlatformNameCol = PushNotificationToken::platform_name;

        // temporary for testing api

        $this->currencyService = $currencyService;
        $this->publish = Constants::publish;
        $this->default = Constants::default;
        $this->forbiddenStatusCode = Constants::forbiddenStatusCode;
        $this->dangerFlag = Constants::danger;

        $this->loginUserIdParaApi = ps_constant::loginUserIdParaFromApi;
        $this->deviceTokenParaApi = ps_constant::deviceTokenKeyFromApi;

        $this->userAccessApiTokenService = $userAccessApiTokenService;
    }

    public function store($request)
    {
        DB::beginTransaction();

        try {
            $noti = new PushNotificationToken();

            if (isset($request->device_token) && !empty($request->device_token)) {
                $noti->device_token = $request->device_token;
            }

            if (isset($request->user_id) && !empty($request->user_id)) {
                $noti->user_id = $request->user_id;
            }

            if (isset($request->platform_name) && !empty($request->platform_name)) {
                $noti->platform_name = $request->platform_name;
            }

            $noti->added_user_id = $request->user_id;
            $noti->save();

            DB::commit();
        } catch (\Throwable$e) {
            DB::rollBack();
            return ['error' => $e->getMessage()];
        }
    }

    public function update($id, $request)
    {
        DB::beginTransaction();

        try {
            $noti = $this->getNotiToken($id);

            if (isset($request->device_token) && !empty($request->device_token)) {
                $noti->device_token = $request->device_token;
            }

            if (isset($request->user_id) && !empty($request->user_id)) {
                $noti->user_id = $request->user_id;
            }

            if (isset($request->platform_name) && !empty($request->platform_name)) {
                $noti->platform_name = $request->platform_name;
            }

            $noti->updated_user_id = $request->user_id;
            $noti->update();

            DB::commit();
        } catch (\Throwable$e) {
            DB::rollBack();
            return ['error' => $e->getMessage()];
        }
    }

    public function destroyBy($conds)
    {
        $notiToken = $this->getNotiToken(null, $conds);

        if (empty($notiToken)) {
            $msg = "Record Not Found with given Id";
            return $msg;
        }

        $notiToken->delete();

        $status = [
            // 'msg' => 'This token row has been deleted successfully.',
            'msg'=>__('core__be_delete_success',['attribute'=>'token']),
            'flag' => $this->dangerFlag,
        ];

        return $status;
    }

    public function getNotiTokens($relation = null, $conds = null, $limit = null, $offset = null)
    {
        $notiTokens = PushNotificationToken::when($relation, function ($q, $relation) {
            $q->with($relation);
        })
            ->when($conds, function ($q, $conds) {
                $q->where($conds);
            })
            ->when($limit, function ($query, $limit) {
                $query->limit($limit);
            })
            ->when($offset, function ($query, $offset) {
                $query->offset($offset);
            })->get();
        return $notiTokens;
    }

    public function getNotiToken($id = null, $conds = null, $relation = null)
    {
        $notiToken = PushNotificationToken::when($id, function ($q, $id) {
            $q->where($this->notiIdCol, $id);
        })
            ->when($conds, function ($q, $conds) {
                $q->where($conds);
            })
            ->when($relation, function ($q, $relation) {
                $q->with($relation);
            })
            ->first();
        return $notiToken;
    }


    public function deleteAllBy($conds = null)
    {
        DB::beginTransaction();
        try {
            PushNotificationToken::when($conds, function ($query, $conds) {
                $query->where($conds);

            })->delete();

            DB::commit();
            return true;
        } catch (\Throwable $e) {
            DB::rollBack();
            return ['error' => $e->getMessage()];
        }
    }

    public function exists($conds)
    {
        $noti = PushNotificationToken::where($conds)->count();
        if ($noti > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function storeOrUpdateNotiToken($request, $user_id)
    {
        $noti_token['device_token'] = $request->device_token;

        $noti_count = PushNotificationToken::where($noti_token)->count();

        if ($noti_count == 1) {
            if ($this->exists($noti_token)) {
                $noti = $this->getNotiToken(null, $noti_token);

                $noti_data = new \stdClass;
                $noti_data->user_id = $user_id;
                $noti_data->platform_name = $request->platform_name;

                $errMsg = $this->update($noti->id, $noti_data);
            } else {

                $noti_data = new \stdClass;
                $noti_data->user_id = $user_id;
                $noti_data->device_token = $request->device_token;
                $noti_data->platform_name = $request->platform_name;

                $errMsg = $this->store($noti_data);
            }
        } else {
            $this->destroyBy($noti_token);

            $noti_data = new \stdClass;
            $noti_data->user_id = $user_id;
            $noti_data->device_token = $request->device_token;
            $noti_data->platform_name = $request->platform_name;

            $noti = $this->store($noti_data);
        }

        if (isset($noti['error'])) {
            return false;
        } else {
            return true;
        }
    }

    public function registerFromApi($request)
    {
        // check permission start
        $deviceToken = $request->header($this->deviceTokenParaApi);
        $userAccessApiToken = $this->userAccessApiTokenService->getUserAccessApiToken( $request->login_user_id, $deviceToken);

        if (empty($userAccessApiToken)){
            $msg = __('core__api_update_no_permission',[],$request->language_symbol);
            return ['error' =>  $msg,'status' =>  $this->forbiddenStatusCode ];
        }
        // check permission end

        $conds['device_token'] = $request->device_token;
        $conds['user_id'] = $request->user_id;

        $noti = PushNotificationToken::where($conds)->count();

        if ($noti != 0) {
            return  ['error' => __('noti__api_already_registered',[],$request->language_symbol) ,'status' =>  $this->badRequestStatusCode ];
        } else {
            $push_noti_token = $this->store($request);

            if (isset($push_noti_token['error'])) {
                return  ['error' => __('noti__api_db_error',[],$request->language_symbol) ,'status' =>  $this->internalServerErrorStatusCode ];
            }
            return  ['success' => __('noti__api_register_success',[],$request->language_symbol) ,'status' =>  $this->createdStatusCode ];

        }

    }

    public function unregisterFromApi($request)
    {
        // check permission start
        $deviceToken = $request->header($this->deviceTokenParaApi);
        $userAccessApiToken = $this->userAccessApiTokenService->getUserAccessApiToken( $request->login_user_id, $deviceToken);

        if (empty($userAccessApiToken)){
            $msg = __('core__api_update_no_permission',[],$request->language_symbol);
            return ['error' =>  $msg,'status' =>  $this->forbiddenStatusCode ];
        }
        // check permission end

        $conds['device_token'] = $request->device_token;
        $conds['user_id'] = $request->user_id;

        $noti = PushNotificationToken::where($conds)->count();

        if ($noti == 0) {
            return  ['error' => __('noti__api_token_not_exit',[],$request->language_symbol) ,'status' =>  $this->badRequestStatusCode ];
        } else {
            $notis = PushNotificationToken::where($conds)->first();
            $id = $notis->id;

            $noti = $this->destroy($id);
            return  ['success' => __('noti__api_unregister_success',[],$request->language_symbol) ,'status' =>  $this->okStatusCode ];
        }

    }

    public function destroy($id)
    {
        $noti = PushNotificationToken::find($id);

        $noti->delete();
    }

    public function testStatusCode($request)
    {

        $test_code = $request->test_code;

        if ($test_code == "500") {

            return responseMsgApi(__('db_error',[],$request->language_symbol), $this->internalServerErrorStatusCode);
        } elseif ($test_code == "200_msg") {

            return responseMsgApi('user deleted successfully.', $this->okStatusCode, $this->successStatus);

        } elseif ($test_code == "200_list") {

            $offset = "0";
            $limit = "10";

            $tests = CurrencyApiResource::collection($this->currencyService->getCurrencies($this->publish, $this->default, $limit, $offset));
            return responseDataApi($tests);

        } elseif ($test_code == "201_msg") {

            return responseMsgApi('user followed successfully', $this->createdStatusCode, $this->successStatus);

        } elseif ($test_code == "201_obj") {

            $id = "1";
            $tests = new CurrencyApiResource($this->currencyService->getCurrency($id));
            return responseDataApi($tests, $this->createdStatusCode, $this->successStatus);

        } elseif ($test_code == "no_data_db") {

            return responseMsgApi(__('no data'), $this->noContentStatusCode, $this->successStatus);

        } elseif ($test_code == "no_data_pag") {

            $tests = [];
            return responseDataApi($tests);

        } elseif ($test_code == "404") {

            return responseMsgApi(__('rescoure not found.'), $this->notFoundStatusCode);

        } elseif ($test_code == "400") {

            return responseMsgApi('user id required.', $this->badRequestStatusCode);

        } elseif ($test_code == "403") {

            return responseMsgApi(__("you don't have permission to edit this post."), $this->forbiddenStatusCode);

        }
    }
}
