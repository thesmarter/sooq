<template>

    <!-- For Large Responsive-->
    <ps-card class="flex w-full bg-feAchromatic-50 dark:bg-feSecondary-800 flex-col lg:rounded-lg shadow-sm " :enabledHover="true">
        <div class="flex flex-col px-2 py-4 lg:p-4 rounded-lg border border-feSecondary-50 dark:border-feSecondary-800">
            <div class="flex justify-between ">

                <div v-if="!appInfoProvider.selectPriceType(PsConst.NO_PRICE)" class="flex flex-wrap items-center gap-1">
                    <div
                        v-if="productStore.isItemDiscount() && appInfoProvider.isShowDiscount()">
                        <ps-label
                            textColor="line-through text-lg font-semibold text-feSecondart-600 dark:text-feSecondary-200">
                            <span v-if="appInfoProvider.selectPriceType(PsConst.NORMAL_PRICE)">
                                {{ productStore.item?.data?.itemCurrency?.currencySymbol }}
                                {{ formatPrice(productStore.item?.data ? productStore.item?.data?.originalPrice : '') }}
                            </span>
                        </ps-label>
                    </div>
                    <ps-label textColor="text-4xl font-semibold text-fePrimary-500 ">
                        <span v-if="appInfoProvider.selectPriceType(PsConst.NORMAL_PRICE)">{{ productStore.item?.data?.itemCurrency?.currencySymbol }}</span>
                        {{ formatPrice(productStore.item?.data ? productStore.item?.data?.price :
                            '') }}</ps-label>
                </div>
                <ps-button
                    v-if="!productStore.isUserItem(loginUserId)"
                    padding="p-2"
                    colors="bg-feAchromatic-50 text-fePrimary-500 dark:bg-feSecondary-700 dark:text-fePrimary-500"
                    border="border" hover="" focus="" @click="favouriteClicked">
                    <ps-icon textColor="text-fePrimary-500 dark:text-feAchromatic-50"
                        v-if="productStore.isFavourite()" name="heart" w="24" h="24" />
                    <ps-icon textColor="text-fePrimary-500 dark:text-fePrimary-500" v-else
                        name="heart-outline" w="24" h="24" />
                </ps-button>
                <ps-route-link
                    v-else-if="productStore.isUserItem(loginUserId) && submitButtonShow == true"
                    class="cursor-pointer"
                    :to="{ name: 'fe_item_entry', query: { itemId: productStore.item?.data?.id, categoryId : productStore.item?.data?.category?.catId } }">
                    <ps-icon textColor="text-fePrimary-500" name="pencil" w="24" h="24" />
                </ps-route-link>
            </div>
            <div v-if="isUploadedByVendor && !productStore.isUserItem(loginUserId)">
                <div class="flex justify-between items-center mt-4">
                    <div class="flex">
                        <ps-icon name="location" w="20" h="20" viewBox="0 -2 19 19" />
                        <ps-label
                            textColor="text-sm font-normal text-feSecondary-600 dark:text-feSecondary-50">{{
                                productStore.item?.data ? productStore.item?.data?.itemLocation.name : ''
                            }}</ps-label>
                    </div>
                    <ps-label textColor="text-sm font-normal text-feSecondary-600 dark:text-feSecondary-50">{{
                        productStore.item?.data ? productStore.item?.data?.addedDateStr : '' }}</ps-label>
                </div>
                <ps-label textColor="mt-4 text-base font-normal text-feSecondary-600 dark:text-feSecondary-50">{{
                    productStore.item?.data ? productStore.item?.data?.title : '' }}</ps-label>
                <div class="flex items-center gap-4 mt-4">
                    <div class="flex border rounded overflow-hidden">
                        <span @click="discrease()" class="text-center text-base w-8 h-8 cursor-pointer border-r-2">&minus;</span>
                        <span class="text-center w-9 h-8 align-middle pt-1">{{qty}}</span>
                        <span @click="increase()" class="text-center text-base w-8 h-8 cursor-pointer border-l-2">&plus;</span>
                    </div>
                    <ps-label v-if="inStock == 0" textColor="text-base font-normal text-fePrimary-500 dark:text-fePrimary-500">{{ $t("item_list__sold_item") }}</ps-label>
                    <ps-label v-else textColor="text-base font-normal text-feSecondary-600 dark:text-feSecondary-50">{{ $t("core_fe__items_in_stock",{"attribute":inStock}) }}</ps-label>
                </div>
            </div>
            <div v-else>
                <ps-label textColor="mt-4 text-base font-normal text-feSecondary-600 dark:text-feSecondary-50">{{
                    productStore.item?.data ? productStore.item?.data?.title : '' }}</ps-label>
                <ps-label v-for="(productRelation,index) in productStore.item?.data?.productRelation.filter((pr)=>pr.coreKeysId == 'ps-itm00010')" :key="index" textColor="mt-4 text-base font-normal text-feSecondary-600 dark:text-feSecondary-50">
                    <span v-if="productRelation.selectedValue[0].value == 0">{{$t('core_fe__items_in_stock',{"attribute":'0'})}}</span>
                    <span v-else>{{$t('core_fe__items_in_stock',{"attribute":productRelation.selectedValue[0].value})}}</span>
                </ps-label>
                <div class="flex justify-between items-center mt-4">
                    <div class="flex">
                        <ps-icon name="location" w="20" h="20" viewBox="0 -2 19 19" />
                        <ps-label
                            textColor="text-sm font-normal text-feSecondary-600 dark:text-feSecondary-50">{{
                                productStore.item?.data ? productStore.item?.data?.itemLocation.name : ''
                            }}</ps-label>
                    </div>
                    <ps-label textColor="text-sm font-normal text-feSecondary-600 dark:text-feSecondary-50">{{
                        productStore.item?.data ? productStore.item?.data?.addedDateStr : '' }}</ps-label>
                </div>
            </div>

            <div class="mt-7" v-if="!productStore.isUserItem(loginUserId)">
                <div v-if="isUploadedByVendor && productStore.item.data != null && galleryProvider.galleryList.data != null">

                    <div v-if="inStock !== 0 && inStock !== '0'">
                        <ps-route-link :to="{
                            name: 'fe_vendor_checkout',query : {itemId:itemId,qty:qty}
                        }">
                            <ps-button class="w-full">{{$t("core_fe__buy_now")}}</ps-button>
                        </ps-route-link>
                    </div>
                    <div v-else>
                        <ps-button @click="handleBuyNowBtn" class="w-full">{{$t("core_fe__buy_now")}}</ps-button>
                    </div>

                </div>
                <div v-else>
                    <div v-if="selectedChatType != PsConst.NO_CHAT && whatsAppNo != ''">
                        <ps-route-link class="flex flex-grow" :to="{
                            name: 'fe_chat',
                            query: {
                                buyer_user_id: loginUserId,
                                seller_user_id: productStore.item?.data?.addedUserId,
                                item_id: productStore.item?.data?.id,
                                chat_flag: PsConst.CHAT_FROM_SELLER,

                            }}">
                            <ps-button class="w-full flex items-center justify-center" padding="px-4 py-1.5 ml-1" :disabled="isVendorExpired">
                                <ps-label textColor="font-medium text-base">{{ $t("item_detail__chat") }}</ps-label>
                            </ps-button>
                            <!-- WhatApp Button-->
                            <ps-button v-if="whatsAppNo != ''" @click="toWhatsApp()" class="flex items-center justify-center" colors="bg-feBrand-phone" padding="ml-3 px-2 py-2" :disabled="isVendorExpired">
                                <ps-icon class="cursor-pointer" textColor="text-feAchromatic-50" :class="{'dark:text-feAchromatic-600' : isVendorExpired}" name="whatsapp" h="24" w="24" />
                            </ps-button>
                        </ps-route-link>

                    </div>
                    <div v-else-if= "selectedChatType != PsConst.NO_CHAT && whatsAppNo == ''">
                        <ps-route-link class="flex flex-grow" :to="{
                            name: 'fe_chat',
                            query: {
                                buyer_user_id: loginUserId,
                                seller_user_id: productStore.item?.data?.addedUserId,
                                item_id: productStore.item?.data?.id,
                                chat_flag: PsConst.CHAT_FROM_SELLER,

                            }}">
                            <ps-button class="w-full flex items-center justify-center" padding="px-4 py-1.5" :disabled="isVendorExpired">
                                <ps-label textColor="font-medium text-base">{{ $t("item_detail__chat") }}</ps-label>
                            </ps-button>
                        </ps-route-link>

                    </div>
                </div>
            </div>
            <div class="mt-6" v-else>
                <div class="w-full flex gap-6 mt-1">
                    <ps-button padding="p-2" @click="deleteClicked()" :disabled="isVendorExpired">
                        <ps-icon name="trash-alt" w="24" h="24" :class="{'dark:text-feAchromatic-600' : isVendorExpired}"/>
                    </ps-button>
                    <ps-button class="grow" v-if="productStore.showPromoteButton(isPromoteSuccessful,isAllPaymentDisable)" @click="promoteClicked" :disabled="isVendorExpired">
                        {{ $t('item_detail__promote') }}
                    </ps-button>
                    <ps-button class="grow" v-else disabled :disabled="isVendorExpired">
                        {{ $t('item_detail__promote') }}
                    </ps-button>
                </div>
                <div class="w-full flex gap-6 mt-6">
                    <ps-button class="grow"
                        v-if="productStore.isSoldOut(loginUserId)"
                        @click="markAsSold" :disabled="isVendorExpired">
                        {{ $t('item_detail__mark_as_sold') }}
                    </ps-button>
                    <ps-button class="grow" v-else disabled>
                        {{ $t('item_detail__mark_as_sold') }}
                    </ps-button>
                    <ps-button class="grow" v-if="productStore.productStatus('1')"
                        @click="markAsEnableDisable('disable')" :disabled="isVendorExpired">
                        {{ $t('item_detail__mark_as_disable') }}
                    </ps-button>
                    <ps-button class="grow" v-if="productStore.productStatus('2')"
                        @click="markAsEnableDisable('accept')" :disabled="isVendorExpired">
                        {{ $t('item_detail__mark_as_enable') }}
                    </ps-button>
                </div>
            </div>
            <promote-item-modal ref="promote_item_modal" @isPromoteSuccessful="handlePromote"/>
            <ps-confirm-dialog ref="ps_confirm_dialog" />
            <ps-error-dialog ref="ps_error_dialog" />
            <ps-loading-dialog ref="ps_loading_dialog" />
        </div>
    </ps-card>

