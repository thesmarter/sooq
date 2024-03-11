<?php

namespace Modules\Template\PSXFETemplate\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Services\PsService;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\Core\Http\Services\ItemService;
use Modules\Core\Http\Services\UserService;
use Illuminate\Contracts\Support\Renderable;
use Modules\Core\Http\Services\ImageService;
use Modules\Core\Http\Services\CategoryService;
use Modules\Core\Http\Services\LandingPageService;
use Modules\Blog\Http\Services\BlogService;
use Modules\Blog\Transformers\Api\App\V1_0\BlogApiResource;
use Modules\Core\Entities\FrontendSetting;
use Modules\PushNotificationMessage\Http\Services\PushNotificationMessageService;
use Modules\Core\Http\Services\AboutService;
use Modules\Core\Entities\BackendSetting;

class MPCTemplate1Controller extends Controller
{
    const parentPath = "Pages/vendor/views/";
    const notificationListPage = self::parentPath."notification/NotificationList";
    const notificationDetailPage = self::parentPath."notification/Notification";
    const profileEditPage = self::parentPath."user/EditProfile";
    const landingPage = self::parentPath."landing_page/Show";
    const dashboardPage = self::parentPath."dashboard/Dashboard";
    const categoryPage = self::parentPath."category/list/CategoryList";
    const subCategoryPage = self::parentPath."subCategory/list/SubCategoryList";
    const itemListPage = self::parentPath."item/list/ItemList";
    const itemEntryPage = self::parentPath."item/entry/ItemEntry";
    const profilePage = self::parentPath."user/Profile";
    const otherProfilePage = self::parentPath."user/OtherProfile";
    const blogListPage = self::parentPath."blog/list/BlogList";
    const blogDetailPage = self::parentPath."blog/detail/BlogDetail";
    const contactPage = self::parentPath."contact/ContactUs";
    const aboutPage = self::parentPath."about/About";
    const faqPage = self::parentPath."about/FaqPage";
    const termsAndConditionsPage = self::parentPath."about/TermsAndConditions";
    const privacyPage = self::parentPath."privacy/Privacy";
    const safetyTipsPage = self::parentPath."safety/Safety";
    const activeItemPage = self::parentPath."item/list/ActiveItemList";
    const pendingItemPage = self::parentPath."item/list/PendingItemList";
    const disableItemPage = self::parentPath."item/list/DisableItemList";
    const favouriteItemPage = self::parentPath."item/favourite/FavouriteList";

    const rejectItemPage = self::parentPath."item/list/RejectItemList";
    const paidItemPage = self::parentPath."item/list/PaidItemList";
    const soldoutItemPage = self::parentPath."item/list/SoldoutItemList";
    const followerItemPage = self::parentPath."item/list/FollowerItemList";
    const chatListPage = self::parentPath."chat/list/ChatList";
    const chatPage = self::parentPath."chat/Chat";
    const itemDetailPage = self::parentPath."item/detail/ItemDetail";
    const followerListPage = self::parentPath."user/FollowerList";
    const followingListPage = self::parentPath."user/FollowingList";
    const userListPage = self::parentPath."user/AccountList";
    const topRatedSellerListPage = self::parentPath."user/TopRatedSellerList";
    const packagePage = self::parentPath."transaction/BuyAdTransaction";
    const offerListPage = self::parentPath."chat/list/OfferList";
    const blockUserListPage = self::parentPath."user/BlockedUserList";
    const reportedItemListPage = self::parentPath."item/reported/ReportedItemList";
    const buyAdTransactionPage = self::parentPath."transaction/BuyAdTransaction";
    const reviewListPage = self::parentPath."review/ReviewList";
    const uiCollectionPage = self::parentPath."general/UiCollection";
    const vendorListPage = self::parentPath."user/VendorList";
    const vendorInfoPage = self::parentPath."vendor/VendorInfo";
    const vendorFilterPage = self::parentPath."vendor/VendorFilter";
    const vendorCheckoutPage = self::parentPath."vendor_checkout/VendorCheckout";
    const orderSuccessfulPage = self::parentPath."vendor_checkout/OrderSuccessful";

