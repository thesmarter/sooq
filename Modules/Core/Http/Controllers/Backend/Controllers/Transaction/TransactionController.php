<?php

namespace Modules\Core\Http\Controllers\Backend\Controllers\Transaction;

use Inertia\Inertia;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\Core\Constants\Constants;
use Modules\Core\Entities\PaymentStatus;
use Modules\Core\Entities\TransactionCount;
use Modules\Core\Entities\TransactionDetail;
use Modules\Core\Entities\TransactionHeader;
use Modules\Core\Entities\TransactionStatus;
use Modules\Core\Exports\TransactionsExport;
use Modules\Core\Http\Requests\TransactionRequest;
use Modules\Core\Http\Services\TransactionService;

class TransactionController extends Controller
{
    const parentPath = "transaction/";
    const indexPath = self::parentPath."Index";
    const createPath = self::parentPath."Create";
    const editPath = self::parentPath."Edit";
    const indexRoute = "transaction.index";
    const createRoute = "transaction.create";
    const editRoute = "transaction.edit";

    protected $transactionService, $successFlag, $dangerFlag, $csvFile, $warningFlag;

    public function __construct(TransactionService $transactionService)
    {
        $this->transactionService = $transactionService;
        $this->successFlag = Constants::success;
        $this->dangerFlag = Constants::danger;
        $this->warningFlag = Constants::warning;
        $this->csvFile = Constants::csvFile;
    }

    public function index()
    {
        $dataArr = $this->transactionService->index();
        return renderView(self::indexPath, $dataArr);
    }

    public function edit($id)
    {
        $dataArr = $this->transactionService->edit($id);
        return renderView(self::editPath, $dataArr);
    }

    public function update(TransactionRequest $request, $id)
    {

        $transaction = $this->transactionService->update($id, $request);

        // if have error
        if (isset($transaction['error'])){
            $msg = $transaction['error'];
            return redirectView(self::indexRoute, $msg, $this->dangerFlag);
        }

        return redirect()->back();

    }

    public function destroy($id)
    {
        $msg = $this->transactionService->destroy($id);
        return redirectView(self::indexRoute, $msg, $this->dangerFlag);

    }

    public function csvExport(){
        return $this->transactionService->csvExport();
    }
}
