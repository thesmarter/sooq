<?php

namespace Modules\Chat\Http\Services;

use App\Config\ps_constant;
use App\Http\Services\PsService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Modules\Core\Constants\Constants;
use Illuminate\Support\Facades\Auth;
use Modules\BlockUser\Http\Services\BlockUserService;
use Modules\Chat\Entities\ChatHistory;
use Modules\Chat\Entities\ChatNoti;
use Modules\Chat\Transformers\Api\App\V1_0\Chat\ChatHistoryApiResource;
use Modules\Chat\Transformers\Api\App\V1_0\Chat\UnreadCountApiResource;
use Modules\ComplaintItem\Http\Services\ComplaintItemService;
use Modules\Core\Entities\CoreImage;
use Modules\Core\Entities\PushNotificationUser;
use Modules\Core\Http\Services\ImageService;
use Modules\Core\Http\Services\ItemService;
use Modules\Core\Http\Services\PushNotificationTokenService;
use Modules\Core\Http\Services\SystemConfigService;
use Modules\Core\Http\Services\UserAccessApiTokenService;
use Modules\Core\Http\Services\UserService;
use Modules\Core\Transformers\Api\App\V1_0\CoreImage\CoreImageApiResource;
use Modules\PushNotificationMessage\Http\Services\PushNotificationMessageService;
use stdClass;

class ChatService extends PsService
{
    protected $chatNotiIdCol, $chatNotiItemIdCol, $chatNotiChatFlagCol, $chatNotiSellerUserIdCol, $chatNotiBuyerUserIdCol, $coreImageImgWidthCol, $coreImageImgHeightCol, $coreImageImgPathCol, $imgType, $chatIdCol, $chatMessageType, $offerReceivedType, $offerRejectedType, $offerAcceptedType, $chatFromBuyer, $chatFromSeller, $createdStatusCode, $okStatusCode, $noContentStatusCode, $successStatus, $imageService, $chatToSeller, $chatToBuyer, $coverImgType, $coreImageImgParentIdCol, $coreImageImgTypeCol, $chatApiRelations, $itemService, $badRequestStatusCode, $internalServerErrorStatusCode, $pushNotificationTokenService, $userService, $notFoundStatusCode, $chatBuyerReturnType, $chatSellerReturnType, $userBoughtService, $blockUserService, $complaintItemService, $systemConfigService, $deviceTokenParaApi, $loginUserIdParaApi, $userAccessApiTokenService, $forbiddenStatusCode, $pushNotificationMessageService;

    public function __construct(PushNotificationMessageService $pushNotificationMessageService, ImageService $imageService, ItemService $itemService, PushNotificationTokenService $pushNotificationTokenService, UserService $userService, UserBoughtService $userBoughtService, BlockUserService $blockUserService, ComplaintItemService $complaintItemService, SystemConfigService $systemConfigService, UserAccessApiTokenService $userAccessApiTokenService)
    {
        $this->imageService = $imageService;
        $this->itemService = $itemService;
        $this->pushNotificationTokenService = $pushNotificationTokenService;
        $this->userService = $userService;
        $this->userBoughtService = $userBoughtService;
        $this->blockUserService = $blockUserService;
        $this->complaintItemService = $complaintItemService;
        $this->systemConfigService = $systemConfigService;
        $this->pushNotificationMessageService = $pushNotificationMessageService;

        $this->coreImageImgParentIdCol = CoreImage::imgParentId;
        $this->coreImageImgTypeCol = CoreImage::imgType;
        $this->coreImageImgWidthCol = CoreImage::imgWidth;
        $this->coreImageImgHeightCol = CoreImage::imgHeight;
        $this->coreImageImgPathCol = CoreImage::imgPath;

        $this->imgType = 'chat';

        $this->chatToBuyer = Constants::chatToBuyer;
        $this->chatToSeller = Constants::chatToSeller;
        $this->chatBuyerReturnType = Constants::chatBuyerReturnType;
        $this->chatSellerReturnType = Constants::chatSellerReturnType;
        $this->chatFromBuyer = Constants::chatFromBuyer;
        $this->chatFromSeller = Constants::chatFromSeller;
        $this->chatMessageType = Constants::chatMessageType;
        $this->offerAcceptedType = Constants::offerAcceptedType;
        $this->offerRejectedType = Constants::offerRejectedType;
        $this->offerReceivedType = Constants::offerReceivedType;

        $this->chatApiRelations = ['item', 'buyer', 'seller', 'defaultPhoto'];

        $this->chatIdCol = ChatHistory::id;
        $this->chatBuyerUserIdCol = ChatHistory::buyerUserId;
        $this->chatSellerUserIdCol = ChatHistory::sellerUserId;
        $this->chatItemIdCol = ChatHistory::itemId;
        $this->chatBuyerUnreadCountCol = ChatHistory::buyerUnreadCount;
        $this->chatSellerUserCountCol = ChatHistory::sellerUnreadCount;
        $this->chatNegoPriceCol = ChatHistory::negoPrice;
        $this->chatIsAcceptCol = ChatHistory::isAccept;
        $this->chatOfferStatusCol = ChatHistory::offerStatus;

        $this->chatNotiIdCol = ChatNoti::id;
        $this->chatNotiBuyerUserIdCol = ChatNoti::buyerUserId;
        $this->chatNotiSellerUserIdCol = ChatNoti::sellerUserId;
        $this->chatNotiChatFlagCol = ChatNoti::chatFlag;
        $this->chatNotiItemIdCol = ChatNoti::itemId;

        $this->badRequestStatusCode = Constants::badRequestStatusCode;
        $this->internalServerErrorStatusCode = Constants::internalServerErrorStatusCode;
        $this->notFoundStatusCode = Constants::notFoundStatusCode;
        $this->okStatusCode = Constants::okStatusCode;
        $this->noContentStatusCode = Constants::noContentStatusCode;
        $this->successStatus = Constants::successStatus;
        $this->createdStatusCode = Constants::createdStatusCode;
        $this->forbiddenStatusCode = Constants::forbiddenStatusCode;

        $this->userAccessApiTokenService = $userAccessApiTokenService;
        $this->loginUserIdParaApi = ps_constant::loginUserIdParaFromApi;
        $this->deviceTokenParaApi = ps_constant::deviceTokenKeyFromApi;
    }

