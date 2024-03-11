<?php

namespace Modules\Core\Http\Services;

use App\Http\Services\PsService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Modules\Core\Entities\CoreKey;
use Modules\Core\Entities\CoreKeyCounter;

class CoreKeyCounterService extends PsService
{
    protected $coreKeyCounterIdCol, $coreKeyCounterCodeCol, $coreKeyCounterCountCol;

    public function __construct()
    {
        $this->coreKeyCounterIdCol = CoreKeyCounter::id;
        $this->coreKeyCounterCodeCol = CoreKeyCounter::code;
        $this->coreKeyCounterCountCol = CoreKeyCounter::counter;
    }

    public function getCoreKeyCounter($id = null, $conds = null){
        $coreKeyCounter = CoreKeyCounter::when($id, function ($query, $id) {
                $query->where($this->coreKeyCounterIdCol, $id);
            })
            ->when($conds, function ($query, $conds) {
                $query->where($conds);
            })
            ->first();
        return $coreKeyCounter;
    }

    public function getCoreKeyCounters($conds = null, $limit = null, $offset = null){
        $coreKeyCounters = CoreKey::when($conds, function ($query, $conds) {
                $query->where($conds);
            })
            ->when($limit, function ($query, $limit) {
                $query->limit($limit);
            })
            ->when($offset, function ($query, $offset) {
                $query->offset($offset);
            })
            ->latest()->get();
        return $coreKeyCounters;
    }

    public function store($request)
    {
        DB::beginTransaction();

        try {
            $coreKeyCounter = new CoreKeyCounter();

            if (isset($request->code) && !empty($request->code))
                $coreKeyCounter->code = $request->code;

            if (isset($request->counter))
                $coreKeyCounter->counter = $request->counter;

            if (isset($request->added_user_id) && !empty($request->added_user_id))
                $coreKeyCounter->added_user_id = $request->added_user_id;
            else
                $coreKeyCounter->added_user_id = Auth::user()->id;

            $coreKeyCounter->save();

            DB::commit();
            return $coreKeyCounter;
        } catch (\Throwable $e) {
            DB::rollBack();
            return ['error' => $e->getMessage()];
        }
    }

    public function update($id, $request)
    {
        DB::beginTransaction();

        try {
            $coreKeyCounter = $this->getCoreKeyCounter($id);

            if (isset($request->code) && !empty($request->code))
                $coreKeyCounter->code = $request->code;

            if (isset($request->counter) && !empty($request->counter))
                $coreKeyCounter->counter = $request->counter;

            if (isset($request->updated_user_id) && !empty($request->updated_user_id))
                $coreKeyCounter->updated_user_id = $request->updated_user_id;
            else
                $coreKeyCounter->updated_user_id = Auth::user()->id;

            $coreKeyCounter->update();

            DB::commit();
            return $coreKeyCounter;
        } catch (\Throwable $e) {
            DB::rollBack();
            return ['error' => $e->getMessage()];
        }
    }


}
