<?php

namespace Modules\Core\Http\Controllers\Backend\Controllers\Item;

use App\Config\ps_config;
use App\Config\ps_constant;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Modules\Core\Constants\Constants;
use Modules\Core\Entities\CoreImage;
use Modules\Core\Entities\Currency;
use Modules\Core\Entities\BackendSetting;
// use Modules\Core\Entities\Item;
use Modules\Core\Http\Services\ItemService;
use Modules\Core\Http\Services\ImageService;
use Modules\Core\Http\Services\SystemConfigService;
use Session;
use Modules\Core\Http\Services\CoreFieldFilterSettingService;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\File;
use Modules\Core\Http\Requests\StoreItemRequest;
use Modules\Core\Http\Requests\UpdateItemRequest;
use Modules\Core\Http\Services\VendorService;

class ItemController extends Controller
{
    const parentPath = "item/";
    const indexPath = self::parentPath . 'Index';
    const createPath = self::parentPath . 'Create';
    const editPath = self::parentPath . 'Edit';
    const indexRoute = "item.index";

    protected $vendorService, $imageService, $paginationPerPage, $coreFieldFilterSettingService, $systemConfigService, $dangerFlag, $successFlag, $itemService, $code, $parentPath, $getImgPath, $coreFieldFilterForRelation, $viewAnyAbility, $createAbility, $editAbility, $deleteAbility;

    public function __construct(VendorService $vendorService, CoreFieldFilterSettingService $coreFieldFilterSettingService, ImageService $imageService, ItemService $itemService, SystemConfigService $systemConfigService)
    {
        $this->itemService = $itemService;
        $this->vendorService = $vendorService;
        $this->systemConfigService = $systemConfigService;
        $this->code = Constants::item;
        $this->parentPath = Constants::parentPath;
        $this->getImgPath = Constants::imgPath;
        $this->viewAnyAbility = Constants::viewAnyAbility;
        $this->createAbility = Constants::createAbility;
        $this->editAbility = Constants::editAbility;
        $this->deleteAbility = Constants::deleteAbility;
        $this->coreFieldFilterForRelation = ps_config::coreFieldFilterForRelation;
        $this->paginationPerPage = ps_config::pagPerPage;
        $this->successFlag = Constants::success;
        $this->dangerFlag = Constants::danger;
        $this->imageService = $imageService;

        $this->coreFieldFilterSettingService = $coreFieldFilterSettingService;
    }

    public function index(Request $request)
    {

        $dataArr = $this->itemService->index($request);
        // check permission
        $checkPermission = $dataArr["checkPermission"];
        if (!empty($checkPermission)) {
            return $checkPermission;
        }
        // Session::put('items_url',request()->fullUrl());


        // return $dataArr;
        // dd($dataArr);
        return renderView(self::indexPath, $dataArr);
    }

    public function create(Request $request)
    {
        $dataArr = $this->itemService->create(self::indexRoute, $request);
        // check permission
        $checkPermission = $dataArr["checkPermission"];
        if (!empty($checkPermission)) {
            return $checkPermission;
        }
        $createAsVendorIds = haveVendorAndCreateAccess(Auth::user()->id);

        $dataArr['vendor_list'] = $this->vendorService->getVendorByIds(['logo'], $createAsVendorIds);

        return renderView(self::createPath, $dataArr);
    }

