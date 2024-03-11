<template>
    <div class="mt-6" v-if="!productStore.isUserItem(loginUserId)">
        <div v-if="selectedChatType != PsConst.NO_CHAT">
            <ps-route-link class="flex flex-grow" :to="{
                name: 'fe_chat',
                query: {
                    buyer_user_id: loginUserId,
                    seller_user_id: productStore.item?.data?.addedUserId,
                    item_id: productStore.item?.data?.id,
                    chat_flag: PsConst.CHAT_FROM_SELLER,

                }}">
                <ps-button class="w-full flex items-center justify-center" padding="px-4 py-1.5">
                    <ps-icon class="cursor-pointer" textColor="text-feAchromatic-50 mr-2" name="message" h="20" w="20" />
                    <ps-label textColor="font-medium text-base">{{ $t("item_detail__chat") }}</ps-label>
                </ps-button>
            </ps-route-link>
        </div>

        <!-- WhatApp Button-->
        <ps-button v-if="whatsAppNo != ''" @click="toWhatsApp()" class="w-full flex items-center justify-center mt-4" colors="bg-green-900 text-feAchromatic-50" padding="px-4 py-1.5">
            <ps-icon class="cursor-pointer" textColor="text-feAchromatic-50 mr-2" name="whatsapp" h="20" w="20" />
            <ps-label textColor="font-medium text-base"> WhatsApp </ps-label>
        </ps-button>
    </div>
    <div class="mt-6" v-else>
        <div
            class="flex flex-col px-2 py-4 lg:p-4 rounded-lg bg-feSecondary-50 dark:bg-feSecondary-800">
            <div class="flex items-center gap-1">
                <ps-icon name="statistic" w="24" h="24" />
                <ps-label
                    textColor="text-base font-semibold text-feSecondary-800 dark:text-feSecondary-50">{{
                        $t('item_detail__statistic') }}</ps-label>
            </div>
            <div class="grid grid-cols-2 mt-6">
                <div class="flex flex-col items-center border-e-2 border-feSecondary-500">
                    <ps-label
                        textColor="font-semibold text-xl text-feSecondary-800 dark:text-feSecondary-50">
                        {{ productStore.item?.data?.touchCount }} </ps-label>
                    <div
                        class="flex items-center gap-1 mt-1 text-feSecondary-800 dark:text-feSecondary-200">
                        <ps-icon name="eye-on" w="24" h="24" />
                        <ps-label textColor="font-medium text-base"> {{ $t('item_detail__views') }}
                        </ps-label>
                    </div>
                </div>
                <div class="flex flex-col items-center">
                    <ps-label
                        textColor="font-semibold text-xl text-feSecondary-800 dark:text-feSecondary-50">
                        {{ productStore.item?.data?.favouriteCount }} </ps-label>
                    <div
                        class="flex items-center gap-1 mt-1 text-feSecondary-800 dark:text-feSecondary-200">
                        <ps-icon name="heart-outline" w="24" h="24" />
                        <ps-label textColor="font-medium text-base"> {{ $t('item_detail__favourites') }}
                        </ps-label>
                    </div>
                </div>
            </div>
        </div>
        <div class="w-full flex gap-6 mt-7">
            <ps-button padding="p-2" @click="deleteClicked()">
                <ps-icon name="trash-alt" w="24" h="24" />
            </ps-button>
            <ps-button class="grow" v-if="productStore.showPromoteButton(isPromoteSuccessful,isAllPaymentDisable)" @click="promoteClicked">
                {{ $t('item_detail__promote') }}
            </ps-button>
            <ps-button class="grow" v-else disabled>
                {{ $t('item_detail__promote') }}
            </ps-button>
        </div>
        <div class="w-full flex gap-6 mt-6">
            <ps-button class="grow"
                v-if="productStore.isSoldOut(loginUserId)"
                @click="markAsSold">
                {{ $t('item_detail__mark_as_sold') }}
            </ps-button>
            <ps-button class="grow" v-else disabled>
                {{ $t('item_detail__mark_as_sold') }}
            </ps-button>
            <ps-button class="grow" v-if="productStore.productStatus('1')"
                @click="markAsEnableDisable('disable')">
                {{ $t('item_detail__mark_as_disable') }}
            </ps-button>
            <ps-button class="grow" v-if="productStore.productStatus('2')"
                @click="markAsEnableDisable('accept')">
                {{ $t('item_detail__mark_as_enable') }}
            </ps-button>
        </div>
    </div>

    <promote-item-modal ref="promote_item_modal" @isPromoteSuccessful="handlePromote"/>
    <ps-confirm-dialog ref="ps_confirm_dialog" />
    <ps-error-dialog ref="ps_error_dialog" />
    <ps-loading-dialog ref="ps_loading_dialog" />
