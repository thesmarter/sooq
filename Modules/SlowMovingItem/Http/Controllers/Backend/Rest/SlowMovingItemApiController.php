<?php
namespace Modules\SlowMovingItem\Http\Controllers\Backend\Rest;

use Modules\SlowMovingItem\Transformers\Api\App\V1_0\SlowMovingItem\SlowMovingItemApiResource;
use Modules\Core\Constants\Constants;

class SlowMovingItemApiController
{
    protected $publish, $unPublish, $slowMovingItemApiRelation, $noContentStatusCode, $successStatus;

    public function __construct()
    {
        $this->publish = Constants::publish;
        $this->unPublish = Constants::unPublish;

        $this->slowMovingItemApiRelation = ['city'];

        $this->noContentStatusCode = Constants::noContentStatusCode;
        $this->successStatus = Constants::successStatus;

    }

    public function index()
    {
        $relation = ['owner', 'editor', 'city'];
        $slowMovingItems = $this->itemService->getItem($relation);

        $dataArr = [
            "slowMovingItems" => $slowMovingItems,
        ];
        return $dataArr;
    }

    public function edit($id)
    {
        $dataWithRelation = ['cover', 'city'];
        $slowMovingItem = $this->itemService->getItem($id, $dataWithRelation);
        $cities = $this->locationCityService->getLocationCities(null, $this->publish);
        $dataArr = [
            "slowMovingItem" => $slowMovingItem,
            "cities" => $cities,
        ];
        return $dataArr;
    }

    // for api
    public function searchFromApi($request)
    {
        $offset = $request->offset;
        $limit = $request->limit;

        $conds['date_limit'] = $request->date_limit;
        $conds['order_by'] = $request->order_by;
        $conds['order_type'] = $request->order_type;

        $slowMovingItemApiRelation = $this->slowMovingItemApiRelation;
        $slowMovingItems = SlowMovingItemApiResource::collection($this->itemService->getItems($slowMovingItemApiRelation, $this->publish, $limit, $offset, $conds));

        if ($offset > 0 && $slowMovingItems->isEmpty()) {
            // no paginate data
            $data = [];
            return responseDataApi($data);

        } else if($slowMovingItems->isEmpty()) {
            // no data db
            return responseMsgApi(__('no_data'), $this->noContentStatusCode, $this->successStatus);
        } else {
            return responseDataApi($slowMovingItems);
        }
    }

}
