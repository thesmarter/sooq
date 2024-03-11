<?php

namespace Modules\Core\Http\Controllers\Backend\Controllers\TransactionStatus;

use Inertia\Inertia;
use Illuminate\Routing\Controller;
use Modules\Core\Constants\Constants;
use Modules\Core\Entities\TransactionStatus;
use Modules\Core\Http\Services\TransactionStatusService;
use Modules\Core\Http\Requests\StoreTransactionStatusRequest;
use Modules\Core\Http\Requests\UpdateTransactionStatusRequest;

class TransactionStatusController extends Controller
{
    const parentPath = "transaction_status/";
    const indexPath = self::parentPath."Index";
    const createPath = self::parentPath."Create";
    const editPath = self::parentPath."Edit";
    const indexRoute = "transaction_status.index";
    const createRoute = "transaction_status.create";
    const editRoute = "transaction_status.edit";

    protected $transactionStatusService, $successFlag, $dangerFlag, $csvFile, $warningFlag;

    public function __construct(TransactionStatusService $transactionStatusService)
    {
        $this->transactionStatusService = $transactionStatusService;
        $this->successFlag = Constants::success;
        $this->dangerFlag = Constants::danger;
        $this->warningFlag = Constants::warning;
        $this->csvFile = Constants::csvFile;
    }

    public function index()
    {
        $dataArr = $this->transactionStatusService->index();
        return renderView(self::indexPath, $dataArr);
    }

    public function create()
    {
        return renderView(self::createPath);
    }

    public function store(StoreTransactionStatusRequest $request)
    {

        $transactionStatus = $this->transactionStatusService->store($request);

        // if have error
        if (isset($transactionStatus['error'])){
            $msg = $transactionStatus['error'];
            return redirectView(self::indexRoute, $msg, $this->dangerFlag);
        }

        return redirect()->back();

    }

//    public function show(TransactionStatus $transaction_status)
//    {
//        return redirect()->route('transaction_status.edit', $transaction_status);
//    }

    public function edit($id)
    {
        $dataArr = $this->transactionStatusService->edit($id);
        return renderView(self::editPath, $dataArr);    }

    public function update(UpdateTransactionStatusRequest $request, $id)
    {
        $transactionStatus = $this->transactionStatusService->update($id, $request);

        // if have error
        if (isset($transactionStatus['error'])){
            $msg = $transactionStatus['error'];
            return redirectView(self::indexRoute, $msg, $this->dangerFlag);
        }

        return redirect()->back();
    }

    public function destroy($id)
    {
        $dataArr = $this->transactionStatusService->destroy($id);
        return redirectView(self::indexRoute, $dataArr['msg'], $dataArr['flag']);
    }
}
