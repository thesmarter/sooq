<template>
    <div class="flex flex-row w-full items-center justify-between">
        <ps-label textColor="text-feSecondary-800 dark:text-feSecondary-300" class="font-semibold text-lg lg:text-lg"> {{
            $t("latest_vendor") }} </ps-label>
        <ps-input-with-right-icon @keyup.enter="nameFilterClicked" v-model:value="vendorSearchStore.paramHolder.searchterm"
            className="sm:w-80 w-full bg-feAchromatic-50 dark:bg-feAchromatic-900"
            placeholderColor="placeholder-feSecondary-400 dark:placeholder-feSecondary-400" padding=" px-4 h-10"
            v-bind:placeholder="$t('search_for_large_screem__search')">
            <template #icon>
                <ps-icon  name="search" @click="nameFilterClicked" textColor="text-feSecondary-400"
                    class='cursor-pointer' />
                <!-- <ps-icon v-else @click="[vendorSearchStore.paramHolder.searchTerm = '', nameFilterClicked()]" name="cross"
                    textColor="text-feSecondary-400" class='cursor-pointer' /> -->
            </template>
        </ps-input-with-right-icon>

    </div>
    <ps-loading-dialog ref="ps_loading_dialog" class='z-40' />
</template>

<script>

import { useVendorSearchStoreState } from "@templateCore/store/modules/vendor/VendorSearchStore";
import { PsValueStore } from "@templateCore/store/modules/core/PsValueStore";
// Components
import PsLabel from '@template1/vendor/components/core/label/PsLabel.vue';
import PsInputWithRightIcon from '@template1/vendor/components/core/input/PsInputWithRightIcon.vue';
import PsIcon from '@template1/vendor/components/core/icons/PsIcon.vue';
import PsLoadingDialog from '@template1/vendor/components/core/dialog/PsLoadingDialog.vue';

export default ({
    name : "VendorFilterViewBy",
    components : {
        PsLabel,
        PsInputWithRightIcon,
        PsIcon,
        PsLoadingDialog,
    },
    setup() {
        const vendorSearchStore = useVendorSearchStoreState();
        const psValueStore = PsValueStore();
        const loginUserId = psValueStore.getLoginUserId();

        function nameFilterClicked(){
            vendorSearchStore.paramHolder.ownerUserId = loginUserId;
            if(vendorSearchStore.paramHolder.searchterm != '' && vendorSearchStore.paramHolder.searchterm != null){
                vendorSearchStore.getVendorSearchList(loginUserId,vendorSearchStore.paramHolder);
            }
        }

        return {
            nameFilterClicked,
            vendorSearchStore
        }
    },
})
</script>
