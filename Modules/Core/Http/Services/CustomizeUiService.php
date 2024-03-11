<?php

namespace Modules\Core\Http\Services;

use App\Http\Services\PsService;
use Modules\Core\Entities\Table;
use Modules\Core\Entities\CoreKey;
use Modules\Core\Entities\Project;
use Modules\Core\Entities\CustomizeUi;
use Modules\Core\Entities\CoreKeyCounter;
use Modules\Core\Entities\LanguageString;
use Modules\Core\Http\Services\LanguageStringService;
use Modules\Core\Entities\CoreFieldFilterSetting;
use Modules\Core\Entities\CoreKeyType;

class CustomizeUiService extends PsService
{

    private $languageStringService, $customizeUiIsDeleteCol, $customizeUiNameCol, $customizeUiIdCol, $customizeUiTableIdCol, $customizeUiCoreKeysIdCol;

    public function __construct(LanguageStringService $languageStringService)
    {
        $this->languageStringService = $languageStringService;
        $this->customizeUiNameCol = CustomizeUi::name;
        $this->customizeUiIdCol = CustomizeUi::id;
        $this->customizeUiTableIdCol = CustomizeUi::tableId;
        $this->customizeUiCoreKeysIdCol = CustomizeUi::coreKeysId;
        $this->customizeUiIsDeleteCol = CustomizeUi::isDelete;

    }

    public function getCustomFields($relation = null, $tableId = null, $withNoPag = null, $tableIds = null, $coreKeysIds = null, $sort = null,$order = null,$search = null,$row = null, $isDelete = null, $ids = null){

        $customFields = CustomizeUi::when($relation, function ($q, $relation){
                            $q->with($relation);
                        })
                        ->when($tableIds, function ($q, $tableIds){
                            $q->whereIn($this->customizeUiTableIdCol, $tableIds);
                        })
                        ->when($coreKeysIds, function ($q, $coreKeysIds){
                            $q->whereIn($this->customizeUiCoreKeysIdCol, $coreKeysIds);
                        })
                        ->when($search,function($query,$search){
                            $query->where($this->customizeUiNameCol,'like','%'.$search.'%');
                        })
                        ->when($tableId, function ($q, $tableId){
                            $q->where($this->customizeUiTableIdCol, $tableId);
                        })
                        ->when($sort && $sort !== 'ui_type_id', function ($q) use($sort,$order){

                            $q->orderBy($sort, $order);
                        })
                        ->when($ids, function ($q, $ids){
                            $q->whereIn("id", $ids);
                        })
                        ->when($isDelete !== null, function ($q) use ($isDelete){
                            if ($isDelete !== null){
                                $q->where($this->customizeUiIsDeleteCol, $isDelete);
                            }
                        });
        if ($withNoPag){
            $customFields = $customFields->get();
        } else {
            $customFields = $customFields->paginate($row)->withQueryString();
        }

        return $customFields;
    }

    public function getCustomField($id = null, $tableId = null, $relation = null, $coreKeysId = null){
        $customField = CustomizeUi::when($id, function ($q, $id){
            $q->where($this->customizeUiIdCol, $id);
        })
            ->when($relation, function ($q, $relation){
                $q->with($relation);
            })
            ->when($tableId, function ($q, $tableId){
                $q->where($this->customizeUiTableIdCol, $tableId);
            })
            ->when($coreKeysId, function ($q, $coreKeysId){
                $q->where($this->customizeUiCoreKeysIdCol, $coreKeysId);
            })
            ->first();
        return $customField;
    }

