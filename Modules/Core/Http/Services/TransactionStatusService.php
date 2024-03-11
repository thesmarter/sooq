<?php
namespace Modules\Core\Http\Services;

use App\Http\Services\PsService;
use Illuminate\Support\Facades\DB;
use Modules\Core\Constants\Constants;
use Modules\Core\Entities\TransactionStatus;
use Illuminate\Support\Facades\Auth;

class TransactionStatusService extends PsService
{
    protected $transactionStatusIdCol, $dangerFlag;

    public function __construct()
    {
        $this->transactionStatusIdCol = TransactionStatus::id;
        $this->dangerFlag = Constants::danger;
    }

    public function store($request)
    {
        DB::beginTransaction();

        try {
            $transaction_status = new TransactionStatus();
            $transaction_status->title = $request->title;
            $transaction_status->ordering = $request->ordering;
            $transaction_status->color_value = $request->color_value;
            $transaction_status->start_stage = $request->stage == 'Start Stage'? 1: 0;
            $transaction_status->final_stage = $request->stage == 'Final Stage'? 1: 0;
            $transaction_status->is_optional = $request->stage == 'Optional'? 1: 0;
            $transaction_status->is_refundable = $request->is_refundable;
            $transaction_status->added_user_id = Auth::user()->id;
            $transaction_status->save();

            DB::commit();
        } catch (\Throwable $e){
            DB::rollBack();
            return ['error' => $e->getMessage()];
        }

        return $transaction_status;
    }

    public function update($id, $request)
    {
        DB::beginTransaction();

        try {
            $transaction_status = $this->getTransactionStatus($id);
            $transaction_status->title = $request->title;
            $transaction_status->ordering = $request->ordering;
            $transaction_status->color_value = $request->color_value;
            $transaction_status->start_stage = $request->stage == 'Start Stage'? 1: 0;
            $transaction_status->final_stage = $request->stage == 'Final Stage'? 1: 0;
            $transaction_status->is_optional = $request->stage == 'Optional'? 1: 0;
            $transaction_status->is_refundable = $request->is_refundable;
            $transaction_status->updated_user_id = Auth::user()->id;
            $transaction_status->update();

            DB::commit();
        } catch (\Throwable $e){
            DB::rollBack();
            return ['error' => $e->getMessage()];
        }

        return $transaction_status;
    }

    public function getTransactionStatuses(){
        $transactionStatuses = TransactionStatus::latest()->get();
        return $transactionStatuses;
    }

    public function getTransactionStatus($id = null){
        $transactionStatuses = TransactionStatus::when($id, function ($q, $id){
                                                    $q->where($this->transactionStatusIdCol, $id);
                                                })->first();
        return $transactionStatuses;
    }

    // ------------------------

    public function index(){
        $transactionStatuses = $this->getTransactionStatuses();
        $dataArr = [
            'transaction_statuses' => $transactionStatuses
        ];
        return $dataArr;
    }

    public function edit($id){
        $transactionStatus = $this->getTransactionStatus($id);
        $dataArr = [
            'transaction_status' => $transactionStatus
        ];
        return $dataArr;
    }

    public function destroy($id){
        $transaction_status = $this->getTransactionStatus($id);
        $name = $transaction_status->title;
        $transaction_status->delete();

        $status = [
            // 'msg' => 'The '.$name.' row has been deleted successfully.',
            'msg'=>__('core__be_delete_success',['attribute'=>$name]),
            'flag' => $this->dangerFlag,
        ];

        return $status;
    }

}
