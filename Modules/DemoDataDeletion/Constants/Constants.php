<?php

namespace Modules\DemoDataDeletion\Constants;

class Constants
{
    const dataTableDefaultRow = 10;
    const dashboardDataTableDefaultRow = 5;

    // For module (not existed model)
    const slowMovingItemModule = 4;
    const pendingModule = 15;
    const blueMarkUserModule = 16;
    const disableModule = 17;
    const rejectModule = 18;
    const buyerReportModule = 22;
    const sellerReportModule = 23;
    const dailyActiveUserReportModule = 25;
    const successfulDealCountReportModule = 26;
    const soldOutItemReportModule = 27;
    const itemReportModule = 28;
    const userReportModule = 24;
    const slowMovingItemReportModule = 69;
    const bannedUserModule = 35;
    const paymentSettingModule = 56;
    const offlinePaymentSettingModule = 57;
    const promotionInAppPurchaseModule = 58;
    const packageInAppPurchaseModule = 59;
    const packageReportModule = 32;
    const privacyPolicyModule = 51;
    const dataDeletionPolicyModule = 52;
    const dataReset = 47;

    // For module (existed model)
    const itemModule = 1;
    const currencyModule = 3;
    const categoryModule = 5;
    const categoryReportModule = 30;
    const tableModule = 62;
    const tableFieldModule = 64;  //64
    const customizeUiDetailModule = 65;
    const subCategoryModule = 6;
    const coreKeyTypeModule = 13;
    const paymentModule = 14;
    const userModule = 34;
    const userRoleModule = 36;
    const languageModule = 48;
    const languageStringModule = 67; //67
    const mobileSettingModule = 39;
    const complaintItemReportModule = 31;
    const offlinePaidItemModule = 19;
    const offlinePackageBoughtModule = 20;
    const apiTokenModule = 53;
    const promotionReportModule = 29;

    // core key
    const availableCurrency = 'ava-cur';
    const availableCurrencyModule = 63;
    const blogModule = 60;
    const aboutModule = 50;
    const backendSettingModule = 38;
    const phoneCountryCodeModule = 66;
    const coreMenuModule = 42;
    const coreSubMenuModule = 43;
    const systemConfigModule = 41;
    const CoreModule = 44;

    const moduleModule = 61;

    const locationCityModule = 7;
    const locationTownshipModule = 8;
    const contactModule = 33;
    const pushNotificationModule = 46;
    const mobileLanguageModule = 49;
    const mobileLanguageStringModule = 68; //68

    // core key
    const item = 'itm';
    const user = 'usr';
    const payment = 'pmt';
    const category = 'ctg';
    const tableField = 'tbl-field';
    const subcategory = 'sub-cat';
    const mobileLanguageString = 'mb-lang-stg';
    const mobileLanguage = 'mb-lang';
    const locationTownship = 'loc-tsp';
    const locationCity = 'loc';
    const blog = 'blog';
    const backendSetting = 'be-stg';
    const contact = 'contact';
    const currency = 'itm-cur';
    const coreModule = 'core-mde';
    const module = 'mdl';
    const coreMenuGroup = 'menu-gp';
    const coreSubMenuGroup = 'sub-menu-gp';
    const customizeUiDetail = 'cus-ui-dtl';
    const coreKeyType = 'core-key-type';
    const dataDeletion = 'core-data-del';
    const frontendSetting = 'fe-stg';
    const itemReport = 'itm-rpt';
    const language = 'lang';
    const languageString = 'lang-str';
    const mobileSetting = 'mb-stg';
    const privacyPolicy = 'prv-pcy';
    const pushNotificationMessage = 'push-noti-msg';
    const paidItem = 'paid-itm';
    const role = 'role';
    const about = 'abt';
    const phoneCountryCode = 'phone';
    const rating = 'rate';
    const systemConfig = 'sys-config';
    const userPermission = 'usr-psm';
    const uiType = 'uit';
    const apiToken = 'api-tkn';
    const color = 'color';

    // for payment code (used for id genetation)
    const paymentTableCode = 'payment';
    const middleCoreKeyCode = '0000';

    // for base dir for view folder path
    const parentPath = 'core/product/';

    // flag
    const success = "success";
    const danger = "danger";
    const warning = "warning";

    // for permission
    const viewAnyAbility = 'viewAny';
    const createAbility = 'create';
    const editAbility = 'update';
    const deleteAbility = 'delete';

