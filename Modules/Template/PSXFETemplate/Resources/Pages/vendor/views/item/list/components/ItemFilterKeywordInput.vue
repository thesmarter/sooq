<template>
    <div>
        <ps-input-with-right-icon @keyup.enter="nameFilterClicked" v-model:value="itemProvider.paramHolder.searchTerm"
            className="sm:w-80 w-full bg-feAchromatic-50 dark:bg-feAchromatic-900"
            placeholderColor="placeholder-feSecondary-400 dark:placeholder-feSecondary-400" padding=" px-4 h-10"
            v-bind:placeholder="$t('search_for_large_screem__search')">
            <template #icon>
                <ps-icon v-if="itemProvider.paramHolder.searchTerm == ''" name="search" textColor="text-feSecondary-400"
                    class='cursor-pointer' />
                <ps-icon v-else @click="[itemProvider.paramHolder.searchTerm = '', nameFilterClicked()]" name="cross"
                    textColor="text-feSecondary-400" class='cursor-pointer' />
            </template>
        </ps-input-with-right-icon>

        <ps-loading-dialog ref="ps_loading_dialog" class='z-40' />
    </div>
</template>

<script>
// Libs
import { ref } from 'vue';
// Components
import PsInputWithRightIcon from '@template1/vendor/components/core/input/PsInputWithRightIcon.vue';
import PsIcon from '@template1/vendor/components/core/icons/PsIcon.vue';
import PsLoadingDialog from '@template1/vendor/components/core/dialog/PsLoadingDialog.vue';
// Providers
import { PsValueStore } from "@templateCore/store/modules/core/PsValueStore";
import { useProductStore } from "@templateCore/store/modules/item/ProductStore";
//Utils
import PsUtils from '../../../../../../../../../TemplateCore/utils/PsUtils';

export default {
    name: 'ItemFilterKeywordInput',
    components: {
        PsInputWithRightIcon,
        PsIcon,
        PsLoadingDialog,
    },
    setup() {
        const psValueStore = PsValueStore();
        const itemProvider = useProductStore('list');
        const ps_loading_dialog = ref();

        async function nameFilterClicked() {
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
            nameFilterClicked
        }
    }

}

</script>
