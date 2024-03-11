<?php

namespace Modules\Chat\Http\Controllers\Backend\Rests\App\V1_0\Chat;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use Modules\Chat\Http\Services\ChatService;
use Modules\Core\Constants\Constants;
use Illuminate\Contracts\Translation\Translator;

class ChatApiController extends Controller
{
    protected $translator,$chatService, $badRequestStatusCode;

    public function __construct(Translator $translator,ChatService $chatService)
    {
        $this->chatService = $chatService;

        $this->badRequestStatusCode = Constants::badRequestStatusCode;
        $this->translator = $translator;
    }

    public function store(Request $request)
    {
        //prepare validation
        $validator = Validator::make($request->all(), [
            'item_id' => 'required|exists:psx_items,id',
            'buyer_user_id' => 'required|exists:users,id',
            'seller_user_id' => 'required|exists:users,id',
            'type' => 'required',
            'is_user_online' => 'required',
        ]);

        if($request->language_symbol){
            $this->translator->setLocale($request->language_symbol);
            $validator->setTranslator($this->translator);
        }

        if ($validator->fails()) {
            return responseMsgApi(implode("\n", Arr::flatten($validator->getMessageBag()->getMessages())), $this->badRequestStatusCode);
        }
        /// validation end

        $chat = $this->chatService->storeFromApi($request);
        return $chat;
    }

    public function updatePrice(Request $request)
    {
        //prepare validation
        $validator = Validator::make($request->all(), [
            'item_id' => 'required|exists:psx_items,id',
            'buyer_user_id' => 'required|exists:users,id',
            'seller_user_id' => 'required|exists:users,id',
            'nego_price' => 'required',
            'type' => 'required',
            'is_user_online' => 'required',
        ]);

        if($request->language_symbol){
            $this->translator->setLocale($request->language_symbol);
            $validator->setTranslator($this->translator);
        }

        if ($validator->fails()) {
            return responseMsgApi(implode("\n", Arr::flatten($validator->getMessageBag()->getMessages())), $this->badRequestStatusCode);
        }
        /// validation end

        $chat = $this->chatService->updatePriceFromApi($request);
        return $chat;
    }

    public function show(Request $request)
    {
        //prepare validation
        $validator = Validator::make($request->all(), [
            'item_id' => 'required|exists:psx_items,id',
            'buyer_user_id' => 'required|exists:users,id',
            'seller_user_id' => 'required|exists:users,id',
        ]);

        if($request->language_symbol){
            $this->translator->setLocale($request->language_symbol);
            $validator->setTranslator($this->translator);
        }

        if ($validator->fails()) {
            return responseMsgApi(implode("\n", Arr::flatten($validator->getMessageBag()->getMessages())), $this->badRequestStatusCode);
        }
        /// validation end

        $chat = $this->chatService->getChatHistoryFromApi($request);
        return $chat;
    }

    public function chatImageUpload(Request $request){

        //prepare validation
        $validator = Validator::make($request->all(), [
            'item_id' => 'required|exists:psx_items,id',
            'buyer_user_id' => 'required|exists:users,id',
            'seller_user_id' => 'required|exists:users,id',
            'type' => 'required',
            'is_user_online' => 'required',
            'sender_id' => 'required',
            'file' => 'required',
        ]);

        if($request->language_symbol){
            $this->translator->setLocale($request->language_symbol);
            $validator->setTranslator($this->translator);
        }

        if ($validator->fails()) {
            return responseMsgApi(implode("\n", Arr::flatten($validator->getMessageBag()->getMessages())), $this->badRequestStatusCode);
        }
        /// validation end

        $chat = $this->chatService->chatImageUploadFromApi($request);
        return $chat;
    }

    public function chatImageDelete(Request $request){
        //prepare validation
        $validator = Validator::make($request->all(), [
            'file_name' => 'required',
        ]);

        if($request->language_symbol){
            $this->translator->setLocale($request->language_symbol);
            $validator->setTranslator($this->translator);
        }

        if ($validator->fails()) {
            return responseMsgApi(implode("\n", Arr::flatten($validator->getMessageBag()->getMessages())), $this->badRequestStatusCode);
        }
        /// validation end

        $chat = $this->chatService->chatImageDeleteFromApi($request);
        return $chat;
    }

