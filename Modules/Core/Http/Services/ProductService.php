<?php

namespace Modules\Core\Http\Services;

use App\Config\ps_config;
use App\Http\Services\PsService;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Modules\Core\Entities\CustomizeUi;
use Modules\Core\Entities\CustomizeUiDetail;
use Modules\Core\Constants\Constants;
use Modules\Core\Entities\Category;
use Modules\Core\Entities\CoreFieldFilterSetting;
use Modules\Core\Entities\CoreImage;
use Modules\Core\Entities\Currency;
use Modules\Core\Entities\LocationCity;
use Modules\Core\Entities\LocationTownship;
use Modules\Core\Entities\Product;
use Modules\Core\Entities\ProductInfo;
use Modules\Core\Entities\ScreenDisplayUiSetting;
use Modules\Core\Entities\Shop;
use Modules\Core\Entities\Subcategory;

class  ProductService extends PsService
{

    protected $imageService, $currencyService, $locationTownshipService, $locationCityService, $publish, $subcategoryService, $categoryService, $currencyCurrencyShortFormCol, $prdShopIdCol, $prdItemCurrencyIdCol, $prdItemLocationTownshipIdCol, $shopNameCol, $locationTownshipNameCol, $locationCityNameCol, $subCatNameCol, $prdItemLocationIdCol, $prdSubCatIdCol, $shopTableName, $currencyTableName, $locationTownshipTableName, $locationCityTableName, $subCatTableName, $uploadPathForDel, $thumb1xPathForDel, $thumb2xPathForDel, $thumb3xPathForDel, $delete, $unDelete, $enable, $disable, $hide, $show, $thumbnail3xPath, $thumbnail2xPath, $thumbnail1xPath, $originPath, $componentName, $customUiMandatoryCol, $prdInfoCoreKeysIdCol, $customUiIsDelCol, $screenDisplayUiIsShowCol, $catNameCol, $userNameCol, $prdCatIdCol, $prdUserIdCol, $screenDisplayUiIdCol, $coreFieldFilterIdCol, $screenDisplayUiKeyCol, $coreFieldFilterModuleNameCol, $customUiEnableCol, $prdInfoProductIdCol, $customUiCoreKeysIdCol, $prdTableName, $userTableName, $catTableName, $code, $parentPath, $getImgPath, $coreFieldFilterForRelation, $viewAnyAbility ,$createAbility, $editAbility, $deleteAbility;

    public function __construct(ImageService $imageService, CurrencyService $currencyService, LocationTownshipService $locationTownshipService, LocationCityService $locationCityService, CategoryService $categoryService, SubcategoryService $subcategoryService)
    {
        $this->categoryService = $categoryService;
        $this->imageService = $imageService;
        $this->subcategoryService = $subcategoryService;
        $this->locationCityService = $locationCityService;
        $this->locationTownshipService = $locationTownshipService;
        $this->currencyService = $currencyService;
        $this->publish = Constants::publish;
        $this->code = Constants::item;
        $this->parentPath = Constants::parentPath;
        $this->getImgPath = Constants::imgPath;
        $this->viewAnyAbility = Constants::viewAnyAbility;
        $this->createAbility = Constants::createAbility;
        $this->editAbility = Constants::editAbility;
        $this->deleteAbility = Constants::deleteAbility;
        $this->coreFieldFilterForRelation = ps_config::coreFieldFilterForRelation;
        $this->paginationPerPage = ps_config::pagPerPage;

        $this->prdTableName = Product::tableName;
        $this->prdIdCol = Product::id;
        $this->prdCatIdCol = Product::categoryId;
        $this->prdSubCatIdCol = Product::subCategoryId;
        $this->prdItemLocationIdCol = Product::itemLocationId;
        $this->prdItemLocationTownshipIdCol = Product::itemLocationTownshipId;
        $this->prdUserIdCol = Product::userId;
        $this->prdItemCurrencyIdCol = Product::itemCurrencyId;
        $this->prdShopIdCol = Product::shopId;

        $this->catTableName = Category::tableName;
        $this->catNameCol = Category::name;

        $this->userTableName = User::tableName;
        $this->userNameCol = User::name;

        $this->subCatTableName = Subcategory::tableName;
        $this->subCatNameCol = Subcategory::name;

        $this->locationCityTableName = LocationCity::tableName;
        $this->locationCityNameCol = LocationCity::name;

        $this->locationTownshipTableName = LocationTownship::tableName;
        $this->locationTownshipNameCol = LocationTownship::name;

        $this->currencyTableName = Currency::tableName;
        $this->currencyCurrencyShortFormCol = Currency::currencyShortForm;

        $this->shopTableName = Shop::tableName;
        $this->shopNameCol = Shop::name;

        $this->customUiIdCol = CustomizeUi::id;
        $this->customUiCoreKeysIdCol = CustomizeUi::coreKeysId;
        $this->customUiEnableCol = CustomizeUi::enable;
        $this->customUiIsDelCol = CustomizeUi::isDelete;
        $this->customUiMandatoryCol = CustomizeUi::mandatory;

        $this->prdInfoProductIdCol = ProductInfo::productId;
        $this->prdInfoCoreKeysIdCol = ProductInfo::coreKeysId;

        $this->coreFieldFilterModuleNameCol = CoreFieldFilterSetting::moduleName;
        $this->coreFieldFilterIdCol = CoreFieldFilterSetting::id;

        $this->screenDisplayUiKeyCol = ScreenDisplayUiSetting::key;
        $this->screenDisplayUiIdCol = ScreenDisplayUiSetting::id;
        $this->screenDisplayUiIsShowCol = ScreenDisplayUiSetting::isShow;

        $this->componentName = "product";

        $this->originPath = Constants::originPath;
        $this->thumbnail1xPath = Constants::thumbnail1xPath;
        $this->thumbnail2xPath = Constants::thumbnail2xPath;
        $this->thumbnail3xPath = Constants::thumbnail3xPath;

        $this->uploadPathForDel = Constants::uploadPathForDel;
        $this->thumb1xPathForDel = Constants::thumb1xPathForDel;
        $this->thumb2xPathForDel = Constants::thumb2xPathForDel;
        $this->thumb3xPathForDel = Constants::thumb3xPathForDel;

        $this->show = Constants::show;
        $this->hide = Constants::hide;
        $this->enable = Constants::enable;
        $this->disable = Constants::disable;
        $this->delete = Constants::delete;
        $this->unDelete = Constants::unDelete;

    }


