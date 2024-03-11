<?php

namespace Modules\Core\Imports;

use Illuminate\Support\Collection;
use Modules\Core\Entities\MobileLanguage;
use Illuminate\Support\Facades\Auth;
use Modules\Core\Entities\MobileLanguageString;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Modules\Core\Constants\Constants;

class MobileLanguageStringImport implements ToCollection, WithHeadingRow, WithValidation, SkipsOnFailure
{
    use Importable, SkipsFailures;

    private $language_id;

    public function __construct(MobileLanguage $language)
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
        foreach ($rows as $row)
        {
            $langStr = MobileLanguageString::where('mobile_language_id',$this->language_id)->where('key',$row['key'])->first();

            if($langStr){
                // update language string
                $langStr->value = $row['value'];
                $langStr->update();
            }else{
                // save language string
                $language_string = new MobileLanguageString();
                $language_string->key = $row['key'];
                $language_string->value = $row['value'];
                $language_string->mobile_language_id = $this->language_id;
                $language_string->added_user_id = Auth::user()->id;
                $language_string->save();
            }

        }
        //get Header row
        // $header = $rows[0];
        // $length = count($rows);
        // for($j = 1; $j < $length; $j++){
        //     // en, ar, fr, es, pt, hi, id, ja, ms, ru, tr, de, it, ko, th, zh
            
        //     $len = count($rows[$j]);
        //     $key = $rows[$j][0];
            
        //     if(!empty($key)){
        //         for($i = 1; $i < $len; $i++){
        //             $value = $rows[$j][$i];
        //             if($value){
        //                 $symbol = explode("_", $header[$i])[0]; // get en from en_value

        //                 //check language exist or not from languages table
        //                 $selectedLanguage = Language::where('symbol', $symbol)->first();
        //                 if($selectedLanguage){
        //                     $lang['key'] = $key;
        //                     $lang['mobile_language_id'] = $selectedLanguage->id;
        //                     $langExist = MobileLanguageString::where($lang)->count();
        //                     if($langExist == 0){
        //                         // if language exit, save language string
        //                         $language_string = new MobileLanguageString();
        //                         $language_string->key = $key;
        //                         $language_string->value = $value;
        //                         $language_string->mobile_language_id = $selectedLanguage->id;
        //                         $language_string->added_user_id = Auth::user()->id;
        //                         $language_string->save();
        //                     }                            
        //                 }
        //             }
        //         }
        //     }
        // }
    }

    /**
     * Validation
     */
    public function rules(): array
    {
        return [
            // 'key' => 'required',
            // "value" => "required",
        ];
    }
}
