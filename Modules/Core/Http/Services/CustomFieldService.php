<?php
namespace Modules\Core\Http\Services;

use App\Http\Services\PsService;
use Illuminate\Support\Facades\Auth;
use Modules\Core\Entities\CustomizeUiDetail;
use Modules\Core\Entities\CustomizeUi;

class CustomFieldService extends PsService
{
    public function custom_field_header_create($request, $module_name = false)
    {
        $custom_field_header = new CustomizeUi();
        $custom_field_header->name = $request->name;
        // $custom_field_header->placeholder = $request->placeholder;
        $custom_field_header->ui_type_id = $request->ui_type_id;
        $custom_field_header->mandatory = $request->mandatory;
        if($module_name){
            $custom_field_header->module_name = $module_name;
            if($request->ui_type_id == '1' || $request->ui_type_id == '3' || $request->ui_type_ids == '8'){
                $custom_field_header->enable = 0;
            }else{
                $custom_field_header->enable = 1;
            }
        }else{
            $custom_field_header->module_name = $request->module_name;
            $custom_field_header->enable = 1;
        }
        $custom_field_header->added_user_id = Auth::user()->id;
        $custom_field_header->save();

        return $custom_field_header;
    }

    public function custom_field_header_update($custom_field_header,$request, $module_name)
    {
        $custom_field_header->name = $request->name;
        $custom_field_header->ui_type_id = $request->ui_type_id;
        $custom_field_header->mandatory = $request->mandatory;
        $custom_field_header->module_name = $module_name;
        $custom_field_header->enable = 1;
        $custom_field_header->updated_user_id = Auth::user()->id;
        $custom_field_header->update();

        return $custom_field_header;
    }

    public function custom_field_detail_create($request)
    {
        $custom_field_detail = new CustomizeUiDetail();
        $custom_field_detail->attribute = $request->attribute;
        $custom_field_detail->custom_field_header_id = $request->custom_field_header_id;
        $custom_field_detail->added_user_id = Auth::user()->id;
        $custom_field_detail->save();

        return $custom_field_detail;
    }

    public function custom_field_detail_update($custom_field_detail,$request)
    {
        $custom_field_detail->attribute = $request->attribute;
        $custom_field_detail->custom_field_header_id = $request->custom_field_header_id;
        $custom_field_detail->updated_user_id = Auth::user()->id;
        $custom_field_detail->update();

        return $custom_field_detail;
    }

    public function enableOrDisable($custom_field_header){

        if ($custom_field_header->enable === 1){
            $custom_field_header->enable = 0;
            $custom_field_header->update();
            return $custom_field_header->name.' have been successfully disabled.';
        } else {
            if($custom_field_header->ui_type_id == '1' || $custom_field_header->ui_type_id == '3' || $custom_field_header == '8'){
                $custom_field_detail = CustomizeUiDetail::where('custom_field_header_id', $custom_field_header->id)->get();

                if(count($custom_field_detail) > 0){
                    $custom_field_header->enable = 1;
                    $custom_field_header->update();
                    return $custom_field_header->name.' have been successfully enabled.';
                }

                return $custom_field_header->name.' cannot be enabled. Please firstly add attributes.';
            }else{
                $custom_field_header->enable = 1;
                $custom_field_header->update();
                return $custom_field_header->name.' have been successfully enabled.';
            }
        }
    }

    public function optionalOrMandatory($custom_field_header){

        if ($custom_field_header->mandatory === 1){
            $custom_field_header->mandatory = 0;
            $custom_field_header->updated_user_id = Auth::user()->id;
            $custom_field_header->update();
            return $custom_field_header->name.' have been updated to Optional successfully';
        } else {
            if($custom_field_header->ui_type_id == '1' || $custom_field_header->ui_type_id == '3' || $custom_field_header == '8'){
                $custom_field_detail = CustomizeUiDetail::where('custom_field_header_id', $custom_field_header->id)->get();

                if(count($custom_field_detail) > 0){
                    $custom_field_header->mandatory = 1;
                    $custom_field_header->updated_user_id = Auth::user()->id;
                    $custom_field_header->update();
                    return $custom_field_header->name.' have been updated to Mandatory successfully';
                }

                return $custom_field_header->name.' cannot be changed to Mandatory. Please firstly add attributes.';
            }else{
                $custom_field_header->mandatory = 1;
                $custom_field_header->updated_user_id = Auth::user()->id;
                $custom_field_header->update();
                return $custom_field_header->name.' have been updated to Mandatory successfully';
            }
        }
    }


}
