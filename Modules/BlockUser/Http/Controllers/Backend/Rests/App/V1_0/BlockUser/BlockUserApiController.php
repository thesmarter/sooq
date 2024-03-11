<?php

namespace Modules\BlockUser\Http\Controllers\Backend\Rests\App\V1_0\BlockUser;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Arr;
use Modules\BlockUser\Http\Services\BlockUserService;
use Modules\Core\Constants\Constants;
use Modules\Core\Http\Services\UserService;
use Illuminate\Support\Facades\Validator;
use Illuminate\Contracts\Translation\Translator;

class BlockUserApiController extends Controller
{

    protected $translator,$badRequestStatusCode, $internalServerErrorStatusCode, $blockUserService, $userService, $createdStatusCode, $successStatus, $errorStatus;

    public function __construct(Translator $translator,BlockUserService $blockUserService, UserService $userService)
    {
        $this->blockUserService = $blockUserService;
        $this->userService = $userService;
        $this->badRequestStatusCode = Constants::badRequestStatusCode;
        $this->internalServerErrorStatusCode = Constants::internalServerErrorStatusCode;
        $this->createdStatusCode = Constants::createdStatusCode;
        $this->successStatus = Constants::successStatus;
        $this->errorStatus = Constants::errorStatus;
        $this->translator = $translator;
    }

    public function blockUser(Request $request)
    {
        //prepare validation
        $validator = Validator::make($request->all(), [
            'from_block_user_id' => 'required|exists:users,id',
            'to_block_user_id' => 'required|exists:users,id',
        ]);

        if($request->language_symbol){
            $this->translator->setLocale($request->language_symbol);
            $validator->setTranslator($this->translator);
        }


        if ($validator->fails()) {
            return responseMsgApi(implode("\n", Arr::flatten($validator->getMessageBag()->getMessages())), $this->badRequestStatusCode);
        }

        $data = $this->blockUserService->blockUserFromApi($request);
        return responseMsgApi($data['msg'], $data['statusCode'], $data['flag']);
    }

    public function unblockUser(Request $request)
    {
        //prepare validation
        $validator = Validator::make($request->all(), [
            'from_block_user_id' => 'required|exists:users,id',
            'to_block_user_id' => 'required|exists:users,id',
        ]);

        if($request->language_symbol){
            $this->translator->setLocale($request->language_symbol);
            $validator->setTranslator($this->translator);
        }

        if ($validator->fails()) {
            return responseMsgApi(implode("\n", Arr::flatten($validator->getMessageBag()->getMessages())), $this->badRequestStatusCode);
        }

        $data = $this->blockUserService->unblockUserFromApi($request);
        return responseMsgApi($data['msg'], $data['statusCode'], $data['flag']);
    }

    public function getBlockedUser(Request $request)
    {
        $data = $this->blockUserService->getBlockedUsersFromApi($request);
        return $data;
    }
}