    protected $landingPageService;
    protected $frontendSettingService;

    protected $metaTitle;

    protected $metaDescription, $blogService;

    public function __construct(AboutService $aboutService,BlogService $blogService,PushNotificationMessageService $pushnotificationMessageService,LandingPageService $landingPageService, ItemService $itemService, ImageService $imageService, CategoryService $categoryService, UserService $userService)
    {
        $this->landingPageService = $landingPageService;
        $this->itemService = $itemService;
        $this->imageService = $imageService;
        $this->categoryService = $categoryService;
        $this->userService = $userService;
        $this->pushnotificationMessageService = $pushnotificationMessageService;
        $this->blogService = $blogService;
        $this->aboutService = $aboutService;
        $this->frontendSettingService = FrontendSetting::first();
        $this->metaTitle = $this->frontendSettingService->frontend_meta_title;
        $this->metaDescription = $this->frontendSettingService->frontend_meta_description;
    }

    public function index()
    {
        return renderView(self::dashboardPage);
    }

    /**
     * Display a listing of the resource.
     * @return Renderable
     */

     public function feCustom($slug)
     {

        dd($slug);
     }
    public function feLanding()
    {

        //for meta
        $this->setMeta($this->metaTitle ?? __('site_name'), $this->metaDescription ?? null, null);

        $dataArr = $this->landingPageService->index();
        return renderView(self::landingPage, $dataArr);
    }

    public function feDashboard()
    {
        $feSetting = BackendSetting::first()->fe_setting;
        //for meta
        $this->setMeta($this->metaTitle ?? __('site_name'), $this->metaDescription ?? null, null);

        if($feSetting == 1){
            return renderView(self::dashboardPage);
        }
        else{
            $dataArr = $this->landingPageService->index();
            return renderView(self::landingPage, $dataArr);
        }
    }

    public function feCategory()
    {
        //for meta
        $this->setMeta($this->metaTitle ?? __('site_name'), $this->metaDescription ?? null, null);

        return renderView(self::categoryPage);
    }

    public function feSubCategory(Request $request )
    {
        //for meta
        $this->setMeta($this->metaTitle ?? __('site_name'), $this->metaDescription ?? null, null);

        $dataArr = [
            'query' => json_decode(json_encode($request->all()))
        ];
        return renderView(self::subCategoryPage, $dataArr);
    }

    public function feItemList(Request $request)
    {
        //for meta
        $this->setMeta($this->metaTitle ?? __('site_name'), $this->metaDescription ?? null, null);

        $dataArr = [
            'query' => json_decode(json_encode($request->all()))
        ];
        return renderView(self::itemListPage, $dataArr);
    }

    public function feItemEntry(Request $request)
    {

        $dataArr = [
            'query' => json_decode(json_encode($request->all()))
        ];

        //for meta
        if(isset($dataArr['query']->itemId) && $dataArr['query']->itemId){
            $item = $this->itemService->getItem($dataArr['query']->itemId);
            $image =[
                'img_parent_id' => $dataArr['query']->itemId,
                'img_type' => 'item'
            ];
            $this->setMeta($item->title, $item->description, $image);
        }else{
            $this->setMeta($this->metaTitle ?? __('site_name'), $this->metaDescription ?? null, null);
        }

        return renderView(self::itemEntryPage,$dataArr);
    }

    public function feProfile()
    {
        //for meta
        $this->setMeta($this->metaTitle ?? __('site_name'), $this->metaDescription ?? null, null);

        if(Auth::user()){
            $user = $this->userService->getUser(Auth::user()['id']);
            if($user){
                $this->setMeta($user->name, $user->user_about_me, $user->user_cover_photo);
            }
        }else{
            $this->feDashboard();
        }

        return renderView(self::profilePage);
    }