    public function store($request)
    {
        DB::beginTransaction();

        try {
            $chat = new ChatHistory();

            if (isset($request->item_id) && !empty($request->item_id))
                $chat->item_id = $request->item_id;

            if (isset($request->buyer_user_id) && !empty($request->buyer_user_id))
                $chat->buyer_user_id = $request->buyer_user_id;

            if (isset($request->seller_user_id) && !empty($request->seller_user_id))
                $chat->seller_user_id = $request->seller_user_id;

            if (isset($request->nego_price) && !empty($request->nego_price))
                $chat->nego_price = $request->nego_price;
            else
                $chat->nego_price = 0;

            if (isset($request->buyer_unread_count) && !empty($request->buyer_unread_count))
                $chat->buyer_unread_count = $request->buyer_unread_count;
            else
                $chat->buyer_unread_count = 0;

            if (isset($request->seller_unread_count) && !empty($request->seller_unread_count))
                $chat->seller_unread_count = $request->seller_unread_count;
            else
                $chat->seller_unread_count = 0;

            if (isset($request->is_accept))
                $chat->is_accept = $request->is_accept;

            if (isset($request->message) && !empty($request->message))
                $chat->latest_chat_message = $request->message;

            if (isset($request->offer_status))
                $chat->offer_status = $request->offer_status;

            if (isset($request->added_user_id) && !empty($request->added_user_id))
                $chat->added_user_id = $request->added_user_id;
            else{
                if(Auth::check()){
                    $chat->added_user_id = Auth::user()->id;
                }else{
                    $chat->added_user_id = 0;
                }
            }

            $chat->save();

            // save chat photo
            if (isset($request->file) && !empty($request->file)){
                $imgParentId = $chat->id;

                $this->updateOrCreateImage($request, "cover", $imgParentId, $this->coverImgType);
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            return ['error' => $e->getMessage()];
        }

        return $chat;
    }

    public function update($id, $request)
    {
        DB::beginTransaction();

        try {
            $chat = $this->getChatHistory($id);

            if (isset($request->item_id) && !empty($request->item_id)) {
                $chat->item_id = $request->item_id;
            }

            if (isset($request->buyer_user_id) && !empty($request->buyer_user_id)) {
                $chat->buyer_user_id = $request->buyer_user_id;
            }

            if (isset($request->seller_user_id) && !empty($request->seller_user_id)) {
                $chat->seller_user_id = $request->seller_user_id;
            }

            if (isset($request->nego_price) && !empty($request->nego_price)) {
                $chat->nego_price = $request->nego_price;
            } else {
                $chat->nego_price = 0;
            }

            if (isset($request->buyer_unread_count) && !empty($request->buyer_unread_count)) {
                $chat->buyer_unread_count = $request->buyer_unread_count;
            } else {
                $chat->buyer_unread_count = 0;
            }

            if (isset($request->seller_unread_count) && !empty($request->seller_unread_count)) {
                $chat->seller_unread_count = $request->seller_unread_count;
            } else {
                $chat->seller_unread_count = 0;
            }

            if (isset($request->is_accept)) {
                $chat->is_accept = $request->is_accept;
            }

            if (isset($request->offer_status)) {
                $chat->offer_status = $request->offer_status;
            }

            if (isset($request->message) && !empty($request->message)) {
                $chat->latest_chat_message = $request->message;
            }

            if (isset($request->updated_user_id) && !empty($request->updated_user_id)) {
                $chat->updated_user_id = $request->updated_user_id;
            } else {
                if (Auth::check()) {
                    $chat->updated_user_id = Auth::user()->id;
                } else {
                    $chat->updated_user_id = 0;
                }
            }
            $chat->update();

            if(isset($request->file) && !empty($request->file)){
                $imgParentId = $chat->id;
                // save chat photo
                $this->updateOrCreateImage($request, "file", $imgParentId, $this->coverImgType);
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            return ['error' => $e->getMessage()];
        }

        return $chat;
    }

    public function updateOrCreateImage($request, $fileKey, $imgParentId, $imgType)
    {
        if ($request->file($fileKey)) {

            $conds[$this->coreImageImgParentIdCol] = $imgParentId;
            $conds[$this->coreImageImgTypeCol] = $imgType;
            $image = $this->getImage($conds);

            $file = $request->file($fileKey);
            $data[$this->coreImageImgParentIdCol] = $imgParentId;
            $data[$this->coreImageImgTypeCol] = $imgType;

            // if image, delete existing file and update
            if (!empty($image)) {
                // delete image from storage folder
                $this->imageService->deleteImage($image->img_path);
                $this->imageService->insertImage($file, $data, $image);
            } else {
                $this->imageService->insertImage($file, $data);
            }
        }
    }

    public function getChatNotis($conds = null, $relation = null, $limit = null, $offset = null)
    {
        $chats = ChatNoti::when($relation, function ($q, $relation) {
                    $q->with($relation);
                })
                ->when($limit, function ($query, $limit) {
                    $query->limit($limit);
                })
                ->when($offset, function ($query, $offset) {
                    $query->offset($offset);
                })
                ->when($conds, function ($query, $conds) {
                    if(isset($conds['login_user_id']) && $conds['login_user_id']!=null){

                        $conds1[$this->chatNotiBuyerUserIdCol] = $conds['login_user_id'];
                        $conds1[$this->chatNotiChatFlagCol] = $this->chatFromSeller;
                        if(isset($conds['is_read']) && $conds['is_read'] !== null){
                            $conds1['is_read'] = $conds['is_read'];
                        }
                        if(isset($conds['item_id']) && $conds['item_id'] !== null){
                            $conds1['item_id'] = $conds['item_id'];
                        }
                        $query->where($conds1);

                        $conds2[$this->chatNotiSellerUserIdCol] = $conds['login_user_id'];
                        $conds2[$this->chatNotiChatFlagCol] = $this->chatFromBuyer;
                        if(isset($conds['is_read']) && $conds['is_read'] !== null){
                            $conds2['is_read'] = $conds['is_read'];
                        }
                        if(isset($conds['item_id']) && $conds['item_id'] !== null){
                            $conds2['item_id'] = $conds['item_id'];
                        }
                        $query->orWhere(function($query) use ($conds2){
                            $query->where($conds2);
                        });
                    }else{
                        if(isset($conds[$this->chatNotiSellerUserIdCol]) && $conds[$this->chatNotiSellerUserIdCol] !== null){
                            $conds2[$this->chatNotiSellerUserIdCol] = $conds[$this->chatNotiSellerUserIdCol];
                        }
                        if(isset($conds[$this->chatNotiBuyerUserIdCol]) && $conds[$this->chatNotiBuyerUserIdCol] !== null){
                            $conds2[$this->chatNotiBuyerUserIdCol] = $conds[$this->chatNotiBuyerUserIdCol];
                        }
                        if(isset($conds[$this->chatNotiChatFlagCol]) && $conds[$this->chatNotiChatFlagCol] !== null){
                            $conds2[$this->chatNotiChatFlagCol] = $conds[$this->chatNotiChatFlagCol];
                        }
                        if(isset($conds['is_read']) && $conds['is_read'] !== null){
                            $conds2['is_read'] = $conds['is_read'];
                        }
                        if(isset($conds['item_id']) && $conds['item_id'] !== null){
                            $conds2['item_id'] = $conds['item_id'];
                        }
                        $query->orWhere(function($query) use ($conds2){
                            $query->where($conds2);
                        });
                    }

                })
                ->latest()->get();
        return $chats;
    }

    public function updateChatNoti($request){
        $chat = $this->getChatNoti($request->id);
        if(isset($request->is_read))
            $chat->is_read = $request->is_read;

        if(isset($request->updated_user_id))
            $chat->updated_user_id = $request->updated_user_id;

        $chat->update();


        if($chat->is_read == 1){
            $conds1[$this->chatNotiItemIdCol] = $chat->item_id;
            $conds1[$this->chatNotiBuyerUserIdCol] = $chat->buyer_user_id;
            $conds1[$this->chatNotiSellerUserIdCol] = $chat->seller_user_id;
            $conds1['chat_flag'] = $chat->chat_flag;
            $conds1['is_read'] = 0;
            $chats = $this->getChatNotis($conds1);
            foreach($chats as $chatNoti){
                $chatNoti->is_read = $chat->is_read;
                $chatNoti->update();
            }
        }


        $conds[$this->chatNotiItemIdCol] = $chat->item_id;
        $conds[$this->chatNotiBuyerUserIdCol] = $chat->buyer_user_id;
        $conds[$this->chatNotiSellerUserIdCol] = $chat->seller_user_id;
        $chatHistory = $this->getChatHistory(null, $conds);

        if (!empty($chatHistory)) {
            if($chat->is_read == 1){
                if ($chat->chat_flag == $this->chatFromBuyer) {
                    $chatHistory->seller_unread_count = 0;
                } else if ($chat->chat_flag == $this->chatFromSeller) {
                    $chatHistory->buyer_unread_count = 0;
                }
            }else{
                $conds1[$this->chatNotiItemIdCol] = $chat->item_id;
                $conds1[$this->chatNotiBuyerUserIdCol] = $chat->buyer_user_id;
                $conds1[$this->chatNotiSellerUserIdCol] = $chat->seller_user_id;
                $conds1['chat_flag'] = $chat->chat_flag;
                $conds1['is_read'] = 0;
                $count = $this->getChatNotis($conds1)->count();

                if ($chat->chat_flag == $this->chatFromBuyer) {
                    $chatHistory->seller_unread_count = $count;
                } else if ($chat->chat_flag == $this->chatFromSeller) {
                    $chatHistory->buyer_unread_count = $count;
                }
            }

            $chatHistory = $this->update($chatHistory->id, $chatHistory);

        }



        return $chat;
    }

    public function getChatNoti($id = null, $conds = null, $relation = null)
    {
        $chat = ChatNoti::when($id, function ($q, $id) {

                $q->where($this->chatIdCol, $id);
            })
            ->when($conds, function ($q, $conds) {
                $q->where($conds);
            })->when($relation, function ($q, $relation) {
                $q->with($relation);
            })->first();
        return $chat;
    }

    public function getChatHistories($relation = null, $limit = null, $offset = null, $conds = null, $in_conds = null, $condsNotIn = null)
    {
        $chats = ChatHistory::when($relation, function ($q, $relation) {
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
                ->when($in_conds, function ($query, $in_conds) {
                    if(isset($in_conds[$this->chatNotiBuyerUserIdCol]) && !empty($in_conds[$this->chatNotiBuyerUserIdCol]))
                        $query->whereIn($this->chatBuyerUserIdCol, $in_conds[$this->chatNotiBuyerUserIdCol]);

                    if(isset($in_conds[$this->chatNotiSellerUserIdCol]) && !empty($in_conds[$this->chatNotiSellerUserIdCol]))
                        $query->whereIn($this->chatSellerUserIdCol, $in_conds[$this->chatNotiSellerUserIdCol]);

                    if (isset($in_conds[$this->chatNotiItemIdCol]) && !empty($in_conds[$this->chatNotiItemIdCol]))
                        $query->whereIn($this->chatItemIdCol, $in_conds[$this->chatNotiItemIdCol]);

                })
                ->when($condsNotIn, function ($query, $condsNotIn) {
                    if(isset($condsNotIn[$this->chatNotiBuyerUserIdCol]) && !empty($condsNotIn[$this->chatNotiBuyerUserIdCol]))
                        $query->whereNotIn($this->chatBuyerUserIdCol, $condsNotIn[$this->chatNotiBuyerUserIdCol]);

                    if(isset($condsNotIn[$this->chatNotiSellerUserIdCol]) && !empty($condsNotIn[$this->chatNotiSellerUserIdCol]))
                        $query->whereNotIn($this->chatSellerUserIdCol, $condsNotIn[$this->chatNotiSellerUserIdCol]);

                    if (isset($condsNotIn[$this->chatNotiItemIdCol]) && !empty($condsNotIn[$this->chatNotiItemIdCol]))
                        $query->whereNotIn($this->chatItemIdCol, $condsNotIn[$this->chatNotiItemIdCol]);

                })
                ->latest()->get();
        return $chats;
    }

    public function getChatHistory($id = null, $conds = null, $relation = null)
    {
        $chat = ChatHistory::when($id, function ($q, $id) {
                $q->where($this->chatIdCol, $id);
            })
            ->when($conds, function ($q, $conds) {
                $q->where($conds);
            })->when($relation, function ($q, $relation) {
                $q->with($relation);
            })->first();
        return $chat;
    }

    public function deleteAllBy($conds = null)
    {
        DB::beginTransaction();
        try {
            ChatHistory::when($conds, function ($query, $conds) {
                $query->where($conds);

            })->delete();

            DB::commit();
            return true;
        } catch (\Throwable $e) {
            DB::rollBack();
            return ['error' => $e->getMessage()];
        }
    }

    public function getImages($chat)
    {
        $images = CoreImage::where($this->coreImageImgParentIdCol, $chat->id)->get();
        return $images;
    }

    public function getImage($conds)
    {
        $image = $this->imageService->getImage($conds);
        return $image;
    }

    public function destroy($id = null, $conds = null)
    {
        if($id){
            $chat = ChatHistory::find($id);
            $images = $this->getImages($chat);
            deleteImages($images);
            $chat->delete();
        }

        if($conds){
            $chats = $this->getChatHistories(null, null, null, $conds);

            foreach($chats as $chat) {
                $images = $this->getImages($chat);
                deleteImages($images);
                $chat->delete();
            }
        }
    }

    /**
     * need message, flag, buyer_user_id, seller_user_id, sender_user_id, receiver_user_id
     */
    public function sendChatNoti($chat_data)
    {
        // start noti send to sender user
        $token_conds['user_id'] = $chat_data['receiver_user_id'];
        $notiTokens = $this->pushNotificationTokenService->getNotiTokens(null, $token_conds);
        $device_ids = [];
        $platform_names = [];
        foreach ($notiTokens as $token) {
            $device_ids[] = $token->device_token;
            $platform_names[] = $token->platform_name;
        }

        // get reveiver data
        $receiver = $this->userService->getUser($chat_data['receiver_user_id']);

        $user = $this->userService->getUser($chat_data['sender_user_id']);
        $data['message'] = $chat_data['message'];
        $data[$this->chatNotiBuyerUserIdCol] = $chat_data[$this->chatNotiBuyerUserIdCol];
        $data[$this->chatNotiSellerUserIdCol] = $chat_data[$this->chatNotiSellerUserIdCol];
        $data['sender_name'] = $user->name;
        $data['user_name'] = $receiver->name;
        $data[$this->chatNotiItemIdCol] = $chat_data[$this->chatNotiItemIdCol];
        $data['sender_profile_photo'] = $user->user_cover_photo;
        $data['user_profile_photo'] = $receiver->user_cover_photo;
        $data['flag'] = Constants::chatNotiFlag;
        $data['chat_flag'] = $chat_data['flag'];
        send_android_fcm($device_ids, $data, $platform_names);
        // end noti send to sender user

    }

    // for api
    /**
     * Add Chat History
     */
    public function storeFromApi($request)
    {

        /// check permission start
        $deviceToken = $request->header($this->deviceTokenParaApi);
        $userId = $request->login_user_id;

        // user token layer permission start
        $userAccessApiToken = $this->userAccessApiTokenService->getUserAccessApiToken($userId, $deviceToken);
        // user token layer permission end

        /// check permission end

        // if (empty($userAccessApiToken)){
        //     $msg = __('chatting__api_create_no_permission',[],$request->language_symbol);
        //     return responseMsgApi($msg, $this->forbiddenStatusCode);
        // }else{
            $item = $this->itemService->getItem($request->item_id);

            if($item->status == -1){
                return responseMsgApi(__('chatting__api_cannot_chat',[],$request->language_symbol), $this->badRequestStatusCode);
            }
            $conds[$this->chatNotiItemIdCol] = $request->item_id;
            $conds[$this->chatNotiBuyerUserIdCol] = $request->buyer_user_id;
            $conds[$this->chatNotiSellerUserIdCol] = $request->seller_user_id;
            $chat_histories = $this->getChatHistories(null, null, null, $conds);

            if(count($chat_histories) == 0){
                $chatData = new \stdClass();
                $chatData->item_id = $request->item_id;
                $chatData->buyer_user_id = $request->buyer_user_id;
                $chatData->seller_user_id = $request->seller_user_id;

                if(isset($request->message) && !empty($request->message)){
                    $chatData->message = $request->message;
                }

                if($request->is_user_online == 1) {
                    //if user is online, no need to send noti and no need to add unread count

                    if($request->type == $this->chatToBuyer){
                        $chatData->buyer_unread_count = 0;
                    }elseif($request->type == $this->chatToSeller){
                        $chatData->seller_unread_count = 0;
                    }

                    $chatData->offer_status = 1;

                    $chat = $this->store($chatData);
                    if (isset($chat['error'])) {
                        return responseMsgApi(__('chatting__api_db_error',[],$request->language_symbol), $this->internalServerErrorStatusCode);
                    }
                }else{
                    //if user is offline, send noti and add unread count, save chat_notis

                    // for send noti data
                    $data[$this->chatNotiSellerUserIdCol] = $request->seller_user_id;
                    $data[$this->chatNotiBuyerUserIdCol] = $request->buyer_user_id;
                    $data['message'] = $request->message;
                    $data[$this->chatNotiItemIdCol] = $request->item_id;

                    if ($request->type == $this->chatToBuyer) {
                        $chatData->buyer_unread_count = 1;

                        // for send noti data
                        $data['sender_user_id'] = $request->seller_user_id;
                        $data['receiver_user_id'] = $request->buyer_user_id;
                        $data['flag'] = $this->chatFromSeller;

                        // save noti to chat_notis
                        $noti = new ChatNoti();
                        $noti->seller_user_id = $request->seller_user_id;
                        $noti->buyer_user_id = $request->buyer_user_id;
                        $noti->item_id = $request->item_id;
                        $noti->type = $this->chatMessageType;
                        $noti->chat_noti_message = 'chatting__api_new_message_received';
                        $noti->chat_flag = $this->chatFromSeller;
                        $noti->is_read = 0;
                        $noti->added_user_id = $request->login_user_id;
                        $noti->added_date = Carbon::now();
                        $noti->save();
                    } elseif ($request->type == $this->chatToSeller) {
                        $chatData->seller_unread_count = 1;

                        // for send noti data
                        $data['sender_user_id'] = $request->buyer_user_id;
                        $data['receiver_user_id'] = $request->seller_user_id;
                        $data['flag'] = $this->chatFromBuyer;

                        // save noti to chat_notis
                        $noti = new ChatNoti();
                        $noti->seller_user_id = $request->seller_user_id;
                        $noti->buyer_user_id = $request->buyer_user_id;
                        $noti->item_id = $request->item_id;
                        $noti->type = $this->chatMessageType;
                        $noti->chat_noti_message = 'chatting__api_new_message_received';
                        $noti->chat_flag = $this->chatFromBuyer;
                        $noti->is_read = 0;
                        $noti->added_user_id = $request->login_user_id;
                        $noti->added_date = Carbon::now();
                        $noti->save();
                    }

                    $chatData->offer_status = 1;
                    $chat = $this->store($chatData);

                    if (isset($chat['error'])) {
                        return responseMsgApi(__('chatting__api_db_error',[],$request->language_symbol), $this->internalServerErrorStatusCode);
                    }

                    $this->sendChatNoti($data);
                }

            }else {
                $id = $chat_histories[0]['id'];

                $chat  = $this->getChatHistory($id);

                if(isset($request->message) && !empty($request->message)){
                    $chat->message = $request->message;
                }
                if ($request->is_user_online == 1) {
                    //if user is online, no need to send noti and no need to add unread count

                    $chat = $this->update($chat->id, $chat);

                    if (isset($chat['error'])) {
                        return responseMsgApi(__('chatting__api_db_error',[],$request->language_symbol), $this->internalServerErrorStatusCode);
                    }

                } else {
                    //if user is offline, send noti and add unread count, save chat_notis

                    // for send noti data
                    $data[$this->chatNotiSellerUserIdCol] = $request->seller_user_id;
                    $data[$this->chatNotiBuyerUserIdCol] = $request->buyer_user_id;
                    $data['message'] = $request->message;
                    $data[$this->chatNotiItemIdCol] = $request->item_id;

                    if ($request->type == $this->chatToBuyer) {
                        // increase noti count
                        $chat->buyer_unread_count = (int)$chat->buyer_unread_count + 1;

                        // for send noti data
                        $data['sender_user_id'] = $request->seller_user_id;
                        $data['receiver_user_id'] = $request->buyer_user_id;
                        $data['flag'] = $this->chatFromSeller;


                        // save noti to chat_notis
                        $noti = new ChatNoti();
                        $noti->seller_user_id = $request->seller_user_id;
                        $noti->buyer_user_id = $request->buyer_user_id;
                        $noti->item_id = $request->item_id;
                        $noti->type = $this->chatMessageType;
                        $noti->chat_noti_message = 'chatting__api_new_message_received';
                        $noti->chat_flag = $this->chatFromSeller;
                        $noti->is_read = 0;
                        $noti->added_user_id = $request->login_user_id;
                        $noti->added_date = Carbon::now();
                        $noti->save();
                    } elseif ($request->type == $this->chatToSeller) {
                        // increase noti count
                        $chat->seller_unread_count = (int)$chat->seller_unread_count + 1;

                        // for send noti data
                        $data['sender_user_id'] = $request->buyer_user_id;
                        $data['receiver_user_id'] = $request->seller_user_id;
                        $data['flag'] = $this->chatFromBuyer;

                        // save noti to chat_notis
                        $noti = new ChatNoti();
                        $noti->seller_user_id = $request->seller_user_id;
                        $noti->buyer_user_id = $request->buyer_user_id;
                        $noti->item_id = $request->item_id;
                        $noti->type = $this->chatMessageType;
                        $noti->chat_noti_message = 'chatting__api_new_message_received';
                        $noti->chat_flag = $this->chatFromBuyer;
                        $noti->is_read = 0;
                        $noti->added_user_id = $request->login_user_id;
                        $noti->added_date = Carbon::now();
                        $noti->save();
                    }

                    $chat = $this->update($id, $chat);
                    if (isset($chat['error'])) {
                        return responseMsgApi(__('chatting__api_db_error',[],$request->language_symbol), $this->internalServerErrorStatusCode);
                    }
                    $this->sendChatNoti($data);
                }
            }

            $chatApiRelations = $this->chatApiRelations;
            $chat = $this->getChatHistory($chat['id'], null, $chatApiRelations);

            $chat = new ChatHistoryApiResource($chat);

            return responseDataApi($chat, $this->createdStatusCode, $this->successStatus);
        //}
    }

    /**
     * Update Price (Make Offer or Reject Offer)
     */
    public function updatePriceFromApi($request){

        /// check permission start
        $deviceToken = $request->header($this->deviceTokenParaApi);
        $userId = $request->login_user_id;

        // user token layer permission start
        $userAccessApiToken = $this->userAccessApiTokenService->getUserAccessApiToken($userId, $deviceToken);
        // user token layer permission end

        /// check permission end

        // if (empty($userAccessApiToken)){
        //     $msg = __('chatting__api_update_no_permission',[],$request->language_symbol);
        //     return responseMsgApi($msg, $this->forbiddenStatusCode);
        // }else{
            $item = $this->itemService->getItem($request->item_id);
            if($item->is_sold_out == 1 && $request->nego_price !=0){
                return responseMsgApi(__('chatting__api_already_sold_out',[],$request->language_symbol), $this->badRequestStatusCode);
            }else {

                $conds[$this->chatNotiItemIdCol] = $request->item_id;
                $conds[$this->chatNotiBuyerUserIdCol] = $request->buyer_user_id;
                $conds[$this->chatNotiSellerUserIdCol] = $request->seller_user_id;
                $chat = $this->getChatHistory(null, $conds);

                if (empty($chat)) {
                    $chatData = new \stdClass();
                    $chatData->item_id = $request->item_id;
                    $chatData->buyer_user_id = $request->buyer_user_id;
                    $chatData->seller_user_id = $request->seller_user_id;
                    $chatData->nego_price = $request->nego_price;

                    if ($request->nego_price == 0) {
                        $data['message'] = __('chatting__api_offer_rejected',[],$request->language_symbol);
                        $chatData->offer_status = 4;
                        $noti_message = "chatting__api_offer_rejected";
                        $noti_type = $this->offerRejectedType;
                    } else {
                        $data['message'] = __('chatting__api_make_offer',[],$request->language_symbol);
                        $chatData->offer_status = 2;
                        $noti_message = "chatting__api_offer_received";
                        $noti_type = $this->offerReceivedType;
                    }

                    if ($request->is_user_online == 1) {
                        //if user is online, no need to send noti and no need to add unread count

                        if ($request->type == $this->chatToBuyer) {
                            $chatData->buyer_unread_count = 0;
                        } elseif ($request->type == $this->chatToSeller) {
                            $chatData->seller_unread_count = 0;
                        }

                        $chat = $this->store($chatData);
                        if (isset($chat['error'])) {
                            return responseMsgApi(__('chatting__api_db_error',[],$request->language_symbol), $this->internalServerErrorStatusCode);
                        }
                    } else {
                        //if user is offline, send noti and add unread count, save chat_notis

                        // for send noti data
                        $data[$this->chatNotiSellerUserIdCol] = $request->seller_user_id;
                        $data[$this->chatNotiBuyerUserIdCol] = $request->buyer_user_id;
                        $data[$this->chatNotiItemIdCol] = $request->item_id;
                        // $data['message'] = '';

                        if ($request->type == $this->chatToBuyer) {
                            $chatData->buyer_unread_count = 1;

                            // for send noti data
                            $data['sender_user_id'] = $request->seller_user_id;
                            $data['receiver_user_id'] = $request->buyer_user_id;
                            $data['flag'] = $this->chatFromSeller;

                            // save noti to chat_notis
                            $noti = new ChatNoti();
                            $noti->seller_user_id = $request->seller_user_id;
                            $noti->buyer_user_id = $request->buyer_user_id;
                            $noti->item_id = $request->item_id;
                            $noti->type = $noti_type;
                            $noti->chat_noti_message = $noti_message;
                            $noti->chat_flag = $this->chatFromSeller;
                            $noti->is_read = 0;
                            $noti->added_user_id = $request->login_user_id;
                            $noti->added_date = Carbon::now();
                            $noti->save();
                        } elseif ($request->type == $this->chatToSeller) {
                            $chatData->seller_unread_count = 1;

                            // for send noti data
                            $data['sender_user_id'] = $request->buyer_user_id;
                            $data['receiver_user_id'] = $request->seller_user_id;
                            $data['flag'] = $this->chatFromBuyer;

                            // save noti to chat_notis
                            $noti = new ChatNoti();
                            $noti->seller_user_id = $request->seller_user_id;
                            $noti->buyer_user_id = $request->buyer_user_id;
                            $noti->item_id = $request->item_id;
                            $noti->type = $noti_type;
                            $noti->chat_noti_message = $noti_message;
                            $noti->chat_flag = $this->chatFromBuyer;
                            $noti->is_read = 0;
                            $noti->added_user_id = $request->login_user_id;
                            $noti->added_date = Carbon::now();
                            $noti->save();
                        }

                        $chat = $this->store($chatData);

                        if (isset($chat['error'])) {
                            return responseMsgApi(__('chatting__api_db_error',[],$request->language_symbol), $this->internalServerErrorStatusCode);
                        }

                        $this->sendChatNoti($data);
                    }

                } else {
                    if ($chat->offer_status != 2 && $chat->is_accept != 0 && $request->nego_price == 0) {
                        return responseMsgApi(__('chatting__api_need_make_offer',[],$request->language_symbol), $this->badRequestStatusCode);
                    } elseif ($chat->offer_status == 2 && $request->nego_price != 0) {
                        return responseMsgApi(__('chatting__api_already_accept_offer',[],$request->language_symbol), $this->badRequestStatusCode);
                    }else{
                        $chat->item_id = $request->item_id;
                        $chat->buyer_user_id = $request->buyer_user_id;
                        $chat->seller_user_id = $request->seller_user_id;

                        if ($request->nego_price == 0) {
                            $data['message'] = __('chatting__api_offer_rejected',[],$request->language_symbol);
                            $chat->offer_status = 4;
                            $noti_message = "chatting__api_offer_rejected";
                            $noti_type = $this->offerRejectedType;
                        } else {
                            $data['message'] = __('chatting__api_make_offer',[],$request->language_symbol);
                            $chat->offer_status = 2;
                            $noti_message = "chatting__api_offer_received";
                            $noti_type = $this->offerReceivedType;
                        }

                        if ($request->is_user_online == 1) {
                            //if user is online, no need to send noti and no need to add unread count

                            $chat = $this->update($chat->id, $chat);
                            if (isset($chat['error'])) {
                                return responseMsgApi(__('chatting__api_db_error',[],$request->language_symbol), $this->internalServerErrorStatusCode);
                            }
                        } else {
                            //if user is offline, send noti and add unread count, save chat_notis

                            // for send noti data
                            $data[$this->chatNotiSellerUserIdCol] = $request->seller_user_id;
                            $data[$this->chatNotiBuyerUserIdCol] = $request->buyer_user_id;
                            // $data['message'] = $request->message;
                            $data[$this->chatNotiItemIdCol] = $request->item_id;

                            if ($request->type == $this->chatToBuyer) {
                                // increase noti count
                                $chat->buyer_unread_count = (int) $chat->buyer_unread_count + 1;

                                // for send noti data
                                $data['sender_user_id'] = $request->seller_user_id;
                                $data['receiver_user_id'] = $request->buyer_user_id;
                                $data['flag'] = $this->chatFromSeller;

                                // save noti to chat_notis
                                $noti = new ChatNoti();
                                $noti->seller_user_id = $request->seller_user_id;
                                $noti->buyer_user_id = $request->buyer_user_id;
                                $noti->item_id = $request->item_id;
                                $noti->type = $noti_type;
                                $noti->chat_noti_message = $noti_message;
                                $noti->chat_flag = $this->chatFromSeller;
                                $noti->is_read = 0;
                                $noti->added_user_id = $request->login_user_id;
                                $noti->added_date = Carbon::now();
                                $noti->save();
                            } else if ($request->type == $this->chatToSeller) {
                                // increase noti count
                                $chat->seller_unread_count = (int) $chat->seller_unread_count + 1;

                                // for send noti data
                                $data['sender_user_id'] = $request->buyer_user_id;
                                $data['receiver_user_id'] = $request->seller_user_id;
                                $data['flag'] = $this->chatFromBuyer;

                                // save noti to chat_notis
                                $noti = new ChatNoti();
                                $noti->seller_user_id = $request->seller_user_id;
                                $noti->buyer_user_id = $request->buyer_user_id;
                                $noti->item_id = $request->item_id;
                                $noti->type = $noti_type;
                                $noti->chat_noti_message = $noti_message;
                                $noti->chat_flag = $this->chatFromBuyer;
                                $noti->is_read = 0;
                                $noti->added_user_id = $request->login_user_id;
                                $noti->added_date = Carbon::now();
                                $noti->save();
                            }

                            $chat = $this->update($chat->id, $chat);
                            if (isset($chat['error'])) {
                                return responseMsgApi(__('chatting__api_db_error',[],$request->language_symbol), $this->internalServerErrorStatusCode);
                            }
                            $this->sendChatNoti($data);
                        }
                    }
                }

                $chatApiRelations = $this->chatApiRelations;
                $chat = new ChatHistoryApiResource($this->getChatHistory($chat->id,null, $chatApiRelations));

                return responseDataApi($chat, $this->okStatusCode, $this->successStatus);
            //}
        }
    }

    /**
     * Get chat history
     */
    public function getChatHistoryFromApi($request)
    {
        /// check permission start
        $deviceToken = $request->header($this->deviceTokenParaApi);
        $userId = $request->login_user_id;

        // user token layer permission start
        $userAccessApiToken = $this->userAccessApiTokenService->getUserAccessApiToken($userId, $deviceToken);
        // user token layer permission end

        /// check permission end

        if (empty($userAccessApiToken)){
            $msg = __('chatting__api_no_permission',[],$request->language_symbol);
            return responseMsgApi($msg, $this->forbiddenStatusCode);
        }else{
            $conds[$this->chatNotiItemIdCol] = $request->item_id;
            $conds[$this->chatNotiBuyerUserIdCol] = $request->buyer_user_id;
            $conds[$this->chatNotiSellerUserIdCol] = $request->seller_user_id;
            $chat = $this->getChatHistory(null, $conds, $this->chatApiRelations);

            if($chat){
                $chat = new ChatHistoryApiResource($chat);
            }else{
                $chat = new \stdClass();
                $chat->chatItemId = $request->item_id;
                $chat = new ChatHistoryApiResource($chat);
            }

            return responseDataApi($chat, $this->okStatusCode, $this->successStatus);
        }
    }

    /**
     * Chat image upload
     */
    public function chatImageUploadFromApi($request)
    {
        /// check permission start
        $deviceToken = $request->header($this->deviceTokenParaApi);
        $userId = $request->login_user_id;

        // user token layer permission start
        $userAccessApiToken = $this->userAccessApiTokenService->getUserAccessApiToken($userId, $deviceToken);
        // user token layer permission end

        /// check permission end

        if (empty($userAccessApiToken)){
            $msg = __('chatting__api_no_permission',[],$request->language_symbol);
            return responseMsgApi($msg, $this->forbiddenStatusCode);
        }else{
            $conds[$this->chatNotiItemIdCol] = $request->item_id;
            $conds[$this->chatNotiBuyerUserIdCol] = $request->buyer_user_id;
            $conds[$this->chatNotiSellerUserIdCol] = $request->seller_user_id;
            $chat_histories = $this->getChatHistories(null, null, null, $conds);

            if (count($chat_histories) == 0) {
                $chatData = new \stdClass();
                $chatData->item_id = $request->item_id;
                $chatData->buyer_user_id = $request->buyer_user_id;
                $chatData->seller_user_id = $request->seller_user_id;

                if ($request->is_user_online == 1) {
                    //if user is online, no need to send noti and no need to add unread count

                    if ($request->type == $this->chatToBuyer) {
                        $chatData->buyer_unread_count = 0;
                    } elseif ($request->type == $this->chatToSeller) {
                        $chatData->seller_unread_count = 0;
                    }

                    $chatData->offer_status = 1;

                    $chat = $this->store($chatData);
                    if (isset($chat['error'])) {
                        return responseMsgApi(__('chatting__api_db_error',[],$request->language_symbol), $this->internalServerErrorStatusCode);
                    }
                } else {
                    //if user is offline, send noti and add unread count, save chat_notis

                    // for send noti data
                    $data[$this->chatNotiSellerUserIdCol] = $request->seller_user_id;
                    $data[$this->chatNotiBuyerUserIdCol] = $request->buyer_user_id;
                    $data['message'] = $request->message;
                    $data[$this->chatNotiItemIdCol] = $request->item_id;

                    if ($request->type == $this->chatToBuyer) {
                        $chatData->buyer_unread_count = 1;

                        // for send noti data
                        $data['sender_user_id'] = $request->seller_user_id;
                        $data['receiver_user_id'] = $request->buyer_user_id;
                        $data['flag'] = $this->chatFromSeller;

                        // save noti to chat_notis
                        $noti = new ChatNoti();
                        $noti->seller_user_id = $request->seller_user_id;
                        $noti->buyer_user_id = $request->buyer_user_id;
                        $noti->item_id = $request->item_id;
                        $noti->type = $this->chatMessageType;
                        $noti->chat_noti_message = 'chatting__api_new_message_received';
                        $noti->chat_flag = $this->chatFromSeller;
                        $noti->is_read = 0;
                        $noti->added_user_id = $request->login_user_id;
                        $noti->added_date = Carbon::now();
                        $noti->save();
                    } elseif ($request->type == $this->chatToSeller) {
                        $chatData->seller_unread_count = 1;

                        // for send noti data
                        $data['sender_user_id'] = $request->buyer_user_id;
                        $data['receiver_user_id'] = $request->seller_user_id;
                        $data['flag'] = $this->chatFromBuyer;

                        // save noti to chat_notis
                        $noti = new ChatNoti();
                        $noti->seller_user_id = $request->seller_user_id;
                        $noti->buyer_user_id = $request->buyer_user_id;
                        $noti->item_id = $request->item_id;
                        $noti->type = $this->chatMessageType;
                        $noti->chat_noti_message = 'chatting__api_new_message_received';
                        $noti->chat_flag = $this->chatFromBuyer;
                        $noti->is_read = 0;
                        $noti->added_user_id = $request->login_user_id;
                        $noti->added_date = Carbon::now();
                        $noti->save();
                    }

                    $chatData->offer_status = 1;
                    $chat = $this->store($chatData);

                    if (isset($chat['error'])) {
                        return responseMsgApi(__('chatting__api_db_error',[],$request->language_symbol), $this->internalServerErrorStatusCode);
                    }

                    $this->sendChatNoti($data);
                }

            } else {
                $id = $chat_histories[0]['id'];

                $chat = $this->getChatHistory($id);

                if ($request->is_user_online == 1) {
                    //if user is online, no need to send noti and no need to add unread count

                    $chat = $this->update($chat->id, $chat);

                    if (isset($chat['error'])) {
                        return responseMsgApi(__('chatting__api_db_error',[],$request->language_symbol), $this->internalServerErrorStatusCode);
                    }
                } else {
                    //if user is offline, send noti and add unread count, save chat_notis

                    // for send noti data
                    $data[$this->chatNotiSellerUserIdCol] = $request->seller_user_id;
                    $data[$this->chatNotiBuyerUserIdCol] = $request->buyer_user_id;
                    $data['message'] = __('chatting__api_chat_image',[],$request->language_symbol);
                    $data[$this->chatNotiItemIdCol] = $request->item_id;

                    if ($request->type == $this->chatToBuyer) {
                        // increase noti count
                        $chat->buyer_unread_count = (int) $chat->buyer_unread_count + 1;

                        // for send noti data
                        $data['sender_user_id'] = $request->seller_user_id;
                        $data['receiver_user_id'] = $request->buyer_user_id;
                        $data['flag'] = $this->chatFromSeller;

                        // save noti to chat_notis
                        $noti = new ChatNoti();
                        $noti->seller_user_id = $request->seller_user_id;
                        $noti->buyer_user_id = $request->buyer_user_id;
                        $noti->item_id = $request->item_id;
                        $noti->type = $this->chatMessageType;
                        $noti->chat_noti_message = 'chatting__api_new_message_received';
                        $noti->chat_flag = $this->chatFromSeller;
                        $noti->is_read = 0;
                        $noti->added_user_id = $request->login_user_id;
                        $noti->added_date = Carbon::now();
                        $noti->save();
                    } elseif ($request->type == $this->chatToSeller) {
                        // increase noti count
                        $chat->seller_unread_count = (int) $chat->seller_unread_count + 1;

                        // for send noti data
                        $data['sender_user_id'] = $request->buyer_user_id;
                        $data['receiver_user_id'] = $request->seller_user_id;
                        $data['flag'] = $this->chatFromBuyer;

                        // save noti to chat_notis
                        $noti = new ChatNoti();
                        $noti->seller_user_id = $request->seller_user_id;
                        $noti->buyer_user_id = $request->buyer_user_id;
                        $noti->item_id = $request->item_id;
                        $noti->type = $this->chatMessageType;
                        $noti->chat_noti_message = 'chatting__api_new_message_received';
                        $noti->chat_flag = $this->chatFromBuyer;
                        $noti->is_read = 0;
                        $noti->added_user_id = $request->login_user_id;
                        $noti->added_date = Carbon::now();
                        $noti->save();
                    }

                    $chat = $this->update($chat->id, $chat);
                    if (isset($chat['error'])) {
                        return responseMsgApi(__('chatting__api_db_error',[],$request->language_symbol), $this->internalServerErrorStatusCode);
                    }
                    $this->sendChatNoti($data);
                }
            }

            if ($_FILES){
                $file = file_get_contents($_FILES['file']['tmp_name']);
                $filename = md5(time()) . '.jpg';



                // save image file to storage
                $this->imageService->saveImage($file, $filename,'chatApi');
                $sizeInfo = getimagesize($_FILES['file']['tmp_name']);
                $imageData = new \stdClass();
                $imageData->img_parent_id = $chat->id;
                $imageData->img_type= $this->imgType;
                $imageData->img_width = $sizeInfo[0];
                $imageData->img_height = $sizeInfo[1];
                $imageData->img_path = $filename;
                if($request->type == $this->chatToBuyer){
                    $imageData->added_user_id = $request->seller_user_id;
                }else{
                    $imageData->added_user_id = $request->buyer_user_id;
                }

                // save image data to core_images
                $image = $this->imageService->store($imageData);
                $image = new CoreImageApiResource($image);

                return responseDataApi($image, $this->createdStatusCode, $this->successStatus);
            }
            return responseDataApi(__('chatting__api_not_file',[],$request->language_symbol), $this->badRequestStatusCode);
        }
    }

    /**
     * Chat image delete
     */
    public function chatImageDeleteFromApi($request)
    {
        /// check permission start
        $deviceToken = $request->header($this->deviceTokenParaApi);
        $userId = $request->login_user_id;

        // user token layer permission start
        $userAccessApiToken = $this->userAccessApiTokenService->getUserAccessApiToken($userId, $deviceToken);
        // user token layer permission end

        /// check permission end

        if (empty($userAccessApiToken)){
            $msg = __('chatting__api_no_permission',[],$request->language_symbol);
            return responseMsgApi($msg, $this->forbiddenStatusCode);
        }else{
            $fileName = $request->file_name;

            $conds['img_path'] = $fileName;
            $image = $this->imageService->getImage($conds);
            if(empty($image)){
                return responseMsgApi(__('core__api_record_not_found',[],$request->language_symbol));
            } else {
                $this->imageService->destroy($image->id);
                return responseMsgApi(__('core__api_delete_image_success',[],$request->language_symbol), $this->okStatusCode, $this->successStatus);
            }
        }
    }

    /**
     * Reset unread count
     */
    public function resetCountFromApi($request){
        /// check permission start
        $deviceToken = $request->header($this->deviceTokenParaApi);
        $userId = $request->login_user_id;

        // user token layer permission start
        $userAccessApiToken = $this->userAccessApiTokenService->getUserAccessApiToken($userId, $deviceToken);
        // user token layer permission end

        /// check permission end

        if (empty($userAccessApiToken)){
            $msg = __('chatting__api_no_permission',[],$request->language_symbol);
            return responseMsgApi($msg, $this->forbiddenStatusCode);
        }else{
            $conds1[$this->chatNotiItemIdCol] = $request->item_id;
            $conds1[$this->chatNotiBuyerUserIdCol] = $request->buyer_user_id;
            $conds1[$this->chatNotiSellerUserIdCol] = $request->seller_user_id;
            if($request->type == $this->chatToSeller){
                $conds1['chat_flag'] = $this->chatFromBuyer;
            }else{
                $conds1['chat_flag'] = $this->chatFromSeller;
            }

            //for noti
            $chatNotis = $this->getChatNotis($conds1);

            foreach($chatNotis as $chatNoti){
                $chatNoti->is_read = 1;
                $chatNoti->update();
            }

            $conds[$this->chatNotiItemIdCol] = $request->item_id;
            $conds[$this->chatNotiBuyerUserIdCol] = $request->buyer_user_id;
            $conds[$this->chatNotiSellerUserIdCol] = $request->seller_user_id;
            $chat = $this->getChatHistory(null, $conds);

            if (empty($chat)) {
                return responseMsgApi(__('chatting__api_err_chat_history_not_exist',[],$request->language_symbol), $this->notFoundStatusCode);
            } else {
                $chat->item_id = $request->item_id;
                $chat->buyer_user_id = $request->buyer_user_id;
                $chat->seller_user_id = $request->seller_user_id;

                if ($request->type == $this->chatToSeller) {
                    $chat->seller_unread_count = 0;
                } else if ($request->type == $this->chatToBuyer) {
                    $chat->buyer_unread_count = 0;
                }

                $chat = $this->update($chat->id, $chat);
                if (isset($chat['error'])) {
                    return responseMsgApi(__('chatting__api_db_error',[],$request->language_symbol), $this->internalServerErrorStatusCode);
                }

                $chatApiRelations = $this->chatApiRelations;
                $chat = new ChatHistoryApiResource($this->getChatHistory($chat->id, null, $chatApiRelations));

                return responseDataApi($chat, $this->okStatusCode, $this->successStatus);
            }
        }
    }

    /**
     * get user unread count
     */
    public function unreadCountFromApi($request){
        /// check permission start
        $deviceToken = $request->header($this->deviceTokenParaApi);
        $userId = $request->login_user_id;

        // user token layer permission start
        $userAccessApiToken = $this->userAccessApiTokenService->getUserAccessApiToken($userId, $deviceToken);
        // user token layer permission end

        /// check permission end

        if (empty($userAccessApiToken)){
            $msg = __('chatting__api_no_permission',[],$request->language_symbol);
            return responseMsgApi($msg, $this->forbiddenStatusCode);
        }else{
            //For buyer_unread_count
            $buyer_unread_count = 0;
            $conds_buyer[$this->chatNotiBuyerUserIdCol] = $request->user_id;
            $buyer_unread_records = $this->getChatHistories(null, null, null, $conds_buyer);

            foreach ($buyer_unread_records as $chat) {
                $buyer_unread_count += (int)$chat->buyer_unread_count;
            }

            //For seller_unread_count
            $seller_unread_count = 0;
            $conds_seller[$this->chatNotiSellerUserIdCol] = $request->user_id;
            $seller_unread_records = $this->getChatHistories(null, null, null, $conds_seller);
            foreach ($seller_unread_records as $chat) {
                $seller_unread_count += (int)$chat->seller_unread_count;
            }

            // For noti_unread_count
            $noti_unread_count = 0;
            $conds_noti['login_user_id'] = $userId;
            $conds_noti['is_read'] = 0;
            $chatMessages = $this->getChatNotis($conds_noti);
            $noti_unread_count = count($chatMessages);

            $noti = new stdClass;
            $noti->user_id = $request->user_id;
            $noti->device_token = $request->device_token;
            $pushNotis = $this->pushNotificationMessageService->allNotisFromApi($noti);
            foreach ($pushNotis as $pushNoti){
                $push_noti['device_token'] = $request->device_token;
                $push_noti['user_id'] = $request->user_id;
                $push_noti['noti_id'] = $pushNoti->id;
                $noti_read = PushNotificationUser::where($push_noti)->count();
                if ($noti_read == 0) {
                    $noti_unread_count = $noti_unread_count + 1;
                }
            }

            $count_object = new \stdClass();
            $count_object->buyer_unread_count = $buyer_unread_count;
            $count_object->seller_unread_count = $seller_unread_count;
            $count_object->noti_unread_count = $noti_unread_count;

            $unreadCount = new UnreadCountApiResource($count_object);
            return responseDataApi($unreadCount, $this->okStatusCode, $this->successStatus);
        }
    }

    /**
     * get accept offer list
     */
    public function getAcceptOfferFromApi($request){

        /// check permission start
        $deviceToken = $request->header($this->deviceTokenParaApi);
        $userId = $request->login_user_id;

        // user token layer permission start
        $userAccessApiToken = $this->userAccessApiTokenService->getUserAccessApiToken($userId, $deviceToken);
        // user token layer permission end

        /// check permission end

        if (empty($userAccessApiToken)){
            $msg = __('chatting__api_no_permission',[],$request->language_symbol);
            return responseMsgApi($msg, $this->forbiddenStatusCode);
        }else{
            $systemConfig = $this->systemConfigService->getSystemConfig();
            $limit = $request->limit;
            $offset = $request->offset;

            // $conds['nego_price'] = '0';
            $not_in_conds = null;
            $conds = null;
            if ($request->return_type== $this->chatBuyerReturnType) {
                $conds[$this->chatNotiSellerUserIdCol] = $request->user_id;

                /* Start For Block User */
                if($systemConfig->is_block_user == 1){
                    $block_ids = $this->blockUserService->getBlockUserIds($request->user_id);
                    if (!empty($block_ids)) {
                        $not_in_conds[$this->chatNotiBuyerUserIdCol] = $block_ids;
                    }
                }
                /* End For Block User */

            } else if ($request->return_type== $this->chatSellerReturnType) {
                $conds[$this->chatNotiBuyerUserIdCol] = $request->user_id;

                /* Start For Block User */
                if ($systemConfig->is_block_user == 1) {
                    $block_ids = $this->blockUserService->getBlockUserIds($request->user_id);
                    if (!empty($block_ids)) {
                        $not_in_conds[$this->chatNotiSellerUserIdCol] = $block_ids;
                    }
                }
                /* End For Block User */
            }

            /* Start For Item Report */
            $complaint_ids = $this->complaintItemService->getComplaintItemIds($request->user_id);
            if (!empty($complaint_ids)) {
                $not_in_conds[$this->chatNotiItemIdCol] = $complaint_ids;
            }
            /* End For Item Report */

            $chats = $this->getChatHistories(null, $limit, $offset, $conds,null, $not_in_conds);

            $id = '';
            if (!empty($chats)) {
                foreach ($chats as $chat) {
                    $id .= "" . $chat->id . ",";
                }
            }

            if ($id == "") {
                return responseMsgApi(__('chatting__api_record_not_found',[],$request->language_symbol), $this->noContentStatusCode, $this->successStatus);
            } else {
                $result = rtrim($id, ',');
                $conds[$this->chatIdCol] = $result;
                // echo json_encode($conds);exit;
                $chats = ChatHistoryApiResource::collection($chats);

                if (count($chats) == 0) {
                    return responseMsgApi(__('chatting__api_record_not_found',[],$request->language_symbol), $this->noContentStatusCode, $this->successStatus);
                } else {
                    return responseDataApi($chats, $this->okStatusCode, $this->successStatus);
                }
            }
        }
    }

    /**
     * is user bought
     */
    public function isUserBoughtFromApi($request){

        /// check permission start
        $deviceToken = $request->header($this->deviceTokenParaApi);
        $userId = $request->login_user_id;

        // user token layer permission start
        $userAccessApiToken = $this->userAccessApiTokenService->getUserAccessApiToken($userId, $deviceToken);
        // user token layer permission end

        /// check permission end

        // if (empty($userAccessApiToken)){
        //     $msg = __('chatting__api_no_permission',[],$request->language_symbol);
        //     return responseMsgApi($msg, $this->forbiddenStatusCode);
        // }else{

            $item_name = $this->itemService->getItem($request->item_id)->title;

            /** update accept offer status */
            $conds_chat[$this->chatNotiBuyerUserIdCol] = $request->buyer_user_id;
            $conds_chat[$this->chatNotiSellerUserIdCol] = $request->seller_user_id;
            $conds_chat[$this->chatNotiItemIdCol] = $request->item_id;

            $chat_data = new \stdClass;

            $chat_history_data = $this->getChatHistory(null, $conds_chat);
            if(empty($chat_history_data)){
                return responseMsgApi(__('chatting__api_need_make_offer_first',[],$request->language_symbol), $this->badRequestStatusCode);
            }
            if ($chat_history_data->offer_status == 4) {
                return responseMsgApi(__('chatting__api_already_reject_offer',[],$request->language_symbol), $this->badRequestStatusCode);
            } else {
                if ($chat_history_data->offer_status == '2' && $chat_history_data->is_accept == '1') {

                    $id = $chat_history_data->id;

                    $chat_data->offer_status = 3;
                    $chat_data->is_accept = 1;
                    $chat_data->is_offer = 1;
                    $chat_data->nego_price = $chat_history_data->nego_price;
                    $chat_data->updated_user_id = $request->login_user_id;

                    if ($request->is_user_online == 0) {
                        //if user is offline, send noti and add unread count, save chat_notis

                        /** start send noti to buyer */
                        $user = $this->userService->getUser($request->seller_user_id);
                        $data['message'] = __('chatting__api_you_bought',[],$request->language_symbol) . ' ' . $item_name;
                        $data[$this->chatNotiBuyerUserIdCol] = $request->buyer_user_id;
                        $data[$this->chatNotiSellerUserIdCol] = $request->seller_user_id;
                        $data['sender_name'] = $user->name;
                        $data[$this->chatNotiItemIdCol] = $request->item_id;
                        $data['sender_profile_photo'] = $user->user_cover_photo;
                        $data['flag'] = Constants::chatNotiFlag;
                        $data['chat_flag'] = $this->chatFromSeller;
                        $data['receiver_user_id'] = $request->buyer_user_id;
                        $data['sender_user_id'] = $request->seller_user_id;
                        /** end send noti to buyer */

                        //add buyer unread count
                        $chat_data->buyer_unread_count =  (int)$chat_history_data->buyer_unread_count + 1;

                        // save noti to chat_notis
                        // $noti = new ChatNoti();
                        // $noti->seller_user_id = $request->seller_user_id;
                        // $noti->buyer_user_id = $request->buyer_user_id;
                        // $noti->item_id = $request->item_id;
                        // $noti->type = $this->chatMessageType;
                        // $noti->chat_noti_message = 'chatting__api_you_bought';
                        // $noti->chat_flag = $this->chatFromSeller;
                        // $noti->is_read = 0;
                        // $noti->added_user_id = $request->login_user_id;
                        // $noti->added_date = Carbon::now();
                        // $noti->save();

                        $this->sendChatNoti($data);
                    }

                    $chat = $this->update($id, $chat_data);

                    /** save bought data */
                    $bought_data = new \stdClass();
                    $bought_data->item_id = $request->item_id;
                    $bought_data->buyer_user_id = $request->buyer_user_id;
                    $bought_data->seller_user_id = $request->seller_user_id;
                    $bought_data->added_user_id = $request->login_user_id;
                    $this->userBoughtService->store($bought_data);

                    $chat = new ChatHistoryApiResource($this->getChatHistory($id, null, $this->chatApiRelations));
                    return responseDataApi($chat, $this->okStatusCode ,$this->successStatus);
                } else if ($chat_history_data->offer_status == '3'){
                    return responseMsgApi(__('chatting__api_already_user_bought',[],$request->language_symbol), $this->badRequestStatusCode);
                } else {
                    return responseMsgApi(__('chatting__api_need_accept_offer',[],$request->language_symbol), $this->badRequestStatusCode);
                }
            }
        //}
    }

    public function acceptOfferFromApi($request){

        /// check permission start
        $deviceToken = $request->header($this->deviceTokenParaApi);
        $userId = $request->login_user_id;

        // user token layer permission start
        $userAccessApiToken = $this->userAccessApiTokenService->getUserAccessApiToken($userId, $deviceToken);
        // user token layer permission end

        /// check permission end

        // if (empty($userAccessApiToken)){
        //     $msg = __('chatting__api_no_permission',[],$request->language_symbol);
        //     return responseMsgApi($msg, $this->forbiddenStatusCode);
        // }else{
            $chat_data[$this->chatNotiItemIdCol] = $request->item_id;
            $chat_data[$this->chatNotiBuyerUserIdCol] = $request->buyer_user_id;
            $chat_data[$this->chatNotiSellerUserIdCol] = $request->seller_user_id;
            $chat_history_data = $this->getChatHistory(null, $chat_data);

            if (empty($chat_history_data)) {
                $chatData = new \stdClass();
                $chatData->item_id = $request->item_id;
                $chatData->buyer_user_id = $request->buyer_user_id;
                $chatData->seller_user_id = $request->seller_user_id;
                $chatData->nego_price = $request->nego_price;

                if ($request->is_user_online == '1') {
                    //if user is online, no need to send noti and no need to add unread count

                    if ($request->type == $this->chatToBuyer) {
                        $chatData->buyer_unread_count = 0;
                    } elseif ($request->type == $this->chatToSeller) {
                        $chatData->seller_unread_count = 0;
                    }

                    $chatData->is_accept = 1;
                    $chatData->offer_status = 2;

                    $chat = $this->store($chatData);
                    if (isset($chat['error'])) {
                        return responseMsgApi(__('chatting__api_db_error',[],$request->language_symbol), $this->internalServerErrorStatusCode);
                    }

                } else {
                    // for send noti data
                    $data[$this->chatNotiSellerUserIdCol] = $request->seller_user_id;
                    $data[$this->chatNotiBuyerUserIdCol] = $request->buyer_user_id;
                    $data['message'] = __('chatting__api_accept_offer',[],$request->language_symbol);
                    $data[$this->chatNotiItemIdCol] = $request->item_id;

                    if ($request->type == $this->chatToBuyer) {
                        $chatData->buyer_unread_count = 1;

                        // for send noti data
                        $data['sender_user_id'] = $request->seller_user_id;
                        $data['receiver_user_id'] = $request->buyer_user_id;
                        $data['flag'] = $this->chatFromSeller;

                        // save noti to chat_notis
                        $noti = new ChatNoti();
                        $noti->seller_user_id = $request->seller_user_id;
                        $noti->buyer_user_id = $request->buyer_user_id;
                        $noti->item_id = $request->item_id;
                        $noti->type = $this->offerAcceptedType;
                        $noti->chat_noti_message = 'chatting__api_offer_accepted';
                        $noti->chat_flag = $this->chatFromSeller;
                        $noti->is_read = 0;
                        $noti->added_user_id = $request->login_user_id;
                        $noti->added_date = Carbon::now();
                        $noti->save();
                    } elseif ($request->type == $this->chatToSeller) {
                        $chatData->seller_unread_count = 1;

                        // for send noti data
                        $data['sender_user_id'] = $request->buyer_user_id;
                        $data['receiver_user_id'] = $request->seller_user_id;
                        $data['flag'] = $this->chatFromBuyer;

                        // save noti to chat_notis
                        $noti = new ChatNoti();
                        $noti->seller_user_id = $request->seller_user_id;
                        $noti->buyer_user_id = $request->buyer_user_id;
                        $noti->item_id = $request->item_id;
                        $noti->type = $this->offerAcceptedType;
                        $noti->chat_noti_message = 'chatting__api_offer_accepted';
                        $noti->chat_flag = $this->chatFromBuyer;
                        $noti->is_read = 0;
                        $noti->added_user_id = $request->login_user_id;
                        $noti->added_date = Carbon::now();
                        $noti->save();
                    }

                    $chatData->is_accept = 1;
                    $chat = $this->store($chatData);
                    if (isset($chat['error'])) {
                        return responseMsgApi(__('chatting__api_db_error',[],$request->language_symbol), $this->internalServerErrorStatusCode);
                    }
                    $this->sendChatNoti($data);
                }

            } else {
                if ($chat_history_data->offer_status == 4) {
                    return responseMsgApi(__('chatting__api_already_rejected',[],$request->language_symbol), $this->badRequestStatusCode);
                } else {
                    if ($chat_history_data->offer_status == '2' && $chat_history_data->is_accept == '0') {
                        // is_accept checking
                        if ($chat_history_data->is_accept == 1) {
                            return responseMsgApi(__('chatting__api_err_accept_offer',[],$request->language_symbol), $this->badRequestStatusCode);
                        }
                    }
                    $id = $chat_history_data->id;

                    $chat = $this->getChatHistory($id);
                    $chat->is_accept = 1;
                    $chat->offer_status = 2;
                    if ($request->is_user_online == 1) {
                        //if user is online, no need to send noti and no need to add unread count

                        $chat = $this->update($chat->id, $chat);
                        if (isset($chat['error'])) {
                            return responseMsgApi(__('chatting__api_db_error',[],$request->language_symbol), $this->internalServerErrorStatusCode);
                        }
                    } else {
                        //if user is offline, send noti and add unread count, save chat_notis

                        // for send noti data
                        $data[$this->chatNotiSellerUserIdCol] = $request->seller_user_id;
                        $data[$this->chatNotiBuyerUserIdCol] = $request->buyer_user_id;
                        $data['message'] = __('chatting__api_accept_offer',[],$request->language_symbol);
                        $data[$this->chatNotiItemIdCol] = $request->item_id;

                        if ($request->type == $this->chatToBuyer) {
                            // increase noti count
                            $chat->buyer_unread_count = (int) $chat->buyer_unread_count + 1;

                            // for send noti data
                            $data['sender_user_id'] = $request->seller_user_id;
                            $data['receiver_user_id'] = $request->buyer_user_id;
                            $data['flag'] = $this->chatFromSeller;

                            // save noti to chat_notis
                            $noti = new ChatNoti();
                            $noti->seller_user_id = $request->seller_user_id;
                            $noti->buyer_user_id = $request->buyer_user_id;
                            $noti->item_id = $request->item_id;
                            $noti->type = $this->offerAcceptedType;
                            $noti->chat_noti_message = 'chatting__api_offer_accepted';
                            $noti->chat_flag = $this->chatFromSeller;
                            $noti->is_read = 0;
                            $noti->added_user_id = $request->login_user_id;
                            $noti->added_date = Carbon::now();
                            $noti->save();
                        } elseif ($request->type == $this->chatToSeller) {
                            // increase noti count
                            $chat->seller_unread_count = (int) $chat->seller_unread_count + 1;

                            // for send noti data
                            $data['sender_user_id'] = $request->buyer_user_id;
                            $data['receiver_user_id'] = $request->seller_user_id;
                            $data['flag'] = $this->chatFromBuyer;

                            // save noti to chat_notis
                            $noti = new ChatNoti();
                            $noti->seller_user_id = $request->seller_user_id;
                            $noti->buyer_user_id = $request->buyer_user_id;
                            $noti->item_id = $request->item_id;
                            $noti->type = $this->offerAcceptedType;
                            $noti->chat_noti_message = 'chatting__api_offer_accepted';
                            $noti->chat_flag = $this->chatFromBuyer;
                            $noti->is_read = 0;
                            $noti->added_user_id = $request->login_user_id;
                            $noti->added_date = Carbon::now();
                            $noti->save();
                        }

                        $chat = $this->update($id, $chat);
                        if (isset($chat['error'])) {
                            return responseMsgApi(__('chatting__api_db_error',[],$request->language_symbol), $this->internalServerErrorStatusCode);
                        }
                        $this->sendChatNoti($data);
                    }
                    $this->itemService->decreaseItemQuantity($request->item_id);
                }
            }

            $chatApiRelations = $this->chatApiRelations;
            $chat = $this->getChatHistory($chat['id'], null, $chatApiRelations);
            $chat = new ChatHistoryApiResource($chat);

            return responseDataApi($chat, $this->okStatusCode, $this->successStatus);
        //}
    }

    public function itemSoldOutFromApi($request){

        /// check permission start
        $deviceToken = $request->header($this->deviceTokenParaApi);
        $userId = $request->login_user_id;

        // user token layer permission start
        $userAccessApiToken = $this->userAccessApiTokenService->getUserAccessApiToken($userId, $deviceToken);
        // user token layer permission end

        /// check permission end

        if (empty($userAccessApiToken)){
            $msg = __('chatting__api_no_permission',[],$request->language_symbol);
            return responseMsgApi($msg, $this->forbiddenStatusCode);
        }else{
            $chat_data[$this->chatNotiItemIdCol] = $request->item_id;
            $chat_data[$this->chatNotiBuyerUserIdCol] = $request->buyer_user_id;
            $chat_data[$this->chatNotiSellerUserIdCol] = $request->seller_user_id;
            $chat_history_data = $this->getChatHistory(null, $chat_data);

            if (empty($chat_history_data)) {
                return responseMsgApi(__('chatting__api_cannot_sold_out',[],$request->language_symbol), $this->badRequestStatusCode);
            }else{
                if ($chat_history_data->offer_status == 4) {
                    return responseMsgApi(__('chatting__api_already_reject_offer',[],$request->language_symbol), $this->badRequestStatusCode);
                } else {
                    if ($chat_history_data->offer_status == '3') {
                        // $item_sold_out = new \stdClass;
                        // $item_sold_out->is_sold_out = 1;
                        // $item_sold_out->id = $request->item_id;
                        // $item_sold_out->updated_user_id = $request->seller_user_id;
                        // $this->itemService->updateCoreFieldValues($item_sold_out);
                        $this->itemService->decreaseItemQuantity($request->item_id, true);

                        $chatApiRelations = $this->chatApiRelations;
                        $chat = $this->getChatHistory($chat_history_data['id'], null, $chatApiRelations);
                        $chat = new ChatHistoryApiResource($chat);

                        return responseDataApi($chat, $this->okStatusCode, $this->successStatus);
                    } else {
                        return responseMsgApi(__('chatting__api_need_user_bought',[],$request->language_symbol), $this->badRequestStatusCode);
                    }
                }
            }
        }
    }

    public function getBuyerSellerListFromApi($request){

        /// check permission start
        $deviceToken = $request->header($this->deviceTokenParaApi);
        $userId = $request->login_user_id;

        // user token layer permission start
        $userAccessApiToken = $this->userAccessApiTokenService->getUserAccessApiToken($userId, $deviceToken);
        // user token layer permission end

        /// check permission end

        if (empty($userAccessApiToken)){
            $msg = __('chatting__api_no_permission',[],$request->language_symbol);
            return responseMsgApi($msg, $this->forbiddenStatusCode);
        }else{
            $systemConfig = $this->systemConfigService->getSystemConfig();

            $limit = $request->limit;
            $offset = $request->offset;

            $in_conds = null;
            $not_in_conds = null;
            if ($request->return_type == $this->chatBuyerReturnType) {
                $conds[$this->chatNotiSellerUserIdCol] = $request->user_id;

                /* Start For Block User */
                if ($systemConfig->is_block_user == 1) {
                    $block_ids = $this->blockUserService->getBlockUserIds($request->user_id);
                    if (!empty($block_ids)) {
                        $not_in_conds[$this->chatNotiBuyerUserIdCol] = $block_ids;
                    }
                }
                /* End For Block User */

            } else if ($request->return_type == $this->chatSellerReturnType) {
                $conds[$this->chatNotiBuyerUserIdCol] = $request->user_id;

                /* Start For Block User */
                if ($systemConfig->is_block_user == 1) {
                    $block_ids = $this->blockUserService->getBlockUserIds($request->user_id);
                    if (!empty($block_ids)) {
                        $not_in_conds[$this->chatNotiSellerUserIdCol] = $block_ids;
                    }
                }
                /* End For Block User */
            }

            /* Start For Item Report */
            $complaint_ids = $this->complaintItemService->getComplaintItemIds($request->user_id);
            if (!empty($complaint_ids)) {
                $not_in_conds[$this->chatNotiItemIdCol] = $complaint_ids;
            }
            /* End For Item Report */

            $chats = $this->getChatHistories($this->chatApiRelations, $limit, $offset, $conds, $in_conds, $not_in_conds);
            // $chat_ids = '';
            // if (!empty($chats)) {
            //     foreach ($chats as $chat) {
            //         $chat_ids .= $chat->id . ",";
            //     }
            // }

            // if ($chat_ids == "") {
            //     return responseMsgApi(__('chatting__api_record_not_found',[],$request->language_symbol), $this->noContentStatusCode, $this->successStatus);
            // } else {

                $chats = ChatHistoryApiResource::collection($chats);

                if (count($chats) == 0) {
                    return responseMsgApi(__('chatting__api_record_not_found',[],$request->language_symbol), $this->noContentStatusCode, $this->successStatus);
                    // $chats = ChatHistoryApiResource::collection(['empty']);
                } else {
                    $chats = ChatHistoryApiResource::collection($chats);
                }
                return responseDataApi($chats, $this->okStatusCode, $this->successStatus);
            // }
        }
    }
}
