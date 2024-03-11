<?php

namespace Modules\Core\Http\Controllers\Backend\Controllers\Product;


use App\Config\ps_config;
use App\Http\Requests\StoreNewFieldRequest;
use App\Http\Requests\UpdateNewFieldRequest;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image;
use Modules\Core\Entities\CustomizeUi;
use Modules\Core\Constants\Constants;
use Modules\Core\Entities\Product;
use Modules\Core\Entities\ProductInfo;
use Modules\Core\Http\Requests\StoreProductRequest;
use Modules\Core\Http\Services\ItemService;
use Modules\Core\Http\Services\ProductService;


class ProductController extends Controller
{

    const parentPath = "core/product/";
    const coreFieldFilterSettingPath = self::parentPath."CoreFieldFilterSetting";
    const customizationPath = self::parentPath."Customization";
    const indexPath = self::parentPath.'Index';
    const createPath = self::parentPath.'Create';
    const editPath = self::parentPath.'Edit';
    const customizationDetailIndexPath = self::parentPath."CustomizationDetailIndex";
    const customizationDetailCreatePath = self::parentPath."CustomizationDetailCreate";
    const customizationDetailEditPath = self::parentPath."CustomizationDetailEdit";
    const addNewFieldPath = self::parentPath."AddNewField";
    const addNewFieldEditPath = self::parentPath."AddNewFieldEdit";
    const temDeletedFieldsPath = self::parentPath."TemDeletedFields";
    const indexRoute = "product.index";
    const coreFieldFilterSettingRoute = "product.coreFieldFilterSetting";
    const customizationRoute = "product.customization";
    const customizationDetailIndexRoute = "product.customizationDetail.index";
    const deletedTemFieldsRoute = "product.deletedTemFields";

