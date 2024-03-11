<template>
    <div class="flex flex-row items-center justify-between w-full">
        <ps-label textColor="text-feSecondary-800 dark:text-feSecondary-300" class="font-semibold text-lg lg:text-xl"> {{
            $t("item_list__filter_by") }} </ps-label>
        <ps-label-caption class="font-normal text-lg cursor-pointer" textColor="text-feSecondary-500 dark:text-feSecondary-300"
            @click="clearAllFilter"> {{ $t("item_list__clear_filter") }} </ps-label-caption>
    </div>
</template>

<script>

import { useVendorSearchStoreState } from "@templateCore/store/modules/vendor/VendorSearchStore";
import { PsValueStore } from "@templateCore/store/modules/core/PsValueStore";
import VendorSearchParameterHolder from '@templateCore/object/holder/VendorSearchParameterHolder';

import PsLabel from '@template1/vendor/components/core/label/PsLabel.vue';
import PsLabelCaption from '@template1/vendor/components/core/label/PsLabelCaption.vue';

export default ({
    name : "VendorFilterSortBy",
    components : {
        PsLabel,
        PsLabelCaption
    },
    setup() {
        const vendorSearchStore = useVendorSearchStoreState();
        const psValueStore = PsValueStore();
        const loginUserId = psValueStore.getLoginUserId();

        function clearAllFilter() {
            vendorSearchStore.sortByValue = '';
            
            let vendorSearchParmeterHolder = new VendorSearchParameterHolder().getAllVendorParameterHolder();
            vendorSearchStore.paramHolder = vendorSearchParmeterHolder;
            vendorSearchStore.paramHolder.ownerUserId = loginUserId;
            vendorSearchStore.getVendorSearchList(loginUserId,vendorSearchStore.paramHolder);
            
            vendorSearchStore.showPopUpFilter = false;

        }

        return {
            clearAllFilter
        }
    },
})
</script>