    public function feOtherProfile(Request $request)
    {
        // echo json_encode($request->all());exit;
        $dataArr = [
            'query' => json_decode(json_encode($request->all()))
        ];
        if(Auth::user() && Auth::user()['id'] == $dataArr['query']->userId){
            return redirect()->route('fe_profile');
        }

        //for meta
        $user = $this->userService->getUser($dataArr['query']->userId);
        if($user){
            $this->setMeta($user->name, $user->user_about_me, $user->user_cover_photo);
        }

        return renderView(self::otherProfilePage,$dataArr);
    }

    public function feBlog()
    {
        //for meta
        $this->setMeta($this->metaTitle ?? __('site_name'), $this->metaDescription ?? null, null);

        // $dataArr = [ json_decode(json_encode($request->all()))];
        // from be
        $limit = null;
        $offset = null;
        $conds = null;

        $blogApiRelation = ['city', 'cover'];
        $blogs = $this->blogService->getBlogs($blogApiRelation, 1, $limit, $offset, 1, null, $conds);
        $data = BlogApiResource::collection($blogs);
        $dataArr = [
            'blogs' => $data
        ];

        return renderView(self::blogListPage, $dataArr);
    }

    public function feBlogDetail(Request $request)
    {

        //for meta
        $blog = $this->blogService->getBlog($request->blogId);
        $image =[
            'img_parent_id' => $request->blogId,
            'img_type' => 'blog'
        ];
        $this->setMeta($blog->name, $blog->description, $image);

        $dataArr = [
            'query' => json_decode(json_encode($request->all())),
        ];

        return renderView(self::blogDetailPage, $dataArr);
    }

    public function feContactUs()
    {
        //for meta
        $this->setMeta($this->metaTitle ?? __('site_name'), $this->metaDescription ?? null, null);

        return renderView(self::contactPage);
    }

    public function feAboutUs()
    {
        //for meta
        $about = $this->aboutService->getAbout();
        $image =[
            'img_parent_id' => $about->id,
            'img_type' => 'about'
        ];
        $this->setMeta($about->about_title, $about->about_description, $image);

        return renderView(self::aboutPage);
    }
    public function feTermsAndConditions()
    {
        //for meta
        $this->setMeta($this->metaTitle ?? __('site_name'), $this->metaDescription ?? null, null);

        return renderView(self::termsAndConditionsPage);
    }
    public function feFAQ()
    {
        //for meta
        $this->setMeta($this->metaTitle ?? __('site_name'), $this->metaDescription ?? null, null);

        return renderView(self::faqPage);
    }

    public function fePrivacy()
    {
        //for meta
        $this->setMeta($this->metaTitle ?? __('site_name'), $this->metaDescription ?? null, null);

        return renderView(self::privacyPage);
    }
    public function feSafetyTips()
    {
        //for meta
        $this->setMeta($this->metaTitle ?? __('site_name'), $this->metaDescription ?? null, null);

        return renderView(self::safetyTipsPage);
    }

    public function feActiveItems()
    {
        //for meta
        $this->setMeta($this->metaTitle ?? __('site_name'), $this->metaDescription ?? null, null);

        return renderView(self::activeItemPage);
    }

    public function fePendingItems()
    {
        //for meta
        $this->setMeta($this->metaTitle ?? __('site_name'), $this->metaDescription ?? null, null);

        return renderView(self::pendingItemPage);
    }
    public function feDisableItems()
    {
        //for meta
        $this->setMeta($this->metaTitle ?? __('site_name'), $this->metaDescription ?? null, null);

        return renderView(self::disableItemPage);
    }

    public function feFavouriteItems()
    {
        //for meta
        $this->setMeta($this->metaTitle ?? __('site_name'), $this->metaDescription ?? null, null);

        return renderView(self::favouriteItemPage);
    }

