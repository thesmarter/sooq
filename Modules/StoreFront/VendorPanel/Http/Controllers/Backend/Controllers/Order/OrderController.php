<?php

namespace Modules\StoreFront\VendorPanel\Http\Controllers\Backend\Controllers\Order;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Modules\StoreFront\VendorPanel\Http\Services\OrderService;

class OrderController extends Controller
{
    protected $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    public function index(Request $request)
    {
        return $this->orderService->index($request);
    }

    public function edit($id)
    {
        return $this->orderService->edit($id);
    }

    public function update(Request $request, $id)
    {
        return $this->orderService->update($request, $id);
    }

    public function destroy($id)
    {
        return $this->orderService->destroy($id);
    }
}
