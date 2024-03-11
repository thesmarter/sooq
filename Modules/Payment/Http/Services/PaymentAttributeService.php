<?php

namespace Modules\Payment\Http\Services;

use App\Http\Services\PsService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Modules\Payment\Entities\PaymentAttribute;

class PaymentAttributeService extends PsService
{
    protected $paymentAttrIdCol, $paymentAttrPaymentIdCol, $paymentAttrCoreKeysIdCol, $paymentAttrKeyCol, $paymentAttrValueCol;

    public function __construct()
    {
        $this->paymentAttrIdCol = PaymentAttribute::id;
        $this->paymentAttrPaymentIdCol = PaymentAttribute::paymentId;
        $this->paymentAttrCoreKeysIdCol = PaymentAttribute::coreKeysId;
        $this->paymentAttrKeyCol = PaymentAttribute::attributeKey;
        $this->paymentAttrValueCol = PaymentAttribute::attributeValue;
    }

    public function store($request)
    {
        DB::beginTransaction();

        try {
            $paymentAttribute = new PaymentAttribute();
            if (isset($request->payment_id) && !empty($request->payment_id))
                $paymentAttribute->payment_id = $request->payment_id;

            if (isset($request->core_keys_id) && !empty($request->core_keys_id))
                $paymentAttribute->core_keys_id = $request->core_keys_id;

            if (isset($request->attribute_key) && !empty($request->attribute_key))
                $paymentAttribute->attribute_key = $request->attribute_key;

            if (isset($request->attribute_value))
                $paymentAttribute->attribute_value = $request->attribute_value;

            if (isset($request->added_user_id) && !empty($request->added_user_id))
                $paymentAttribute->added_user_id = $request->added_user_id;
            else
                $paymentAttribute->added_user_id = Auth::user()->id;

            $paymentAttribute->save();

            DB::commit();
            return $paymentAttribute;
        } catch (\Throwable $e) {
            DB::rollBack();
            return ['error' => $e->getMessage()];
        }
    }

    public function update($request)
    {
        DB::beginTransaction();

        try {
            $paymentAttribute = $this->getPaymentAttribute($request->id);

            if (isset($request->payment_id) && !empty($request->payment_id))
                $paymentAttribute->payment_id = $request->payment_id;

            if (isset($request->core_keys_id) && !empty($request->core_keys_id))
                $paymentAttribute->core_keys_id = $request->core_keys_id;

            if (isset($request->attribute_key) && !empty($request->attribute_key))
                $paymentAttribute->attribute_key = $request->attribute_key;

            if (isset($request->attribute_value))
                $paymentAttribute->attribute_value = $request->attribute_value;

            if (isset($request->updated_user_id) && !empty($request->updated_user_id))
                $paymentAttribute->updated_user_id = $request->updated_user_id;
            else
                $paymentAttribute->updated_user_id = Auth::user()->id;

            $paymentAttribute->update();

            DB::commit();
            return $paymentAttribute;
        } catch (\Throwable $e) {
            DB::rollBack();
            return ['error' => $e->getMessage()];
        }
    }

    public function getPaymentAttributes($relations = null, $limit = null, $offset = null, $conds = null)
    {
        $payments = PaymentAttribute::when($relations, function ($query, $relations) {
                $query->with($relations);
            })
            ->when($conds, function ($query, $conds) {
                $query->where($conds);
            })
            ->when($limit, function ($query, $limit) {
                $query->limit($limit);
            })
            ->when($offset, function ($query, $offset) {
                $query->offset($offset);
            })
            ->latest()->get();
        return $payments;
    }

    public function getPaymentAttribute($id =null , $conds = null)
    {
        $payment = PaymentAttribute::when($id, function ($query, $id) {
                $query->where($this->paymentAttrIdCol, $id);
            })
            ->when($conds, function ($query, $conds) {
                $query->where($conds);
            })
            ->first();
        return $payment;
    }
}
