<template>
    <Head :title="$t('item__detail')" />
    <ps-content-container>
        <template #content>
            <div class="mt-32 mb-10">
                <div class="mb-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mt-24">

                    <!-- Bread Crumb -->
                    <ps-breadcrumb-2 :items="breadcrumb" />

                    <!-- Share And Report -->
                    <item-detail-share-and-report :loginUserId="loginUserId" :itemId="itemId" :itemName="itemName"/>

                </div>

                <div v-if="productStore.item?.data?.vendor.expireStatus == PsConst.VendorExpiryExpiredStatus"
                class="mt-10 mb-10 w-full h-20 bg-feWarning-100 rounded-lg mx-auto border border-md border-feWarning-500 py-5 px-10 mb-4 dark:bg-feWarning-800 dark:border-none flex justify-start items-center">
                    <div class="me-5">
                        <ps-icon textColor="text-feWarning-500" class="pt-1" w="30" h="30" name= "warning-triangle" />
                    </div>
                    <div class="text-feAchromatic-800 dark:text-feAchromatic-100">
                        {{ $t('subscription_expired_text') }}
                    </div>
                </div>

                <!-- alert box to update quantity for item owner start -->
                <div v-if="productStore.item?.data?.user?.userId == loginUserId"
                class="mt-10 mb-10 w-full h-20 bg-feWarning-100 rounded-lg mx-auto border border-md border-feWarning-500 py-5 px-10 mb-4 dark:bg-feWarning-800 dark:border-none flex justify-start items-center">
                    <div class="me-5">
                        <ps-icon textColor="text-feWarning-500" class="pt-1" w="30" h="30" name= "warning-triangle" />
                    </div>
                    <div class="text-feAchromatic-800 dark:text-feAchromatic-100">
                        {{ $t('please_update_the_quantity_of_this_item') }}
                    </div>
                </div>
                <!-- alert box to update quantity for item owner end -->

                <div class="grid grid-cols-1 sm:grid-cols-12 gap-4">
                    <div class="col-span-1 sm:col-span-7 md:col-span-8">

                        <!-- gallery -->
                        <gallery-vertical-swiper :galleryList="galleryProvider.galleryList?.data"
                            :video="videoStore.galleryList?.data"
                            :isLoading="galleryProvider.galleryList.data == null && dataReady ? false : true" />

                        <!-- Price Info -->
                        <item-detail-price-info-card class="mt-6 sm:hidden"
                            :favouriteClicked="favouriteClicked"
                            :isAllpaymentDisable="isAllPaymentDisable"
                            :loginUserId="loginUserId"
                            :itemId="itemId"
                            :selectedChatType="appInfoProvider.appInfo.data?.psAppSetting?.SelectedChatType"
                            :loadDataForItemDetail="loadDataForItemDetail"
                            :whatsAppNo="whatsAppNo"/>

                        <item-detail-statistic-card class="mt-6 sm:hidden"
                            :loginUserId="loginUserId"
                            :productStore="productStore"/>

                        <!-- Info Card -->
                        <item-detail-info-card
                            :productRelation="productRelation" />

                        <div class="mt-6 sm:hidden">
                            <!-- owner profile -->
                            <vendor-profile-card v-if="appInfoProvider?.isVendorSettingOn() && productStore.item?.data?.vendor && productStore.item?.data?.vendor.id != '' && productStore.item?.data?.user && productStore.item?.data?.user?.userId != loginUserId"
                                :vendor="productStore.item?.data?.vendor"
                                :itemId="itemId" />

                            <item-detail-user-list-horizontal v-else-if="productStore.item?.data?.user && productStore.item?.data?.user?.userId != loginUserId"
                                :user="productStore.item?.data?.user"
                                :storeName="itemId" />

                            <!-- button group -->
                            <!-- <item-detail-action-card
                                :isAllpaymentDisable="isAllPaymentDisable"
                                :loginUserId="loginUserId"
                                :itemId="itemId"
                                :selectedChatType="appInfoProvider.appInfo.data?.psAppSetting?.SelectedChatType"
                                :loadDataForItemDetail="loadDataForItemDetail"
                                :whatsAppNo="whatsAppNo" /> -->

                            <!-- location Card -->
                            <item-detail-location-card
                                :appInfoProvider="appInfoProvider"
                                :productStore="productStore"/>
                        </div>

                        <div class="sm:hidden block border-2 border-feSecondary-200 rounded-md mt-4 ">
                               <!-- safty tips -->
                                <item-detail-learn-more-card

                                :text="$t('item_detail__safety_tips')"
                               :clickLeranMore="safetyTip">
                              <template #icon>
                               <div>
                   <ps-icon textColor="text-feAchromatic-900 dark:text-feAchromatic-100" class="pt-1" w="25" h="24" name= "safetyTip" />

                               </div>
                              </template>
                           </item-detail-learn-more-card>

                            <!-- T&C -->
                                <item-detail-learn-more-card

                               :text="$t('term_and_condition__term_and_condition')"
                               :clickLeranMore="termsAndConditions">
                              <template #icon>
                               <div>
                   <ps-icon textColor="text-feAchromatic-900 dark:text-feAchromatic-100" name= "termsAndCondition" />

                               </div>
                              </template>
                           </item-detail-learn-more-card>

                                <!-- FAQ -->
                            <item-detail-learn-more-card

                                :text="$t('item_detail__faq')"
                                :clickLeranMore="faq">
                               <template #icon>
                                <div>
                    <ps-icon textColor="text-feAchromatic-900 dark:text-feAchromatic-100" name= "faq" />

                                </div>
                               </template>
                            </item-detail-learn-more-card>
                        </div>
                    </div>
                    <div class="col-span-1 sm:col-span-5 md:col-span-4 hidden sm:block">
                        <!-- Price Info -->
                        <item-detail-price-info-card
                            :favouriteClicked="favouriteClicked"
                            :loginUserId="loginUserId"
                            :isAllpaymentDisable="isAllPaymentDisable"
                            :itemId="itemId"
                            :selectedChatType="appInfoProvider.appInfo.data?.psAppSetting?.SelectedChatType"
                            :loadDataForItemDetail="loadDataForItemDetail"
                            :whatsAppNo="whatsAppNo"
                            :isVendorExpired = "isVendorExpired"/>

                        <vendor-profile-card v-if="appInfoProvider?.isVendorSettingOn() && productStore.item?.data?.vendor && productStore.item?.data?.vendor.id != '' && productStore.item?.data?.user && productStore.item?.data?.user?.userId != loginUserId"
                                :vendor="productStore.item?.data?.vendor"
                                :itemId="itemId" />

                        <item-detail-user-list-horizontal v-else-if="productStore.item?.data?.user && productStore.item?.data?.user?.userId != loginUserId"
                                :user="productStore.item?.data?.user"
                                :storeName="itemId" />

                        <item-detail-statistic-card
                            :loginUserId="loginUserId"
                            :productStore="productStore"/>


                        <!-- button group -->
                        <!-- <item-detail-action-card
                            :isAllpaymentDisable="isAllPaymentDisable"
                            :loginUserId="loginUserId"
                            :itemId="itemId"
                            :selectedChatType="appInfoProvider.appInfo.data?.psAppSetting?.SelectedChatType"
                            :loadDataForItemDetail="loadDataForItemDetail"
                            :whatsAppNo="whatsAppNo" /> -->

                        <!-- location Card -->
                        <item-detail-location-card :appInfoProvider="appInfoProvider" :productStore="productStore" mapContainer="mapContainer2"/>

                        <div class="border-2 border-feSecondary-200 rounded-md mt-4 ">
                               <!-- safty tips -->
                                <item-detail-learn-more-card

                                :text="$t('item_detail__safety_tips')"
                               :clickLeranMore="safetyTip">
                              <template #icon>
                               <div>
                   <ps-icon textColor="text-feAchromatic-900 dark:text-feAchromatic-100" class="pt-1" w="25" h="24"  name= "safetyTip" />

                               </div>
                              </template>
                           </item-detail-learn-more-card>

                            <!-- T&C -->
                                <item-detail-learn-more-card

                               :text="$t('term_and_condition__term_and_condition')"
                               :clickLeranMore="termsAndConditions">
                              <template #icon>
                               <div>
                   <ps-icon textColor="text-feAchromatic-900 dark:text-feAchromatic-100" name= "termsAndCondition" />

                               </div>
                              </template>
                           </item-detail-learn-more-card>

                                <!-- FAQ -->
                            <item-detail-learn-more-card

                                :text="$t('item_detail__faq')"
                                :clickLeranMore="faq">
                               <template #icon>
                                <div>
                    <ps-icon textColor="text-feAchromatic-900 dark:text-feAchromatic-100" name= "faq" />

                                </div>
                               </template>
                            </item-detail-learn-more-card>
                        </div>

                    </div>
                </div>

                <item-detail-related-item-horizontal-list/>

            </div>
        </template>
    </ps-content-container>

    <ps-loading-dialog ref="ps_loading_dialog" />
    <gallery-detail-horizontal-swiper ref="gallery_detail" />
    <ps-learn-more-dialog ref = "ps_learn_more_dialog" />
