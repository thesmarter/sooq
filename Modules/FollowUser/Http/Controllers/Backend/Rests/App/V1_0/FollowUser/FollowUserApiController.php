<?php

namespace Modules\FollowUser\Http\Controllers\Backend\Rests\App\V1_0\FollowUser;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Arr;
use Modules\FollowUser\Http\Services\FollowUserService;
use Modules\Core\Constants\Constants;
use Modules\Core\Http\Services\UserService;
use Illuminate\Support\Facades\Validator;
use Illuminate\Contracts\Translation\Translator;

class FollowUserApiController extends Controller
{
    protected $translator,$badRequestStatusCode, $internalServerErrorStatusCode, $followUserService, $userService, $createdStatusCode;


    public function __construct(Translator $translator,FollowUserService $followUserService, UserService $userService)
    {
        $this->followUserService = $followUserService;
        $this->userService = $userService;
        $this->badRequestStatusCode = Constants::badRequestStatusCode;
        $this->internalServerErrorStatusCode = Constants::internalServerErrorStatusCode;
        $this->createdStatusCode = Constants::createdStatusCode;
        $this->translator = $translator;
    }

     /**
     * To Follow and unfollow
     */
    public function followUser(Request $request)
    {
        //prepare validation
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'followed_user_id' => 'required|exists:users,id',
        ]);

        if($request->language_symbol){
            $this->translator->setLocale($request->language_symbol);
            $validator->setTranslator($this->translator);
        }

        if ($validator->fails()) {
            return responseMsgApi(implode("\n", Arr::flatten($validator->getMessageBag()->getMessages())), $this->badRequestStatusCode);
        }

        $response = $this->followUserService->followUserFromApi($request);
        return $response;
    }

    //follow list
    public function getFollower(Request $request){
        $response = $this->followUserService->getFollowedUsersFromApi($request);
        return $response;
    }

    public function searchFollower(Request $request){
        $response = $this->followUserService->searchFollowedUsersFromApi($request);
        return $response;
    }

    //item list from follower
    public function itemListFromFollower(Request $request){
        $response = $this->followUserService->getItemListFromApi($request);
        return $response;
    }
}
