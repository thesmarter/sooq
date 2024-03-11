<?php

namespace Modules\Chat\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ChatNoti extends Model
{
    use HasFactory;

    protected $fillable = ['id', 'item_id', 'buyer_user_id', 'seller_user_id', 'type', 'chat_noti_message', 'chat_flag', 'is_read', 'added_date', 'added_user_id', 'updated_user_id', 'updated_date', 'updated_flag'];

    protected $table = "psx_chat_notis";

    const CREATED_AT = 'added_date';
    const UPDATED_AT = 'updated_date';

    const tableName = "psx_chat_notis";
    const id = "id";
    const itemId = "item_id";
    const buyerUserId = "buyer_user_id";
    const sellerUserId = "seller_user_id";
    const type = "type";
    const chatNotiMessage = "chat_not_message";
    const chatFlag = "chat_flag";
    const isRead = "is_read";
    const addedDate = 'added_date';

    protected static function newFactory()
    {
        // return \Modules\Chat\Database\factories\ChatNotiFactory::new();
    }
}
