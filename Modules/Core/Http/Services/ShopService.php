<?php
namespace Modules\Core\Http\Services;

use App\Http\Services\PsService;
use Modules\Core\Entities\Shop;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\Facades\Image;
use Modules\Core\Entities\ShopCustomField;
use Modules\Core\Entities\CustomizeUi;
use Modules\Core\Http\Services\ImageService;

class ShopService extends PsService
{
    protected $shopStatusCol, $imageService;

    protected $upload_path = 'public/uploads/';
    protected $thumb1x_path = 'public/thumbnail/';
    protected $thumb2x_path = 'public/thumbnail2x/';
    protected $thumb3x_path = 'public/thumbnail3x/';

    protected $storage_upload_path = 'storage/uploads/';
    protected $storage_thumb1x_path = 'storage/thumbnail/';
    protected $storage_thumb2x_path = 'storage/thumbnail2x/';
    protected $storage_thumb3x_path = 'storage/thumbnail3x/';

    public function __construct(ImageService $imageService)
    {
        $this->imageService = $imageService;
        $this->shopStatusCol = Shop::status;
    }

    public function create($request)
    {
        $shop = new Shop();
        $shop->name = $request->name;
        $shop->description = $request->description;
        $shop->email = $request->email;
        $shop->lat = $request->lat;
        $shop->lng = $request->lng;
        $shop->cod_email = $request->cod_email;
        $shop->cod_enable = $request->cod_enable;
        $shop->payment_status_id = $request->payment_status_id;
        $shop->is_featured = $request->is_featured;
        $shop->overall_tax_label = $request->overall_tax_label;
        $shop->overall_tax_value = $request->overall_tax_value;
        $shop->shipping_tax_label = $request->shipping_tax_label;
        $shop->shipping_tax_value = $request->shipping_tax_value;
        $shop->whatsapp_no = $request->whatsapp_no;
        $shop->refund_policy = $request->refund_policy;
        $shop->terms = $request->terms;
        $shop->facebook = $request->facebook;
        $shop->messenger = $request->messenger;
        $shop->instagram = $request->instagram;
        $shop->youtube = $request->youtube;
        $shop->phone_no = $request->phone_no;
        $shop->address = $request->address;
        $shop->checkout_with_email = $request->checkout == 'checkout_with_email' ? true : false;
        $shop->multi_page_checkout = $request->multi_page_checkout;
        $shop->checkout_with_whatsapp = $request->checkout == 'checkout_with_whatsapp' ? true : false;
        $shop->price_level = $request->price_level;
        $shop->added_user_id = Auth::user()->id;
        $shop->save();

        // save in shop_custom_fields table
        if(!empty($request->custom_fields)){
            foreach ($request->custom_fields as $key => $value){
                if ($value !== null){
                    $shopCustomField = new shopCustomField();
                    if(is_file($value)){
                        if (str_contains($value->getMimeType(),'image')){

                            // change file to new name
                            $file = uniqid()."_".$file->getClientOriginalName();

                            //save uploads
                            $img = Image::make($value);
                            $img->save($this->storage_upload_path.$file,30);

                            //save thumb 1x
                            $thumbnail1x = Image::make($value);
                            $thumbnail1x->resize(200, null, function ($constraint) {
                                $constraint->aspectRatio();
                                $constraint->upsize();
                            });
                            $thumbnail1x->save($this->storage_thumb1x_path.$file);

                            //save thumb 2x
                            $thumbnail2x = Image::make($value);
                            $thumbnail2x->resize(400, null, function ($constraint) {
                                $constraint->aspectRatio();
                                $constraint->upsize();
                            });
                            $thumbnail2x->save($this->storage_thumb2x_path.$file);

                            //save 3x
                            $thumbnail3x = Image::make($value);
                            $thumbnail3x->resize(600, null, function ($constraint) {
                                $constraint->aspectRatio();
                                $constraint->upsize();
                            });
                            $thumbnail3x->save($this->storage_thumb3x_path.$file);

                        } else if(str_contains($value->getMimeType(),'video')){
                            return 'This is video';
                        } else {
                            return 'other';
                        }
                    }

                    $shopCustomField->shop_id = $shop->id;
                    $shopCustomField->custom_field_header_id = $key;
                    if ($value === false) {
                        $shopCustomField->data = 0;
                    }
                    if(is_file($value)) {
                        if (str_contains($value->getMimeType(), 'image')) {
                            $shopCustomField->data = $file;
                        }
                    }
                    if(!is_file($value) && $value !== false) {
                        $shopCustomField->data = $value;
                    }

                    $custom_field_headers = CustomizeUi::all();
                    foreach ($custom_field_headers as $custom_field_header){
                        if($key === $custom_field_header->id){
                            $shopCustomField->ui_type_id = $custom_field_header->ui_type_id;
                        }
                    }
                    $shopCustomField->added_user_id = Auth::user()->id;
                    $shopCustomField->save();
                }
            }
        }

        return $shop;
    }

