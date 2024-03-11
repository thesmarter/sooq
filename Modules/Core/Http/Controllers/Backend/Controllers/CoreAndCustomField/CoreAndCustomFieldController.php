<?php

namespace Modules\Core\Http\Controllers\Backend\Controllers\CoreAndCustomField;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Core\Constants\Constants;
use Modules\Core\Http\Services\CoreAndCustomFieldService;
use App\Config\ps_constant;
use Modules\Core\Http\Services\LanguageStringService;


class CoreAndCustomFieldController extends Controller
{

    const parentPath = "core_and_custom_field/";
    const indexPath = self::parentPath."Index";
    const createPath = self::parentPath."Create";
    const editPath = self::parentPath."Edit";
    const indexRoute = "tables.fields.index";
    const createRoute = "tables.fields.create";
    const editRoute = "tables.fields.edit";

    protected $languageStringService, $coreAndCustomFieldService, $successFlag, $dangerFlag, $csvFile, $warningFlag;

    public function __construct(LanguageStringService $languageStringService, CoreAndCustomFieldService $coreAndCustomFieldService)
    {
        $this->coreAndCustomFieldService = $coreAndCustomFieldService;
        $this->languageStringService = $languageStringService;
        $this->successFlag = Constants::success;
        $this->dangerFlag = Constants::danger;
        $this->warningFlag = Constants::warning;
        $this->csvFile = Constants::csvFile;
    }



    public function index(Request $request)
    {
        if (!permissionControl(Constants::tableFieldModule,ps_constant::readPermission)){
            return redirect()->route('admin.index');
        }

        $dataArr = $this->coreAndCustomFieldService->index($request);
        return renderView(self::indexPath, $dataArr);

    }

    public function screenDisplayUiStore(Request $request) {

        $parameter = ['page' => $request->current_page];

        $this->coreAndCustomFieldService->makeColumnHideShown($request);

        // $msg = 'ScreenDisplay UiSetting is updated.';
        $msg = __('core__be_screen_display_ui_updated');
        // return redirectView(self::indexRoute, $msg,null,$parameter);
        return redirect()->back();
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create($table)
    {
        $dataArr = $this->coreAndCustomFieldService->create($table);

        return renderView(self::createPath, $dataArr);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request, $table)
    {
        $generatedData = $this->coreAndCustomFieldService->generateCoreKeysId( $table, $request->form['ui_type_id']);

        if($generatedData['flag'] != 'success'){
            return redirect()->back()->with('status', $generatedData);
        }

        $form = new \stdClass();
        foreach ($request->form as $key => $value)
        {
            $form->$key = $value;
        }
        
        $dataArr = $this->coreAndCustomFieldService->store($form, $table, $generatedData);

        if($dataArr['flag'] != 'success'){
            return redirect()->back()->with('status', $dataArr);
        }

        if(isset($request->nameForm['values'])){

            $nameForm = new \stdClass();
            foreach ($request->nameForm as $key => $value)
            {
                $nameForm->$key = $value;
            }
            $namedataArr = $this->languageStringService->storeLanguageStrings($nameForm, $generatedData['name_key']);
            if($namedataArr['flag'] != 'success'){
                return redirect()->back()->with('status', $namedataArr);
            }
        }
       
        if(isset($request->placeholderForm['values'])){
            $placeholderForm = new \stdClass();
            foreach ($request->placeholderForm as $key => $value)
            {
                $placeholderForm->$key = $value;
            }
            $placeholderdataArr = $this->languageStringService->storeLanguageStrings($placeholderForm, $generatedData['placeholder_key']);
            if($placeholderdataArr['flag'] != 'success'){
                return redirect()->back()->with('status', $placeholderdataArr);
            }
        }

        return redirect()->back()->with('status', $dataArr);
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        return view('core::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($table, $id)
    {
        $dataArr = $this->coreAndCustomFieldService->edit($table,$id);

        return renderView(self::editPath, $dataArr);
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $table, $id)
    {
        $form = new \stdClass();
        foreach ($request->form as $key => $value)
        {
            $form->$key = $value;
        }
        
        $dataArr = $this->coreAndCustomFieldService->update($form, $table, $id);

        if($dataArr['flag'] != 'success'){
            return redirect()->back()->with('status', $dataArr);
        }

        if(isset($request->nameForm['key']) && $request->nameForm['key'] != '' && isset($request->nameForm['values'])){

            $nameForm = new \stdClass();
            foreach ($request->nameForm as $key => $value)
            {
                $nameForm->$key = $value;
            }
            $namedataArr = $this->languageStringService->updateLanguageStrings($nameForm);
            if($namedataArr['flag'] != 'success'){
                return redirect()->back()->with('status', $namedataArr);
            }
        }
       
        if(isset($request->placeholderForm['key']) && $request->placeholderForm['key'] != '' && isset($request->placeholderForm['values'])){
            $placeholderForm = new \stdClass();
            foreach ($request->placeholderForm as $key => $value)
            {
                $placeholderForm->$key = $value;
            }
            $placeholderdataArr = $this->languageStringService->updateLanguageStrings($placeholderForm);
            if($placeholderdataArr['flag'] != 'success'){
                return redirect()->back()->with('status', $placeholderdataArr);
            }
        }

        return redirect()->back()->with('status', $dataArr);
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function deleteField(Request $request)
    {
        $dataArr = $this->coreAndCustomFieldService->destroy($request->id);

        return redirect()->back();
    }

    public function enableChange(Request $request){
        $this->coreAndCustomFieldService->makePublishOrUnpublish($request);

        return redirect()->back();
    }

    public function handleIsShowSorting(Request $request)
    {
        $this->coreAndCustomFieldService->handleIsShowSorting($request);

        return redirect()->back();
    }

    public function mandatoryChange(Request $request){

        $this->coreAndCustomFieldService->makeMandatoryOrOptional($request);

        return redirect()->back();
    }

    public function eyeStatusChange(Request $request){
        $this->coreAndCustomFieldService->isIncludedAndIsShow($request);

        return redirect()->back();
    }

    public function handleOrdering(Request $request){
        $this->coreAndCustomFieldService->handleOrdering($request);

        return redirect()->back();
    }
}
