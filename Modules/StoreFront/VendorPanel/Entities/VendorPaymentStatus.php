<?php

namespace Modules\StoreFront\VendorPanel\Entities;

use App\Models\User;
use App\Policies\PsVendorPolicy;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Modules\Core\Constants\Constants;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class VendorPaymentStatus extends Model
{

    use HasFactory;

    protected $fillable = ['id', 'name', 'description', 'vendor_id', 'status', 'added_date', 'added_user_id', 'updated_date', 'updated_user_id', 'updated_flag'];

    protected $table = "psx_vendor_payment_statuses";

    const CREATED_AT = 'added_date';
    const UPDATED_AT = 'updated_date';

    const tableName = "psx_vendor_payment_statuses";
    const name = "name";
    const id = "id";
    const description = "description";
    const addedDate = 'added_date';
    const vendorId = 'vendor_id';
    const status = "status";


    protected static function newFactory()
    {
        // return \Modules\Core\Database\factories\CategoryFactory::new();
    }

    public function owner(){
        return $this->belongsTo(User::class, 'added_user_id');
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
            get: fn ($value) => $this->authorizations(Constants::vendorPaymentStatusModule, array_keys($params), PsVendorPolicy::class, $params),
        );

    }

    protected function authorization(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $this->authorizations(['update','delete','create']),
        );
    }


}