</template>

<script  lang="ts">
import { Head } from '@inertiajs/vue3';
import { router } from '@inertiajs/vue3';

// Objects
import PsConst from '@templateCore/object/constant/ps_constants';
import { ref, onMounted, onUnmounted, computed } from 'vue';

// Components
import PsIcon from '@template1/vendor/components/core/icons/PsIcon.vue';

import PsContentContainer from '@template1/vendor/components/layouts/container/PsContentContainer.vue';
import GalleryDetailHorizontalSwiper from '@template1/vendor/components/modules/gallery/GalleryDetailHorizontalSwiper.vue';
import GalleryVerticalSwiper from "@template1/vendor/components/modules/gallery/GalleryVerticalSwiper.vue";
import PsLoadingDialog from '@template1/vendor/components/core/dialog/PsLoadingDialog.vue';
import PsConfirmDialog from '@template1/vendor/components/core/dialog/PsConfirmDialog.vue';
import PsErrorDialog from '@template1/vendor/components/core/dialog/PsErrorDialog.vue';
import PsFrontendLayout from '@template1/vendor/components/layouts/container/PsFrontendLayout.vue';
import PsBreadcrumb2 from "@template1/vendor/components/core/breadcrumbs/PsBreadcrumb2.vue";
import UserListHorizontal from '@template1/vendor/components/modules/user/UserListHorizontal.vue';
import VendorProfileCard from '@template1/vendor/components/modules/vendor/VendorProfileCard.vue';
import PsLearnMoreDialog from '@template1/vendor/components/core/dialogs/PsLearnMoreDialog.vue';

