<template>
    <div class="flex flex-row items-center justify-between w-full">
        <ps-label textColor="text-feSecondary-800 dark:text-feSecondary-300" class="font-semibold text-lg lg:text-xl"> {{
            $t("item_list__filter_by") }} </ps-label>

        <ps-label-caption class="font-normal text-lg cursor-pointer" textColor="text-feSecondary-500 dark:text-feSecondary-300"
            @click="clearAllFilter"> {{ $t("item_list__clear_filter") }} </ps-label-caption>
    </div>
    <ps-loading-dialog ref="ps_loading_dialog" class='z-40' />
</template>

<script>
// Libs
import { ref } from 'vue';
// Components
import PsLabel from '@template1/vendor/components/core/label/PsLabel.vue';
import PsLabelCaption from '@template1/vendor/components/core/label/PsLabelCaption.vue';
import PsLoadingDialog from '@template1/vendor/components/core/dialog/PsLoadingDialog.vue';
// Providers
import { useProductStore } from "@templateCore/store/modules/item/ProductStore";
import { PsValueStore } from "@templateCore/store/modules/core/PsValueStore";
// Models
import ProductParameterHolder from '@templateCore/object/holder/ProductParameterHolder';
//Utils
import PsUtils from '../../../../../../../../../TemplateCore/utils/PsUtils';

export default {
    name: 'ItemFilterClearButton',
    components: {
        PsLabel,
        PsLabelCaption,
        PsLoadingDialog
    },
    setup() {
        const itemProvider = useProductStore('list');
        const psValueStore = PsValueStore();
        const ps_loading_dialog = ref();

        async function clearAllFilter() {
            const orderBy = itemProvider.paramHolder.orderBy;
            const orderType = itemProvider.paramHolder.orderType;
            itemProvider.paramHolder = new ProductParameterHolder().getRecentParameterHolder();
            itemProvider.paramHolder.orderBy = orderBy;
            itemProvider.paramHolder.orderType = orderType;

            itemProvider.isDiscount = false;
            itemProvider.currentstatus.id = "0";
            itemProvider.currentstatus.name = "Available";
            itemProvider.form.product_relation = {};

            PsUtils.addParamToCurrentUrl(itemProvider.getURLforListByParam(itemProvider.paramHolder));

            ps_loading_dialog.value.openModal();
            await itemProvider.resetProductList(psValueStore.getLoginUserId(), itemProvider.paramHolder);
            ps_loading_dialog.value.closeModal();
            //hide popup filter
            itemProvider.showPopUpFilter = false;
        }

        return {
            clearAllFilter,
            ps_loading_dialog
        }
    }

}

</script>
