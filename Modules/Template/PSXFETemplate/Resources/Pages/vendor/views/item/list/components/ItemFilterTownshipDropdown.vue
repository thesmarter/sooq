<template>
    <ps-label class="mt-6 lg:text-base font-medium text-sm" textColor="text-feSecondary-800 dark:text-feSecondary-300">
        {{ $t("item_list__location_township") }} </ps-label>
    <ps-dropdown align="left" class='mt-1 lg:mt-2' @click="loadSubLocation(itemProvider.paramHolder.itemLocationId)">
        <template #select>
            <ps-dropdown-select placeholderLang='item_list__all' border="border dark:border-feSecondary-200" :selectedValue="itemProvider.paramHolder.itemLocationTownshipName
                " />
        </template>
        <template #list>
            <div class="rounded-md shadow-xs w-56 ">
                <div class="pt-2 z-30 ">
                    <div v-if="itemLocationTownshipStore.locationTownshipList.data == null">
                        <ps-label class='p-2 flex'> {{ $t("list__loading") }} </ps-label>
                    </div>
                    <div v-else>
                        <div class="w-72 flex py-4 px-2 hover:bg-fePrimary-50 dark:hover:bg-feAchromatic-800 cursor-pointer items-center"
                            @click="subLocFilterClicked({ id: '', name: $t('item_list__all') })">
                            <ps-label class="ms-2"
                                :class="itemProvider.paramHolder.itemLocationTownship == '' ? ' font-medium' : 'font-light'">
                                {{ $t("item_list__all") }} </ps-label>
                        </div>
                        <div v-for="selectSubLoc in itemLocationTownshipStore.locationTownshipList.data"
                            :key="selectSubLoc.id"
                            class="w-72 flex py-4 px-2 hover:bg-fePrimary-50 dark:hover:bg-feAchromatic-800 cursor-pointer items-center"
                            @click="subLocFilterClicked(selectSubLoc)">
                            <ps-label class="ms-2"
                                :class="selectSubLoc.id == itemProvider.paramHolder.itemLocationTownship ? ' font-medium' : 'font-light'">
                                {{ selectSubLoc.townshipName }} </ps-label>
                        </div>
                    </div>
                </div>
            </div>
        </template>
        <template #loadmore>

            <div class="mb-2 w-56">

                <div v-if="itemLocationTownshipStore.locationTownshipList.data != null
                    && itemLocationTownshipStore.loading.value == true" class='mt-4 ms-4 flex'>
                    <ps-label-caption> {{ $t("list__loading") }} </ps-label-caption>
                </div>

                <ps-label v-if="itemProvider.paramHolder.itemLocationId != '' && !itemLocationTownshipStore.isNoMoreRecord"
                    class="mt-4 ms-4 mb-2 underline font-medium cursor-pointer flex"
                    @click="loadSubLocation(itemProvider.paramHolder.itemLocationId)"> {{ $t("list__load_more") }}
                </ps-label>
            </div>

        </template>
    </ps-dropdown>
    <ps-loading-dialog ref="ps_loading_dialog" class='z-40' />
</template>

<script>
// Libs
import { ref, defineAsyncComponent } from 'vue';
// Components
import PsLabel from '@template1/vendor/components/core/label/PsLabel.vue';
const PsDropdown = defineAsyncComponent(() => import('@template1/vendor/components/core/dropdown/PsDropdown.vue'));
import PsDropdownSelect from '@template1/vendor/components/core/dropdown/PsDropdownSelect.vue';
import PsLabelCaption from '@template1/vendor/components/core/label/PsLabelCaption.vue';
import PsLoadingDialog from '@template1/vendor/components/core/dialog/PsLoadingDialog.vue';
// Providers
import { PsValueStore } from "@templateCore/store/modules/core/PsValueStore";
import { useItemLocationTownshipStoreState } from '@templateCore/store/modules/itemLocationTownship/ItemLocationTownshipStore';
import { useProductStore } from "@templateCore/store/modules/item/ProductStore";
//Utils
import PsUtils from '../../../../../../../../../TemplateCore/utils/PsUtils';

export default {
    name: 'ItemFilterTownshipDropdown',
    components: {
        PsLabel,
        PsDropdown,
        PsDropdownSelect,
        PsLabelCaption,
        PsLoadingDialog
    },
    setup() {
        const psValueStore = PsValueStore();
        const itemLocationTownshipStore = useItemLocationTownshipStoreState();
        const itemProvider = useProductStore('list');
        const ps_loading_dialog = ref();

        function loadSubLocation(itemLocationId) {
            itemLocationTownshipStore.loadItemLocationTownshipList(itemLocationId);
        }

        async function subLocFilterClicked(value) {

            itemProvider.paramHolder.itemLocationTownship = value.id;
            itemProvider.paramHolder.itemLocationTownshipName = value.townshipName;
            ps_loading_dialog.value.openModal();
            PsUtils.addParamToCurrentUrl(itemProvider.getURLforListByParam(itemProvider.paramHolder));
            await itemProvider.resetProductList(psValueStore.getLoginUserId(), itemProvider.paramHolder);
            ps_loading_dialog.value.closeModal();
            //hide popup filter
            itemProvider.showPopUpFilter = false;
        }

        return {
            itemLocationTownshipStore,
            itemProvider,
            loadSubLocation,
            subLocFilterClicked,
            ps_loading_dialog
        }
    }

}

</script>