    public function update($shop,$request)
    {
        $shop->name = $request->name;
        $shop->description = $request->description;
        $shop->email = $request->email;
        $shop->lat = $request->lat;
        $shop->lng = $request->lng;
        $shop->cod_email = $request->cod_email;
        $shop->cod_enable = $request->cod_enable;
        $shop->payment_status_id = $request->payment_status_id;
        $shop->is_featured = $request->is_featured;
        $shop->overall_tax_label = $request->overall_tax_label;
        $shop->overall_tax_value = $request->overall_tax_value;
        $shop->shipping_tax_label = $request->shipping_tax_label;
        $shop->shipping_tax_value = $request->shipping_tax_value;
        $shop->whatsapp_no = $request->whatsapp_no;
        $shop->refund_policy = $request->refund_policy;
        $shop->terms = $request->terms;
        $shop->facebook = $request->facebook;
        $shop->messenger = $request->messenger;
        $shop->instagram = $request->instagram;
        $shop->youtube = $request->youtube;
        $shop->phone_no = $request->phone_no;
        $shop->address = $request->address;
        $shop->checkout_with_email = $request->checkout == 'checkout_with_email' ? true : false;
        $shop->multi_page_checkout = $request->multi_page_checkout;
        $shop->checkout_with_whatsapp = $request->checkout == 'checkout_with_whatsapp' ? true : false;
        $shop->price_level = $request->price_level;
        $shop->updated_user_id = Auth::user()->id;
        $shop->update();

        // update data in item_custom_fields table
        if(!empty($request->custom_fields)){
            foreach ($request->custom_fields as $key => $value){
                if ($value !== null) {
                    $shopCustomFields = ShopCustomField::where('shop_id', $shop->id)->get();
                    $shop_custom_field_ids = $shopCustomFields->pluck('id');
                    $isUpdate = in_array($key, (array)$shop_custom_field_ids);

                    if($isUpdate){
                        foreach ($shopCustomFields as $shopCustomField){

                            if ($key === $shopCustomField->custom_header_id){

                                if(is_file($value)){

                                    if (str_contains($value->getMimeType(),'image')){

                                        // delete existing image
                                        $this->imageService->deleteImage($shopCustomField->data);

                                        // change file to new name
                                        $file = uniqid()."_".$file->getClientOriginalName();

                                        //save uploads
                                        $img = Image::make($value);
                                        $img->save($this->storage_upload_path.$file,30);

                                        //save thumb 1x
                                        $thumbnail1x = Image::make($value);
                                        $thumbnail1x->resize(200, null, function ($constraint) {
                                            $constraint->aspectRatio();
                                            $constraint->upsize();
                                        });
                                        $thumbnail1x->save($this->storage_thumb1x_path.$file);

                                        //save thumb 2x
                                        $thumbnail2x = Image::make($value);
                                        $thumbnail2x->resize(400, null, function ($constraint) {
                                            $constraint->aspectRatio();
                                            $constraint->upsize();
                                        });
                                        $thumbnail2x->save($this->storage_thumb2x_path.$file);

                                        //save thumb 3x
                                        $thumbnail3x = Image::make($value);
                                        $thumbnail3x->resize(600, null, function ($constraint) {
                                            $constraint->aspectRatio();
                                            $constraint->upsize();
                                        });
                                        $thumbnail3x->save($this->storage_thumb3x_path.$file);
                                    } else if(str_contains($value->getMimeType(),'video')){
                                        return 'This is video';
                                    } else {
                                        return 'other';
                                    }
                                }

                                $shopCustomField->shop_id = $shop->id;
                                $shopCustomField->custom_field_header_id = $key;
                                if ($value === false) {
                                    $shopCustomField->data = 0;
                                }
                                if(is_file($value)) {
                                    if (str_contains($value->getMimeType(), 'image')) {
                                        $shopCustomField->data = $file;
                                    }
                                }
                                if(!is_file($value) && $value !== false) {
                                    $shopCustomField->data = $value;
                                }

                                $custom_field_headers = CustomizeUi::all();
                                foreach ($custom_field_headers as $custom_field_header){
                                    if($key === $custom_field_header->id){
                                        $shopCustomField->ui_type_id = $custom_field_header->ui_type_id;
                                    }
                                }

                                $shopCustomField->updated_user_id = Auth::user()->id;
                                $shopCustomField->update();
                            }
                        }
                    }else{
                        $shopCustomField = new shopCustomField();
                        if(is_file($value)){
                            if (str_contains($value->getMimeType(),'image')){

                                // change file to new name
                                $file = uniqid()."_".$file->getClientOriginalName();

                                //save uploads
                                $img = Image::make($value);
                                $img->save($this->storage_upload_path.$file,30);

                                //save thumb 1x
                                $thumbnail1x = Image::make($value);
                                $thumbnail1x->resize(200, null, function ($constraint) {
                                    $constraint->aspectRatio();
                                    $constraint->upsize();
                                });
                                $thumbnail1x->save($this->storage_thumb1x_path.$file);

                                //save thumb 2x
                                $thumbnail2x = Image::make($value);
                                $thumbnail2x->resize(400, null, function ($constraint) {
                                    $constraint->aspectRatio();
                                    $constraint->upsize();
                                });
                                $thumbnail2x->save($this->storage_thumb2x_path.$file);

                                //save 3x
                                $thumbnail3x = Image::make($value);
                                $thumbnail3x->resize(600, null, function ($constraint) {
                                    $constraint->aspectRatio();
                                    $constraint->upsize();
                                });
                                $thumbnail3x->save($this->storage_thumb3x_path.$file);

                            } else if(str_contains($value->getMimeType(),'video')){
                                return 'This is video';
                            } else {
                                return 'other';
                            }
                        }

                        $shopCustomField->shop_id = $shop->id;
                        $shopCustomField->custom_field_header_id = $key;
                        if ($value === false) {
                            $shopCustomField->data = 0;
                        }
                        if(is_file($value)) {
                            if (str_contains($value->getMimeType(), 'image')) {
                                $shopCustomField->data = $file;
                            }
                        }
                        if(!is_file($value) && $value !== false) {
                            $shopCustomField->data = $value;
                        }

                        $custom_field_headers = CustomizeUi::all();
                        foreach ($custom_field_headers as $custom_field_header){
                            if($key === $custom_field_header->id){
                                $shopCustomField->ui_type_id = $custom_field_header->ui_type_id;
                            }
                        }
                        $shopCustomField->added_user_id = Auth::user()->id;
                        $shopCustomField->save();
                    }

                }
            }
        }

        return $shop;
    }

    public function getShops($relation = null, $status = null){
        $shops = Shop::when($relation, function ($q, $relation){
                            $q->with($relation);
                        })
                        ->when($status, function ($q, $status){
                            $q->where($this->shopStatusCol, $status);
                        })->get();
        return $shops;
    }

    public function getShop($id = null, $status = null){
        $shop = Shop::when($id, function ($q, $id){
                            $q->where('id', $id);
                        })
                    ->when($status, function ($q, $status){
                        $q->where($this->shopStatusCol, $status);
                    })->first();
        return $shop;
    }

}
