<?php
namespace Modules\Core\Http\Services;

use App\Http\Services\PsService;
use Illuminate\Support\Facades\DB;
use Modules\Core\Constants\Constants;
use Modules\Core\Entities\Shipping;
use Illuminate\Support\Facades\Auth;

class ShippingService extends PsService
{

    protected $shopService, $successFlag, $publish, $unPublish, $dangerFlag, $shippingIdCol;

    public function __construct(ShopService $shopService)
    {
        $this->shopService = $shopService;
        $this->shippingIdCol = Shipping::id;
        $this->dangerFlag = Constants::danger;
        $this->successFlag = Constants::success;
        $this->publish = Constants::publish;
        $this->unPublish = Constants::unPublish;
    }

    public function store($request)
    {
       DB::beginTransaction();

        try {
            $shipping = new Shipping();
            $shipping->shop_id = $request->shop_id;
            $shipping->name = $request->name;
            $shipping->price = $request->price;
            $shipping->days = $request->days;
            $shipping->status = $request->status;
            $shipping->added_user_id = Auth::user()->id;
            $shipping->save();

            DB::commit();
        } catch (\Throwable $e){
            DB::rollBack();
            return ['error' => $e->getMessage()];
        }

        return $shipping;
    }

    public function update($id,$request)
    {
        DB::beginTransaction();

        try {
            $shipping = $this->getShipping($id);
            $shipping->shop_id = $request->shop_id;
            $shipping->name = $request->name;
            $shipping->price = $request->price;
            $shipping->days = $request->days;
            $shipping->status = $request->status;
            $shipping->updated_user_id = Auth::user()->id;
            $shipping->update();


            DB::commit();
        } catch (\Throwable $e){
            DB::rollBack();
            return ['error' => $e->getMessage()];
        }

        return $shipping;
    }

    public function getShippings($relation = null){
        $subMenuGroups = Shipping::when($relation, function ($q, $relation){
                                $q->with($relation);
                            })->latest()->get();
        return $subMenuGroups;
    }

    public function getShipping($id = null){
        $subMenuGroup = Shipping::when($id, function ($q, $id){
                            $q->where($this->shippingIdCol, $id);
                        })->first();
        return $subMenuGroup;
    }

    public function index(){
        $shippings = $this->getShippings();
        $dataArr = [
            'shippings' => $shippings,
        ];
        return $dataArr;
    }

    public function create(){
        $shops = $this->shopService->getShops(null, $this->publish);
        $dataArr =  [
            'shops' => $shops
        ];
        return $dataArr;
    }

    public function edit($id){
        $shops = $this->shopService->getShops(null, $this->publish);
        $shipping = $this->getShipping($id);
        $dataArr = [
            'shops' => $shops,
            'shipping' => $shipping
        ];
        return $dataArr;
    }

    public function destroy($id){
        $shipping = $this->getShipping($id);
        $name = $shipping->name;
        $shipping->delete();

        $status = [
            // 'msg' => 'The '.$name.' row has been deleted successfully.',
            'msg'=>__('core__be_delete_success',['attribute'=>$name]),
            'flag' => $this->dangerFlag,
        ];

        return $status;
    }

    public function statusChange($id){
        $shipping = $this->getShipping($id);
        if($shipping->status == $this->publish){
            $shipping->status = $this->unPublish;
        }else{
            $shipping->status = $this->publish;
        }
        $shipping->updated_user_id = Auth::user()->id;
        $shipping->update();

        $status = [
            // 'msg' => 'The status of '.$shipping->name.' row has been updated successfully.',
            'msg' =>  __('core__be_status_attribute_updated',['attribute'=>$shipping->name]),
            'flag' => $this->successFlag,
        ];

        return $status;
    }

}