    public function store(Request $request)
    {
        // dd($request->all());
        //prepare for validation
        $errors = validateForCustomField($this->code, $request->product_relation);

        $selcted_array = $this->systemConfigService->getSystemSettingJson();

        if ($selcted_array['selected_price_type']['id'] == "PRICE_RANGE") {
            $default_currency = Currency::where([Currency::isDefault => 1])->first();
        }

        $coreFieldsIds = [];
        // $errors = [];

        $cond['module_name'] = $this->code;
        $cond['mandatory'] = 1;
        $cond['is_core_field'] = 1;

        $coreFields = $this->coreFieldFilterSettingService->getCoreFieldsWithConds($cond);

        // $coreFields = CoreFieldFilterSetting::where('module_name',)->where('mandatory',1)->where('is_core_field',1)->get();
        foreach ($coreFields as $key => $value) {
            if (str_contains($value->field_name, "@@")) {
                $originFieldName = strstr($value->field_name, "@@", true);
            } else {
                $originFieldName = $value->field_name;
            }
            array_push($coreFieldsIds, $originFieldName);
        }


        $validationArr = [];

        if (in_array('title', $coreFieldsIds)) {
            $validationArr['title'] = 'required|min:3|unique:psx_items,title,' . $request->id;
        }
        if (in_array('description', $coreFieldsIds)) {
            $validationArr['description'] = 'required|min:10';
        }
        if (in_array('category_id', $coreFieldsIds)) {
            $validationArr['category_id'] = 'required|exists:psx_categories,id';
        }
        if (in_array('subcategory_id', $coreFieldsIds)) {
            $validationArr['subcategory_id'] = 'required|exists:psx_subcategories,id';
        }
        if (in_array('location_city_id', $coreFieldsIds)) {
            $validationArr['location_city_id'] = 'required|exists:psx_location_cities,id';
        }
        if (in_array('location_township_id', $coreFieldsIds)) {
            $validationArr['location_township_id'] = 'required|exists:psx_location_townships,id';
        }
        if (in_array('currency_id', $coreFieldsIds)) {
            if ($selcted_array['selected_price_type']['id'] == "NORMAL_PRICE") {
                $validationArr['currency_id'] = 'required|exists:psx_currencies,id';
            }
        }
        // if(in_array('price',$coreFieldsIds)){
        //     $validationArr['price'] = 'required';
        // }
        $validationArr['original_price'] = 'required|max:11';
        if(in_array('percent',$coreFieldsIds)){
            $validationArr['percent'] = 'required';
        }
        if (in_array('lat', $coreFieldsIds)) {
            $validationArr['lat'] = 'required';
        }
        if (in_array('lng', $coreFieldsIds)) {
            $validationArr['lng'] = 'required';
        }
        if (in_array('shop_id', $coreFieldsIds)) {
            $validationArr['shop_id'] = 'required';
        }
        if (in_array('search_tag', $coreFieldsIds)) {
            $validationArr['search_tag'] = 'required';
        }
        if (in_array('ordering', $coreFieldsIds)) {
            $validationArr['ordering'] = 'required';
        }
        if (in_array('is_discount', $coreFieldsIds)) {
            $validationArr['is_discount'] = 'required';
        }
        if (in_array('phone', $coreFieldsIds)) {
            $validationArr['phone'] = 'required';
        }
        if (in_array('phone', $coreFieldsIds)) {
            $validationArr['phone'] = 'required';
        }
        if (in_array('item_image', $coreFieldsIds)) {
            // $validationArr['cover'] = 'required|image';
            $validationArr['images'] = 'required';
        } else {
            // $validationArr['cover'] = 'nullable|sometimes|image';
            // $validationArr['images'] = 'required';
        }
        if (in_array('Item Video Icon', $coreFieldsIds)) {
            $validationArr['video_icon'] = 'required|image';
        } else {
            $validationArr['video_icon'] = 'nullable|sometimes|image';
        }
        if (in_array('item_video', $coreFieldsIds)) {
            $validationArr['video'] = 'required|mimetypes:video/mp4';
        } else {
            $validationArr['video'] = 'nullable|sometimes|mimetypes:video/mp4';
        }
        $attributes['original_price.max'] = "The original price must not be greater than 11 digits.";
        //prepare validation for core filed
        $validator = Validator::make($request->all(),$validationArr, $attributes
        //  [
            // 'cover' => 'nullable|file|mimes:jpg,png,jpeg',
            // 'video_icon' => 'nullable|file|mimes:jpg,png,jpeg',
            // 'video' => 'nullable|file|mimes:mp4|max:1500'
            // ]
        );

        // remove validation for item uploaded by user
        if(empty($request->vendor_id) && isset($errors['ps-itm00010'])){
            unset($errors["ps-itm00010"]);
        }


        if ($validator->fails()) {
            return redirect()->route('item.create')->with('product_relation_errors', $errors)
                ->withErrors($validator)
                ->withInput();
        } else {

            if (collect($errors)->isNotEmpty()) {
                return redirect()->route('item.create')->with('product_relation_errors', $errors);
            }
        }

        /// validation end
        $product = $this->itemService->store($request);
        if (isset($product['error'])) {
            // go back to index
            $msg = $product['error'];
            return redirectView(self::indexRoute, $msg, $this->dangerFlag);
        }

        $msg = __('core__be_item_created');
        // dd($product);
        // return back()->with('data',[
        //     'product' => $product,
        // ]);
        return redirectView(self::indexRoute, $msg);
    }

