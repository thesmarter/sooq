<template>
    <ps-button theme="bg-fePrimary-500 dark:bg-feAccent-500 text-feAchromatic-50 dark:text-feAchromatic-900 py-3 "
        textSize="text-xs" class="w-full text-center mt-6 mb-2" @click="loadDataList"> {{
            $t("fe_map_with_marker_moadl__apply")
        }} </ps-button>

    <ps-loading-dialog ref="ps_loading_dialog" class='z-40' />
</template>

<script>
// Libs
import { ref } from 'vue';
// Components
import PsButton from '@template1/vendor/components/core/buttons/PsButton.vue';
import PsLoadingDialog from '@template1/vendor/components/core/dialog/PsLoadingDialog.vue';
// Providers
import { PsValueStore } from "@templateCore/store/modules/core/PsValueStore";
import { useProductStore } from "@templateCore/store/modules/item/ProductStore";
//Utils
import PsUtils from '../../../../../../../../../TemplateCore/utils/PsUtils';

export default {
    name: 'ItemFilterApplyButton',
    components: {
        PsLoadingDialog,
        PsButton
    },
    setup() {
        const psValueStore = PsValueStore();
        const itemProvider = useProductStore('list');
        const ps_loading_dialog = ref();

        async function loadDataList() {
            ps_loading_dialog.value.openModal();
            PsUtils.addParamToCurrentUrl(itemProvider.getURLforListByParam(itemProvider.paramHolder));
            await itemProvider.resetProductList(psValueStore.getLoginUserId(), itemProvider.paramHolder);
            ps_loading_dialog.value.closeModal();
            
            //hide popup filter
            itemProvider.showPopUpFilter = false;
        }

        return {
            ps_loading_dialog,
            itemProvider,
            loadDataList
        }
    }

}

</script>
