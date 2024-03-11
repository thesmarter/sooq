<?php

namespace Modules\Core\Http\Services;

use App\Http\Services\PsService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Modules\Core\Constants\Constants;
use Modules\Core\Entities\VendorApplication;

class VendorApplicationService extends PsService
{
    protected $userService, $vendorService;

    public function __construct(UserService $userService, VendorService $vendorService)
    {
        $this->userService = $userService;
        $this->vendorService = $vendorService;
    }

    public function store($request)
    {
        DB::beginTransaction();
        try{
            $vendorApplication = new VendorApplication();
            $vendorApplication->vendor_id = $request->vendor_id;
            $vendorApplication->user_id = $request->login_user_id;
            $vendorApplication->document = $this->storeDocument($request);
            $vendorApplication->cover_letter = $request->cover_letter;
            $vendorApplication->added_user_id = $request->login_user_id;
            $vendorApplication->save();

            DB::commit();
            return $vendorApplication;
        }catch(\Throwable $e){
            DB::rollBack();
            return ['error' => $e->getMessage()];
        }
    }

    public function update($request)
    {
        $vendorApplication = $this->getVendorApplication($request->id);
        DB::beginTransaction();
        try{
            //if file exist delete old document
            if(isset($request->document) || !empty($request->document)){
                $oldFile = public_path() . '/document/' . $vendorApplication->document;
                if(is_file($oldFile)) {
                    unlink($oldFile);
                }
                $vendorApplication->document = $this->storeDocument($request);
            }


            $vendorApplication->vendor_id = $request->vendor_id;
            $vendorApplication->user_id = $request->login_user_id;
            $vendorApplication->cover_letter = $request->cover_letter;
            $vendorApplication->updated_user_id = Auth::user()->id;
            $vendorApplication->save();

            DB::commit();
            return $vendorApplication;
        }catch(\Throwable $e){
            DB::rollBack();
            return ['error' => $e->getMessage()];
        }
    }

    public function submitApplication($request)
    {
        $vendor = $this->vendorService->storeFromApi($request);

        if(!isset($vendor['error'])){
            $request['vendor_id'] = $vendor->id;
            $vendorApplication = $this->store($request);

            if(isset($vendorApplication['error'])){
                $this->vendorService->destroy($vendor->id);
            }
            return $vendor;
        }
        return $vendor;

    }

    public function updateApplication($request)
    {
        $vendor = $this->vendorService->updateFromApi($request);

        if(!isset($vendor['error'])){
            $vendorApplication = $this->update($request);

            return $vendor;
        }
        return $vendor;
    }

    public function storeDocument($request)
    {
        if($request->file('document')){
            $document = $request->file('document');
            $fileName = uniqid() . "_." . $document->getClientOriginalExtension();
            $document->move(public_path() . '/document', $fileName);

            return $fileName;
        }
    }

    public function getVendorApplication($id = null, $vendorId = null)
    {
        $appliction = VendorApplication::when($id !== null, function($query) use($id){
            $query->where(VendorApplication::id, $id);
        })->when($vendorId !== null, function($query) use($vendorId){
            $query->where(VendorApplication::vendorId, $vendorId);
        })->first();

        return $appliction;
    }

    public function destroy($id)
    {
        $appliction = $this->getVendorApplication($id, null);

        $appliction->delete();

        $dataArr = [
            'status' => Constants::success
        ];

        return $dataArr;
    }
}