    // for Backend
    public function store($request){

        DB::beginTransaction();

        try {

            $product = new Product();
            $product->category_id = $request->category_id;
            $product->subcategory_id = $request->subCategory_id;
            $product->title = $request->title;
            $product->city_id = $request->city_id;
            $product->township_id = $request->township_id;
            $product->description = $request->desc;
            $product->price = $request->unit_price;
            $product->lat = $request->lat;
            $product->lng = $request->lng;
            $product->status = $request->status; // publish or not
            $product->currency_id = $request->currency_id;
            $product->is_available = $request->is_available;
            $product->orginal_price = $request->orginal_price;
            $product->is_discount = $request->is_discount;
            $product->added_user_id = Auth::id();
            $product->save();


            // save item cover photo
            if ($request->file('cover')){
                $file = $request->file('cover');
                $data['img_parent_id'] = $product->id;
                $data['img_type'] = 'item';
                $this->imageService->insertImage($file, $data);
            }

            // save item video icon photo
            if ($request->file('video_icon')){
                $file = $request->file('video_icon');
                $data['img_parent_id'] = $product->id;
                $data['img_type'] = 'item-video-icon';
                $this->imageService->insertImage($file, $data);
            }

            // save item video photo
            if ($request->file('video')){
                $file = $request->file('video');
                $data['img_parent_id'] = $product->id;
                $data['img_type'] = 'item-video';
                $this->imageService->insertVideo($file, $data);
            }

            // generate deeplink
            $img = CoreImage::where(['img_parent_id' => $product->id, 'img_type' => 'item'])->first()->img_path;
            $getProduct = $this->getProduct($product->id);
            $dynamic_link = deeplinkGenerate($getProduct->id, $getProduct->title, $getProduct->description, $img);
            $getProduct->dynamic_link = $dynamic_link;
            $getProduct->update();


            // save in product_relations table
            $customizeHeaders = $this->getCustomizeFields($this->code,null,null,0);
            $customizeHeaderIds = $this->getValueByPluck($customizeHeaders,$this->customUiCoreKeysIdCol);

            $this->storeCustomFieldValues($request, $product, $customizeHeaderIds);

            DB::commit();

        } catch (\Throwable $e){
            DB::rollBack();
            $error = $e->getMessage() . "</br>" .$e->getLine();
            return $error;
        }
    }

    public function storeCustomFieldValues($request, $product, $coreKeysIds){

        if (!empty($request->product_relation)){
            foreach ($coreKeysIds as $coreKeysId){
                foreach ($request->product_relation as $key => $value){

                    if ($key === $coreKeysId){
                        $productRelation = new ProductInfo();

                        if(is_file($value)) {
                            $file = $this->checkFileInCustomFieldValue($value);
                        }

                        $productRelation->product_id = $product->id;

                        if ($value === false) {
                            $productRelation->value = 0;
                        }
                        if(is_file($value)) {
                            if (str_contains($value->getMimeType(), 'image')) {
                                $productRelation->value = $file;
                            }
                        }
                        if(!is_file($value) && $value !== false) {
                            $productRelation->value = $value;
                        }

                        $customizeHeaders = $this->getCustomizeFields($this->code,null,null,0);
                        foreach ($customizeHeaders as $key2=>$customizeHeader){
                            if($key === $customizeHeader->core_keys_id){
                                $productRelation->ui_type_id = $customizeHeader->ui_type_id;
                                $productRelation->core_keys_id = $customizeHeader->core_keys_id;
                            }
                        }
                        $productRelation->save();
                    }

                }
            }
        }

    }

    public function update($product,$request){

        DB::beginTransaction();

        try {

            $product->category_id = $request->category_id;
            $product->subcategory_id = $request->subCategory_id;
            $product->title = $request->title;
            $product->city_id = $request->city_id;
            $product->township_id = $request->township_id;
            $product->description = $request->desc;
            $product->price = $request->unit_price;
            $product->lat = $request->lat;
            $product->lng = $request->lng;
            $product->status = $request->status; // publish or not
            $product->currency_id = $request->currency_id;
            $product->is_available = $request->is_available;
            $product->orginal_price = $request->orginal_price;
            $product->is_discount = 0;
            $product->added_user_id = Auth::id();
            $product->update();

            // save item cover photo
            if ($request->file('cover')){
                $file = $request->file('cover');

                $conds['img_parent_id'] = $product->id;
                $codns['img_type'] = 'item';
                $image = CoreImage::where($conds)->first();

                // if image, delete existing file
                if(!empty($image)){
                    // delete image from storage folder
                    $this->imageService->deleteImage($image->img_path);
                }


                $data['img_parent_id'] = $product->id;
                $data['img_type'] = 'item';
                $this->imageService->insertImage($file, $data, $image);
            }

            // save item video icon photo
            if ($request->file('video_icon')){
                $file = $request->file('video_icon');

                $conds['img_parent_id'] = $product->id;
                $codns['img_type'] = 'item-video-icon';
                $image = CoreImage::where($conds)->first();

                // if image, delete existing file
                if(!empty($image)){
                    // delete image from storage folder
                    $this->imageService->deleteImage($image->img_path);
                }

                $data['img_parent_id'] = $product->id;
                $data['img_type'] = 'item';
                $this->imageService->insertImage($file, $data, $image);
            }

            // save item video photo
            if ($request->file('video')){
                $file = $request->file('video');

                $conds['img_parent_id'] = $product->id;
                $codns['img_type'] = 'item-video';
                $image = CoreImage::where($conds)->first();

                // if image, delete existing file
                if(!empty($image)){
                    // delete image from storage folder
                    $this->imageService->deleteVideo($image->img_path);
                }

                $data['img_parent_id'] = $product->id;
                $data['img_type'] = 'item-video';
                $this->imageService->insertVideo($file, $data);
            }


            // generate deeplink
            $image = CoreImage::where(['img_parent_id' => $product->id, 'img_type' => 'item'])->first();
            $img = $image? $image->img_path : '';
            $dynamic_link = deeplinkGenerate($product->id, $product->title, $product->description, $img);
            $product->dynamic_link = $dynamic_link;
            $product->update();

            $allDataIndex = [];
            $oldDataIndex = [];

            // save in post_relations table
            foreach ($request->product_relation as $key => $value){

                array_push($allDataIndex,$key);
                if ($value !== null && $value !== "undefined") {

                    $productRelations = ProductInfo::where('product_id', $product->id)->get();

                    foreach ($productRelations as $key3=>$productRelation){
                        array_push($oldDataIndex, $productRelation->core_keys_id);

                        if ($key == $productRelation->core_keys_id) {

                            if(is_file($value)) {
                                // del file from local first
                                delImageFromCustomFieldValue($productRelation, $this->uploadPathForDel, $this->thumb1xPathForDel, $this->thumb2xPathForDel, $this->thumbnail3xPath);

                                // save new file in local again
                                $file = $this->checkFileInCustomFieldValue($value);
                            }


                            // update in db start
                            $productRelation->product_id = $product->id;
                            if ($value === false) {
                                $productRelation->value = 0;
                            }
                            if (is_file($value)) {
                                if (str_contains($value->getMimeType(), 'image')) {
                                    $productRelation->value = $file;
                                }
                            }
                            if (!is_file($value) && $value !== false) {
                                $productRelation->value = $value;
                            }

                            $customizeHeaders = CustomizeUi::all();
                            foreach ($customizeHeaders as $key2 => $customizeHeader) {
                                if ($key === $key2) {
                                    $productRelation->ui_type_id = $customizeHeader->ui_type_id;
                                    $productRelation->core_keys_id = $customizeHeader->core_keys_id;
                                }
                            }

                            $productRelation->update();
                            // update in db end
                        }
                    }
                }
            }

            $newDataIndexs = array_diff(array_unique($allDataIndex),array_unique($oldDataIndex));

            $this->storeCustomFieldValues($request, $product, $newDataIndexs);


            DB::commit();
        } catch (\Throwable $e){
            DB::rollBack();
            $error = $e->getMessage() . "</br>" .$e->getLine();
            return $error;
        }

    }

