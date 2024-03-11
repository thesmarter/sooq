<?php

namespace Modules\Chat\Entities;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Core\Entities\CoreImage;
use Modules\Core\Entities\Item;

class ChatHistory extends Model
{
    use HasFactory;

    protected $fillable = ['id', 'item_id', 'buyer_user_id', 'seller_user_id', 'nego_price', 'buyer_unread_count', 'seller_unread_count', 'is_accept', 'offer_status', 'added_date', 'added_user_id', 'updated_date', 'updated_user_id', 'updated_flag'];

    protected $table = "psx_chat_histories";

    const CREATED_AT = 'added_date';
    const UPDATED_AT = 'updated_date';

    const tableName = "psx_chat_histories";
    const id = "id";
    const itemId = "item_id";
    const buyerUserId = "buyer_user_id";
    const sellerUserId = "seller_user_id";
    const negoPrice = "nego_price";
    const buyerUnreadCount = "buyer_unread_count";
    const sellerUnreadCount = "sellerunread_count";
    const offerStatus = "is_accept";
    const isAccept = "offer_status";
    const addedDate = 'added_date';

    protected static function newFactory()
    {
        // return \Modules\Chat\Database\factories\ChatHistoryFactory::new();
    }

    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_user_id');
    }

    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_user_id');
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function defaultPhoto()
    {
        return $this->hasOne(CoreImage::class, 'img_parent_id', 'id')->where('img_type', 'chat');
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'added_user_id');
    }

    public function editor()
    {
        return $this->belongsTo(User::class, 'updated_user_id');
    }

}