    public function edit($id)
    {
        $dataArr = $this->itemService->edit($id, self::indexRoute);

        // check permission
        $checkPermission = $dataArr["checkPermission"];
        // if (!empty($checkPermission)){
        //     return $checkPermission;
        // }
        // return $dataArr['item'];
        $createAsVendorIds = haveVendorAndCreateAccess(Auth::user()->id);

        $dataArr['vendor_list'] = $this->vendorService->getVendorByIds(['logo'], $createAsVendorIds);

        return renderView(self::editPath, $dataArr);
    }

    public function update(Request $request)
    {
        // dd($request->all());
        //prepare for validation
        $default_images = CoreImage::where('img_type', 'item')->where('img_parent_id', $request->id)->count();
        // dd($default_images);


        $errors = validateForCustomField($this->code, $request->product_relation);


        // //validation
        // $validator = Validator::make($request->all(), [
        //     'title' => 'required|min:3|unique:psx_items,title,'.$request->id,
        //     'description' => 'required',
        //     'category_id' => 'required|exists:psx_categories,id',
        //     // 'subcategory_id' => 'required|exists:psx_subcategories,id',
        //     'location_city_id' => 'required|exists:psx_location_cities,id',
        //     'currency_id' => 'required|exists:psx_currencies,id',
        //     // 'location_township_id' => 'required|exists:psx_location_townships,id',
        //     'price' => 'required',
        //     'lat' => 'required',
        //     'lng' => 'required',
        //     'cover' => 'nullable|image',
        //     'video_icon' => 'nullable|image',
        //     // 'video_icon' => 'nullable|file|mimes:jpg,png,jpeg',
        //     // 'video' => 'nullable|file|mimes:mp4|max:1500'
        // ]);
        $coreFieldsIds = [];
        // $errors = [];

        $cond['module_name'] = $this->code;
        $cond['mandatory'] = 1;
        $cond['is_core_field'] = 1;

        $coreFields = $this->coreFieldFilterSettingService->getCoreFieldsWithConds($cond);

        // $coreFields = CoreFieldFilterSetting::where('module_name',)->where('mandatory',1)->where('is_core_field',1)->get();
        foreach ($coreFields as $key => $value) {
            if (str_contains($value->field_name, "@@")) {
                $originFieldName = strstr($value->field_name, "@@", true);
            } else {
                $originFieldName = $value->field_name;
            }
            array_push($coreFieldsIds, $originFieldName);
        }

        // dd($coreFieldsIds);
        $validationArr = [];

        if ($default_images == 0) {

            if (in_array('item_image', $coreFieldsIds)) {
                // $validationArr['cover'] = 'required|image';
                $validationArr['images'] = 'required';
            }
        }

        if (in_array('title', $coreFieldsIds)) {
            $validationArr['title'] = 'required|min:3|unique:psx_items,title,' . $request->id;
        }
        if (in_array('description', $coreFieldsIds)) {
            $validationArr['description'] = 'required|min:10';
        }
        if (in_array('category_id', $coreFieldsIds)) {
            $validationArr['category_id'] = 'required|exists:psx_categories,id';
        }
        if (in_array('subcategory_id', $coreFieldsIds)) {
            $validationArr['subcategory_id'] = 'required|exists:psx_subcategories,id';
        }
        if (in_array('location_city_id', $coreFieldsIds)) {
            $validationArr['location_city_id'] = 'required|exists:psx_location_cities,id';
        }
        if (in_array('location_township_id', $coreFieldsIds)) {
            $validationArr['location_township_id'] = 'required|exists:psx_location_townships,id';
        }
        if (in_array('currency_id', $coreFieldsIds)) {
            $validationArr['currency_id'] = 'required|exists:psx_currencies,id';
        }
        // if(in_array('price',$coreFieldsIds)){
        //     $validationArr['price'] = 'required';
        // }
        $validationArr['original_price'] = 'required|max:11';

        if(in_array('percent',$coreFieldsIds)){
            $validationArr['percent'] = 'required';
        }
        if (in_array('lat', $coreFieldsIds)) {
            $validationArr['lat'] = 'required';
        }
        if (in_array('lng', $coreFieldsIds)) {
            $validationArr['lng'] = 'required';
        }
        if (in_array('shop_id', $coreFieldsIds)) {
            $validationArr['shop_id'] = 'required';
        }
        if (in_array('search_tag', $coreFieldsIds)) {
            $validationArr['search_tag'] = 'required';
        }
        if (in_array('ordering', $coreFieldsIds)) {
            $validationArr['ordering'] = 'required';
        }
        if (in_array('is_discount', $coreFieldsIds)) {
            $validationArr['is_discount'] = 'required';
        }
        if (in_array('phone', $coreFieldsIds)) {
            $validationArr['phone'] = 'required';
        }
        if (in_array('phone', $coreFieldsIds)) {
            $validationArr['phone'] = 'required';
        }

        // $validationArr['cover'] = 'nullable|sometimes|image';
        $validationArr['video_icon'] = 'nullable|sometimes|image';
        $validationArr['video'] = 'nullable|sometimes|mimetypes:video/mp4';

        $attributes['original_price.max'] = "The original price must not be greater than 11 digits.";
        //prepare validation for core filed
        $validator = Validator::make($request->all(),$validationArr, $attributes
        //  [
            // 'cover' => 'nullable|file|mimes:jpg,png,jpeg',
            // 'video_icon' => 'nullable|file|mimes:jpg,png,jpeg',
            // 'video' => 'nullable|file|mimes:mp4|max:1500'
            // ]
        );

        // remove validation for item uploaded by user
        if(empty($request->vendor_id) && isset($errors['ps-itm00010'])){
            unset($errors["ps-itm00010"]);
        }

        if ($validator->fails()) {
            return redirect()->route('item.edit', $request->id)->with('product_relation_errors', $errors)
                ->withErrors($validator)
                ->withInput();
        } else {

            if (collect($errors)->isNotEmpty()) {
                return redirect()->route('item.edit', $request->id)->with('product_relation_errors', $errors);
            }
        }

        $product = $this->itemService->update($request);

        if (isset($product['error'])) {
            // go back to index
            $msg = $product['error'];
            return redirectView(self::indexRoute, $msg, $this->dangerFlag);
        }

        return redirectView(self::indexRoute, $product);
    }