    public function updateCustomFieldOldValues($request, $product){
        foreach ($request->product_relation as $key => $value){

            array_push($allDataIndex,$key);
            if ($value !== null && $value !== "undefined") {

                $productRelations = ProductInfo::where('product_id', $product->id)->get();
                $customizeHeaders = CustomizeUi::where("module_name","prd")->get();

                foreach ($productRelations as $key3=>$productRelation){
                    array_push($oldDataIndex, $productRelation->core_keys_id);

                    if ($key == $productRelation->core_keys_id) {

                        $dir = '/public/product/';
                        Storage::delete($dir . $productRelation->value);


                        if (is_file($value)) {
                            if (str_contains($value->getMimeType(), 'image')) {
                                $img = Image::make($value);

                                // change file to new name
                                $file = uniqid() . "_product." . $value->getClientOriginalExtension();


                                //save origin
                                $origin = 'storage/uploads/';
                                $img->save($origin . $file, 30);


                                //save 1x
                                $thumbnail1x = Image::make($value);
                                $thumbnail1xDir = 'storage/thumbnail/';
                                $thumbnail1x->resize(200, null, function ($constraint) {
                                    $constraint->aspectRatio();
                                    $constraint->upsize();
                                });
                                $thumbnail1x->save($thumbnail1xDir . $file);

                                //save 2x
                                $thumbnail2x = Image::make($value);
                                $thumbnail2xDir = 'storage/thumbnail2x/';
                                $thumbnail2x->resize(400, null, function ($constraint) {
                                    $constraint->aspectRatio();
                                    $constraint->upsize();
                                });
                                $thumbnail2x->save($thumbnail2xDir . $file);

                                //save 3x
                                $thumbnail3x = Image::make($value);
                                $thumbnail3xDir = 'storage/thumbnail3x/';
                                $thumbnail3x->resize(600, null, function ($constraint) {
                                    $constraint->aspectRatio();
                                    $constraint->upsize();
                                });
                                $thumbnail3x->save($thumbnail3xDir . $file);


                            } else if (str_contains($value->getMimeType(), 'video')) {
                                return 'This is video';
                            } else {
                                return 'other';
                            }
                        }

                        $productRelation->product_id = $product->id;
                        if ($value === false) {
                            $productRelation->value = 0;
                        }
                        if (is_file($value)) {
                            if (str_contains($value->getMimeType(), 'image')) {
                                $productRelation->value = $file;
                            }
                        }
                        if (!is_file($value) && $value !== false) {
                            $productRelation->value = $value;
                        }

                        $customizeHeaders = CustomizeUi::all();
                        foreach ($customizeHeaders as $key2 => $customizeHeader) {
                            if ($key === $key2) {
                                $productRelation->ui_type_id = $customizeHeader->ui_type_id;
                                $productRelation->core_keys_id = $customizeHeader->core_keys_id;
                            }
                        }

                        $productRelation->update();
                    }

                }

            }


        }
    }

    public function updateCustomFieldNewValues($request, $product, $newDataIndexs){
        foreach ($newDataIndexs as $key3=>$newDataIndex){
            foreach ($request->product_relation as $key => $value){

                if ($key3 == $key) {
                    $productRelation = new ProductInfo();

                    $file = $this->checkFileInCustomFieldValue($value);

                    $productRelation->product_id = $product->id;

                    if ($value === false) {
                        $productRelation->value = 0;
                    }
                    if(is_file($value)) {
                        if (str_contains($value->getMimeType(), 'image')) {
                            $productRelation->value = $file;
                        }
                    }
                    if(!is_file($value) && $value !== false) {
                        $productRelation->value = $value;
                    }

                    $customizeHeaders = CustomizeUi::all();
                    foreach ($customizeHeaders as $key2=>$customizeHeader){
                        if($key === $key2){
                            $productRelation->ui_type_id = $customizeHeader->ui_type_id;
                            $productRelation->core_keys_id = $customizeHeader->core_keys_id;
                        }
                    }
                    $productRelation->save();
                }

            }
        }
    }

