<?php

namespace Modules\Core\Http\Controllers\Backend\Controllers\UserReport;

use App\Config\ps_constant;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Modules\Core\Constants\Constants;
use Modules\Core\Http\Services\UserService;

class UserReportController extends Controller
{
    const parentPath = "user_report/";
    const indexPath = self::parentPath."Index";
    const createPath = self::parentPath."Create";
    const editPath = self::parentPath."Edit";

    const parentBuyerReportPath = "buyer_report/";
    const indexBuyerReportPath = self::parentBuyerReportPath."Index";
    const createBuyerReportPath = self::parentBuyerReportPath."Create";
    const editBuyerReportPath = self::parentBuyerReportPath."Edit";

    const parentSellerReportPath = "seller_report/";
    const indexSellerReportPath = self::parentSellerReportPath."Index";
    const createSellerReportPath = self::parentSellerReportPath."Create";
    const editSellerReportPath = self::parentSellerReportPath."Edit";

    protected $userService;
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    // Buyer Report
    public function buyerReportIndex(Request $request)
    {
        $dataArr = $this->userService->buyerReportIndex($request);
        if (!permissionControl(Constants::buyerReportModule,ps_constant::readPermission)){
            return redirect()->route('admin.index');
        }
        return renderView(self::indexBuyerReportPath, $dataArr);

        // $users = User::join('user_boughts', 'user_boughts.buyer_user_id', '=', 'users.id')
        //     ->select('users.*', DB::raw("count(user_boughts.buyer_user_id) as purchased_item_count"))
        //     ->groupBy('buyer_user_id')
        //     ->orderBy('purchased_item_count', 'desc')
        //     ->get();

        // return Inertia::render('buyer_report/Index', [
        //     'users' => $users,
        // ]);
    }

    public function buyerReportShow($id)
    {
        $relation = ['role'];
        $dataArr = $this->userService->buyerReportShow($id, $relation);
        if (!permissionControl(Constants::buyerReportModule,ps_constant::readPermission)){
            return redirect()->route('admin.index');
        }
        return renderView(self::editBuyerReportPath, $dataArr);
    }

    public function buyerReportCsvExport()
    {
        // filename
		return $this->userService->buyerReportCsvExport();
    }

    // Seller Report
    public function sellerReportIndex(Request $request)
    {
        $dataArr = $this->userService->sellerReportIndex($request);
        if (!permissionControl(Constants::sellerReportModule,ps_constant::readPermission)){
            return redirect()->route('admin.index');
        }
        return renderView(self::indexSellerReportPath, $dataArr);

        // $users = Item::join('users', 'users.id', '=', 'psx_items.added_user_id')
        //     ->select('users.*', DB::raw("count(psx_items.added_user_id) as sold_item_count"))
        //     ->orderBy('sold_item_count', 'desc')
        //     ->orderBy('users.overall_rating', 'desc')
        //     ->latest('users.added_date')
        //     ->groupBy('psx_items.added_user_id')
        //     ->distinct()
        //     ->get();

        // return Inertia::render('seller_report/Index', [
        //     'users' => $users,
        // ]);
    }

    public function sellerReportShow($id)
    {
        $relation = ['role'];
        $dataArr = $this->userService->sellerReportShow($id, $relation);
        if (!permissionControl(Constants::sellerReportModule,ps_constant::readPermission)){
            return redirect()->route('admin.index');
        }
        return renderView(self::editSellerReportPath, $dataArr);
    }

    public function sellerReportCsvExport()
    {
        // filename
		return $this->userService->sellerReportCsvExport();
    }

    // User Report
    public function userReportIndex(Request $request)
    {
        $dataArr = $this->userService->userReportIndex($request);
        if (!permissionControl(Constants::userReportModule,ps_constant::readPermission)){
            return redirect()->route('admin.index');
        }
        return renderView(self::indexPath, $dataArr);
    }

    public function userReportShow($id)
    {
        $relation = ['role'];
        $dataArr = $this->userService->userReportShow($id, $relation);
        return renderView(self::editPath, $dataArr);
    }

    public function userReportCsvExport()
    {
        // filename
		return $this->userService->userReportCsvExport();
    }

    // Daily Active User Report
    public function dailyActiveUserReportIndex(Request $request)
    {
        $dataArr = $this->userService->dailyActiveUserReportIndex($request);
        if (!permissionControl(Constants::dailyActiveUserReportModule,ps_constant::readPermission)){
            return redirect()->route('admin.index');
        }
        return renderView(self::indexPath, $dataArr);

        // $users = User::where(['status' => 1, 'is_banned' => 0])->get();
        // return Inertia::render('daily_active_report/Index', [
        //     'users' => $users,
        // ]);
    }

    public function dailyActiveUserReportShow($id)
    {
        $relation = ['role'];
        $dataArr = $this->userService->dailyActiveUserReportShow($id, $relation);
        return renderView(self::editPath, $dataArr);
    }

    public function dailyActiveUserReportCsvExport()
    {
        // filename
		return $this->userService->dailyActiveUserReportCsvExport();
    }

}