    // For image path in storage
    const imgPath = 'storage/';
    const originPath = 'storage/uploads/';
    const thumbnail1xPath = 'storage/thumbnail/';
    const thumbnail2xPath = 'storage/thumbnail2x/';
    const thumbnail3xPath = 'storage/thumbnail3x/';

//    // For image path in public
//    const uploadPathForDel = 'public/uploads/';
//    const thumb1xPathForDel = 'public/thumbnail/';
//    const thumb2xPathForDel = 'public/thumbnail2x/';
//    const thumb3xPathForDel = 'public/thumbnail3x/';

    // For image path under root public
    const uploadPathForDel = 'storage/uploads/';
    const thumb1xPathForDel = 'storage/thumbnail/';
    const thumb2xPathForDel = 'storage/thumbnail2x/';
    const thumb3xPathForDel = 'storage/thumbnail3x/';

    // for csv file
    const csvFile = "csvFile";

    const show = 1;
    const hide = 0;
    const delete = 1;
    const unDelete = 0;
    const enable = 1;
    const disable = 0;
    const publish = 1;
    const unPublish = 0;
    const default = 1;
    const unDefault = 0;
    const Ban = 1;
    const unBan = 0;
    const available = 1;
    const unAvailable = 0;
    const ascending = "asc";
    const descending = "desc";
    const isSoldOut = 1;
    const yes = 1;
    const no = 0;

    // for item status
    const disableItem = 2;
    const pendingItem = 0;
    const rejectItem = 3;
    const publishItem = 1;

    // for user role
    const superAdminRoleId = 1;
    const normalUserRoleId = 2;

    // for user status
    const noLoginUser = "nologinuser";
    const activeUser = 'active';

    // for user account status
    const publishUser = 1;
    const pendingUser = 2;

    // for dashboard filter (in dropdown)
    const today = 'Today';
    const yesterday = 'Yesterday';
    const last7Days = 'Last 7 days';
    const last14Days = 'Last 14 days';
    const last30Days = 'Last 30 days';
    const last60Days = 'Last 60 days';
    const custom = 'Custom';

    // for api status code
    const internalServerErrorStatusCode = 500;
    const okStatusCode = 200;
    const createdStatusCode = 201;
    const noContentStatusCode = 204;
    const notFoundStatusCode = 404;
    const badRequestStatusCode = 400;
    const forbiddenStatusCode = 403;

    // for api status
    const successStatus = "success";
    const errorStatus = "error";

    // for ui core_keys_id
    const dropDownUi = 'uit00001';
    const textUi = 'uit00002';
    const radioUi = 'uit00003';
    const checkBoxUi = 'uit00004';
    const dateTimeUi = 'uit00005';
    const textAreaUi = 'uit00006';
    const numberUi = 'uit00007';
    const multiSelectUi = 'uit00008';
    const imageUi = 'uit00009';
    const timeOnlyUi = 'uit00010';
    const dateOnlyUi = 'uit00011';

    // for payment core_keys_id
    const paypalPaymentId = 'payment00001';
    const stripePaymentId = 'payment00002';
    const razorPaymentId = 'payment00003';
    const paystackPaymentId = 'payment00004';
    const offlinePaymentId = 'payment00005';
    const promotionInAppPurchasePaymentId = 'payment00006';
    const packageInAppPurchasePaymentId = 'payment00007';

    // for payment custom field core_keys_id
    const paypalMerchantId = 'pmt00001';
    const paypalPublicKey = 'pmt00002';
    const paypalPrivateKey = 'pmt00003';
    const paypalClientId = 'pmt00004';
    const paypalSecretKey = 'pmt00005';
    const paypalEnvironment = 'pmt00006';
    const stripePublishableKey = 'pmt00012';
    const stripeSecretKey = 'pmt00013';
    const razorKey = 'pmt00014';
    const paystackKey = 'pmt00015';

    // for user custom field core_keys_id
    const usrCity = 'ps-usr00001';
    const usrIsVerifyBlueMark = 'ps-usr00002';
    const usrBlueMarkNote = 'ps-usr00003';
    const usrRemainingPost = 'ps-usr00004';
    const usrFollowerCount = 'ps-usr00005';
    const usrFollowingCount = 'ps-usr00006';

    // for item custom field core_keys_id type
    const itmItemType = 'ps-itm00002';
    const itmPurchasedOption = 'ps-itm00003'; // Item price
    const itmConditionOfItem = 'ps-itm00004';
    const itmDealOption = 'ps-itm00007';

