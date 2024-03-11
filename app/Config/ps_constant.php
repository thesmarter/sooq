<?php

namespace App\Config;

use Illuminate\Support\Facades\File;

class ps_constant
{
    const minPhpVersion = "8.1.0";

    const createPermission = 1;
    const readPermission = 2;
    const updatePermission = 3;
    const deletePermission = 4;
    const loginUserIdParaFromApi = "login_user_id";
    const deviceTokenKeyFromApi = "header-token";
    const supportedApiVersionCount = 9;
    const latestApiVersion = 1.0;

    const envatoApiUri = "https://api.envato.com/";
    const envatoApiVersion = "v3";
    const freeTrialTotalDay = 60;


    const flutterNotificationClick = "FLUTTER_NOTIFICATION_CLICK";
    const broadcast = "broadcast";
    const feBroadcast = "fe_broadcast";

    // for one-click-auto update
    const builderDomain = "https://sooq@adaasys.net/";

    // const builderDomain = "http://localhost:80/ps-builder-project/";

    // const builderDomain = "http://127.0.0.1:4000/";
    const builderApiVersion = "v1.0";
    const base_url = ps_constant::builderDomain."api/".ps_constant::builderApiVersion;
    const searchDomain = "https://www.products.smart.sd/";
    const searchSubFolder = "psx-mpc-demo";
    const searchSubFolderWithSlash1 = "/psx-mpc-demo";
    const searchSubFolderWithSlash2 = "psx-mpc-demo/";
    const searchApiToken = "zUMi0HNjAtnREMj3weG7XEv6ogEVovsf6eUFgOp4";

    // file paths to replace domain
    const appJSFilePath = "build/assets/app.*.js";
    const PsApiServiceJSFilePath = "build/assets/PsApiService.*.js";
    const psApiServiceJSFilePath = "build/assets/psApiService.*.js";


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

    // for variable
    const showCoreField = "showCoreField";
    const handlingColumn = "handlingColumn";
    const handlingFilter = "handlingFilter";

    // for condition
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
    // for beta (0 is beta. 1 is not beta)
    const isPublish = 1;
    const notSaveForNextTime = 0;
    const saveForNextTime = 1;

    // vendor order status
    const pendingStatusOfVendorOrder = 1;
    const acceptedStatusOfVendorOrder = 2;
    const deliveringStatusOfVendorOrder = 3;
    const devliveredStatusOfVendorOrder = 4;
    const completedStatusOfVendorOrder = 5;
    const cancelledStatusOfVendorOrder = 6;

    // vendor payment status
    const pendingStatusOfVendorPayment = 1;
    const paidStatusOfVendorPayment = 2;
    const rejectedStatusOfVendorPayment = 3;
    const refundedStatusOfVendorPayment = 4;

}
