<?php

namespace Modules\Core\Transformers\Api\App\V1_0\About;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Core\Entities\CoreImage;
use Modules\Core\Transformers\Api\App\V1_0\CoreImage\CoreImageApiResource;
use Modules\Core\Entities\CorePrivacyPolicy;
use Modules\DataDeletionPolicy\Entities\CoreDataDeletion;

class AboutApiResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        $dataDeletionPolicy = CoreDataDeletion::first();
        $privacyPolicy = CorePrivacyPolicy::first();
        return [
            'id' => isset($this->id)?(string)$this->id:'',
            'about_title' => isset($this->about_title)?(string)$this->about_title:'',
            'about_description' => isset($this->about_description)?(string)$this->about_description:'',
            'about_email' => isset($this->about_email)?(string)$this->about_email:'',
            'about_phone' => isset($this->about_phone)?(string)$this->about_phone:'',
            'about_address' => isset($this->about_address)?(string)$this->about_address:'',
            'about_website' => isset($this->about_website)?(string)$this->about_website:'',
            'facebook' => isset($this->facebook)?(string)$this->facebook:'',
            'google_plus' => isset($this->google_plus)?(string)$this->google_plus:'',
            'instagram' => isset($this->instagram)?(string)$this->instagram:'',
            'youtube' => isset($this->youtube)?(string)$this->youtube:'',
            'pinterest' => isset($this->pinterest)?(string)$this->pinterest:'',
            'twitter' => isset($this->twitter)?(string)$this->twitter:'',
            'GDPR' => isset($this->GDPR)?(string)$this->GDPR:'',
            'upload_point' => isset($this->upload_point)?(string)$this->upload_point:'',
            'safety_tips' => isset($this->safety_tips)?(string)$this->safety_tips:'',
            'faq_pages' => isset($this->faq_pages)?(string)$this->faq_pages:'',
            'terms_and_conditions' => isset($this->terms_and_conditions)?(string)$this->terms_and_conditions:'',
            "default_photo" => new CoreImageApiResource($this->defaultPhoto ? $this->whenLoaded('defaultPhoto'): CoreImage::where('id', $this->id)->get()),
            "privacy_policy" => $privacyPolicy ? $privacyPolicy->content:'',
            "data_deletion_policy" => $dataDeletionPolicy? $dataDeletionPolicy->content:'',
        ];
    }
}
