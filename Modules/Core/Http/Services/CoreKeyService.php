<?php

namespace Modules\Core\Http\Services;

use App\Http\Services\PsService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Modules\Core\Constants\Constants;
use Modules\Core\Entities\CoreKey;
use Modules\Core\Http\Services\CoreKeyCounterService;

class CoreKeyService extends PsService
{
    protected $coreKeyIdCol, $coreKeysIdCol,$coreKeyNameCol, $coreKeyDescriptionCol, $publish, $unPublish, $middleCoreKeyCode, $coreKeyCounterService;

    public function __construct(CoreKeyCounterService $coreKeyCounterService)
    {
        $this->coreKeyIdCol = CoreKey::id;
        $this->coreKeysIdCol = CoreKey::coreKeysId;
        $this->coreKeyNameCol = CoreKey::name;
        $this->coreKeyDescriptionCol = CoreKey::description;

        $this->publish = Constants::publish;
        $this->unPublish = Constants::unPublish;

        $this->middleCoreKeyCode = Constants::middleCoreKeyCode;

        $this->coreKeyCounterService = $coreKeyCounterService;
    }

    public function store($request, $code)
    {
        DB::beginTransaction();

        try {
            $coreKey = new CoreKey();

            if (isset($request->name) && !empty($request->name))
                $coreKey->name = $request->name;

            if (isset($request->description) && !empty($request->description))
                $coreKey->description = $request->description;

            $coreKey->core_keys_id = generateCoreKey($code);

            if (isset($request->added_user_id) && !empty($request->added_user_id))
                $coreKey->added_user_id = $request->added_user_id;
            else
                $coreKey->added_user_id = Auth::user()->id;

            $coreKey->save();

            DB::commit();
            return $coreKey;
        } catch (\Throwable $e) {
            DB::rollBack();
            return ['error' => $e->getMessage()];
        }
    }

    public function update($id, $request)
    {
        // DB::beginTransaction();

        // try {
            $coreKey = $this->getCoreKey($id);
            // dd($coreKey);
            if (isset($request->name) && !empty($request->name))

                $coreKey->name = $request->name;

            if (isset($request->description) && !empty($request->description))
                $coreKey->description = $request->description;

            if (isset($request->updated_user_id) && !empty($request->updated_user_id))
                $coreKey->updated_user_id = $request->updated_user_id;
            else
                $coreKey->updated_user_id = Auth::user()->id;

            $coreKey->update();

            // DB::commit();
            return $coreKey;
        // } catch (\Throwable $e) {
        //     DB::rollBack();
        //     return ['error' => $e->getMessage()];
        // }
    }

    public function getCoreKeys($relations = null, $limit = null, $offset = null, $conds = null)
    {
        $coreKeys = CoreKey::when($relations, function ($query, $relations) {
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
        return $coreKeys;
    }

    public function getCoreKey($id,$conds = null, $core_key =null)
    {
        $coreKey = CoreKey::when($id, function ($q, $id) {
                $q->where($this->coreKeysIdCol, $id);
            })
            ->when($conds, function ($query, $conds) {
                $query->where($conds);
            })
            ->first();
        return $coreKey;
    }

    public function edit($id)
    {
        $paymentCoreKey = $this->getCoreKey($id);
        $dataArr = [
            "paymentCoreKey" => $paymentCoreKey,
        ];
        return $dataArr;
    }
}
