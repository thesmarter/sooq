<?php

namespace Modules\PushNotificationMessage\Transformers\Api\App\V1_0;

use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Core\Constants\Constants;
use Modules\Core\Entities\CoreImage;
use Modules\Core\Entities\Item;
use Modules\Core\Transformers\Api\App\V1_0\CoreImage\CoreImageApiResource;
use Modules\Core\Entities\PushNotificationUser;

class PushNotificationMessageApiResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {

        // for noti message list api
        if(isset($this->chat_flag)){
            $is_read = $this->is_read;
        }else{
            $conds1[PushNotificationUser::noti_id] = $this->id;
            $conds1[PushNotificationUser::user_id] = $request->user_id;
            $conds1[PushNotificationUser::device_token] = $request->device_token;

            $noti_message = PushNotificationUser::where($conds1)->count();
            if ($noti_message > 0) {
                $is_read = 1;
            } else {
                $is_read = 0;
            }
        }

        if(isset($this->chat_flag) && !empty($this->chat_flag)){
            $type = $this->type;
        }else{
            $type = Constants::pushNotiType;
        }

        $message = '';
        $sender_name = '';
        $sender_cover_photo = '';
        if(isset($this->message)){
            $message = $this->message;
        }else if(isset($this->chat_noti_message)){
            if($this->type == Constants::chatMessageType){
                if($this->chat_flag == Constants::chatFromBuyer){
                    $user = User::find($this->buyer_user_id);
                }else{
                    $user = User::find($this->seller_user_id);
                }
                if(!$user){
                    $nameStr = 'Deleted User';
                }else{
                    $nameStr = $user->name;
                }   
                $message = __($this->chat_noti_message, ['name' => $nameStr ]);
                $sender_name = $nameStr;
                $sender_cover_photo = $user ? $user->user_cover_photo : '';
            }else if($this->type == Constants::offerAcceptedType || $this->type == Constants::offerRejectedType){
                $item = Item::find($this->item_id)->title;
                $message = __($this->chat_noti_message, ['item' => $item ]);
                if($this->chat_flag == Constants::chatFromBuyer){
                    $user = User::find($this->buyer_user_id);
                }else{
                    $user = User::find($this->seller_user_id);
                }
                $sender_name = $user->name;
                $sender_cover_photo = $user->user_cover_photo;
            }else{
                $message = __($this->chat_noti_message);
                if($this->chat_flag == Constants::chatFromBuyer){
                    $user = User::find($this->buyer_user_id);
                }else{
                    $user = User::find($this->seller_user_id);
                }
                if(!$user){
                    $nameStr = 'Deleted User';
                }else{
                    $nameStr = $user->name;
                }   
                $sender_name = $nameStr;
                $sender_cover_photo = $user ? $user->user_cover_photo : '';
            }
        }

        return [
            'id' => isset($this->id)?(string)$this->id:'',
            'type' => (string)$type,
            'message' => isset($this->id)?(string)$message:'',
            'description' => isset($this->description)?(string)$this->description:'',
            "is_read" => (string)$is_read,
            "default_photo" => new CoreImageApiResource(isset($this->defaultPhoto) && $this->defaultPhoto ? $this->whenLoaded('defaultPhoto'): CoreImage::where(CoreImage::id, 0)->get()),
            'added_date' => isset($this->added_date)?(string)$this->added_date:'',
            "added_date_str" => isset($this->added_date)?(string)$this->added_date->diffForHumans():'',
            'chat_flag' => isset($this->chat_flag)?(string)$this->chat_flag:'',
            'buyer_user_id' => isset($this->buyer_user_id)?(string)$this->buyer_user_id:'',
            'seller_user_id' => isset($this->seller_user_id)?(string)$this->seller_user_id:'',
            'sender_name' => (string)$sender_name,
            'sender_cover_photo' => (string)$sender_cover_photo,
            'item_id' => isset($this->item_id)?(string)$this->item_id:'',
            "is_empty_object" => $this->when(!isset($this->id),1),
        ];
    }
}
