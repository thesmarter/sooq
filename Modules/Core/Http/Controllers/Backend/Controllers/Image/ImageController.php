<?php

namespace Modules\Core\Http\Controllers\Backend\Controllers\Image;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Core\Constants\Constants;
use Modules\Core\Entities\CoreImage;
use Illuminate\Contracts\Support\Renderable;
use Modules\Core\Http\Services\ImageService;

class ImageController extends Controller
{
    protected $imageService, $dangerFlag;

    public function __construct(ImageService $imageService)
    {
        $this->imageService = $imageService;
        $this->dangerFlag = Constants::danger;
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {

       
        $this->imageService->update($request, $id);
        return redirect()->back();
    }

    public function updateVideo(Request $request, $id)
    {
        $this->imageService->updateVideo($request, $id);
        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        $this->imageService->destroy($id);

        // $msg = 'The image has been deleted successfully.';
        $msg = __('core_images_tb_del');

        return redirectView(null, $msg, $this->dangerFlag);
    }
}