    // for iap types
    const iapTypeAndroid = 'Android';
    const iapTypeIOS = 'IOS';

    // for payment attribute col name for promotion
    const pmtAttrPromoteIapDayCol = 'days';
    const pmtAttrPromoteIapTypeCol = 'type'; // Android or IOS
    const pmtAttrPromoteIapStatusCol = 'status'; // 1 or 0

    // for payment attribute col name for package
    const pmtAttrPackageIapTypeCol = 'type'; // Android or IOS
    const pmtAttrPackageIapPriceCol = 'price';
    const pmtAttrPackageIapCountCol = 'count';
    const pmtAttrPackageIapStatusCol = 'status'; // 1 or 0
    const pmtAttrPackageIapCurrencyCol = 'currency_id'; // id from available_currencies table

    // for payment attribute col name for offline payment
    const pmtAttrOfflinePaymentStatusCol = 'status'; // 1 or 0

    // for payment method
    const offlinePaymentMethod = 'offline';
    const paystackPaymentMethod = 'paystack';
    const razorPaymentMethod = 'razor';
    const paypalPaymentMethod = 'paypal';
    const stripePaymentMethod = 'stripe';
    const iapPaymentMethod = 'In_App_Purchase';

    // for paid item status
    const paidItemProgressStatus = 'Progress'; //1
    const paidItemCompletedStatus = 'Finished'; //2
    const paidItemNotYetStartStatus = 'Not Yet Start'; //3
    const paidItemWaitingForApproval = 'Waiting For Approval';
    const paidItemRejected = 'Rejected';
    const paidItemNotAvailable = 'Not Available';

    // for chat type
    const chatToSeller = 'to_seller';
    const chatToBuyer = 'to_buyer';
    const chatBuyerReturnType = 'buyer';
    const chatSellerReturnType = 'seller';
    const chatFromBuyer = "CHAT_FROM_BUYER";
    const chatFromSeller = "CHAT_FROM_SELLER";

    // for follower/following return type
    const followerReturnType = "follower";
    const followingReturnType = "following";

    // for rating type
    const ratingUserType = 'user';


    const enLanguageId = 1;
    const arLanguageId = 2;
    const frLanguageId = 3;
    const esLanguageId = 4;
    const ptLanguageId = 5;
    const hiLanguageId = 6;
    const idLanguageId = 7;
    const jaLanguageId = 8;
    const msLanguageId = 9;
    const ruLanguageId = 10;
    const trLanguageId = 11;
    const deLanguageId = 12;
    const itLanguageId = 13;
    const koLanguageId = 14;
    const thLanguageId = 15;
    const zhLanguageId = 16;

    // for user account verify type
    const emailVerify = 1;
    const googleVerify = 2;
    const facebookVerify = 3;
    const phoneVerify = 4;
    const appleVerify = 5;

    // for noti type
    const chatMessageType = "CHAT_MESSAGE";
    const pushNotiType = "PUSH_NOTI";
    const offerAcceptedType = "OFFER_ACCEPTED";
    const offerRejectedType = "OFFER_REJECTED";
    const offerReceivedType = "OFFER_RECEIVED";

    // for search history type
    const searchHistoryItemType = "item";
    const searchHistoryUserType = "user";
    const searchHistoryCategoryType = "category";
    const searchHistoryAllType = "all";

    // for ad_post_type key
    const onlyPaidItemAdType = "only_paid_item";
    const normalAdsOnlyAdType = "normal_ads_only";
    const paidItemFirstAdType = "paid_item_first";
    const googleAdsBetweenAdType = "google_ads_between";
    const bumpsUpsBetweenAdType = "bumps_ups_between";
    const bumbsAndGoogleAdsBetweenAdType = "bumps_and_google_ads_between";
    const paidItemFirstWithGoogleAdType= "paid_item_first_with_google";

    // for item ad_type
    const normalAd = "normal_ad";
    const googleAd = "google_ad";
    const paidAd = "paid_ad";

    // for home page search type
    const categoryType = 'category';
    const itemType = 'item';
    const userType = 'user';
    const allType = 'all';

    // for noti flag
    const approvalNotiFlag = 'approval';
    const chatNotiFlag = 'chat';
    const verifyBlueMarkNotiFlag = 'verify_blue_mark';
    const followNotiFlag = 'follow';
    const reviewNotiFlag = "review";
    const subscribeNotiFlag = 'subscribe';

    // rating type
    const itemRatingType = 'item';
    const userRatingType = 'user';
    const shopRatingType = 'shop';
}
