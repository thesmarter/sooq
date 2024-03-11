<?php

namespace Modules\Core\Http\Controllers\Backend\Controllers\Shipping;

use Inertia\Inertia;
use Modules\Core\Constants\Constants;
use Modules\Core\Entities\Shop;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\Core\Http\Services\ShippingService;
use Modules\Core\Http\Requests\StoreShippingRequest;
use Modules\Core\Http\Requests\UpdateShippingRequest;

class ShippingController extends Controller
{
    const parentPath = "shipping/";
    const indexPath = self::parentPath."Index";
    const createPath = self::parentPath."Create";
    const editPath = self::parentPath."Edit";
    const indexRoute = "shipping.index";
    const createRoute = "shipping.create";
    const editRoute = "shipping.edit";

    protected $shippingService, $successFlag, $dangerFlag, $csvFile, $warningFlag;

    public function __construct(ShippingService $shippingService)
    {
        $this->shippingService = $shippingService;
        $this->successFlag = Constants::success;
        $this->dangerFlag = Constants::danger;
        $this->warningFlag = Constants::warning;
        $this->csvFile = Constants::csvFile;
    }

    public function index()
    {
        $dataArr = $this->shippingService->index();
        return renderView(self::indexPath, $dataArr);
    }

    public function create()
    {
        $dataArr = $this->shippingService->create();
        return renderView(self::createPath, $dataArr);
    }

    public function store(StoreShippingRequest $request)
    {
        $shipping = $this->shippingService->store($request);

        // if have error
        if (isset($shipping['error'])){
            $msg = $shipping['error'];
            return redirectView(self::indexRoute, $msg, $this->dangerFlag);
        }

        return redirect()->back();
    }

//    public function show(Shipping $shipping)
//    {
//        return redirect()->route('shipping.edit', $shipping);
//    }

    public function edit($id)
    {
        $dataArr = $this->shippingService->edit($id);
        return renderView(self::editPath, $dataArr);
    }

    public function update(UpdateShippingRequest $request, $id)
    {
        $shipping = $this->shippingService->update($id, $request);

        // if have error
        if (isset($shipping['error'])){
            $msg = $shipping['error'];
            return redirectView(self::indexRoute, $msg, $this->dangerFlag);
        }

        return redirect()->back();
    }

    public function destroy($id)
    {
        $dataArr = $this->shippingService->destroy($id);
        return redirectView(self::indexRoute, $dataArr['msg'], $dataArr['flag']);

    }

    public function statusChange($id){

        if($shipping->status == 1){
            $shipping->status = 0;
        }else{
            $shipping->status = 1;
        }
        $shipping->updated_user_id = Auth::user()->id;
        $shipping->update();

        $status = [
            'flag' => 'success',
            // 'msg' => 'The status of '.$shipping->name.' row has been updated successfully.',
            'msg' => __('core__be_status_attribute_updated',['attribute'=>$shipping->name]),
        ];

        return redirect()->route('shipping.index')->with('status',$status);
    }

}
