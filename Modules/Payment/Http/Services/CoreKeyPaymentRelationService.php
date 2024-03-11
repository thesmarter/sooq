<?php

namespace Modules\Payment\Http\Services;

use App\Http\Services\PsService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Modules\Core\Entities\CoreKey;
use Modules\Payment\Entities\CoreKeyPaymentRelation;

class CoreKeyPaymentRelationService extends PsService
{
    protected $coreKeyPaymentRelationIdCol, $coreKeyPaymentRelationCoreKeysIdCol, $coreKeyPaymentRelationPaymentIdCol;

    public function __construct()
    {
        $this->coreKeyPaymentRelationIdCol = CoreKeyPaymentRelation::id;
        $this->coreKeyPaymentRelationCoreKeysIdCol = CoreKeyPaymentRelation::coreKeysId;
        $this->coreKeyPaymentRelationPaymentIdCol = CoreKeyPaymentRelation::paymentId;
        $this->coreKeyPaymentRelationTableName = CoreKeyPaymentRelation::tableName;

        $this->coreKeyTableName = CoreKey::tableName;
        $this->coreKeyCoreKeysIdCol = CoreKey::coreKeysId;
    }

    public function store($request)
    {
        DB::beginTransaction();

        try {
            $coreKeyPaymentRelation = new CoreKeyPaymentRelation();
            // dd($coreKeyPaymentRelation->get());

            if (isset($request->core_keys_id) && !empty($request->core_keys_id))
                $coreKeyPaymentRelation->core_keys_id = $request->core_keys_id;

            if (isset($request->payment_id) && !empty($request->payment_id))
                $coreKeyPaymentRelation->payment_id = $request->payment_id;

            if (isset($request->added_user_id) && !empty($request->added_user_id))
                $coreKeyPaymentRelation->added_user_id = $request->added_user_id;
            else
                $coreKeyPaymentRelation->added_user_id = Auth::user()->id;

            $coreKeyPaymentRelation->save();

            DB::commit();
            return $coreKeyPaymentRelation;
        } catch (\Throwable $e) {
            DB::rollBack();
            return ['error' => $e->getMessage()];
        }
    }

    public function update($id, $request)
    {
        DB::beginTransaction();

        try {
            $coreKeyPaymentRelation = $this->getCoreKeyPaymentRelation($id);

            if (isset($request->core_keys_id) && !empty($request->core_keys_id))
                $coreKeyPaymentRelation->core_keys_id = $request->core_keys_id;

            if (isset($request->payment_id) && !empty($request->payment_id))
                $coreKeyPaymentRelation->payment_id = $request->payment_id;

            if (isset($request->updated_date_id) && !empty($request->updated_date_id))
                $coreKeyPaymentRelation->updated_date_id = $request->updated_date_id;
            else
                $coreKeyPaymentRelation->updated_date_id = Auth::user()->id;

            $coreKeyPaymentRelation->update();

            DB::commit();
            return $coreKeyPaymentRelation;
        } catch (\Throwable $e) {
            DB::rollBack();
            return ['error' => $e->getMessage()];
        }
    }

    public function getCoreKeyPaymentRelations($relations = null, $limit = null, $offset = null, $conds = null)
    {
        $coreKeyPaymentRelations = CoreKeyPaymentRelation::when($relations, function ($query, $relations) {
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
        return $coreKeyPaymentRelations;
    }

    public function getCoreKeyPaymentRelation($id, $relations = null, $conds = null)
    {
        $coreKeyPaymentRelation = CoreKeyPaymentRelation::where($this->coreKeyPaymentRelationIdCol, $id)
            ->when($relations, function ($query, $relations) {
                $query->with($relations);
            })
            ->when($conds, function ($query, $conds) {
                $query->where($conds);
            })
            ->first();
        return $coreKeyPaymentRelation;
    }

    public function getPaymentCoreKeys($payment_id){
        $coreKeys = CoreKey::join($this->coreKeyPaymentRelationTableName, $this->coreKeyTableName.'.'.$this->coreKeyCoreKeysIdCol, '=', $this->coreKeyPaymentRelationTableName.'.'.$this->coreKeyPaymentRelationCoreKeysIdCol)
            ->where($this->coreKeyPaymentRelationTableName.'.'.$this->coreKeyPaymentRelationPaymentIdCol, $payment_id)
            ->get([$this->coreKeyTableName.'.*']);
        return $coreKeys;
    }
}
