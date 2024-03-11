<?php
namespace Modules\Core\Http\Services;

use App\Http\Services\PsService;
use Illuminate\Support\Facades\DB;
use Modules\Core\Constants\Constants;
use Modules\Core\Entities\TransactionHeader;
use Modules\Core\Entities\TransactionDetail;
use Modules\Core\Entities\TransactionCount;
use Illuminate\Support\Facades\Auth;
use Modules\Core\Exports\TransactionsExport;

class TransactionService extends PsService
{
    protected $dangerFlag, $transactionCsvFileName, $paymentStatusService, $transactionStatusService;

    public function __construct(PaymentStatusService $paymentStatusService, TransactionStatusService $transactionStatusService)
    {
        $this->paymentStatusService = $paymentStatusService;
        $this->transactionStatusService = $transactionStatusService;
        $this->transactionCsvFileName = 'transactions';
        $this->dangerFlag = Constants::danger;
    }

    public function update($transaction,$request)
    {
        DB::beginTransaction();

        try {
            $transaction->transaction_status_id = $request->transaction_status_id;
            $transaction->payment_status_id = $request->payment_status_id;
            $transaction->updated_user_id = Auth::user()->id;
            $transaction->update();

            DB::commit();
        } catch (\Throwable $e){
            DB::rollBack();
            return ['error' => $e->getMessage()];
        }

        return $transaction;
    }

    public function getTransactionHeaders($relation = null){
        $transactionHeaders = TransactionHeader::when($relation, function ($q, $relation){
                                                    $q->with($relation);
                                                })
                                                ->latest()->get();
        return $transactionHeaders;
    }

    public function getTransactionHeader($id = null, $relation = null){
        $transaction = TransactionHeader::when($id, function ($q, $id){
                                            $q->where('id', $id);
                                        })
                                        ->when($relation, function ($q, $relation){
                                            $q->with($relation);
                                        })
                                        ->first();
        return $transaction;

    }

    // -----------------

    public function index(){
        $transactionRelation = ['transaction_status'];
        $transactions = $this->getTransactionHeaders($transactionRelation);
        $dataArr = [
            'transactions' => $transactions
        ];
        return $dataArr;
    }

    public function edit($id){
        $transactionRelation = ['transaction_detail', 'shop', 'transaction_count'];

        $transaction = $this->getTransactionHeader($id, $transactionRelation);
        $transaction_statuses = $this->transactionStatusService->getTransactionStatuses();
        $payment_statuses = $this->paymentStatusService->getPaymentStatuses();

        $dataArr = [
            'transaction' => $transaction,
            'transaction_statuses' => $transaction_statuses,
            'payment_statuses' => $payment_statuses,
        ];
        return $dataArr;
    }

    public function destroy($id){

        DB::beginTransaction();
        try {
            // for soft delete
            $transaction = $this->getTransactionHeader($id);
            $transaction_counts = TransactionCount::where('transaction_header_id', $transaction->id)->get();
//            foreach($transaction_counts as $transaction_count){
//                $transaction_count->delete();
//            }
            TransactionCount::destroy($transaction_counts->pluck('id'));

            $transaction_details = TransactionDetail::where('transaction_header_id', $transaction->id)->get();
            foreach($transaction_details as $transaction_detail){
                $transaction_detail->delete();
            }

            $name = $transaction->trans_code;
            $transaction->update();
            $transaction->delete();

            // for hard delete
            // $transaction->forceDelete();

            DB::commit();
        } catch (\Throwable $e){
            DB::rollBack();
            return ['error' => $e->getMessage()];
        }
        // $msg = 'The '.$name.' transaction row has been deleted successfully.';
        $msg =  __('core__be_delete_success',['attribute'=>$name]);

        return $msg;
    }

    public function csvExport(){

        $filename = newFileNameForExport($this->transactionCsvFileName);
        return (new TransactionsExport)->download($filename, \Maatwebsite\Excel\Excel::CSV, [
            'Content-Type' => 'text/csv',
        ]);
    }


}
