<?php

namespace Modules\StoreFront\VendorPanel\Entities;

use App\Models\User;
use App\Policies\PsVendorPolicy;
use Modules\Core\Entities\Vendor;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Modules\Core\Constants\Constants;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{

    use HasFactory;

    protected $fillable = ['id', 'user_id', 'vendor_id', 'order_date', 'total_amount', 'order_status_id', 'order_code', 'added_date', 'added_user_id', 'updated_date', 'updated_user_id', 'updated_flag'];

    protected $table = "psx_orders";

    const CREATED_AT = 'added_date';
    const UPDATED_AT = 'updated_date';

    const tableName = "psx_orders";
    const userId = "user_id";
    const id = "id";
    const orderCode = "order_code";
    const orderStatusId = "order_status_id";
    const vendorId = "vendor_id";
    const status = "status";
    const addedDate = 'added_date';


    protected static function newFactory()
    {
        // return \Modules\Core\Database\factories\CategoryFactory::new();
    }

    public function orderItems(){
        return $this->hasMany(OrderItem::class, 'order_id', 'id');
    }

    public function orderStatus(){
        return $this->belongsTo(OrderStatus::class, "order_status_id", "id");
    }

    public function shippingAndBilling(){
        return $this->hasOne(ShippingAndBilling::class, 'order_id', 'id');
    }

    public function vendorTransaction(){
        return $this->belongsTo(VendorTransaction::class, 'id', 'order_id');
    }

    public function owner(){
        return $this->belongsTo(User::class, 'added_user_id');
    }

    public function vendor(){
        return $this->belongsTo(Vendor::class, 'vendor_id');
    }

    public function editor(){
        return $this->belongsTo(User::class, 'updated_user_id');
    }

    public function authorizations($module, $abilities = [], $policyClass = null, $params = [])
    {
        $policy = $policyClass ? new $policyClass($module, $this) : null;

        return collect($abilities)->mapWithKeys(function ($ability) use ($policy, $params) {
            return [$ability => $policy ? $policy->{$ability}(...($params[$ability] ?? [])) : false];
        });
    }

    public function vendorAuthorization(): Attribute
    {
        $params = [
            'update' => [Auth::user(), $this],
            'create' => [Auth::user()],
            'delete' => [Auth::user(), $this],
        ];

        return Attribute::make(
            get: fn ($value) => $this->authorizations(Constants::vendorOrderListModule, array_keys($params), PsVendorPolicy::class, $params),
        );

    }

    protected function authorization(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $this->authorizations(['update','delete','create']),
        );
    }


}