    public function checkFileInCustomFieldValue($value){

        if (str_contains($value->getMimeType(),'image')){
            $img = Image::make($value);

            // change file to new name
            $file = uniqid()."_product.".$value->getClientOriginalExtension();

            saveImgAsOriginalThumbNail1x2x3x($value, $file,$this->originPath, $this->thumbnail1xPath, $this->thumbnail2xPath, $this->thumbnail3xPath);

        } else if(str_contains($value->getMimeType(),'video')){
            return 'This is video';
        } else {
            return 'other';
        }
        return $file;

    }

    public function getCoreFieldFilteredLists($code){
        return CoreFieldFilterSetting::where($this->coreFieldFilterModuleNameCol, $code)->get();
    }

    public function deleteCoreFilteredOldData($code){
        $oldcoreFields = CoreFieldFilterSetting::with(["screenDisplayUiSetting"])->where($this->coreFieldFilterModuleNameCol, $code)->get();
        $oldcoreFieldIds = $oldcoreFields->pluck($this->coreFieldFilterIdCol);
        $screenDisplayUiSettingIds = ScreenDisplayUiSetting::whereIn($this->screenDisplayUiKeyCol, $oldcoreFieldIds)->get()->pluck($this->screenDisplayUiIdCol);

        // delete core field from screen_display_ui-settings
        ScreenDisplayUiSetting::destroy($screenDisplayUiSettingIds);

        // delete core field from core_fields
        foreach ($oldcoreFields as $oldcoreField){
            $oldcoreField->delete();
        }
    }

    public function createCoreFieldsForFilter($productCoreLists, $labels, $coreFieldsForFilter, $categories, $subCategories, $locationCities, $locationTownships, $users, $currencies, $shops, $code, $coreFieldFilterForRelation){
        foreach ($productCoreLists as $productCoreListKey=>$productCoreList) {
            foreach ($coreFieldsForFilter as $coreFieldKey => $coreFieldForFilter) {
                if (($coreFieldForFilter !== null && $coreFieldForFilter !== false) && $coreFieldKey == $productCoreListKey) {

                    // save in core_field_filter_settings
                    $coreFieldFilterSetting = $this->saveCoreFieldFilterSettings($labels, $productCoreListKey, $coreFieldKey ,$code, $productCoreList, $categories, $subCategories, $locationCities, $locationTownships, $users, $currencies, $shops, $coreFieldFilterForRelation);

                    // save in screen_display_ui_settings table
                    $this->saveScreenDisplayUiSettings($code, $coreFieldFilterSetting);
                }
            }
        }
    }

    public function saveCoreFieldFilterSettings($labels, $productCoreListKey, $coreFieldKey, $code, $productCoreList, $categories, $subCategories, $locationCities, $locationTownships, $users, $currencies, $shops, $coreFieldFilterForRelation){
        $coreFieldFilterSetting = new CoreFieldFilterSetting();
        $coreFieldFilterSetting->module_name = $code;
        if ($productCoreListKey === $coreFieldKey) {
            if ($productCoreList == $this->prdCatIdCol) {
                $coreFieldFilterSetting->field_name = relationForCoreFieldFilter($productCoreList, $this->prdCatIdCol, $categories, $this->catNameCol, $coreFieldFilterForRelation);
            } elseif ($productCoreList == $this->prdSubCatIdCol) {
                $coreFieldFilterSetting->field_name = relationForCoreFieldFilter($productCoreList, $this->prdSubCatIdCol, $subCategories, $this->subCatNameCol, $coreFieldFilterForRelation);
            } elseif ($productCoreList == $this->prdItemLocationIdCol) {
                $coreFieldFilterSetting->field_name = relationForCoreFieldFilter($productCoreList, $this->prdItemLocationIdCol, $locationCities, $this->locationCityNameCol, $coreFieldFilterForRelation);
            } elseif ($productCoreList == $this->prdItemLocationTownshipIdCol) {
                $coreFieldFilterSetting->field_name = relationForCoreFieldFilter($productCoreList, $this->prdItemLocationTownshipIdCol, $locationTownships, $this->locationTownshipNameCol, $coreFieldFilterForRelation);
            } elseif ($productCoreList == $this->prdUserIdCol) {
                $coreFieldFilterSetting->field_name = relationForCoreFieldFilter($productCoreList, $this->prdUserIdCol, $users, $this->userNameCol, $coreFieldFilterForRelation);
            } elseif ($productCoreList == $this->prdItemCurrencyIdCol) {
                $coreFieldFilterSetting->field_name = relationForCoreFieldFilter($productCoreList, $this->prdItemCurrencyIdCol, $currencies, $this->currencyCurrencyShortFormCol, $coreFieldFilterForRelation);
            } elseif ($productCoreList == $this->prdShopIdCol) {
                $coreFieldFilterSetting->field_name = relationForCoreFieldFilter($productCoreList, $this->prdShopIdCol, $shops, $this->shopNameCol, $coreFieldFilterForRelation);
            } else {
                $coreFieldFilterSetting->field_name = $productCoreList;
            }
        }

        foreach ($labels as $labelKey => $label) {
            if ($coreFieldKey === $labelKey) {
                $coreFieldFilterSetting->label_name = $label;
            }
        }

        $integerFields = ['price', 'ordering', 'lat', 'lng', 'orginal_price', 'overall_rating', 'status', 'is_available'];
        $dateFields = ['added_date', 'updated_date'];
        if (in_array($coreFieldFilterSetting->field_name, $integerFields)) {
            $coreFieldFilterSetting->data_type = 'Integer';
        } elseif (in_array($coreFieldFilterSetting->field_name, $dateFields)){
            $coreFieldFilterSetting->data_type = 'Date';
        } else {
            $coreFieldFilterSetting->data_type = 'String';
        }

        $coreFieldFilterSetting->save();

        return $coreFieldFilterSetting;
    }

    public function saveScreenDisplayUiSettings($code, $coreFieldFilterSetting){
        $screenDisplayUiSetting = new ScreenDisplayUiSetting();
        $screenDisplayUiSetting->module_name = $code;
        $screenDisplayUiSetting->key = $coreFieldFilterSetting->id;
        $screenDisplayUiSetting->is_show = $this->show;
        $screenDisplayUiSetting->save();

        return $screenDisplayUiSetting;
    }

