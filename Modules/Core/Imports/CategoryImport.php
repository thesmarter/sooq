<?php

namespace Modules\Core\Imports;

use Illuminate\Support\Collection;
use Modules\Core\Entities\Category;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Modules\Core\Entities\CoreImage;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class CategoryImport implements ToCollection, WithHeadingRow, WithValidation, SkipsOnFailure
{
    use Importable, SkipsFailures;

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function collection(Collection $rows)
    {
        foreach ($rows as $row)
        {
            // save category
            $category = new Category();
            $category->name = $row['name'];
            $category->ordering = !empty($row['ordering'])? $row['ordering']: 1;
            $category->status = !empty($row['status'])? $row['status']: 1;
            $category->added_user_id = Auth::user()->id;
            $category->save();

            // for category cover photo
            $photo_name = $row['photo_name'];
            if (!empty($photo_name)){
                $file = public_path().'/storage/uploads/'.$photo_name;

                if(File::exists($file)){
                    // save original image to uploads
                    $org_img = Image::make($file);
                    $width = $org_img->width();
                    $height = $org_img->height();
                    $org_img->save(public_path().'/storage/uploads/'.$photo_name,50);

                    //save thumb 1x
                    // $thumbnail1x = Image::make($file);
                    // $thumbnail1x->resize(200, null, function ($constraint) {
                    //     $constraint->aspectRatio();
                    //     $constraint->upsize();
                    // });
                    // $thumbnail1x->save(public_path().'/storage/thumbnail/'.$photo_name);

                    // //save thumb 2x
                    // $thumbnail2x = Image::make($file);
                    // $thumbnail2x->resize(400, null, function ($constraint) {
                    //     $constraint->aspectRatio();
                    //     $constraint->upsize();
                    // });
                    // $thumbnail2x->save(public_path().'/storage/thumbnail2x/'.$photo_name);

                    // //save thumb 3x
                    // $thumbnail3x = Image::make($file);
                    // $thumbnail3x->resize(600, null, function ($constraint) {
                    //     $constraint->aspectRatio();
                    //     $constraint->upsize();
                    // });
                    // $thumbnail3x->save(public_path().'/storage/thumbnail3x/'.$photo_name);

                    // save image to core_images table
                    $cover = new CoreImage();
                    $cover->img_parent_id = $category->id;
                    $cover->img_type = 'category-cover';
                    $cover->img_path = $photo_name;
                    $cover->img_width = $width;
                    $cover->img_height = $height;
                    $cover->added_user_id = Auth::user()->id;
                    $cover->save();
                }
            }

            // for category icon
            $icon_name = $row['icon_name'];
            if(!empty($icon_name)){
                $file = public_path().'/storage/uploads/'.$icon_name;
                if(File::exists($file)){
                    // save original image to uploads
                    $org_img = Image::make($file);
                    $width = $org_img->width();
                    $height = $org_img->height();
                    $org_img->save(public_path().'/storage/uploads/'.$icon_name,50);

                    //save thumb 1x
                    // $thumbnail1x = Image::make($file);
                    // $thumbnail1x->resize(200, null, function ($constraint) {
                    //     $constraint->aspectRatio();
                    //     $constraint->upsize();
                    // });
                    // $thumbnail1x->save(public_path().'/storage/thumbnail/'.$icon_name);

                    // //save thumb 2x
                    // $thumbnail2x = Image::make($file);
                    // $thumbnail2x->resize(400, null, function ($constraint) {
                    //     $constraint->aspectRatio();
                    //     $constraint->upsize();
                    // });
                    // $thumbnail2x->save(public_path().'/storage/thumbnail2x/'.$icon_name);

                    // //save thumb 3x
                    // $thumbnail3x = Image::make($file);
                    // $thumbnail3x->resize(600, null, function ($constraint) {
                    //     $constraint->aspectRatio();
                    //     $constraint->upsize();
                    // });
                    // $thumbnail3x->save(public_path().'/storage/thumbnail3x/'.$icon_name);

                    // save image to core_images table
                    $icon = new CoreImage();
                    $icon->img_parent_id = $category->id;
                    $icon->img_type = 'category-icon';
                    $icon->img_path = $icon_name;
                    $icon->img_width = $width;
                    $icon->img_height = $height;
                    $icon->added_user_id = Auth::user()->id;
                    $icon->save();
                }
            }
        }
    }

    /**
     * Validation
     */
    public function rules(): array
    {
        return [
            'name' => 'required|min:3|unique:psx_categories,name',
            "photo_name" => "required",
            "icon_name" => "required",
        ];
    }

    /**
     * custom validation attributes
     * @return array
     */
    public function customValidationAttributes()
    {
        return [
            'name' => 'category name',
            'ordering' => 'category ordering',
            'status' => 'category status',
        ];
    }

}