import ItemDetailPriceInfoCard from './components/ItemDetailPriceInfoCard.vue';
import ItemDetailLearnMoreCard from './components/ItemDetailLearnMoreCard.vue';
import ItemDetailLocationCard from './components/ItemDetailLocationCard.vue';
import ItemDetailInfoCard from './components/ItemDetailInfoCard.vue';
import ItemDetailStatisticCard from './components/ItemDetailStatisticCard.vue';
import  ItemDetailRelatedItemHorizontalList from './components/ItemDetailRelatedItemHorizontalList.vue';
import ItemDetailShareAndReport from './components/ItemDetailShareAndReport.vue';
import ItemDetailUserListHorizontal from './components/ItemDetailUserListHorizontal.vue';

// Models
import { useProductStore } from '@templateCore/store/modules/item/ProductStore';
import { PsValueStore } from '@templateCore/store/modules/core/PsValueStore';
import { useGalleryStoreState } from '@templateCore/store/modules/gallery/GalleryStore';
import { useAboutUsStoreState } from "@templateCore/store/modules/aboutus/AboutUsStore";
import { useFavouriteItemStoreState } from "@templateCore/store/modules/item/FavouriteItemStore";
import { usePsAppInfoStoreState } from '@templateCore/store/modules/appinfo/AppInfoStore';
import { useTouchCountStore } from '@templateCore/store/modules/item/TouchCountStore';
import { useOfferStoreState } from '@templateCore/store/modules/offer/OfferStore';
// Holders
import ProductParameterHolder from '@templateCore/object/holder/ProductParameterHolder';
import AppInfoParameterHolder from '@templateCore/object/holder/AppInfoParameterHolder';
import TouchCountParameterHolder from "@templateCore/object/holder/TouchCountParameterHolder";
import { trans } from 'laravel-vue-i18n';
import { useCustomFieldStoreState } from '@templateCore/store/modules/customField/CustomFieldStore';

