<?php

namespace Modules\Core\Http\Controllers\Backend\Controllers\Shop;

use Inertia\Inertia;
use Illuminate\Http\Request;
use Modules\Core\Entities\Shop;
use Modules\Core\Entities\UiType;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Modules\Core\Entities\ShopCustomField;
use Modules\Core\Http\Services\ShopService;
use Modules\Core\Entities\CustomizeUiDetail;
use Modules\Core\Entities\CustomizeUi;
use Modules\Core\Http\Requests\StoreShopRequest;
use Modules\Core\Http\Requests\UpdateShopRequest;
use Modules\Core\Http\Services\CustomFieldService;

class ShopController extends Controller
{
    protected $shopService;
    protected $customFieldService;

    public function __construct(ShopService $shopService, CustomFieldService $customFieldService)
    {
        $this->shopService = $shopService;
        $this->customFieldService = $customFieldService;
    }

    public function index()
    {
        $shops = Shop::latest()->get();

        // For Custom Fields
        $conds = [
            'module_name' => 'Shop',
            'enable' => 1,
        ];
        $custom_field_headers = CustomizeUi::where($conds)->with('custom_field_detail')->get();
        $custom_field_details = CustomizeUiDetail::all();
        $custom_field_header_ids = $custom_field_headers->pluck('id');
        $shop_custom_fields = ShopCustomField::whereIn('custom_field_header_id',$custom_field_header_ids)->get();

        return Inertia::render('shop/Index',[
            'shops' => $shops,
            "custom_field_headers" => $custom_field_headers,
            'custom_field_details' => $custom_field_details,
            'shop_custom_fields' => $shop_custom_fields,
        ]);
    }

    public function create()
    {
        // For Custom Fields
        $conds = [
            'module_name' => 'Shop',
            'enable' => 1,
        ];
        $custom_field_headers = CustomizeUi::where("module_name","Shop")->with('custom_field_detail')->get();
        $custom_field_details = CustomizeUiDetail::latest("id")->get();

        return Inertia::render('shop/Create', [
            "custom_field_headers" => $custom_field_headers,
            "custom_field_details" => $custom_field_details,
        ]);
    }

    public function store(StoreShopRequest $request)
    {
        try {
            $shop = $this->shopService->create($request);
        }catch (Throwable $e){
            return redirect()->route('shop.index')->with('status',[ 'flag' => 'danger', 'msg' => $e ]);
        }
        return redirect()->back();
    }

    public function show(Shop $shop)
    {
        return redirect()->route('shop.edit', $shop);
    }

    public function edit(Shop $shop)
    {
        // For Custom Fields
        $conds = [
            'module_name' => 'Shop',
            'enable' => 1,
        ];
        $custom_field_headers = CustomizeUi::where($conds)->with('custom_field_detail')->get();
        $custom_field_details = CustomizeUiDetail::latest("id")->get();
        $custom_field_header_ids = $custom_field_headers->pluck('id');
        $shop_custom_fields = ShopCustomField::whereIn('custom_field_header_id',$custom_field_header_ids)->where('shop_id', $shop->id)->get();

        return Inertia::render('shop/Edit',[
            "custom_field_headers" => $custom_field_headers,
            "custom_field_details" => $custom_field_details,
            "shop_custom_fields" => $shop_custom_fields,
            'shop' => $shop,
        ]);
    }

    public function update(UpdateShopRequest $request, Shop $shop)
    {
        try {
            $shop = $this->shopService->update($shop,$request);
        }catch (Throwable $e){
            return redirect()->route('shop.index')->with('status',[ 'flag' => 'danger', 'msg' => $e ]);
        }
        return redirect()->back();
    }

    public function destroy(Shop $shop)
    {
        $name = $shop->name;
        $shop->delete();

        $status = [
            'flag' => 'danger',
            'msg' => 'The '.$name.' row has been deleted successfully.'
        ];

        return redirect()->route('shop.index')->with('status',$status);

    }