    protected $dangerFlag ,$successFlag, $productService, $code, $parentPath, $getImgPath, $coreFieldFilterForRelation, $viewAnyAbility ,$createAbility, $editAbility, $deleteAbility;


    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
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
    }


    public function filterForCoreField() {

        $dataArr = $this->productService->filterForCoreField();
        return renderView(self::coreFieldFilterSettingPath, $dataArr);
    }

    public function filterForCoreFieldStore(Request $request) {

        $this->productService->filterForCoreFieldStore($request);

        $msg = 'Core Field Filter Setting is updated.';
        return redirectView(self::coreFieldFilterSettingRoute, $msg);
    }

    public function screenDisplayUiStore(Request $request) {

        $this->productService->makeColumnHideShown($request);

        $msg = 'ScreenDisplay UiSetting is updated.';
        return redirectView(self::indexRoute, $msg);
    }

    public function index()
    {
        $dataArr = $this->productService->index(self::indexRoute);
        return renderView(self::indexPath, $dataArr);
    }

    public function create()
    {
        $dataArr = $this->productService->create(self::indexRoute);
        return renderView(self::createPath, $dataArr);
    }

    public function store(Request $request)
    {
        // return $request;
        // check permission
        $checkPermission = $this->checkPermission($this->createAbility,Product::class, 'product.index');
        if($checkPermission){
            return $checkPermission;
        }


        //prepare for validation
        $errors = validateForCustomField($this->code,$request->product_relation);

        //validation
        $validator = Validator::make($request->all(), [
            'title' => 'required|min:3|unique:psx_products,title',
            'desc' => 'required|min:10',
            'category_id' => 'required|exists:psx_categories,id',
            'subCategory_id' => 'required|exists:psx_subcategories,id',
            'city_id' => 'required|exists:psx_location_cities,id',
            'currency_id' => 'required|exists:psx_currencies,id',
            'township_id' => 'required|exists:psx_location_townships,id',
            'unit_price' => 'required',
            'lat' => 'required',
            'lng' => 'required',
            'cover' => 'required|file|mimes:jpg,png,jpeg',
            'video_icon' => 'nullable|file|mimes:jpg,png,jpeg',
            'video' => 'nullable|file|mimes:mp4|max:1500'
        ]);


        if ($validator->fails()) {
            return redirect()->route('product.create')->with('product_relation_errors',$errors)
                ->withErrors($validator)
                ->withInput();
        } else {

            if (collect($errors)->isNotEmpty()){
                return redirect()->route('product.create')->with('product_relation_errors',$errors);
            }
        }


        $product = $this->productService->store($request);
        if ($product){
            // go back to index
            $msg = $product;
            return redirectView(self::indexRoute, $msg, $this->dangerFlag);
        }

        $msg = "Product has been added successfully.";
        return redirectView(self::indexRoute, $msg);

    }

    public function show($id)
    {
        return view('core::show');
    }

    public function edit($id)
    {
        $dataArr = $this->productService->edit($id, self::indexRoute);
        return renderView(self::editPath, $dataArr);
    }


    public function update(Request $request, Product $product)
    {

        // check permission
        $checkPermission = $this->checkPermission($this->editAbility,$product, 'product.index');
        if($checkPermission){
            return $checkPermission;
        }

        //prepare for validation
        $errors = validateForCustomField($this->code,$request->product_relation);

        //validation
        $validator = Validator::make($request->all(), [
            'title' => 'required|min:3|unique:psx_products,title,'.$product->id,
            'desc' => 'required|min:10',
            'category_id' => 'required|exists:psx_categories,id'
        ]);


        if ($validator->fails()) {
            return redirect()->route('product.edit',$product->id)->with('product_relation_errors',$errors)
                ->withErrors($validator)
                ->withInput();
        } else {

            if (collect($errors)->isNotEmpty()){
                return redirect()->route('product.edit',$product->id)->with('product_relation_errors',$errors);
            }
        }

        $product = $this->productService->update($product,$request);

        if ($product){
            // go back to index
            $msg = $product;
            return redirectView(self::indexRoute, $msg, $this->dangerFlag);
        }
        //        $product->title = $request->title;
        //        $product->description = $request->desc;
        //        $product->category_id = $request->category_id;
        //        $product->user_id = Auth::id();
        //        $product->update();
        //
        //        $allDataIndex = [];
        //        $oldDataIndex = [];
        //
        //
        //        // save in post_relations table
        //        foreach ($request->product_relation as $key => $value){
        //
        //           array_push($allDataIndex,$key);
        //           if ($value !== null && $value !== "undefined") {
        //
        //               $productRelations = ProductInfo::where('product_id', $product->id)->get();
        //               $customizeHeaders = CustomizeUi::where("module_name","prd")->get();
        //
        //               foreach ($productRelations as $key3=>$productRelation){
        //                  array_push($oldDataIndex, $productRelation->core_keys_id);
        //
        //                          if ($key == $productRelation->core_keys_id) {
        //
        //                              $dir = '/public/product/';
        //                              Storage::delete($dir . $productRelation->value);
        //
        //
        //                              if (is_file($value)) {
        //                                  if (str_contains($value->getMimeType(), 'image')) {
        //                                      $img = Image::make($value);
        //
        //                                      // change file to new name
        //                                      $file = uniqid() . "_product." . $value->getClientOriginalExtension();
        //
        //
        //                                      //save origin
        //                                      $origin = 'storage/uploads/';
        //                                      $img->save($origin . $file, 30);
        //
        //
        //                                      //save 1x
        //                                      $thumbnail1x = Image::make($value);
        //                                      $thumbnail1xDir = 'storage/thumbnail/';
        //                                      $thumbnail1x->resize(200, null, function ($constraint) {
        //                                          $constraint->aspectRatio();
        //                                          $constraint->upsize();
        //                                      });
        //                                      $thumbnail1x->save($thumbnail1xDir . $file);
        //
        //                                      //save 2x
        //                                      $thumbnail2x = Image::make($value);
        //                                      $thumbnail2xDir = 'storage/thumbnail2x/';
        //                                      $thumbnail2x->resize(400, null, function ($constraint) {
        //                                          $constraint->aspectRatio();
        //                                          $constraint->upsize();
        //                                      });
        //                                      $thumbnail2x->save($thumbnail2xDir . $file);
        //
        //                                      //save 3x
        //                                      $thumbnail3x = Image::make($value);
        //                                      $thumbnail3xDir = 'storage/thumbnail3x/';
        //                                      $thumbnail3x->resize(600, null, function ($constraint) {
        //                                          $constraint->aspectRatio();
        //                                          $constraint->upsize();
        //                                      });
        //                                      $thumbnail3x->save($thumbnail3xDir . $file);
        //
        //
        //                                  } else if (str_contains($value->getMimeType(), 'video')) {
        //                                      return 'This is video';
        //                                  } else {
        //                                      return 'other';
        //                                  }
        //                              }
        //
        //                              $productRelation->product_id = $product->id;
        //                              if ($value === false) {
        //                                  $productRelation->value = 0;
        //                              }
        //                              if (is_file($value)) {
        //                                  if (str_contains($value->getMimeType(), 'image')) {
        //                                      $productRelation->value = $file;
        //                                  }
        //                              }
        //                              if (!is_file($value) && $value !== false) {
        //                                  $productRelation->value = $value;
        //                              }
        //
        //                              $customizeHeaders = CustomizeUi::all();
        //                              foreach ($customizeHeaders as $key2 => $customizeHeader) {
        //                                  if ($key === $key2) {
        //                                      $productRelation->ui_type_id = $customizeHeader->ui_type_id;
        //                                      $productRelation->core_keys_id = $customizeHeader->core_keys_id;
        //                                  }
        //                              }
        //
        //                           $productRelation->update();
        //                          }
        //
        //               }
        //
        //           }
        //
        //
        //        }
        //
        //        $newDataIndexs = array_diff(array_unique($allDataIndex),array_unique($oldDataIndex));
        //        return array_unique($newDataIndexs);
        //        foreach ($newDataIndexs as $key3=>$newDataIndex){
        //            foreach ($request->product_relation as $key => $value){
        //                if ($newDataIndex == $key) {
        //                    $productRelation = new ProductInfo();
        //
        //                    if(is_file($value)){
        //                        if (str_contains($value->getMimeType(),'image')){
        //                            $img = Image::make($value);
        //
        //                            // change file to new name
        //                            $file = uniqid() . "_product." . $value->getClientOriginalExtension();
        //
        //
        //                            //save origin
        //                            $origin = 'storage/uploads/';
        //                            $img->save($origin . $file, 30);
        //
        //
        //                            //save 1x
        //                            $thumbnail1x = Image::make($value);
        //                            $thumbnail1xDir = 'storage/thumbnail/';
        //                            $thumbnail1x->resize(200, null, function ($constraint) {
        //                                $constraint->aspectRatio();
        //                                $constraint->upsize();
        //                            });
        //                            $thumbnail1x->save($thumbnail1xDir . $file);
        //
        //                            //save 2x
        //                            $thumbnail2x = Image::make($value);
        //                            $thumbnail2xDir = 'storage/thumbnail2x/';
        //                            $thumbnail2x->resize(400, null, function ($constraint) {
        //                                $constraint->aspectRatio();
        //                                $constraint->upsize();
        //                            });
        //                            $thumbnail2x->save($thumbnail2xDir . $file);
        //
        //                            //save 3x
        //                            $thumbnail3x = Image::make($value);
        //                            $thumbnail3xDir = 'storage/thumbnail3x/';
        //                            $thumbnail3x->resize(600, null, function ($constraint) {
        //                                $constraint->aspectRatio();
        //                                $constraint->upsize();
        //                            });
        //                            $thumbnail3x->save($thumbnail3xDir . $file);
        //
        //
        ////                        $dir = '/public/blog/original/';
        ////                        $value->storeAs($dir,$file);
        //
        //                        } else if(str_contains($value->getMimeType(),'video')){
        //                            return 'This is video';
        //                        } else {
        //                            return 'other';
        //                        }
        //                    }
        //
        //                    $productRelation->product_id = $product->id;
        //
        //                    if ($value === false) {
        //                        $productRelation->value = 0;
        //                    }
        //                    if(is_file($value)) {
        //                        if (str_contains($value->getMimeType(), 'image')) {
        //                            $productRelation->value = $file;
        //                        }
        //                    }
        //                    if(!is_file($value) && $value !== false) {
        //                        $productRelation->value = $value;
        //                    }
        //
        //                    $customizeHeaders = CustomizeUi::all();
        //                    foreach ($customizeHeaders as $key2=>$customizeHeader){
        //                        if($key === $customizeHeader->core_keys_id){
        //                            $productRelation->ui_type_id = $customizeHeader->ui_type_id;
        //                            $productRelation->core_keys_id = $customizeHeader->core_keys_id;
        //                        }
        //                    }
        //                    $productRelation->save();
        //                }
        //
        //
        //            }
        //        }

        $msg = 'Product is updated.';
        return redirectView(self::indexRoute, $msg);

    }


    public function destroy($id)
    {
        $title = $this->productService->destroy($id, self::indexRoute);

        // go back to index
        $msg = "$title has been deleted successfully.";
        return redirectView(self::indexRoute, $msg);
    }


    public function customization()
    {
        $dataArr = $this->productService->customization();
        return renderView(self::customizationPath, $dataArr);
    }

    public function customizationDestroy(CustomizeUi $customizeHeader)
    {

        $this->productService->customizationDestroy($customizeHeader);

        $msg = 'CustomizeUi has been temporary deleted successfully.';
        return redirectView(self::customizationRoute, $msg);
    }

    public function customizationDetailIndex($coreKeysId)
    {

        $dataArr = $this->productService->customizationDetailIndex($coreKeysId);

        return renderView(self::customizationDetailIndexPath, $dataArr);
    }

    public function customizationDetailCreate($coreKeysId)
    {
        $dataArr = $this->productService->customizationDetailCreate($coreKeysId);
        return renderView(self::customizationDetailCreatePath, $dataArr);
    }

    public function customizationDetailStore(Request $request)
    {
        $customizeDetail = $this->productService->customizationDetailStore($request);

        $msg = 'Attribute has been created successfully.';
        return redirectView(self::customizationDetailIndexRoute, $msg, $this->successFlag, $request->core_keys_id);
    }

    public function customizationDetailDestroy($id)
    {
        $customizationDetail = $this->productService->customizationDetailDestroy($id);

        $msg = "$customizationDetail->name has been deleted successfully.";
        return redirectView("product.customizationDetail.index", $msg, $this->successFlag, $customizationDetail->core_keys_id);
    }

    public function customizationDetailEdit($id)
    {
        $dataArr = $this->productService->customizationDetailEdit($id);
        return renderView(self::customizationDetailEditPath, $dataArr);
    }

    public function customizationDetailUpdate($id, Request $request)
    {
        $this->productService->customizationDetailUpdate($id, $request);
        $msg = "$request->name has been created successfully.";
        return redirectView(self::customizationDetailIndexRoute, $msg, $this->successFlag, $request->core_keys_id);
    }

    public function addNewField()
    {
        $dataArr = $this->productService->addNewField();
        return renderView(self::addNewFieldPath, $dataArr);
    }

    public function addNewFieldStore(StoreNewFieldRequest $request)
    {
        $this->productService->addNewFieldStore($request);

        $msg = "New Field has been added successfully.";
        return redirectView("product.customization", $msg, $this->successFlag);
    }

    public function addNewFieldEdit($coreKeysId){
        $dataArr = $this->productService->addNewFieldEdit($coreKeysId);
        return renderView(self::addNewFieldEditPath, $dataArr);
    }

    public function addNewFieldUpdate(UpdateNewFieldRequest $request, $coreKeysId){
        $this->productService->addNewFieldUpdate($request, $coreKeysId);

        $msg = "New Field has been updated successfully.";
        return redirectView(self::customizationRoute, $msg, $this->successFlag);
    }

    public function enableOrDisableField($coreKeysId){

        $customizeHeader = $this->productService->enableOrDisableField($coreKeysId);

        $msg = $customizeHeader->name.' have been updated '.($customizeHeader->enable ? 'enabled' : 'disabled') .' successfully';
        return redirectView(self::customizationRoute, $msg, $this->successFlag);

    }

    public function addNewFieldMandatory($coreKeysId) {

        $customizeHeader = $this->productService->addNewFieldMandatory($coreKeysId);

        $msg = $customizeHeader->name.' have been updated to '.($customizeHeader->mandatory ? 'Mandatory' : 'Optional') .' successfully';
        return redirectView(self::customizationRoute, $msg,$this->successFlag);

    }

    public function deletedTemFields() {

        $dataArr = $this->productService->deletedTemporaryFields();
        return renderView(self::temDeletedFieldsPath, $dataArr);

    }

    public function undeletedForCustomization(CustomizeUi $customizeHeader){

        $this->productService->undeletedForCustomization($customizeHeader);

        $msg = $customizeHeader->name." has been undeleted successfully.";
        return redirectView(self::deletedTemFieldsRoute,$msg);

    }


