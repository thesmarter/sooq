<?php

namespace Modules\Core\Http\Controllers\Backend\Rests\App\V1_0\Product;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use Modules\Core\Constants\Constants;
// use Modules\Core\Entities\MobileLanguage;
// use Modules\Core\Entities\CoreFieldFilterSetting;
use Modules\Core\Http\Services\ItemService;
use Modules\Core\Http\Services\UserService;
use Modules\Core\Http\Services\SystemConfigService;
use Modules\Core\Http\Services\UserInfoService;
use Modules\Core\Transformers\Api\App\V1_0\Product\ProductApiResource;
use Modules\Core\Transformers\Api\App\V1_0\CoreImage\CoreImageApiResource;
use Modules\Core\Transformers\Api\App\V1_0\CustomizeUi\CustomizeUiApiResource;
use Modules\Core\Transformers\Api\App\V1_0\CoreKey\CoreFieldApiResource;
use Modules\Core\Transformers\Api\App\V1_0\CustomizeUiDetail\CustomizeUiDetailApiResource;
use Modules\Core\Transformers\Api\App\V1_0\HomePageSearch\HomePageSearchApiResource;
use Illuminate\Contracts\Translation\Translator;
use Modules\Core\Entities\BackendSetting;
use Modules\Core\Http\Services\CoreFieldFilterSettingService;
use Modules\Core\Http\Services\VendorService;
use Modules\Core\Transformers\Api\App\V1_0\Vendor\VendorForItemEntryApiResource;

use Modules\Core\Entities\UserInfo;

class ProductApiController extends Controller
{
    protected $vendorService, $userService, $systemConfigService, $coreFieldFilterSettingService, $translator, $notFoundStatusCode, $itemService, $code, $dropDownUi, $textUi, $radioUi, $checkBoxUi, $dateTimeUi, $numberUi, $textAreaUi, $multiSelectUi, $imageUi, $timeOnlyUi, $dateOnlyUi, $systemConfig, $userInfoService, $internalServerErrorStatusCode, $pendingItem;

    public function __construct(VendorService $vendorService, CoreFieldFilterSettingService $coreFieldFilterSettingService, Translator $translator, ItemService $itemService, SystemConfigService $systemConfigService, UserInfoService $userInfoService, UserService $userService)
    {
        $this->itemService = $itemService;
        $this->vendorService = $vendorService;
        $this->systemConfigService = $systemConfigService;
        $this->userInfoService = $userInfoService;
        $this->translator = $translator;

        $this->code = Constants::item;
        $this->userService = $userService;
        $this->dropDownUi = Constants::dropDownUi;
        $this->textUi = Constants::textUi;
        $this->radioUi = Constants::radioUi;
        $this->checkBoxUi = Constants::checkBoxUi;
        $this->dateTimeUi = Constants::dateTimeUi;
        $this->textAreaUi = Constants::textAreaUi;
        $this->numberUi = Constants::numberUi;
        $this->multiSelectUi = Constants::multiSelectUi;
        $this->imageUi = Constants::imageUi;
        $this->timeOnlyUi = Constants::timeOnlyUi;
        $this->dateOnlyUi = Constants::dateOnlyUi;
        $this->notFoundStatusCode = Constants::notFoundStatusCode;
        $this->noContentStatusCode = Constants::noContentStatusCode;
        $this->internalServerErrorStatusCode = Constants::internalServerErrorStatusCode;
        $this->successStatus = Constants::successStatus;
        $this->createdStatusCode = Constants::createdStatusCode;
        $this->forbiddenStatusCode = Constants::forbiddenStatusCode;
        $this->badRequestStatusCode = Constants::badRequestStatusCode;
        $this->okStatusCode = Constants::okStatusCode;
        $this->pendingItem = Constants::pendingItem;

        $this->coreFieldFilterSettingService = $coreFieldFilterSettingService;
    }