    public function statusChange(Shop $shop){

        if($shop->status == 1){
            $shop->status = 0;
        }else{
            $shop->status = 1;
        }
        $shop->updated_user_id = Auth::user()->id;
        $shop->update();

        $status = [
            'flag' => 'success',
            'msg' => 'The status of '.$shop->name.' row has been updated successfully.'
        ];

        return redirect()->route('shop.index')->with('status',$status);
    }

    // Custom Field Header
    public function customization()
    {
        $custom_field_headers = CustomizeUi::with('custom_field_detail')->where("module_name","Shop")->get();
        return Inertia::render('shop/Customization',[
            "custom_field_headers" => $custom_field_headers,
        ]);
    }

    public function customizationDestroy(CustomizeUi $custom_field_header)
    {
        $custom_field_header->delete();
        return redirect()->route('shop.customization')->with("status","Custom field has been deleted successfully.");
    }

    public function addNewField()
    {
        $uiTypes = UiType::all();

        return Inertia::render('shop/AddNewField',[
            'uiTypes' => $uiTypes,
        ]);
    }

    public function addNewFieldStore(Request $request)
    {
        try {
            $custom_field_detail = $this->customFieldService->custom_field_header_create($request, 'Shop');
        }catch (\Throwable $e){
            return redirect()->route('shop.customization')->with('status',[ 'flag' => 'danger', 'msg' => $e ]);
        }

        return redirect()->route('shop.customization')->with('status',"Custom New Field has been added successfully.");

    }

    /**
     * Switch optional/mandatory
     */
    public function optionalOrMandatoryChange(CustomizeUi $custom_field_header) {

        $status = $this->customFieldService->optionalOrMandatory($custom_field_header);
        return redirect()->back()->with('status', $status);
    }

    /**
     * Switch enable or disable
     */
    public function enableOrDisableChange(CustomizeUi $custom_field_header){

        $status = $this->customFieldService->enableOrDisable($custom_field_header);
        return redirect()->back()->with('status', $status);
    }

    // Custom Field Detail
    public function customizationDetailIndex(CustomizeUi $custom_field_header)
    {
        $custom_field_details = CustomizeUiDetail::where('custom_field_header_id',$custom_field_header->id)->latest()->get();
        return Inertia::render('shop/CustomizationDetailIndex',[
            "custom_field_header" => $custom_field_header,
            "custom_field_details" => $custom_field_details,
        ]);
    }

    public function customizationDetailCreate(CustomizeUi $custom_field_header)
    {
        return Inertia::render('shop/CustomizationDetailCreate',[
            "custom_field_header" => $custom_field_header,
        ]);
    }

    public function customizationDetailStore(CustomizeUi $custom_field_header, Request $request)
    {
        try {
            $custom_field_detail = $this->customFieldService->custom_field_detail_create($request);
        }catch (Throwable $e){
            return redirect()->route('shop.customizationDetail.index',$custom_field_header)->with('status',[ 'flag' => 'danger', 'msg' => $e ]);
        }

        return redirect()->route('shop.customizationDetail.index',$custom_field_header)->with('success',"Custom field attribute has been created successfully.");
    }

    public function customizationDetailDestroy(CustomizeUi $custom_field_header, CustomizeUiDetail $custom_field_detail)
    {
        $custom_field_detail->delete();
        return redirect()->route('shop.customizationDetail.index',$custom_field_header)->with('status',"Custom field attribute has been deleted successfully.");
    }

    public function customizationDetailEdit(CustomizeUi $custom_field_header, CustomizeUiDetail $custom_field_detail)
    {
        return Inertia::render('shop/CustomizationDetailEdit',[
            'custom_field_detail' => $custom_field_detail,
            "custom_field_header" => $custom_field_header,
        ]);
    }

    public function customizationDetailUpdate(CustomizeUi $custom_field_header, CustomizeUiDetail $custom_field_detail, Request $request)
    {
        try {
            $custom_field_detail = $this->customFieldService->custom_field_detail_update($custom_field_detail,$request);
        }catch (Throwable $e){
            return redirect()->route('shop.customizationDetail.index',$custom_field_header)->with('status',[ 'flag' => 'danger', 'msg' => $e ]);
        }

        return redirect()->route('shop.customizationDetail.index',$custom_field_header)->with('status', $request->attribute . " has been created successfully.");
    }

}
