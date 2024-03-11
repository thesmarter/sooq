<?php

namespace Modules\ContactUsMessage\Http\Controllers\Backend\Controllers;

use Illuminate\Routing\Controller;
use Modules\ContactUsMessage\Http\Services\ContactService;
use Illuminate\Http\Request;

class ContactUsMessageController extends Controller
{
    const parentPath = "contact_us_message/";
    const indexPath = self::parentPath . "Index";
    const createPath = self::parentPath . "Create";
    const editPath = self::parentPath . "Edit";
    const indexRoute = "contact_us_message.index";
    const createRoute = "contact_us_message.create";
    const editRoute = "contact_us_message.edit";

    protected $contactService;

    public function __construct(ContactService $contactService)
    {
        $this->contactService = $contactService;
    }

    public function index()
    {
        $dataArr = $this->contactService->index();

        $checkPermission = $dataArr["checkPermission"];
        if (!empty($checkPermission)){
            return $checkPermission;
        }

        return renderView(self::indexPath, $dataArr);
    }

    public function destroy($id)
    {
        $dataArr = $this->contactService->destroy($id);

        $checkPermission = $dataArr["checkPermission"];
        if (!empty($checkPermission)){
            return $checkPermission;
        }

        return redirect()->back();
    }

    public function markAllAsRead()
    {
        $dataArr = $this->contactService->markAllAsRead();
        return redirect()->back();
    }

    public function getContactFormTitle(){
        $dataArr =  $this->contactService->getContactsFormNav();
        return $dataArr;
    }

    public function show($id)
    {
        $dataArr = $this->contactService->edit($id);

        $checkPermission = $dataArr["checkPermission"];
        if (!empty($checkPermission)){
            return $checkPermission;
        }
        
        return renderView(self::editPath, $dataArr);
    }

    public function edit($id)
    {
        $dataArr = $this->contactService->edit($id);

        $checkPermission = $dataArr["checkPermission"];
        if (!empty($checkPermission)){
            return $checkPermission;
        }

        return renderView(self::editPath, $dataArr);
    }

    public function multipleDelete(Request $request)
    {
        $dataArr = $this->contactService->multiDelete($request->ids);
        return redirect()->back();
    }

    public function csvExport()
    {
        return $this->contactService->csvExport();
    }
}