    public function makeColumnHideShown($request){
        $hideShowFields = $request->value;
//        foreach ($hideShowFields as $hideShowField){
//            $ScreenDisplayUiSetting = ScreenDisplayUiSetting::find($hideShowField[$this->screenDisplayUiIdCol]);
//            $ScreenDisplayUiSetting->is_show = $hideShowField['hidden'] ? $this->hide : $this->show;
//            $ScreenDisplayUiSetting->update();
//        }
        foreach ($hideShowFields as $hideShowField){
            $hideShowFieldData[] = [
                'id' => $hideShowField['id'],
                'is_show' => $hideShowField['hidden'] ? $this->hide : $this->show,
            ];
        }
        ScreenDisplayUiSetting::upsert(
            $hideShowFieldData,
            ['id'], ['is_show']
        );
    }

    public function hiddenShownForCoreAndCustomField($isShown, $code){
        $hiddenShownForFields = ScreenDisplayUiSetting::with(['customizeField' => function ($q){
            $q->where($this->customUiEnableCol, $this->enable)->where($this->customUiIsDelCol, $this->unDelete);
        }, 'coreField'=> function($q){
            $q->where($this->coreFieldFilterModuleNameCol, $this->code);
        }])
            ->where($this->coreFieldFilterModuleNameCol, $code)
            ->where($this->screenDisplayUiIsShowCol, $isShown)
            ->get();
        return $hiddenShownForFields;
    }

    public function getFilteredCoreFields($code){
        $coreFields = CoreFieldFilterSetting::where($this->coreFieldFilterModuleNameCol, $code)->get();
        return $coreFields;
    }

    public function getCustomizeFields($code = null, $dataWithRelation = null, $coreKeysId = null, $isDel = null){
        $customizeHeader  =  CustomizeUi::when($dataWithRelation, function ($q, $data){
                                $q->with($data);
                             })
                            ->when($code, function ($q, $data){
                                $q->where($this->coreFieldFilterModuleNameCol, $data);
                            })
                            ->when($isDel !== null,  function ($q) use ($isDel){
                                $q->where($this->customUiIsDelCol, $isDel);
                            })
                            ->when($coreKeysId, function ($q, $data){
                                $q->where($this->customUiCoreKeysIdCol, $data);
                            })
                            ->get();
        return $customizeHeader;
    }

    public function getCustomizeField($code = null, $dataWithRelation = null, $coreKeysId = null){
        $customizeHeader  =  CustomizeUi::when($dataWithRelation, function ($q, $data){
                                $q->with($data);
                            })
                            ->when($code, function ($q, $data){
                                $q->where($this->coreFieldFilterModuleNameCol, $data);
                            })
                            ->when($coreKeysId, function ($q, $data){
                                $q->where($this->customUiCoreKeysIdCol, $data);
                            })
                            ->first();
        return $customizeHeader;
    }

    public function getCustomizeFieldAttrs($customizeHeaderId = null){
        $customizeDetails = CustomizeUiDetail::when($customizeHeaderId,function ($q, $data){
                                                    $q->where($this->customUiCoreKeysIdCol, $data);
                                                })
                                                ->latest()->get();
        return $customizeDetails;
    }

    public function getCustomizeFieldAttr($id){
        $customizeDetail = CustomizeUiDetail::find($id);
        return $customizeDetail;
    }

    public function deleteCustomizeFieldAttrs($customizeDetails){
        if ($customizeDetails->isNotEmpty()) {
            $customizeDetailIds = $customizeDetails->pluck($this->customUiIdCol);
            CustomizeUiDetail::destroy($customizeDetailIds);
        }
    }

    public function getSubCategories(){
        $Subcategory = Category::latest('id')->get();

        return $Subcategory;
    }

    public function getValueByPluck($values, $pluckColumn){
        $pluckedValues = $values->pluck($pluckColumn)->unique()->values();
        return $pluckedValues;
    }

    public function getValuesForCustomizeField($customizeHeaderIds = null, $productId = null){
        $values = ProductInfo::when($customizeHeaderIds,function ($q, $data){
                                    $q->whereIn($this->prdInfoCoreKeysIdCol, $data);
                                })
                                ->when($productId, function ($q, $data){
                                    $q->where($this->prdInfoProductIdCol,$data);
                                })
                                ->get();
        return $values;
    }

    public function getProducts($pagPerPage = 10){
        $products = Product::with([
                            'category',
                            'subCategory',
                            'city',
                            'township',
                            'currency',
                            'productRelation.uiType',
                            'productRelation.customizeUi',
                            'owner'])
                            ->latest($this->prdIdCol)
                            ->paginate($pagPerPage);
        return $products;
    }

    public function getProduct($id, $dataWithRelation = null){
        $product = Product::when($dataWithRelation, function ($q, $data){
                                $q->with($data);
                            })->find($id);
        return $product;
    }

    public function deleteCustomizeFieldData($productRelations){


        foreach ($productRelations as $productRelation) {

            delImageFromCustomFieldValue($productRelation, $this->uploadPathForDel, $this->thumb1xPathForDel, $this->thumb2xPathForDel, $this->thumbnail3xPath);
            $productRelation->delete();
        }
    }

    // ----------------

    public function filterForCoreField(){
        $productCoreLists = getAllCoreFields($this->prdTableName);
        $coreFieldFilterSettings = $this->getCoreFieldFilteredLists($this->code);

        $dataArr = [
            'productCoreLists' => $productCoreLists,
            'coreFieldFilterSettings' => $coreFieldFilterSettings,
        ];

        return $dataArr;
    }

    public function filterForCoreFieldStore($request){
        // delete old data start
        $this->deleteCoreFilteredOldData($this->code);
        // delete old data end

        // prepare for needing data start
        $productCoreLists = getAllCoreFields($this->prdTableName);
        $categories = getAllCoreFields($this->catTableName);
        $subCategories = getAllCoreFields($this->subCatTableName);
        $locationCities = getAllCoreFields($this->locationCityTableName);
        $locationTownships = getAllCoreFields($this->locationTownshipTableName);
        $users = getAllCoreFields($this->userTableName);
        $currencies = getAllCoreFields($this->currencyTableName);
        $shops = getAllCoreFields($this->shopTableName);


        $code = $this->code;
        $coreFieldFilterForRelation = $this->coreFieldFilterForRelation;

        $coreFieldsForFilter = $request->coreFieldsForFilter;
        $labels = $request->label;
        // prepare for needing data end

        $this->createCoreFieldsForFilter(
            $productCoreLists,
            $labels,
            $coreFieldsForFilter,
            $categories,
            $subCategories,
            $locationCities,
            $locationTownships,
            $users,
            $currencies,
            $shops,
            $code,
            $coreFieldFilterForRelation);

    }