    public function feNotificationList()
    {
        //for meta
        $this->setMeta($this->metaTitle ?? __('site_name'), $this->metaDescription ?? null, null);

        return renderView(self::notificationListPage);
    }

    public function feNotificationDetail(Request $request)
    {
        $dataArr = [
            'query' => json_decode(json_encode($request->all()))
        ];

        //for meta
        $noti = $this->pushnotificationMessageService->getPushNotificationMessage($request->notiId);
        $image =[
            'img_parent_id' => $request->notiId,
            'img_type' => 'push_notification_message'
        ];
        $this->setMeta($noti->message, $noti->description, $image);

        return renderView(self::notificationDetailPage, $dataArr);
    }

    public function feProfileEdit()
    {
        //for meta
        $this->setMeta($this->metaTitle ?? __('site_name'), $this->metaDescription ?? null, null);

        if(Auth::user()){
            $user = $this->userService->getUser(Auth::user()['id']);
            if($user){
                $this->setMeta($user->name, $user->user_about_me, $user->user_cover_photo);
            }
        }

        return renderView(self::profileEditPage);
    }

    public function feRejectItems()
    {
        //for meta
        $this->setMeta($this->metaTitle ?? __('site_name'), $this->metaDescription ?? null, null);

        return renderView(self::rejectItemPage);
    }

    public function fePaidItems()
    {
        //for meta
        $this->setMeta($this->metaTitle ?? __('site_name'), $this->metaDescription ?? null, null);

        return renderView(self::paidItemPage);
    }

    public function feLimitAds()
    {
        //for meta
        $this->setMeta($this->metaTitle ?? __('site_name'), $this->metaDescription ?? null, null);

        return renderView(self::buyAdTransactionPage);
    }

    public function feReviewList(Request $request)
    {
        $dataArr = [
            'query' => json_decode(json_encode($request->all()))
        ];

        //for meta
        $this->setMeta($this->metaTitle ?? __('site_name'), $this->metaDescription ?? null, null);

        return renderView(self::reviewListPage, $dataArr);
    }

    public function feSoldoutItems()
    {
        //for meta
        $this->setMeta($this->metaTitle ?? __('site_name'), $this->metaDescription ?? null, null);

        return renderView(self::soldoutItemPage);
    }

    public function feFollowerItems()
    {
        //for meta
        $this->setMeta($this->metaTitle ?? __('site_name'), $this->metaDescription ?? null, null);

        return renderView(self::followerItemPage);
    }

    public function feChatList()
    {
        //for meta
        $this->setMeta($this->metaTitle ?? __('site_name'), $this->metaDescription ?? null, null);

        return renderView(self::chatListPage);
    }

    public function feChat(Request $request)
    {
        $dataArr = [
            'query' => json_decode(json_encode($request->all()))
        ];

        //for meta
        $this->setMeta($this->metaTitle ?? __('site_name'), $this->metaDescription ?? null, null);

        return renderView(self::chatPage, $dataArr);
    }

    public function feItemDetail(Request $request)
    {

        //for meta
        $item = $this->itemService->getItem($request->item_id);

        if(!$item){
            abort(404);
        }

        $image =[
            'img_parent_id' => $request->item_id,
            'img_type' => 'item'
        ];
        $this->setMeta($item->title, $item->description, $image);

        $dataArr = [
            'query' => json_decode(json_encode($request->all()))
        ];
        return renderView(self::itemDetailPage, $dataArr);
    }

    public function feFollowerList(Request $request)
    {
        $dataArr = [
            'query' => json_decode(json_encode($request->all()))
        ];

        //for meta
        $this->setMeta($this->metaTitle ?? __('site_name'), $this->metaDescription ?? null, null);

        return renderView(self::followerListPage, $dataArr);
    }

