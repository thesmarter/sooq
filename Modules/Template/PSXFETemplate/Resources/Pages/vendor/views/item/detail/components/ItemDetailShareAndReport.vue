<template>
    <div class="flex items-center gap-7 self-end">
        <ps-label textColor="text-sm text-feInfo-500 cursor-pointer"
            @click="shareClicked(productStore.item?.data?.dynamicLink)">{{ $t('item_detail__share')
            }}</ps-label>
        <div v-if="!productStore.isUserItem(loginUserId)">
            <ps-label textColor="text-sm text-fePrimary-500 cursor-pointer" @click="reportItemClicked">{{
                $t('item_detail__report_this_ad') }}</ps-label>
        </div>
    </div>

    <share-to-social-modal ref="share_modal" />
    <ps-confirm-dialog ref="ps_confirm_dialog" />
    <ps-error-dialog ref="ps_error_dialog" />

</template>

<script>
    import { ref } from 'vue';
    
    import PsLabel from '@template1/vendor/components/core/label/PsLabel.vue';
    import ShareToSocialModal from '@template1/vendor/components/layouts/share/ShareToSocialModal.vue';

    import PsConfirmDialog from '@template1/vendor/components/core/dialog/PsConfirmDialog.vue';
    import PsErrorDialog from '@template1/vendor/components/core/dialog/PsErrorDialog.vue';

    import { useProductStore } from '@templateCore/store/modules/item/ProductStore';
    import { useReportedItemStoreState } from '@templateCore/store/modules/item/ReportedItemStore';
    import ReportItemHolder from '@templateCore/object/holder/ReportItemHolder';
    import PsConst from '@templateCore/object/constant/ps_constants';
    import { router } from '@inertiajs/vue3';
    import { trans } from 'laravel-vue-i18n'; 
    import PsUtils from '@templateCore/utils/PsUtils';
    import PsStatus from '@templateCore/api/common/PsStatus';

    export default {
        name : "ItemDetailShareAndReport",
        components : {
            PsLabel,
            ShareToSocialModal,
            PsConfirmDialog,
            PsErrorDialog
        },
        props : {
            loginUserId : null,
            itemId : null,
            itemName : String
        },
        setup(props){
            
            const productStore = useProductStore('detail');
            const reportedItemStore = useReportedItemStoreState();
            const reportItemHolder = new ReportItemHolder();
            const share_modal = ref();
            const ps_confirm_dialog = ref();
            const ps_error_dialog = ref();

            function shareClicked(url) {
                share_modal.value.openModal(url, props.itemId, props.itemName);
            }

            // Report Item Functions
            function reportItemClicked() {
                if (props.loginUserId && props.loginUserId != PsConst.NO_LOGIN_USER) {
                    ps_confirm_dialog.value.openModal(
                        trans('item_detail__confirm'),
                        trans('item_detail__confirm_to_report_item'),
                        trans('item_detail__report'),
                        trans('item_detail__cancel'),
                        () => {
                            doReport();
                        },
                        () => {
                            PsUtils.log("Cancel");
                        }
                    );
                }
                else {
                    router.get(route('login'));
                }
            }

            async function doReport() {
                reportItemHolder.itemId = props.itemId;
                reportItemHolder.reportedUserId = props.loginUserId;
                const item = await reportedItemStore.addReportItem(props.loginUserId, reportItemHolder);
                
                if (item.status == PsStatus.SUCCESS) {
                    router.get(route('dashboard'));
                }
                else {
                    ps_error_dialog.value.openModal(item.message);
                }
            }
            return {
                productStore,
                shareClicked,
                reportItemClicked,
                share_modal,
                ps_confirm_dialog,
                ps_error_dialog,
            }
        }
    }
</script>