    public function index($routeName){
        $code = $this->code;

        // check permission
        // $checkPermission = $this->checkPermission($this->viewAnyAbility,Product::class, $routeName);
        // if($checkPermission){
        //     return $checkPermission;
        // }

        $hiddenForCoreAndCustomField = $this->hiddenShownForCoreAndCustomField($this->hide, $code);
        $shownForCoreAndCustomField = $this->hiddenShownForCoreAndCustomField($this->show, $code);

        $coreFields = $this->getFilteredCoreFields($code);
        $productCoreLists = getAllCoreFields($this->prdTableName);

        $dataWithRelation = ['hideShowSettingForFields'];

        $customizeHeader = $this->getCustomizeFields($code, $dataWithRelation,null,0);

        $customizeHeaderIds = $this->getValueByPluck($customizeHeader,$this->customUiCoreKeysIdCol);

        $productRelations = $this->getValuesForCustomizeField($customizeHeaderIds);
        $productIds = $this->getValueByPluck($productRelations, $this->prdInfoProductIdCol);

        $customizeDetails = $this->getCustomizeFieldAttrs();
        $imgUrl = asset($this->getImgPath);
        $products = $this->getProducts();

        $dataArr = [
            'products' => $products,
            "customizeHeaders" => $customizeHeader,
            'imgUrl' => $imgUrl,
            'customizeDetails' => $customizeDetails,
            'productRelations' => $productRelations,
            'hiddenForCoreAndCustomField' => $hiddenForCoreAndCustomField,
            'shownForCoreAndCustomField' => $shownForCoreAndCustomField,
            'coreFields' => $coreFields,
            'productCoreLists' => $productCoreLists
        ];

        return $dataArr;
    }

    public function create($routeName){
        $code = $this->code;

        // check permission
        $checkPermission = $this->checkPermission($this->createAbility,Product::class, $routeName);
        if($checkPermission){
            return $checkPermission;
        }

        $coreFieldFilterSettings = $this->getCoreFieldFilteredLists($code);
        $customizeHeader = $this->getCustomizeFields($code,null,null,0);
        $customizeDetail = $this->getCustomizeFieldAttrs();
        $categories = $this->categoryService->getCategories(null, $this->publish);
        $subCategories = $this->subcategoryService->getSubCategories(null, $this->publish);
        $locationCities = $this->locationCityService->getLocationCities(null, $this->publish);
        $locationTownships = $this->locationTownshipService->getLocationTownships(null, $this->publish);
        $currencies = $this->currencyService->getCurrencies($this->publish);

        $dataArr = [
            "customizeHeaders" => $customizeHeader,
            "customizeDetails" => $customizeDetail,
            'categories' => $categories,
            'subCategories' => $subCategories,
            "locationCities" => $locationCities,
            "locationTownships" => $locationTownships,
            'currencies' => $currencies,
            'coreFieldFilterSettings' => $coreFieldFilterSettings
        ];

        return $dataArr;
    }

    public function edit($id, $routeName){
        $dataWithRelation = ["productRelation"];
        $product = $this->getProduct($id, $dataWithRelation);
        $code = $this->code;

        // check permission
        $checkPermission = $this->checkPermission($this->editAbility,$product, $routeName);
        if($checkPermission){
            return $checkPermission;
        }

        $coreFieldFilterSettings = $this->getCoreFieldFilteredLists($code);
        $customizeHeader = $this->getCustomizeFields($code,null,null,0);
        $customizeDetail = $this->getCustomizeFieldAttrs();
        $categories = $this->categoryService->getCategories(null, $this->publish);
        $subCategories = $this->subcategoryService->getSubCategories(null, $this->publish);
        $locationCities = $this->locationCityService->getLocationCities(null, $this->publish);
        $locationTownships = $this->locationTownshipService->getLocationTownships(null, $this->publish);
        $currencies = $this->currencyService->getCurrencies($this->publish);

        $dataArr = [
            'product'   => $product,
            'categories' => $categories,
            'subCategories' => $subCategories,
            "locationCities" => $locationCities,
            "locationTownships" => $locationTownships,
            'currencies' => $currencies,
            "customizeHeaders" => $customizeHeader,
            "customizeDetails" => $customizeDetail,
            'coreFieldFilterSettings' => $coreFieldFilterSettings
        ];

        return $dataArr;
    }

    public function destroy($id, $routeName){
        //delete in posts table
        $product = $this->getProduct($id);

        // check permission
        $checkPermission = $this->checkPermission($this->deleteAbility,$product, $routeName);
        if($checkPermission){
            return $checkPermission;
        }

        $title = $product->title;
        $product->delete();

        $images = $this->imageService->getImages($id, 'item');
        $this->imageService->deleteImagesFromBoth($images);


        //delete in product_relations table
        $productRelations = $this->getValuesForCustomizeField('',$product->id);
        $this->deleteCustomizeFieldData($productRelations);

        return $title;
    }

    public function customization(){
        $code = $this->code;
        $customizeHeader = $this->getCustomizeFields($code,null,null,0);
        $dataArr = [
            "customizeHeaders" => $customizeHeader,
        ];
        return $dataArr;
    }

    public function customizationDestroy($customizeHeader){
        // delete temporary ( update is_delete to 1 in customize_headers table )
        $customizeHeader->is_delete = $this->delete;
        $customizeHeader->update();
    }

    public function customizationDetailIndex($coreKeysId){
        $customizeHeader = $this->getCustomizeField(null, null, $coreKeysId);
        $customizeDetail = $this->getCustomizeFieldAttrs($coreKeysId);
        // return $customizeDetail;
        $dataArr = [
            "customizeHeader" => $customizeHeader,
            "customizeDetails" => $customizeDetail,
        ];
        return $dataArr;
    }