    public function export(){
        $project = Project::first();
        $clientTables = Table::get();
        $clientCustomizeUies = CustomizeUi::where('is_core_field',0)->get();
        $clientCoreFields = CoreFieldFilterSetting::where('is_core_field',1)->get();
        $clientLanguageStrings = LanguageString::where(['language_id'=>1, 'is_from_builder'=>0])->get();
        $clientCoreKeys = CoreKey::get();
        // $clientCoreKeysCounters = CoreKeyCounter::get();

        $coreKeysTypes = CoreKeyType::get();


        $clientTablesArr = [];
        $clientCustomizeUiesArr = [];
        $clientLanguageStringsArr = [];
        $clientCoreKeysArr = [];

        foreach($clientTables as $clientTable){
            $clientTablesArr[] = [
                "id" => $clientTable->id,
                "project_id" => $project->id,
                "name" => $clientTable->name,
                "description" => $clientTable->description,
                "is_only_for_core_field" => $clientTable->is_only_for_core_field,
                "core_key_type_id" => $clientTable->core_key_type_id,
                "table_used_type_id" => $clientTable->table_used_type_id,
            ];
        }

        foreach($coreKeysTypes as $coreKeysType){
            $clientCoreKeysCountersArr[] = [
                "id" => $coreKeysType->id,
                "code" => $coreKeysType->client_code,
                "count" => 0,
                "project_id" => $project->id,
            ];
        }

        foreach($clientCustomizeUies as $clientCustomizeUi){
            if (!str_starts_with($clientCustomizeUi->core_keys_id, 'ps-')) {
                //if client custom field
                $counter = 0;
                foreach($coreKeysTypes as $coreKeysType){

                    if(str_starts_with($clientCustomizeUi->core_keys_id, $coreKeysType->client_code)){
                        $clientCoreKeysCountersArr[$counter]['count']++;

                        //add core keys
                        $clientCoreKeysArr[] = [
                            // "id" => $clientCoreKey->id,
                            "core_keys_id" => $clientCustomizeUi->core_keys_id,
                            "name" => $this->keyToValue($clientCustomizeUi->name),
                            "description" => $this->keyToValue($clientCustomizeUi->name) . ' desc',
                            "project_id" => $project->id,
                        ];

                        break;
                    }
                    $counter++;
                }
            }


            $clientCustomizeUiesArr[] = [
                "id" => $clientCustomizeUi->id,
                "table_id" => $clientCustomizeUi->table_id,
                "project_id" => $clientCustomizeUi->project_id,
                "project_name" => $clientCustomizeUi->project_name,
                "name" => $this->keyToValue($clientCustomizeUi->name),
                "placeholder" => $this->keyToValue($clientCustomizeUi->placeholder),
                "ui_type_id" => $clientCustomizeUi->ui_type_id,
                "core_keys_id" => $clientCustomizeUi->core_keys_id,
                "is_core_field" => $clientCustomizeUi->is_core_field,
                "is_delete" => $clientCustomizeUi->is_delete,
                "data_type" => $clientCustomizeUi->data_type,
                "module_name" => $clientCustomizeUi->module_name,
                "base_module_name" => $clientCustomizeUi->base_module_name,
                "enable" => $clientCustomizeUi->enable,
                "mandatory" => $clientCustomizeUi->mandatory,
                "is_show_sorting" => $clientCustomizeUi->is_show_sorting,
                "is_show_in_filter" => $clientCustomizeUi->is_show_in_filter,
                "ordering" => $clientCustomizeUi->ordering,
                "is_include_in_hideshow" => $clientCustomizeUi->is_include_in_hideshow,
                "is_show" => $clientCustomizeUi->is_show,
                "permission_for_enable_disable" => $clientCustomizeUi->permission_for_enable_disable,
                "permission_for_delete" => $clientCustomizeUi->permission_for_delete,
                "permission_for_mandatory" => $clientCustomizeUi->permission_for_mandatory,
            ];
        }

        foreach($clientCoreFields as $clientCoreField){
            $clientCustomizeUiesArr[] = [
                "id" => $clientCoreField->id,
                "table_id" => $clientCoreField->table_id,
                "project_id" => $clientCoreField->project_id,
                "project_name" => $clientCoreField->project_name,
                "name" => $this->keyToValue($clientCoreField->label_name),
                "placeholder" => $this->keyToValue($clientCoreField->placeholder),
                "ui_type_id" => null,
                "core_keys_id" => $clientCoreField->field_name,
                "is_core_field" => $clientCoreField->is_core_field,
                "is_delete" => $clientCoreField->is_delete,
                "data_type" => $clientCoreField->data_type,
                "module_name" => $clientCoreField->module_name,
                "base_module_name" => $clientCoreField->base_module_name,
                "enable" => $clientCoreField->enable,
                "mandatory" => $clientCoreField->mandatory,
                "is_show_sorting" => $clientCoreField->is_show_sorting,
                "is_show_in_filter" => $clientCoreField->is_show_in_filter,
                "ordering" => $clientCoreField->ordering,
                "is_include_in_hideshow" => $clientCoreField->is_include_in_hideshow,
                "is_show" => $clientCoreField->is_show,
                "permission_for_enable_disable" => $clientCoreField->permission_for_enable_disable,
                "permission_for_delete" => $clientCoreField->permission_for_delete,
                "permission_for_mandatory" => $clientCoreField->permission_for_mandatory,
            ];
        }

        foreach($clientLanguageStrings as $clientLanguageString){
            $clientLanguageStringsArr[] = [
                "id" => $clientLanguageString->id,
                "language_id" => $clientLanguageString->language_id,
                "key" => $clientLanguageString->key,
                "value" => $clientLanguageString->value,
                "project_id" => $project->id,
            ];
        }

        $clientProject['client_tables'] = $clientTablesArr;
        $clientProject['client_customize_ui'] = $clientCustomizeUiesArr;
        $clientProject['client_language_strings'] = $clientLanguageStringsArr;
        $clientProject['client_core_keys'] = $clientCoreKeysArr;
        $clientProject['client_core_keys_counter'] = $clientCoreKeysCountersArr;

        return $clientProject;
    }

    public function keyToValue($key)
    {
        $conds['searchterm'] = $key;
        $languageStirngs = $this->languageStringService->getLanguageStrings(1, null, null, $conds)->toArray();
        if(!$languageStirngs || count($languageStirngs) == 0){
            return $key;
        }
        return $languageStirngs[0]['value'];
    }

}
