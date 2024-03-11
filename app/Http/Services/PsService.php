<?php

namespace App\Http\Services;
use Modules\Core\Entities\CoreImage;
use Modules\Core\Constants\Constants;
use Modules\Core\Entities\FrontendSetting;

class PsService extends PsCoreService
{

    protected static $meta = [];

    public static function addMeta($name, $content)
    {
        static::$meta[$name] = $content;
    }

    public static function render()
    {
        $folder_path_thumbnail1x= 'storage/' .Constants::folderPath. '/thumbnail';
        $frontendService = FrontendSetting::first();
        $imageService = CoreImage::where('img_type', 'backend-meta-image')->first();
        $imagePath = '';
        if($imageService){
            $imagePath = $imageService->img_path;
        }

        $default_meta_image = asset($folder_path_thumbnail1x) .'/'. $imagePath;

        $default_meta_description=$frontendService->frontend_meta_description ?? __('default_meta_description');
        $html = '';
        if(empty(static::$meta)){
            $default_meta_title=$frontendService->frontend_meta_title ?? __('default_meta_title');
            static::$meta['title']= $default_meta_title;
            static::$meta['description'] = $default_meta_description;
            static::$meta['image'] = $default_meta_image;
        }
        // dd(static::$meta);
        foreach (static::$meta as $name => $content) {
            if($name == 'title'){
                $html .= '<title>'.$content.'</title>';
            }
            if($name == 'image'){
                if(static::$meta['image'] == null || static::$meta['image'] == "" || static::$meta['image'] == $default_meta_image){
                    $content = $default_meta_image;
                }else{
                    $content = asset($folder_path_thumbnail1x) .'/'. $content;
                }
            }
            if($name == 'description'){
                if(static::$meta['description'] == null){
                    $content = $default_meta_description;
                }
            }
            $html .= '<meta name="'.$name.'" content="'.$content.'" />'.PHP_EOL;
            // $html .= '<meta property="og:'.$name.'" content="'.$content.'"  inertia="o-'.$name.'">'.PHP_EOL;
            // $html .= '<meta itemprop="g-'.$name.'" content="'.$content.'" inertia="o-'.$name.'">'.PHP_EOL;
            // $html .= '<meta inertia="g-:'.$name.'" itemprop="'.$name.'" content="'.$content.'" >'.PHP_EOL;
            // $html .= '<meta inertia="t-:'.$name.'" name="twitter:'.$name.'" content="'.$content.'" >'.PHP_EOL;
            $html .= '<meta property="og:'.$name.'" content="'.$content.'" />'.PHP_EOL;
            $html .= '<meta property="twitter:'.$name.'" content="'.$content.'" />'.PHP_EOL;
        }
        if(!isset($frontendService->color_changed_code) || empty($frontendService->color_changed_code)){
            $html .= ' <link rel="stylesheet" href="css/custom_color.css">'.PHP_EOL;
        }
        else if(file_exists('css/custom_color_'.$frontendService->color_changed_code.'.css')){
            $html .= ' <link rel="stylesheet" href="css/custom_color_'.$frontendService->color_changed_code.'.css">'.PHP_EOL;
        }else{
            syncFrontendColors();
            $html .= ' <link rel="stylesheet" href="css/custom_color_'.$frontendService->color_changed_code.'.css">'.PHP_EOL;
        }
        
       
        return $html;
    }

    public static function cleanup()
    {
        static::$meta = [];
    }


}