    public function index(Request $request)
    {
        $response = $this->itemService->indexFromApi($request);
        if (isset($response['error'])) {
            if (isset($response['status'])) {
                return responseMsgApi($response['error'], $response['status']);
            } else {
                return responseMsgApi($response['error'], $this->badRequestStatusCode);
            }
        } else {

            $data = ProductApiResource::collection($response);

            $hasError = $this->itemService->noDataError($request->offset, $data);

            if ($hasError)
                return $hasError;
            else {
                return $data;
            }
        }
    }

    public function uploadMulti(Request $request)
    {
        if ($request->hasFile('file')) {
            // dd($request->all());
            $file = $request->file('file');
            $fileName = $this->itemService->updateMultiImage($request, 'cover', 'item', $request->item_id);

            return response()->json(['success' => $fileName, 'msg' => 'success']);
            // $file = $request->file('file');
            // $image = Image::make($file);
            // $fileName = uniqid()."_".".".$file->getClientOriginalExtension();
            // if (!File::isDirectory(public_path() . '/storage/uploads/items')) {
            //     File::makeDirectory(public_path() . '/storage/uploads/items', 0777, true, true);
            // }
            // // $image = $request->file('imageFilepond')->store('uploads/items','public');
            // // dd($fileName);
            // // return $request->file('imageFilepond')->store('uploads/items','public');

            //  $image->save(public_path() . '/storage/uploads/items/' . $fileName, 50);


            // // dd($image);
            // // return $fileName;
            // return response()->json(['success'=>$fileName,'msg'=>'success']);

        }
    }

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'language_symbol' => 'required|exists:psx_languages,symbol',
        ]);

        if ($request->language_symbol) {
            $this->translator->setLocale($request->language_symbol);
            $validator->setTranslator($this->translator);
        }

        $msg = implode("\n", Arr::flatten($validator->getMessageBag()->getMessages()));

        if ($validator->fails()) {
            return responseMsgApi($msg, $this->badRequestStatusCode);
        }

        $custom = [];
        $core = [];

        $entryForCoreAndCustom = $this->itemService->createFromApi($request);

        if (isset($entryForCoreAndCustom['custom'])) {
            // return response()->json([
            //     "custom" => $entryForCoreAndCustom['custom'],
            // ],200);
            $custom = CustomizeUiApiResource::collection($entryForCoreAndCustom['custom']);
        }
        if (isset($entryForCoreAndCustom['core'])) {

            $core = CoreFieldApiResource::collection($entryForCoreAndCustom['core']);
        }

        if (isset($entryForCoreAndCustom['createAsVendor']) && count($entryForCoreAndCustom['createAsVendor']) > 0) {
            $createAsVendor = VendorForItemEntryApiResource::collection($this->vendorService->getVendorByIds(null ,$entryForCoreAndCustom['createAsVendor']));
        }
        else{
            $createAsVendor = [];
        }

        return response()->json([
            "vendor_list" => $createAsVendor,
            "custom" => $custom,
            "core" => $core

        ], 200);
    }

    public function getGalleryList(Request $request)
    {
        $response = $this->itemService->getGalleryListFromApi($request);

        if (isset($response['error'])) {
            if (isset($response['status'])) {
                return responseMsgApi($response['error'], $response['status']);
            } else {
                return responseMsgApi($response['error'], $this->badRequestStatusCode);
            }
        } else {

            $data = CoreImageApiResource::collection($response);

            $hasError = $this->itemService->noDataError($request->offset, $data);

            if ($hasError)
                return $hasError;
            else {
                return $data;
            }
        }
    }




    public function store(Request $request)
    {

        // dd($request->all());
        $upload_setting = BackendSetting::first()->upload_setting;
        $conds['id'] = $request->login_user_id;
        $user_data = $this->userService->getUser(null, $conds);
        // dd($user_data);
        if($user_data){
            // dd($user_data);
            $bluemark_conds['core_keys_id'] = Constants::usrIsVerifyBlueMark;
            $bluemark_conds['user_id'] = $user_data->id;
            // dd($bluemark_conds);
            $is_verify_blue_mark = '';
            $blueMarkUser = UserInfo::where($bluemark_conds)->first();
            // dd($blueMarkUser);
            if ($blueMarkUser) {
                $is_verify_blue_mark = $blueMarkUser->value;
                // dd($is_verify_blue_mark);

            }
            if($upload_setting == 'admin-bluemark'){
                if($user_data->role_id != 1 &&  $is_verify_blue_mark != 1){
                    $uploadSettingErrors = __('core__api_item_upload_not_allow', [], $request->language_symbol);
                }
            }
            if($upload_setting == 'admin'){
                if($user_data->role_id != 1){
                    $uploadSettingErrors = __('core__api_item_upload_not_allow', [], $request->language_symbol);
                }
            }
        }

        if (isset($uploadSettingErrors)) {

            return responseMsgApi($uploadSettingErrors, $this->badRequestStatusCode);
        }
        // dd("here");
        /// validation start

        //prepare validation for custom filed
        $errors = validateForCustomFieldFromApi($this->code, $request->product_relation);

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
            $validationArr['currency_id'] = 'required|exists:psx_currencies,id';
        }
        if (in_array('vendor_id', $coreFieldsIds)) {
            $validationArr['vendor_id'] = 'required|exists:psx_vendors,id';
        }
        // if(in_array('price',$coreFieldsIds)){
        //     $validationArr['price'] = 'required';
        // }
        $validationArr['original_price'] = 'required|max:11';
        if (in_array('percent', $coreFieldsIds)) {
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
        $attributes['original_price.max'] = "The original price must not be greater than 11 digits.";
        //prepare validation for core filed
        $validator = Validator::make(
            $request->all(),
            $validationArr,
            $attributes
            //  [
            // 'cover' => 'nullable|file|mimes:jpg,png,jpeg',
            // 'video_icon' => 'nullable|file|mimes:jpg,png,jpeg',
            // 'video' => 'nullable|file|mimes:mp4|max:1500'
            // ]
        );
        if ($request->language_symbol) {
            $this->translator->setLocale($request->language_symbol);
            $validator->setTranslator($this->translator);
        }

        // remove validation for item uploaded by user
        if(empty($request->vendor_id) && isset($errors['ps-itm00010'])){
            unset($errors["ps-itm00010"]);
        }

        if ($validator->fails()) {
            $validationError = implode("\n", Arr::flatten($validator->getMessageBag()->getMessages()));
            $errorMsg = implode("\n", $errors);
            if ($errorMsg) {
                $validationError = $validationError . "\n" . $errorMsg;
            }
            return responseMsgApi($validationError, $this->badRequestStatusCode);
        } else {
            if (collect($errors)->isNotEmpty()) {
                $errorMsg = implode("\n", $errors);

                return responseMsgApi($errorMsg, $this->badRequestStatusCode);
            }
        }

        /// validation end

        $id = $request->id;
        // dd($id);

        if (empty($id)) {
            //store data in database
            // try {
                // dd("here");
            $item = $this->itemService->storeFromApi($request, $this->code);
            // dd($item);

            if (isset($item['error'])) {
                if (isset($item['status'])) {
                    return responseMsgApi($item['error'], $item['status']);
                } else {
                    return responseMsgApi($item['error'], $this->badRequestStatusCode);
                }
            }

            $itemApiRelation = ['category', 'subcategory', 'city', 'township', 'currency', 'owner', 'itemRelation', 'cover', 'video', 'icon'];

            $item = new ProductApiResource($this->itemService->getItem($item->id, $itemApiRelation));
            return responseDataApi($item);

            // }catch (\Throwable $e){
            // return responseDataApi($e);
            // return responseMsgApi(__('core__api_db_error',[],$request->language_symbol), $this->internalServerErrorStatusCode);
            // }
        } else {
            $item = $this->itemService->getItem($id);
            if ($item) {
                //update data in database
                try {
                    $updatedproductId = $this->itemService->updateFromApi($request, $item);
                    // dd($updatedproductId);

                    if (isset($updatedproductId['error'])) {
                        if (isset($updatedproductId['status'])) {
                            return responseMsgApi($updatedproductId['error'], $updatedproductId['status']);
                        } else {
                            return responseMsgApi($updatedproductId['error'], $this->badRequestStatusCode);
                        }
                    }

                    $productApiRelation = ['category', 'subCategory', 'city', 'township', 'currency', 'owner', 'itemRelation'];
                    $item = new ProductApiResource($this->itemService->getItem($updatedproductId, $productApiRelation));

                    return responseDataApi($item);
                } catch (\Throwable $e) {
                    return responseMsgApi(__('core__api_db_error', [], $request->language_symbol), $this->internalServerErrorStatusCode);
                }
            } else {
                return responseMsgApi(__('core__api_item_invalid', [], $request->language_symbol), $this->badRequestStatusCode);
            }
        }
    }

    public function deleteItem(Request $request)
    {
        $msg = $this->itemService->destroyFromApi($request, $request->id);
        if (isset($msg['error'])) {
            if (isset($msg['status'])) {
                return responseMsgApi($msg['error'], $msg['status']);
            } else {
                return responseMsgApi($msg['error'], $this->notFoundStatusCode);
            }
        }
        if (isset($msg['success'])) {
            if (isset($msg['status'])) {
                return responseMsgApi($msg['success'], $msg['status'], $this->successStatus);
            } else {
                return responseMsgApi($msg['success'], $this->okStatusCode, $this->successStatus);
            }
        }
    }

    public function destroyImage(Request $request)
    {
        $msg = $this->itemService->destoryImageFromApi($request, $request->img_id);
        if (isset($msg['error'])) {
            if (isset($msg['status'])) {
                return responseMsgApi($msg['error'], $msg['status']);
            } else {
                return responseMsgApi($msg['error'], $this->notFoundStatusCode);
            }
        }
        if (isset($msg['success'])) {
            if (isset($msg['status'])) {
                return responseMsgApi($msg['success'], $msg['status'], $this->successStatus);
            } else {
                return responseMsgApi($msg['success'], $this->okStatusCode, $this->successStatus);
            }
        }
    }

    public function destroyVideo(Request $request)
    {
        $msg = $this->itemService->destoryVideoFromApi($request, $request->img_id);
        if (isset($msg['error'])) {
            if (isset($msg['status'])) {
                return responseMsgApi($msg['error'], $msg['status']);
            } else {
                return responseMsgApi($msg['error'], $this->notFoundStatusCode);
            }
        }
        if (isset($msg['success'])) {
            if (isset($msg['status'])) {
                return responseMsgApi($msg['success'], $msg['status'], $this->successStatus);
            } else {
                return responseMsgApi($msg['success'], $this->okStatusCode, $this->successStatus);
            }
        }
    }

    public function getProductById(Request $request)
    {
        $response = $this->itemService->getByIdFromApi($request);
        if (isset($response['error'])) {
            if (isset($response['status'])) {
                return responseMsgApi($response['error'], $response['status']);
            } else {
                return responseMsgApi($response['error'], $this->badRequestStatusCode);
            }
        } else if (isset($response['success'])) {
            if (isset($response['status'])) {
                return responseMsgApi($response['success'], $response['status'], $this->successStatus);
            } else {
                return responseMsgApi($response['success'], $this->okStatusCode, $this->successStatus);
            }
        } else {

            $item = new ProductApiResource($response);
            return responseDataApi($item);
        }
    }


    public function customizeDetails(Request $request)
    {
        $coreKeysId = $request->customize_header_key;
        $offset = $request->offset;
        $limit = $request->limit;

        $msg =  __('core__api_record_not_found', [], $request->language_symbol);

        $response = custiomizeDetailsApi($coreKeysId, $limit, $offset, $msg);

        if (isset($response['error'])) {
            if (isset($response['status'])) {
                return responseMsgApi($response['error'], $response['status']);
            } else {
                return responseMsgApi($response['error'], $this->notFoundStatusCode);
            }
        } else {

            $data = CustomizeUiDetailApiResource::collection($response);

            $hasError = $this->itemService->noDataError($request->offset, $data);

            if ($hasError)
                return $hasError;
            else {
                return $data;
            }
        }
    }


    public function customizeHeadersForCustomizeDetails(Request $request)
    {
        $coreKeyIdsForUiType = [$this->dropDownUi, $this->radioUi, $this->multiSelectUi];
        $offset = $request->offset;
        $limit = $request->limit;

        $msg = __('core__api_record_not_found', [], $request->language_symbol);
        $response = uiTypesForCustomizeDetailsApi($coreKeyIdsForUiType, $limit, $offset, $msg);

        if (isset($response['error'])) {
            if (isset($response['status'])) {
                return responseMsgApi($response['error'], $response['status']);
            } else {
                return responseMsgApi($response['error'], $this->notFoundStatusCode);
            }
        } else {

            $data = CustomizeUiDetailApiResource::collection($response);

            $hasError = $this->itemService->noDataError($request->offset, $data);

            if ($hasError)
                return $hasError;
            else {
                return $data;
            }
        }
    }

    public function coverUpload(Request $request)
    {
        $response = $this->itemService->uploadCoverFromApi($request);

        if (isset($response['error'])) {
            if (isset($response['status'])) {
                return responseMsgApi($response['error'], $response['status']);
            } else {
                return responseMsgApi($response['error'], $this->badRequestStatusCode);
            }
        } else if (isset($response['success'])) {
            if (isset($response['status'])) {
                return responseMsgApi($response['success'], $response['status'], $this->successStatus);
            } else {
                return responseMsgApi($response['success'], $this->okStatusCode, $this->successStatus);
            }
        } else {

            $image = new CoreImageApiResource($response);
            return responseDataApi($image);
        }
    }

    public function iconUpload(Request $request)
    {
        $response = $this->itemService->uploadIconFromApi($request);
        if (isset($response['error'])) {
            if (isset($response['status'])) {
                return responseMsgApi($response['error'], $response['status']);
            } else {
                return responseMsgApi($response['error'], $this->badRequestStatusCode);
            }
        } else if (isset($response['success'])) {
            if (isset($response['status'])) {
                return responseMsgApi($response['success'], $response['status'], $this->successStatus);
            } else {
                return responseMsgApi($response['success'], $this->okStatusCode, $this->successStatus);
            }
        } else {

            $image = new CoreImageApiResource($response);
            return responseDataApi($image);
        }
    }

    public function videoUpload(Request $request)
    {
        $response = $this->itemService->uploadVideoFromApi($request);
        if (isset($response['error'])) {
            if (isset($response['status'])) {
                return responseMsgApi($response['error'], $response['status']);
            } else {
                return responseMsgApi($response['error'], $this->badRequestStatusCode);
            }
        } else if (isset($response['success'])) {
            if (isset($response['status'])) {
                return responseMsgApi($response['success'], $response['status'], $this->successStatus);
            } else {
                return responseMsgApi($response['success'], $this->okStatusCode, $this->successStatus);
            }
        } else {

            $image = new CoreImageApiResource($response);
            return responseDataApi($image);
        }
    }

    // public function appInfo(Request $request)
    // {
    //     // requests
    //     $language_code = $request->language_code;
    //     $country_code = $request->country_code;

    //     $obj = new \stdClass();

    //     // mobile language
    //     if (!empty($language_code) && !empty($country_code)){

    //         $mobile_language_conds['language_code'] = $language_code;
    //         $mobile_language_conds['country_code'] = $country_code;
    //         $mobile_language_conds['enable'] = 1;

    //         $obj->mobile_language = MobileLanguage::where($mobile_language_conds)->first();


    //         if (empty($obj->mobile_language)) {
    //             return response([
    //                 "status" => "error",
    //                 "message" =>  "Language not Found",
    //             ],404);
    //         }

    //     } else {
    //         // If lang_code and country_code will empty , will return default lang
    //         $obj->mobile_language = MobileLanguage::where("status",1)->first();
    //     }
    //     return response()->json($obj);
    // }

    public function reorderImages(Request $request)
    {
        $msg = $this->itemService->reorderImagesFromApi($request);
        if (isset($msg['error'])) {
            if (isset($msg['status'])) {
                return responseMsgApi($msg['error'], $msg['status']);
            } else {
                return responseMsgApi($msg['error'], $this->notFoundStatusCode);
            }
        }
        if (isset($msg['success'])) {
            if (isset($msg['status'])) {
                return responseMsgApi($msg['success'], $msg['status'], $this->successStatus);
            } else {
                return responseMsgApi($msg['success'], $this->okStatusCode, $this->successStatus);
            }
        }
    }

    public function search(Request $request)
    {
        $items = $this->itemService->searchFromApi($request);

        $data = ProductApiResource::collection($items);

        $hasError = $this->itemService->noDataError($request->offset, $data);

        if ($hasError)
            return $hasError;
        else {
            return $data;
        }
    }

    public function getRelatedTrending(Request $request)
    {
        $items = $this->itemService->getRelatedTrendingFromApi($request);

        $data = ProductApiResource::collection($items);

        $hasError = $this->itemService->noDataError($request->offset, $data);

        if ($hasError)
            return $hasError;
        else {
            return $data;
        }
    }

    public function soldOutFromItemDetail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'item_id' => 'required|exists:psx_items,id',
        ]);

        if ($request->language_symbol) {
            $this->translator->setLocale($request->language_symbol);
            $validator->setTranslator($this->translator);
        }

        $msg = implode("\n", Arr::flatten($validator->getMessageBag()->getMessages()));

        if ($validator->fails()) {
            return responseMsgApi($msg, $this->badRequestStatusCode);
        }

        $response = $this->itemService->soldOutFromItemDetailFromApi($request);

        if (isset($response['error'])) {
            if (isset($response['status'])) {
                return responseMsgApi($response['error'], $response['status']);
            } else {
                return responseMsgApi($response['error'], $this->badRequestStatusCode);
            }
        } else if (isset($response['success'])) {
            if (isset($response['status'])) {
                return responseMsgApi($response['success'], $response['status'], $this->successStatus);
            } else {
                return responseMsgApi($response['success'], $this->okStatusCode, $this->successStatus);
            }
        } else {

            $item = new ProductApiResource($response);
            return responseDataApi($item);
        }
    }

    public function allSearch(Request $request)
    {
        $data = $this->itemService->allSearchFromApi($request);
        $data = new HomePageSearchApiResource(collect($data));

        $hasError = $this->itemService->noDataError($request->offset, $data);

        if ($hasError)
            return $hasError;
        else {
            return $data;
        }
    }

    public function statusChangeFromApi(Request $request)
    {
        // dd("here");


        $validator = Validator::make($request->all(), [
            'item_id' => 'required|exists:psx_items,id',
        ]);

        if ($request->language_symbol) {
            $this->translator->setLocale($request->language_symbol);
            $validator->setTranslator($this->translator);
        }

        $msg = implode("\n", Arr::flatten($validator->getMessageBag()->getMessages()));

        if ($validator->fails()) {
            return responseMsgApi($msg, $this->badRequestStatusCode);
        }

        $response = $this->itemService->disableOrPendingOrRejectStatusChangeFromApi($request->item_id, $request);
        // $response = $this->itemService->soldOutFromItemDetailFromApi($request);

        if (isset($response['error'])) {
            if (isset($response['status'])) {
                return responseMsgApi($response['error'], $response['status']);
            } else {
                return responseMsgApi($response['error'], $this->badRequestStatusCode);
            }
        } else if (isset($response['success'])) {
            if (isset($response['status'])) {
                return responseMsgApi($response['success'], $response['status'], $this->successStatus);
            } else {
                return responseMsgApi($response['success'], $this->okStatusCode, $this->successStatus);
            }
        } else {

            $item = new ProductApiResource($response);
            return responseDataApi($item);
        }
    }
}