</template>

<script>
    import { ref , defineAsyncComponent} from 'vue'
    import { router } from '@inertiajs/vue3';

    import PsLabel from '@template1/vendor/components/core/label/PsLabel.vue';
    import PsIcon from '@template1/vendor/components/core/icons/PsIcon.vue';
    import PsRouteLink from '@template1/vendor/components/core/link/PsRouteLink.vue';
    import PsButton from '@template1/vendor/components/core/buttons/PsButton.vue';
    import PsConst from '@templateCore/object/constant/ps_constants';
    import PsLoadingDialog from '@template1/vendor/components/core/dialog/PsLoadingDialog.vue';

    const PromoteItemModal = defineAsyncComponent(() => import('@template1/vendor/components/modules/item/PromoteItemModal.vue'));
    const PsConfirmDialog = defineAsyncComponent(() => import('@template1/vendor/components/core/dialog/PsConfirmDialog.vue'));
    const PsErrorDialog = defineAsyncComponent(() => import('@template1/vendor/components/core/dialog/PsErrorDialog.vue'));

    import { trans } from 'laravel-vue-i18n';
    import PsUtils from '@templateCore/utils/PsUtils';
    import PsStatus from '@templateCore/api/common/PsStatus';
    import { useProductStore } from '@templateCore/store/modules/item/ProductStore';
    import MarkSoldOutItemParameterHolder from '@templateCore/object/holder/MarkSoldOutItemParameterHolder';
    import { useOfferStoreState } from '@templateCore/store/modules/offer/OfferStore';
    import ProductStatusChangeParameterHolder from '@templateCore/object/holder/ProductStatusChangeParameterHolder';

    export default{
        name : "ItemDetailActionCard",
        components : {
            PsLabel,
            PsIcon,
            PsRouteLink,
            PsButton,
            PromoteItemModal,
            PsConfirmDialog,
            PsErrorDialog,
            PsLoadingDialog
        },
        props : {
            selectedChatType : String,
            loginUserId : null,
            itemId : null,
            whatsAppNo : '',
            loadDataForItemDetail : Function,
            isAllpaymentDisable : null
        },
        setup(props) {

            const isPromoteSuccessful = ref(false);
            const promote_item_modal = ref();
            const ps_confirm_dialog = ref();
            const ps_error_dialog = ref();
            const ps_loading_dialog = ref();

            const productStore = useProductStore('detail');
            const offerProvider = useOfferStoreState();
            const markAsSoldHolder = new MarkSoldOutItemParameterHolder().markSoldOutItemHolder();
            const ProductStatusChangeHolder = new ProductStatusChangeParameterHolder().ProductStatusChangeItemHolder();

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

            return {
                PsConst,
                isPromoteSuccessful,
                promoteClicked,
                handlePromote,
                toWhatsApp,
                deleteClicked,
                promote_item_modal,
                ps_confirm_dialog,
                ps_error_dialog,
                ps_loading_dialog,
                productStore,
                markAsSold,
                markAsEnableDisable
            }
        }
    }
</script>