    public function resetCount(Request $request){

        //prepare validation
        $validator = Validator::make($request->all(), [
            'item_id' => 'required|exists:psx_items,id',
            'buyer_user_id' => 'required|exists:users,id',
            'seller_user_id' => 'required|exists:users,id',
            'type' => 'required',
        ]);
        if($request->language_symbol){
            $this->translator->setLocale($request->language_symbol);
            $validator->setTranslator($this->translator);
        }

        if ($validator->fails()) {
            return responseMsgApi(implode("\n", Arr::flatten($validator->getMessageBag()->getMessages())), $this->badRequestStatusCode);
        }
        /// validation end

        $chat = $this->chatService->resetCountFromApi($request);
        return $chat;
    }

    public function unreadCount(Request $request){

        //prepare validation
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
        ]);

        if($request->language_symbol){
            $this->translator->setLocale($request->language_symbol);
            $validator->setTranslator($this->translator);
        }

        if ($validator->fails()) {
            return responseMsgApi(implode("\n", Arr::flatten($validator->getMessageBag()->getMessages())), $this->badRequestStatusCode);
        }
        /// validation end

        $data = $this->chatService->unreadCountFromApi($request);
        return $data;
    }

    /**
     * Offer list Api
     */
    function getOfferList(Request $request)
    {
        //prepare validation
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'return_type' => 'required',
        ]);

        if($request->language_symbol){
            $this->translator->setLocale($request->language_symbol);
            $validator->setTranslator($this->translator);
        }

        if ($validator->fails()) {
            return responseMsgApi(implode("\n", Arr::flatten($validator->getMessageBag()->getMessages())), $this->badRequestStatusCode);
        }
        /// validation end

        $data = $this->chatService->getAcceptOfferFromApi($request);
        return $data;
    }

    /**
     * is user bought
     */
    function isUserBought(Request $request)
    {
        //prepare validation
        $validator = Validator::make($request->all(), [
            'item_id' => 'required|exists:psx_items,id',
            'buyer_user_id' => 'required|exists:users,id',
            'seller_user_id' => 'required|exists:users,id',
            'is_user_online' => 'required',
        ]);

        if($request->language_symbol){
            $this->translator->setLocale($request->language_symbol);
            $validator->setTranslator($this->translator);
        }

        if ($validator->fails()) {
            return responseMsgApi(implode("\n", Arr::flatten($validator->getMessageBag()->getMessages())), $this->badRequestStatusCode);
        }
        /// validation end

        $data = $this->chatService->isUserBoughtFromApi($request);
        return $data;
    }

    /**
     * accept offer
     */
    function updateAccept(Request $request)
    {
        //prepare validation
        $validator = Validator::make($request->all(), [
            'item_id' => 'required|exists:psx_items,id',
            'buyer_user_id' => 'required|exists:users,id',
            'seller_user_id' => 'required|exists:users,id',
            'is_user_online' => 'required',
            'type' => 'required',
        ]);

        if($request->language_symbol){
            $this->translator->setLocale($request->language_symbol);
            $validator->setTranslator($this->translator);
        }

        if ($validator->fails()) {
            return responseMsgApi(implode("\n", Arr::flatten($validator->getMessageBag()->getMessages())), $this->badRequestStatusCode);
        }
        /// validation end

        $data = $this->chatService->acceptOfferFromApi($request);
        return $data;
    }

    /**
     * Item Sold Out
     */
    function itemSoldOut(Request $request){
       //prepare validation
        $validator = Validator::make($request->all(), [
            'item_id' => 'required|exists:psx_items,id',
            'buyer_user_id' => 'required|exists:users,id',
            'seller_user_id' => 'required|exists:users,id',
        ]);

        if($request->language_symbol){
            $this->translator->setLocale($request->language_symbol);
            $validator->setTranslator($this->translator);
        }

        if ($validator->fails()) {
            return responseMsgApi(implode("\n", Arr::flatten($validator->getMessageBag()->getMessages())), $this->badRequestStatusCode);
        }
        /// validation end

        $data = $this->chatService->itemSoldOutFromApi($request);
        return $data;
    }

    /**
     * Item Sold Out
     */
    function getBuyerSellerList(Request $request)
    {
        //prepare validation
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'return_type' => 'required',
        ]);

        if($request->language_symbol){
            $this->translator->setLocale($request->language_symbol);
            $validator->setTranslator($this->translator);
        }

        if ($validator->fails()) {
            return responseMsgApi(implode("\n", Arr::flatten($validator->getMessageBag()->getMessages())), $this->badRequestStatusCode);
        }
        /// validation end

        $data = $this->chatService->getBuyerSellerListFromApi($request);
        return $data;
    }
}