</template>

<script>

// import { ref, onMounted } from 'vue';
import { ref , onMounted, defineAsyncComponent, watch} from 'vue'
import { router } from '@inertiajs/vue3';

// Components
import PsLabel from '@template1/vendor/components/core/label/PsLabel.vue';
import PsIcon from '@template1/vendor/components/core/icons/PsIcon.vue';
import PsButton from '@template1/vendor/components/core/buttons/PsButton.vue';
import PsRouteLink from '@template1/vendor/components/core/link/PsRouteLink.vue';
import PsLoadingDialog from '@template1/vendor/components/core/dialog/PsLoadingDialog.vue';

const PromoteItemModal = defineAsyncComponent(() => import('@template1/vendor/components/modules/item/PromoteItemModal.vue'));
const PsConfirmDialog = defineAsyncComponent(() => import('@template1/vendor/components/core/dialog/PsConfirmDialog.vue'));
const PsErrorDialog = defineAsyncComponent(() => import('@template1/vendor/components/core/dialog/PsErrorDialog.vue'));

import { trans } from 'laravel-vue-i18n';
import format from 'number-format.js';
import PsUtils from '@templateCore/utils/PsUtils';
import PsStatus from '@templateCore/api/common/PsStatus';
import PsConst from '@templateCore/object/constant/ps_constants';
import { useProductStore } from '@templateCore/store/modules/item/ProductStore';
import { useGalleryStoreState } from '@templateCore/store/modules/gallery/GalleryStore';
import { usePsAppInfoStoreState } from '@templateCore/store/modules/appinfo/AppInfoStore';
import { useUserStore } from "@templateCore/store/modules/user/UserStore";
import { useCustomFieldStoreState } from '@templateCore/store/modules/customField/CustomFieldStore';
import MarkSoldOutItemParameterHolder from '@templateCore/object/holder/MarkSoldOutItemParameterHolder';
import { useOfferStoreState } from '@templateCore/store/modules/offer/OfferStore';
import ProductStatusChangeParameterHolder from '@templateCore/object/holder/ProductStatusChangeParameterHolder';