    public function destroy($id)
    {
        $dataArr = $this->itemService->destroy($id, self::indexRoute);
        // check permission
        $checkPermission = $dataArr["checkPermission"];
        if (!empty($checkPermission)) {
            return $checkPermission;
        }
        return redirectView(self::indexRoute, $dataArr['msg'], $dataArr['flag']);
    }

    public function uploadMulti(Request $request)
    {
        if ($request->edit_mode == 1) {
            // dd($request->all());

            if ($request->hasFile('file')) {
                $currentImages = CoreImage::where('img_parent_id', $request->item_id)->where('img_type', 'item')->get()->count();

                $systemConfig = $this->systemConfigService->getSystemConfig();
                if ($currentImages >= $systemConfig->max_img_upload_of_item) {
                    return response()->json(['success' => 'You have reach max image upload', 'msg' => 'fail']);
                }
                // dd($currentImages);
                $file = $request->file('file');
                // $image = Image::make($file);
                // $fileName = uniqid()."_".".".$file->getClientOriginalExtension();

                $fileName = $this->itemService->updateMultiImage($request, 'cover', 'item', $request->item_id);
                // $image = $request->file('imageFilepond')->store('uploads/items','public');
                // dd($fileName);
                // return $request->file('imageFilepond')->store('uploads/items','public');

                //  $image->save(public_path() . '/storage/uploads/items/' . $fileName, 50);


                // dd($image);
                // return $fileName;
                return response()->json(['success' => $fileName, 'msg' => 'success']);
            }
        } else {
            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $image = Image::make($file);
                $fileName = uniqid() . "_" . "." . $file->getClientOriginalExtension();
                if (!File::isDirectory(public_path() . '/storage/uploads/items')) {
                    File::makeDirectory(public_path() . '/storage/uploads/items', 0777, true, true);
                }
                // $image = $request->file('imageFilepond')->store('uploads/items','public');
                // dd($fileName);
                // return $request->file('imageFilepond')->store('uploads/items','public');

                $image->save(public_path() . '/storage/uploads/items/' . $fileName, 50);


                // dd($image);
                // return $fileName;
                return response()->json(['success' => $fileName, 'msg' => 'success']);
            }
        }

