<template>
    <ps-label class="mt-6 lg:text-base font-medium text-sm" textColor="text-feSecondary-800 dark:text-feSecondary-300">
        {{ $t("item_list__sub_categories") }} </ps-label>
    <ps-dropdown align="left" class='mt-1 lg:mt-2 w-full' @click="loadSubCategory(itemProvider.paramHolder.catId),load">
        <template #select>
            <ps-dropdown-select placeholderLang='item_list__all' border="border dark:border-feSecondary-200" :selectedValue="itemProvider.paramHolder.subCatName
                " />
        </template>
        <template #filter>
            <div class="w-56">
                <ps-input-with-right-icon class="rounded-xl flex-1 " @keyup.enter="filterSubCatUpdate(subCatKeyword)"
                    v-model:value="subCatKeyword" v-bind:placeholder="$t('item_list__search_subcat')">
                    <template #icon>
                        <ps-icon textColor="text-feSecondary-400 dark:text-feAchromatic-500" name="search"
                            class='cursor-pointer' @click="filterSubCatUpdate(subCatKeyword)" />
                    </template>
                </ps-input-with-right-icon>
            </div>
        </template>
        <template #list>
            <div class="rounded-md shadow-xs w-56 ">
                <div class="pt-2 z-30 ">
                    <div v-if="subCategoryStore.subCategoryList.data == null">
                        <ps-label class='p-2 flex'> {{ $t("list__loading") }} </ps-label>
                    </div>
                    <div v-else>
                        <div class="w-72 flex py-4 px-2 hover:bg-fePrimary-50 dark:hover:bg-feAchromatic-800 cursor-pointer items-center"
                            @click="subCategoryFilterClicked({ id: '', name: $t('item_list__all') })">
                            <ps-label class="ms-2"
                                :class="itemProvider.paramHolder.subCatId == '' ? ' font-medium' : 'font-light'"> {{
                                    $t("item_list__all") }} </ps-label>
                        </div>
                        <div v-for="selectSubCat in subCategoryStore.subCategoryList.data" :key="selectSubCat.id"
                            class="w-72 flex py-4 px-2 hover:bg-fePrimary-50 dark:hover:bg-feAchromatic-800 cursor-pointer items-center"
                            @click="subCategoryFilterClicked(selectSubCat)">
                            <ps-label class="ms-2"
                                :class="selectSubCat.id == itemProvider.paramHolder.subCatId ? ' font-medium' : 'font-light'">
                                {{ selectSubCat.name }} </ps-label>
                        </div>
                    </div>
                </div>
            </div>
        </template>
        <template #loadmore>

            <div class="mb-2 w-56">

                <div v-if="subCategoryStore.subCategoryList.data != null
                    && subCategoryStore.loading.value == true" class='mt-4 ms-4 flex'>
                    <ps-label-caption> {{ $t("list__loading") }} </ps-label-caption>
                </div>

                <ps-label v-if="itemProvider.paramHolder.catId != '' && !subCategoryStore.isNoMoreRecord"
                    class="mt-4 ms-4 mb-2 underline font-medium cursor-pointer flex"
                    @click="loadSubCategory(itemProvider.paramHolder.catId)"> {{ $t("list__load_more") }} </ps-label>
            </div>

        </template>
    </ps-dropdown>
    <ps-loading-dialog ref="ps_loading_dialog" class='z-40' />
</template>

<script>
// Libs
import { ref, onMounted, defineAsyncComponent } from 'vue';
// Components
import PsLabel from '@template1/vendor/components/core/label/PsLabel.vue';
const PsDropdown = defineAsyncComponent(() => import('@template1/vendor/components/core/dropdown/PsDropdown.vue'));
import PsDropdownSelect from '@template1/vendor/components/core/dropdown/PsDropdownSelect.vue';
import PsLabelCaption from '@template1/vendor/components/core/label/PsLabelCaption.vue';
import PsLoadingDialog from '@template1/vendor/components/core/dialog/PsLoadingDialog.vue';
// Providers
import { PsValueStore } from "@templateCore/store/modules/core/PsValueStore";
import { useSubCategoryStoreState } from '@templateCore/store/modules/subCategory/SubCategoryStore';
import { useProductStore } from "@templateCore/store/modules/item/ProductStore";
//Utils
import PsUtils from '../../../../../../../../../TemplateCore/utils/PsUtils';

export default {
    name: 'ItemFilterSubCategoryDropdown',
    components: {
        PsLabel,
        PsDropdown,
        PsDropdownSelect,
        PsLabelCaption,
        PsLoadingDialog
    },
    setup() {
        const psValueStore = PsValueStore();
        const subCategoryStore = useSubCategoryStoreState();
        const itemProvider = useProductStore('list');
        const ps_loading_dialog = ref();

        function loadSubCategory(catId) {
            subCategoryStore.loadSubCategoryList(catId);
        }

        async function subCategoryFilterClicked(value) {
            itemProvider.paramHolder.subCatId = value.id;
            itemProvider.paramHolder.subCatName = value.name;
            ps_loading_dialog.value.openModal();
            PsUtils.addParamToCurrentUrl(itemProvider.getURLforListByParam(itemProvider.paramHolder));
            await itemProvider.resetProductList(psValueStore.getLoginUserId(), itemProvider.paramHolder);
            ps_loading_dialog.value.closeModal();
            //hide popup filter
            itemProvider.showPopUpFilter = false;

        }

        onMounted(() => {
            if (itemProvider.paramHolder.catId != '') {
                loadSubCategory(itemProvider.paramHolder.catId);
            }
        })

        return {
            subCategoryStore,
            itemProvider,
            loadSubCategory,
            subCategoryFilterClicked,
            ps_loading_dialog
        }
    }

}

</script>