export default {
    name : "ItemDetailPriceInfoCard",
    components: {
        PsLabel,
        PsIcon,
        PsButton,
        PsRouteLink,
        PromoteItemModal,
        PsConfirmDialog,
        PsErrorDialog,
        PsLoadingDialog,
    },
    props : {
        loginUserId : Number,
        favouriteClicked : Function,
        whatsAppNo : '',
        loadDataForItemDetail : Function,
        isAllpaymentDisable : null,
        isVendorExpired : '',
        itemId: Number,
    },
    setup(props)  {
        const isPromoteSuccessful = ref(false);
        const promote_item_modal = ref();
        const ps_confirm_dialog = ref();
        const ps_error_dialog = ref();
        const ps_loading_dialog = ref();

        const productStore = useProductStore('detail');
        const galleryProvider = useGalleryStoreState('detail');
        const appInfoProvider = usePsAppInfoStoreState();
        const userProvider = useUserStore();
        const itemCustomFieldStore = useCustomFieldStoreState('detail');

        const offerProvider = useOfferStoreState();
        const markAsSoldHolder = new MarkSoldOutItemParameterHolder().markSoldOutItemHolder();
        const ProductStatusChangeHolder = new ProductStatusChangeParameterHolder().ProductStatusChangeItemHolder();

        const submitButtonShow = ref();
        const qty = ref(0);
        const inStock = ref(0);
        const isUploadedByVendor = ref(false);

        onMounted(async () => {
            await productStore.loadItem(props.itemId, props.loginUserId);
            let quantity = productStore?.item?.data?.productRelation.filter((pr)=>pr.coreKeysId == 'ps-itm00010');
            inStock.value = quantity.length == 0 ? 0 : quantity[0].value;
            qty.value = quantity.length == 0 ? 0 : Number(quantity[0].value) > 0 ? 1 : 0;
            isUploadedByVendor.value = productStore.item?.data?.vendor && productStore.item?.data?.vendor.id != '' ? true : false;
            isSubmitButtonShow();
        });

        function increase(){
            if(qty.value < inStock.value){
                qty.value++;
            }
        }
        function discrease(){
            if(qty.value > 1){
                qty.value--;
            }
        }

        function formatPrice(value) {
                if(appInfoProvider.appInfo.data?.psAppSetting?.SelectedPriceType ==  PsConst.NORMAL_PRICE && value.toString() == '0'){
                    return trans('item_price__free');
                }else{
                    if (appInfoProvider.appInfo.data?.psAppSetting?.SelectedPriceType ==  PsConst.PRICE_RANGE) {
                        const floatValue = parseFloat(value);
                        const intValue = parseInt(floatValue);
                        if (intValue > 5) {
                            return '$'.repeat(5);
                        }
                        if (intValue < 1) {
                            return '$'.repeat(1);
                        }
                        return '$'.repeat(intValue);
                    } else {
                        if(appInfoProvider?.appInfo?.data?.mobileSetting?.price_format === "###,###"){
                            const formattedNumber = new Intl.NumberFormat('en-FR', {
                                                                            style: 'decimal',
                                                                            useGrouping: true,
                                                                            minimumFractionDigits: 0,
                                                                            }).format(value);
                            return formattedNumber.replace(",", ' ');
                        }
                        else if(appInfoProvider?.appInfo?.data?.mobileSetting?.price_format === "##,####"){

                                const formattedValue = parseFloat(value).toFixed(4);
                                let formattedNumberArr = formattedValue.split('.');
                                let formattedNumber = formattedNumberArr.shift();

                                return formattedNumber.replace(/(\d)(?=(\d{4})+$)/g, '$1,');


                        }else{
                            return format(appInfoProvider?.appInfo?.data?.mobileSetting?.price_format, value);
                        }
                    }
                }
        }

        async function isSubmitButtonShow() {
            if (props.loginUserId != 'nologinuser') {
                await userProvider.loadUser(props.loginUserId);
                const roleId = await userProvider.user.data ? userProvider.user.data.roleId : '';
                const isVerifybluemark = await userProvider.user.data ? userProvider.user.data.isVerifybluemark : '';

                submitButtonShow.value = appInfoProvider.isSubmitButtonShow(roleId , isVerifybluemark);

            } else {
                submitButtonShow.value = true
            }
        }

        async function promoteClicked() {
            isPromoteSuccessful.value = true;
            await PsUtils.waitingComponent(promote_item_modal, 20);
            promote_item_modal.value.openModal(
                productStore.item?.data?.id
            );
        }

        function handlePromote(value){
            isPromoteSuccessful.value = value;
        }

        function toWhatsApp() {
            const whatsappURL = `https://api.whatsapp.com/send?phone=${props.whatsAppNo}&text=${encodeURIComponent(productStore.item?.data?.title)}`;
            window.open(whatsappURL, '_blank');
        }

        function deleteClicked() {
            ps_confirm_dialog.value.openModal(
                trans('item_detail__delete_this_item'),
                trans('item_detail__confirm_to_delete_item'),
                trans('item_detail__delete'),
                trans('item_detail__cancel'),
                () => { doDelete() },
                () => { PsUtils.log("Cancel"); }
            );
        }

        async function doDelete() {

            const item = await productStore.userDeleteItem(props.loginUserId, props.itemId);
            if (item.status == PsStatus.SUCCESS) {
                router.get(route("dashboard"));
            }
            else {
                ps_error_dialog.value.openModal(item.message);
            }

        }

        async function loadDataForItemDetail() {
            if (props.loadDataForItemDetail) {
                await props.loadDataForItemDetail();
            }
        }

        async function markAsEnableDisable(status) {
            if (props.loginUserId && props.loginUserId != PsConst.NO_LOGIN_USER) {
                ps_confirm_dialog.value.openModal(
                    status == 'accept' ? trans('item_detail__mark_this_item_enable') : trans('item_detail__mark_this_item_disable'),
                    status == 'accept' ? trans('item_detail__are_you_sure_enable') : trans('item_detail__are_you_sure_disable'),
                    trans('core_fe__confirm'),
                    trans('item_detail__cancel'),
                    async () => {
                        ProductStatusChangeHolder.itemId = productStore?.item.data?.id;
                        ProductStatusChangeHolder.status = status
                        ps_loading_dialog.value.openModal();

                        await offerProvider.productStatusChange(props.loginUserId, ProductStatusChangeHolder);
                        loadDataForItemDetail();

                        ps_loading_dialog.value.closeModal();
                    },
                    () => { PsUtils.log("Cancel"); }
                );
            } else {
            router.get(route('login'));
            }
        }

        async function markAsSold() {
            if (props.loginUserId && props.loginUserId != PsConst.NO_LOGIN_USER) {
                ps_confirm_dialog.value.openModal(
                    trans('item_detail__item_sold_out'),
                    trans('item_detail__are_you_sure'),
                    trans('core_fe__confirm'),
                    trans('item_detail__cancel'),
                    async () => {
                        markAsSoldHolder.itemId = productStore?.item.data?.id;
                        ps_loading_dialog.value.openModal();

                        await offerProvider.markAsSoldFromDetail(props.loginUserId, markAsSoldHolder);
                        loadDataForItemDetail();

                        ps_loading_dialog.value.closeModal();
                    },
                    () => { PsUtils.log("Cancel"); }
                );
            } else {
            router.get(route('login'));
            }
        }

        function handleBuyNowBtn() {
            ps_error_dialog.value.openModal(trans('ps_error_dialog__error'), trans('you_are_unable_to_purchase_this_item'));
        }

        return {
            handleBuyNowBtn,
            PsConst,
            formatPrice,
            productStore,
            galleryProvider,
            appInfoProvider,
            submitButtonShow,
            itemCustomFieldStore,
            promoteClicked,
            handlePromote,
            toWhatsApp,
            deleteClicked,
            markAsSold,
            markAsEnableDisable,
            promote_item_modal,
            ps_confirm_dialog,
            ps_error_dialog,
            ps_loading_dialog,
            isPromoteSuccessful,
            qty,inStock,
            increase,discrease,
            isUploadedByVendor,
        }
    }
}
</script>
