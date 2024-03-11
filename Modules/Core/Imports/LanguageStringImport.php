<?php

namespace Modules\Core\Imports;

use Illuminate\Support\Collection;
use Modules\Core\Entities\Language;
use Illuminate\Support\Facades\Auth;
use Modules\Core\Entities\LanguageString;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Modules\Core\Constants\Constants;

class LanguageStringImport implements ToCollection, WithHeadingRow, WithValidation, SkipsOnFailure
{
    use Importable, SkipsFailures;

    private $language_id;

    public function __construct(Language $language)
    {
        $this->language_id = $language->id;
    }


    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function collection(Collection $rows)
    {
        $fileKeys = [];
        $existKeys = [];

        foreach ($rows as $row){
            array_push($fileKeys,$row['key']);
        }

        //filter exist keys
        $langStrs = LanguageString::where('language_id',$this->language_id)->whereIn('key',$fileKeys)->get();
        
        //for update
        foreach ($langStrs as $langStr){
            array_push($existKeys,$langStr->key);

            foreach ($rows as $row){
                if($row['key'] == $langStr->key){
                    $langStr->value = $row['value'];

                    $langStr->update();
                }
            }
        }

        //for new keys
        foreach ($rows as $row){
            //if not in exist keys
            if (!in_array($row['key'], $existKeys)) {
                $language_string = new LanguageString();
                $language_string->key = $row['key'];
                $language_string->value = $row['value'];
                $language_string->language_id = $this->language_id;
                $language_string->added_user_id = Auth::user()->id;
                $language_string->save();
            }
        }
        // foreach ($rows as $row)
        // {
        //     $langStr = LanguageString::where('language_id',$this->language_id)->where('key',$row['key'])->first();

        //     if($langStr){
        //         // update language string
        //         $langStr->value = $row['value'];
        //         $langStr->update();
        //     }else{
        //         // save language string
        //         $language_string = new LanguageString();
        //         $language_string->key = $row['key'];
        //         $language_string->value = $row['value'];
        //         $language_string->language_id = $this->language_id;
        //         $language_string->added_user_id = Auth::user()->id;
        //         $language_string->save();
        //     }

        // }
    }

    /**
     * Validation
     */
    public function rules(): array
    {
        return [
            'key' => 'required',
            "value" => "required",
        ];
    }
}