    public function customizationDetailCreate($coreKeysId){
        $customizeHeader = $this->getCustomizeField(null, null, $coreKeysId);
        $dataArr = [
            "customizeHeader" => $customizeHeader,
        ];
        return $dataArr;
    }

    public function customizationDetailStore($request){
        $customizeDetail = createCustomizeAttr($request);
        return $customizeDetail;
    }

    public function customizationDetailDestroy($id){
        $customizationDetail = $this->getCustomizeFieldAttr($id);
        $customizationDetail->delete();
        return $customizationDetail;
    }

    public function customizationDetailEdit($id){
        $customizationDetail = $this->getCustomizeFieldAttr($id);
        $customizeHeader =  $this->getCustomizeField(null, null, $customizationDetail->core_keys_id);

        $dataArr = [
            'customizationDetail' => $customizationDetail,
            "customizeHeader" => $customizeHeader,
        ];

        return $dataArr;
    }

    public function customizationDetailUpdate($id, $request){
        $customizationDetail = $this->getCustomizeFieldAttr($id);
        updateCustomizeAttr($customizationDetail, $request);
    }

    public function addNewField(){
        $uiTypes = getSupportedUi();

        $dataArr = [
            'uiTypes' => $uiTypes,
        ];
        return $dataArr;
    }

    public function addNewFieldStore($request){
        $code = $this->code;

        // save in customize_headers table
        $customizeHeader = createCustomField($request, $code);

        // save in core_keys table
        $coreKey = createCoreKey($customizeHeader, $code);

        // save in screen_display_ui_settings table
        createForHideShow($coreKey, $code);
    }

    public function addNewFieldEdit($coreKeysId){
        $customizeHeader = $this->getCustomizeField(null, null, $coreKeysId);
        $uiTypes = getSupportedUi();

        $dataArr = [
            "customizeHeader" => $customizeHeader,
            'uiTypes' => $uiTypes
        ];
        return $dataArr;
    }

    public function addNewFieldUpdate($request, $coreKeysId){
        $code = $this->code;
        $customizeHeader = $this->getCustomizeField(null, null, $coreKeysId);

        // save in customize_headers table
        $updatedCustomField = updateCustomField($customizeHeader,$request, $code);

        // save in core_keys table
        $coreKey = getCoreKey($coreKeysId);
        $updatedCoreKey = updateCoreKey($coreKey, $updatedCustomField, $code);
    }

    public function enableOrDisableField($coreKeysId){
        $customizeHeader = $this->getCustomizeField(null, null, $coreKeysId);

        customFieldStatusUpdate($customizeHeader, $this->customUiEnableCol);

        return $customizeHeader;
    }

    public function addNewFieldMandatory($coreKeysId){
        $customizeHeader = $this->getCustomizeField(null, null, $coreKeysId);

        customFieldStatusUpdate($customizeHeader, $this->customUiMandatoryCol);
        return $customizeHeader;
    }

    public function deletedTemporaryFields(){
        $temDeletedFields = $this->getCustomizeFields($this->code, null, null, $this->delete);
        $dataArr = [
            "temDeletedFields" => $temDeletedFields,
        ];
        return $dataArr;
    }

    public function undeletedForCustomization($customizeHeader){
        $customizeHeader->is_delete = $this->unDelete;
        $customizeHeader->update();
    }

    // for mobile
    public function createFromApi($request, $moduleName){
        $product = new Product();
        $product->title = $request->title;
        $product->description = $request->desc;
        $product->category_id = $request->category_id;
        $product->user_id = Auth::id();
        $product->save();

        // save in product_relations table
        $haveValueCoreKeysIds = [];
        $customizeHeaderIds = [];


        $productRelations = json_decode(json_encode($request->product_relation));
        foreach ($productRelations as $key => $value){
            array_push($haveValueCoreKeysIds, $value->core_keys_id);
            $productRelation = new ProductInfo();
            // return $value->getMimeType();
            // return $value;
            if(is_file($value->value)){
                if (str_contains($value->value->getMimeType(),'image')){
                    $img = Image::make($value->value);

                    // change file to new name
                    $file = uniqid()."_blog.".$value->value->getClientOriginalExtension();

                    // make watermark for origin
                    $watermark = Image::make(public_path('ps-logo.jpg'));
                    $watermark->fit(300,300);
                    $img->insert($watermark,'bottom-right',20,20);

                    //save origin
                    $origin = 'storage/product/original/';
                    $img->save($origin.$file,30);


                    //save 1x
                    $thumbnail1x = Image::make($value->value);
                    $thumbnail1xDir = 'storage/product/thumbnail1x/';
                    $thumbnail1x->resize(200, null, function ($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    });
                    $thumbnail1x->save($thumbnail1xDir.$file);

                    //save 2x
                    $thumbnail2x = Image::make($value->value);
                    $thumbnail2xDir = 'storage/product/thumbnail2x/';
                    $thumbnail2x->resize(400, null, function ($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    });
                    $thumbnail2x->save($thumbnail2xDir.$file);

                    //save 3x
                    $thumbnail3x = Image::make($value->value);
                    $thumbnail3xDir = 'storage/product/thumbnail3x/';
                    $thumbnail3x->resize(600, null, function ($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    });
                    $thumbnail3x->save($thumbnail3xDir.$file);


                // $dir = '/public/blog/original/';
                // $value->value->storeAs($dir,$file);

                } else if(str_contains($value->value->getMimeType(),'video')){
                    return 'This is video';
                } else {
                    return 'other';
                }
            }

            $productRelation->product_id = $product->id;

            if ($value->value === false) {
                $productRelation->value = 0;
            }
            if(is_file($value->value)) {
                if (str_contains($value->value->getMimeType(), 'image')) {
                    $productRelation->value = $file;
                }
            }
            if(!is_file($value->value) && $value->value !== false) {
                $productRelation->value = $value->value;
            }

            $customizeHeaders = CustomizeUi::all();
            foreach ($customizeHeaders as $key2=>$customizeHeader){
                if($value->core_keys_id === $customizeHeader->core_keys_id){
                    $productRelation->ui_type_id = $customizeHeader->ui_type_id;
                    $productRelation->core_keys_id = $customizeHeader->core_keys_id;
                }
            }
            $productRelation->save();

        }

        $customizeHeaders = CustomizeUi::where('module_name',$moduleName)->get();
        foreach ($customizeHeaders as $key => $value){
            array_push($customizeHeaderIds,$value->core_keys_id);
        }

        $NoValueCoreKeysIds = array_diff($customizeHeaderIds, $haveValueCoreKeysIds);

        foreach ($NoValueCoreKeysIds as $noValueCoreKeysId) {
            foreach ($customizeHeaders as $customizeHeader){
                if($noValueCoreKeysId === $customizeHeader->core_keys_id){
                    $productRelation = new ProductInfo();
                    $productRelation->product_id = $product->id;
                    $productRelation->value = null;
                    $productRelation->ui_type_id = $customizeHeader->ui_type_id;
                    $productRelation->core_keys_id = $customizeHeader->core_keys_id;
                    $productRelation->save();
                }
            }
        }


        return $product->id;
    }