export default {
    name: "ItemDetailView",
    components: {
        PsLearnMoreDialog,
        PsContentContainer,
        PsBreadcrumb2,
        PsConfirmDialog,
        PsLoadingDialog,
        PsErrorDialog,
        GalleryDetailHorizontalSwiper,
        UserListHorizontal,
        GalleryVerticalSwiper,
        Head,
        ItemDetailPriceInfoCard,
        ItemDetailLearnMoreCard,
        ItemDetailLocationCard,
        ItemDetailInfoCard,
        ItemDetailStatisticCard,
        ItemDetailRelatedItemHorizontalList,
        ItemDetailShareAndReport,
        ItemDetailUserListHorizontal,
        VendorProfileCard,
        PsIcon
    },
    layout: PsFrontendLayout,
    props: ['query'],
    setup(props) {

        const itemId = props.query.item_id;
        const itemName = '';

        // Inject Providers
        const relatedItemStore = useProductStore('related_item');
        const productStore = useProductStore('detail');
        const galleryProvider = useGalleryStoreState('detail');
        const videoStore = useGalleryStoreState('video');
        const aboutUsProvider = useAboutUsStoreState();
        const FavouriteItemProvider = useFavouriteItemStoreState();
        relatedItemStore.paramHolder = new ProductParameterHolder().getRelatedProductParameterHolder();
        const touchCountStore = useTouchCountStore();
        const itemCustomFieldStore = useCustomFieldStoreState('detail');
        const userCustomFieldStore = useCustomFieldStoreState('owner');

        const dataReady = ref(false);
        const imageCount = ref(1);
        let totalCount = ref(0);
        let whatsAppNo = ref('');
        let isVendorExpired = ref(false);

        // Init Variables
        const psValueStore = PsValueStore();

        const loginUserId = ref(psValueStore.getLoginUserId());
        const ps_loading_dialog = ref();
        const ps_learn_more_dialog = ref();
        const gallery_detail = ref();
        const isPromote = ref(false);
        const isAllPaymentDisable = ref();
        const holder = new TouchCountParameterHolder();
        const appInfoProvider = usePsAppInfoStoreState();
        const appInfoParameterHolder = new AppInfoParameterHolder();
        appInfoParameterHolder.userId = psValueStore.getLoginUserId();

        let isVideoSetting = ref(false);
        let productRelation = ref([]);

        onMounted(async () => {
            // Load Item Data
            loadDataForItemDetail();
            holder.typeId = itemId;
            holder.userId = loginUserId.value;
            holder.typeName = 'item';
            if(loginUserId.value != 'nologinuser') {
                touchCountStore.postTouchCount(loginUserId.value, holder);
            }
            if (!isAllPaymentDisable.value) {
                isAllPaymentDisable.value = appInfoProvider.isAllPaymentDisable();
            }

        });
        onUnmounted(() => {
            productStore.item.data = null;
            galleryProvider.galleryList.data = null;
            videoStore.galleryList.data = null;
        });

        loadAppInfo();

        function safetyTip() {
            ps_learn_more_dialog.value.openModal('item_detail__safety_tips', aboutUsProvider.aboutus?.data?.safetyTips);
        }

        function termsAndConditions() {
            ps_learn_more_dialog.value.openModal('term_and_condition__term_and_condition', aboutUsProvider.aboutus?.data?.termsAndConditions);
        }

        function faq() {
            ps_learn_more_dialog.value.openModal('item_detail__faq', aboutUsProvider.aboutus?.data?.faqPage);
        }

        async function loadDataForItemDetail() {
            await aboutUsProvider.loadAboutUs(loginUserId.value);
            await productStore.loadItem(itemId, loginUserId.value);
            await userCustomFieldStore.loadUserCustomFieldList(loginUserId.value);

            productRelation.value = productStore.loadProductRelation(['ps-itm00002', 'ps-itm00004', 'ps-itm00007']);

            for (const customField of userCustomFieldStore.customField.data?.customList) {
                if (customField.coreKeysId == PsConst.WHATSAPP_CORE_KEY_Id && customField.isVisible == '1') {
                    const ownerWhatsAppRelation = productStore.item?.data?.user?.userRelation.filter((pr) => pr.coreKeysId == PsConst.WHATSAPP_CORE_KEY_Id);
                    if (ownerWhatsAppRelation.length != 0 && ownerWhatsAppRelation[0]?.value != null ) {
                        whatsAppNo.value = ownerWhatsAppRelation[0]?.value;
                    }
                }
            }
            console.log(productStore);
            console.log(productStore.item?.data?.vendor);

            isVendorExpired.value = productStore.item?.data?.vendor.expireStatus == PsConst.VendorExpiryExpiredStatus ? true : false;

            await galleryProvider.resetGalleryList(loginUserId.value, itemId, PsConst.ITEM__RELATED_TYPE);
            relatedItemStore.paramHolder.catId = productStore.item.data.catId;

            await relatedItemStore.resetProductList(loginUserId.value, relatedItemStore.paramHolder);

            if (appInfoProvider.isShowItemVideo()) {
                videoStore.galleryList.data = galleryProvider.galleryList.data.filter(item => item.imgType != 'item-video-icon');
                galleryProvider.galleryList.data = galleryProvider.galleryList.data.filter(item => item.imgType != 'item-video');
            } else {
                videoStore.galleryList.data = galleryProvider.galleryList.data.filter(item => item.imgType != 'item-video-icon' && item.imgType != 'item-video');
                galleryProvider.galleryList.data = galleryProvider.galleryList.data.filter(item => item.imgType != 'item-video' && item.imgType != 'item-video-icon');
            }

            if (productStore.promoteStatus() && !isAllPaymentDisable.value) {
                isPromote.value = true;
            } else {
                isPromote.value = false;
            }

            await itemCustomFieldStore.loadCustomFieldList(loginUserId.value);
            dataReady.value = true;
        }

        async function loadAppInfo() {
            await appInfoProvider.loadAppInfo(appInfoParameterHolder);
            if (appInfoProvider.isShowItemVideo() == PsConst.ONE) {
                isVideoSetting.value = true;
            }
            if (parseInt(appInfoProvider.maxImgUploadOfItem()) >= 1) {
                imageCount.value = parseInt(appInfoProvider.maxImgUploadOfItem());
                totalCount.value = imageCount.value + 1;
            }
        }

        async function favouriteClicked() {
            if (loginUserId.value && loginUserId.value != PsConst.NO_LOGIN_USER) {
                ps_loading_dialog.value.openModal();
                if (productStore.isFavourite()) {
                    ps_loading_dialog.value.message = trans('item_detail__removing_item_from_favourite');
                } else {
                    ps_loading_dialog.value.message = trans('item_detail__adding_item_to_favourite');
                }

                await FavouriteItemProvider.postFavourite(itemId, loginUserId.value);

                await loadDataForItemDetail();
                ps_loading_dialog.value.closeModal();
            }
            else {
                router.get(route('login'));
            }
        }

        const breadcrumb = computed(() => {
            if (appInfoProvider?.isShowSubCategory() || productStore.subCategoryIdIsEmpty()) {
                return [
                    {
                        label: trans('ps_nav_bar__home'),
                        url: route('dashboard')
                    },
                     {
                       label: productStore.item?.data?.category?.catName,
                       url: route('fe_item_list', {
                         cat_id: productStore.item?.data?.category?.catId,
                         cat_name: productStore.item?.data?.category?.catName,
                         status: 1
                       })
                     },
                     {
                       label: productStore.item?.data?.title,
                       color: "text-fePrimary-500"
                     }
                    ];
            } else {
                  return [
                    {
                      label: trans('ps_nav_bar__home'),
                      url: route('dashboard')
                    },
                    {
                      label: productStore.item?.data?.category?.catName,
                      url: route('fe_item_list', {
                        cat_id: productStore.item?.data?.category?.catId,
                        cat_name: productStore.item?.data?.category?.catName,
                        status: 1
                      })
                    },
                    {
                      label: productStore.item?.data?.subCategory?.name,
                      url: route('fe_item_list', {
                        cat_id: productStore.item?.data?.category?.catId,
                        cat_name: productStore.item?.data?.category?.catName,
                        sub_cat_id: productStore.item?.data?.subCategory?.id,
                        sub_cat_name: productStore.item?.data?.subCategory?.name,
                        status: 1
                      })
                    },
                    {
                      label: productStore.item?.data?.title,
                      color: "text-fePrimary-500"
                    }
                ];
            }
        });

        const relatedItem = () => {
          let ids = [];
          ids.push(itemId);

          return relatedItemStore.itemList?.data.filter( (item) => ids.indexOf(item.id) == -1);
        };

        return {
            loadDataForItemDetail,
            dataReady,
            appInfoProvider,
            loginUserId,
            itemId,
            itemName,
            productStore,
            galleryProvider,
            videoStore,
            aboutUsProvider,
            favouriteClicked,
            ps_loading_dialog,
            ps_learn_more_dialog,
            gallery_detail,
            isPromote,
            PsConst,
            safetyTip,
            termsAndConditions,
            faq,
            totalCount,
            imageCount,
            itemCustomFieldStore,
            relatedItemStore,
            productRelation,
            isAllPaymentDisable,
            whatsAppNo,
            breadcrumb,
            relatedItem,
            isVendorExpired,
        }
    },

}
</script>