//    public function exportConfigs(){
//        $customizeHeaders = CustomizeUi::all();
//
//        $file = public_path('fbs_config.json');
//        file_put_contents($file,$customizeHeaders);
////        $data = file_get_contents($file);
////        $data = json_decode($data,JSON_PRETTY_PRINT);
//
//        return \Illuminate\Support\Facades\Response::download($file);
//    }
//
//    public function importConfigs(Request $request){
//
//        // Firstly, delete all data from CustomHeader table
//        CustomizeUi::whereNotNull('id')->delete();
//
//
//        $file = $request->file('configFile');
//        $dataFromImportFile = json_decode($file->getContent());
//
//        foreach ($dataFromImportFile as $value){
//            $customizeHeader = new CustomizeUi();
//            $customizeHeader->id = $value->id;
//            $customizeHeader->name = $value->name;
//            $customizeHeader->ui_type_id = $value->ui_type_id;
//            $customizeHeader->mandatory = $value->mandatory;
//            $customizeHeader->enable = $value->enable;
//            $customizeHeader->module_name = $value->module_name;
//            $customizeHeader->save();
//        }
//        return "data";
//        $fileName = $file->getClientOriginalName();
//        $dir = '/public/configs/';
//        $file->storeAs($dir,$fileName);
//
////        $oldFile = public_path('fbs_config.json');
////        $importFile = public_path('/storage/configs/'.$fileName);
////
////        $oldData = file_get_contents($oldFile);
////        $importData = file_get_contents($importFile);
////
////        $removeOldData = file_put_contents($oldFile,' ');
////        $addImportData = file_put_contents($oldFile,$importData);
//        return to_route('blog.index')->with('status',' Config has been imported successfully');
//    }

}