        return '';
    }

    public function removeMulti(Request $request)
    {
        // dd($request->all());
        if ($request->edit_mode == 0) {
            $fileName = $request->image;
            $image_path = public_path() . '/storage/uploads/items/' . $fileName;
            if (File::exists($image_path)) {
                File::delete($image_path);
            }
            return response()->json(['success' => $fileName]);
        } else {
            $fileName = $request->image;
            $this->imageService->deleteMultiImage($request->img_parent_id, 'item', $fileName);
            return response()->json(['success' => $fileName]);
        }


        // if($request->image)
    }


    public function duplicateRow($id)
    {

        // echo json_encode($id);exit;
        $item = $this->itemService->getItem($id);

        $systemConfig = $this->systemConfigService->getSystemConfig();
        if ($systemConfig->is_approved_enable == 1) {
            $status = 0;
        } else {
            $status = $item->status;
        }

        // update data for duplicate item
        $update_copies = [
            'title' => 'Copy of ' . $item->title,
            'status' => $status,
            'added_user_id' => $item->added_user_id,
            'added_date' => Carbon::now(),
            'updated_user_id' => null,
            'updated_date' => null,
        ];

        $duplicate = duplicate($item, $update_copies, true);

        // For deeplink generate for duplicate item
        $img_conds['img_parent_id'] = $duplicate->id;
        $img_conds['img_type'] = 'item';
        $image = $this->imageService->getImage($img_conds);
        // $image = CoreImage::where(['img_parent_id' => $duplicate->id, 'img_type' => 'item'])->first();
        $img = empty($image) ? '' : $image->img_path;

        $dynamic_link = deeplinkGenerate($duplicate->id, $duplicate->title, $duplicate->description, $img);

        $duplicate->dynamic_link  = $dynamic_link['msg'];
        $duplicate->update();

        $status = [
            'flag' => 'success',
            // 'msg' => 'The '.$item->title.' row has been duplicated successfully.',
            'msg' => __('core__be_duplicate_success', ['attribute' => $item->title])
        ];

        if ($duplicate->status == 1) {
            return redirect()->route('item.index')->with('status', $status);
        } else {
            return redirect()->route('pending_item.index')->with('status', $status);
        }
    }

    public function deeplink($id)
    {

        // echo json_encode($id);exit;
        $item = $this->itemService->getItem($id);

        $img_conds['img_parent_id'] = $item->id;
        $img_conds['img_type'] = 'item';
        $img = $this->imageService->getImage($img_conds)->img_path;

        // $img = CoreImage::where(['img_parent_id' => $item->id, 'img_type' => 'item'])->first()->img_path;

        $dynamic_link = deeplinkGenerate($item->id, $item->title, $item->description, $img);

        $item->dynamic_link = $dynamic_link;
        $item->update();

        $status = [
            'flag' => 'success',
            // 'msg' => 'The deeplink of '.$item->title.' row has been generated successfully.',
            'msg' => __('core__be_deep_link_generate', ['attribute' => $item->title])

        ];

        return redirect()->route('item.index')->with('status', $status);
    }

    public function screenDisplayUiStore(Request $request)
    {
        $parameter = ['page' => $request->current_page];
        $this->itemService->makeColumnHideShown($request);

        $msg = 'ScreenDisplay UiSetting is updated.';
        // return redirectView(self::indexRoute, $msg,null,$parameter);
        return redirect()->back();
    }

    public function statusChange($id)
    {
        // dd("here");

        $this->itemService->makePublishOrUnpublish($id);


        // $dataArr = $this->itemService->index($request);
        // // check permission
        // $checkPermission = $dataArr["checkPermission"];
        // if (!empty($checkPermission)){
        //     return $checkPermission;
        // }

        $status = [
            'flag' => 'success',
            // 'msg' => 'The status of item has been updated successfully.',
            'msg' => __('core__be_status_attribute_updated', ['attribute' => __('core__be_item_label')])

        ];


        return redirect()->route('item.index')->with('status', $status);
    }
}