    public function feFollowingList(Request $request)
    {
        $dataArr = [
            'query' => json_decode(json_encode($request->all()))
        ];

        //for meta
        $this->setMeta($this->metaTitle ?? __('site_name'), $this->metaDescription ?? null, null);

        return renderView(self::followingListPage, $dataArr);
    }

    public function feUserList()
    {
        //for meta
        $this->setMeta($this->metaTitle ?? __('site_name'), $this->metaDescription ?? null, null);

        return renderView(self::userListPage);
    }

    public function feTopRatedSellerList()
    {
        //for meta
        $this->setMeta($this->metaTitle ?? __('site_name'), $this->metaDescription ?? null, null);

        return renderView(self::topRatedSellerListPage);
    }

    public function feBlockUserList()
    {
        //for meta
        $this->setMeta($this->metaTitle ?? __('site_name'), $this->metaDescription ?? null, null);

        return renderView(self::blockUserListPage);
    }

    public function fePackageList()
    {
        //for meta
        $this->setMeta($this->metaTitle ?? __('site_name'), $this->metaDescription ?? null, null);

        return renderView(self::packagePage);
    }

    public function feOfferList()
    {
        //for meta
        $this->setMeta($this->metaTitle ?? __('site_name'), $this->metaDescription ?? null, null);

        return renderView(self::offerListPage);
    }

    public function feReportedItems()
    {
        //for meta
        $this->setMeta($this->metaTitle ?? __('site_name'), $this->metaDescription ?? null, null);

        return renderView(self::reportedItemListPage);
    }

    public function feUiCollection()
    {
        //for meta
        $this->setMeta($this->metaTitle ?? __('site_name'), $this->metaDescription ?? null, null);

        return renderView(self::uiCollectionPage);
    }

    public function feVendorList(Request $request)
    {
        $dataArr = [
            'query' => json_decode(json_encode($request->all()))
        ];

        //for meta
        $this->setMeta($this->metaTitle ?? __('site_name'), $this->metaDescription ?? null, null);

        return renderView(self::vendorListPage, $dataArr);
    }

    public function feVendorInfo(Request $request)
    {
        $dataArr = [
            'query' => json_decode(json_encode($request->all()))
        ];

        //for meta
        $this->setMeta($this->metaTitle ?? __('site_name'), $this->metaDescription ?? null, null);

        return renderView(self::vendorInfoPage, $dataArr);
    }

    public function feVendorFilter(Request $request)
    {
        $dataArr = [
            'query' => json_decode(json_encode($request->all()))
        ];

        //for meta
        $this->setMeta($this->metaTitle ?? __('site_name'), $this->metaDescription ?? null, null);

        return renderView(self::vendorFilterPage, $dataArr);
    }

    public function feVendorCheckout(Request $request)
    {
        $dataArr = [
            'query' => json_decode(json_encode($request->all()))
        ];

        //for meta
        $this->setMeta($this->metaTitle ?? __('site_name'), $this->metaDescription ?? null, null);

        return renderView(self::vendorCheckoutPage, $dataArr);
    }

    public function feOrderSuccessful(Request $request)
    {
        $dataArr = [
            'query' => json_decode(json_encode($request->all()))
        ];

        //for meta
        $this->setMeta($this->metaTitle ?? __('site_name'), $this->metaDescription ?? null, null);

        return renderView(self::orderSuccessfulPage, $dataArr);
    }



    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        return view('mpctemplate1::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        return view('mpctemplate1::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        return view('mpctemplate1::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        //
    }

    private function setMeta($title, $description, $image){
        $psService = new PsService();
        $imagePath = '';
        if(is_array($image)){
            $img = $this->imageService->getImage($image);
            if($img){
                $imagePath = $img->img_path;
            }

        }else{
            $imagePath = $image;
        }
        if($title != null ){
            $psService::addMeta('title', $title);
            $psService::addMeta('description', $description);
            $psService::addMeta('image', $imagePath);
        }
    }
}