    public function updateFromApi($request, $product){

        $product->title = $request->title;
        $product->description = $request->desc;
        $product->category_id = $request->category_id;
        $product->user_id = Auth::id();
        $product->update();

        // save in product_relations table

        $productRelationsFromRequest = json_decode(json_encode($request->product_relation));

        $haveValueCoreKeysIds = [];
        $oldDataIndex = [];

        foreach ($productRelationsFromRequest as $key => $value){
            array_push($haveValueCoreKeysIds, $value->core_keys_id);
            if ($value !== null && $value !== "undefined") {

                $productRelations = ProductInfo::where('product_id', $product->id)->get();

                foreach ($productRelations as $key3=>$productRelation){
                    array_push($oldDataIndex, $productRelation->core_keys_id);
                    if ($value->core_keys_id == $productRelation->core_keys_id) {
                        if(is_file($value->value)){
                            if (str_contains($value->value->getMimeType(),'image')){
                                $img = Image::make($value->value);

                                // change file to new name
                                $file = uniqid()."_blog.".$value->value->getClientOriginalExtension();

                                // make watermark for origin
                                $watermark = Image::make(public_path('ps-logo.jpg'));
                                $watermark->fit(300,300);
                                $img->insert($watermark,'bottom-right',20,20);

                                //save origin
                                $origin = 'storage/product/original/';
                                $img->save($origin.$file,30);


                                //save 1x
                                $thumbnail1x = Image::make($value->value);
                                $thumbnail1xDir = 'storage/product/thumbnail1x/';
                                $thumbnail1x->resize(200, null, function ($constraint) {
                                    $constraint->aspectRatio();
                                    $constraint->upsize();
                                });
                                $thumbnail1x->save($thumbnail1xDir.$file);

                                //save 2x
                                $thumbnail2x = Image::make($value->value);
                                $thumbnail2xDir = 'storage/product/thumbnail2x/';
                                $thumbnail2x->resize(400, null, function ($constraint) {
                                    $constraint->aspectRatio();
                                    $constraint->upsize();
                                });
                                $thumbnail2x->save($thumbnail2xDir.$file);

                                //save 3x
                                $thumbnail3x = Image::make($value->value);
                                $thumbnail3xDir = 'storage/product/thumbnail3x/';
                                $thumbnail3x->resize(600, null, function ($constraint) {
                                    $constraint->aspectRatio();
                                    $constraint->upsize();
                                });
                                $thumbnail3x->save($thumbnail3xDir.$file);


                            // $dir = '/public/blog/original/';
                            // $value->value->storeAs($dir,$file);

                            } else if(str_contains($value->value->getMimeType(),'video')){
                                return 'This is video';
                            } else {
                                return 'other';
                            }
                        }

                        $productRelation->product_id = $product->id;

                        if ($value->value === false) {
                            $productRelation->value = 0;
                        }
                        if(is_file($value->value)) {
                            if (str_contains($value->value->getMimeType(), 'image')) {
                                $productRelation->value = $file;
                            }
                        }
                        if(!is_file($value->value) && $value->value !== false) {
                            $productRelation->value = $value->value;
                        }

                        $customizeHeaders = CustomizeUi::all();
                        foreach ($customizeHeaders as $key2=>$customizeHeader){
                            if($value->core_keys_id === $customizeHeader->core_keys_id){
                                $productRelation->ui_type_id = $customizeHeader->ui_type_id;
                                $productRelation->core_keys_id = $customizeHeader->core_keys_id;
                            }
                        }
                        $productRelation->update();
                    }

                }


            }


        }

        $newDataIndexs = array_diff(array_unique($haveValueCoreKeysIds),array_unique($oldDataIndex));

        foreach ($newDataIndexs as $coreKeysId){
            foreach ($request->product_relation as $key => $value){

                if ($value->core_keys_id === $coreKeysId){
                    $productRelation = new ProductInfo();

                    $value = $value->value;

                    if(is_file($value)) {
                        $file = $this->checkFileInCustomFieldValue($value);
                    }

                    $productRelation->product_id = $product->id;

                    if ($value === false) {
                        $productRelation->value = 0;
                    }
                    if(is_file($value)) {
                        if (str_contains($value->getMimeType(), 'image')) {
                            $productRelation->value = $file;
                        }
                    }
                    if(!is_file($value) && $value !== false) {
                        $productRelation->value = $value;
                    }

                    $customizeHeaders = $this->getCustomizeFields($this->code,null,null,0);
                    foreach ($customizeHeaders as $key2=>$customizeHeader){
                        if($key === $customizeHeader->core_keys_id){
                            $productRelation->ui_type_id = $customizeHeader->ui_type_id;
                            $productRelation->core_keys_id = $customizeHeader->core_keys_id;
                        }
                    }
                    $productRelation->save();
                }

            }
        }

            //        foreach ($newDataIndexs as $noValueCoreKeysId) {
            //            foreach ($customizeHeaders as $customizeHeader){
            //                if($noValueCoreKeysId === $customizeHeader->core_keys_id){
            //                    $productRelation = new ProductInfo();
            //                    $productRelation->product_id = $product->id;
            //                    $productRelation->value = ;
            //                    $productRelation->ui_type_id = $customizeHeader->ui_type_id;
            //                    $productRelation->core_keys_id = $customizeHeader->core_keys_id;
            //                    $productRelation->save();
            //                }
            //            }
            //        }


        return $product->id;
    }
}
