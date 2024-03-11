<template>
    <div class="mb-4 flex flex-col sm:flex-row gap-4 justify-end">
        <div class="flex items-center">

            <!-- <ps-label textColor="text-xs font-medium me-2 dark:text-feSecondary-200">{{ $t("core_fe__sort_by") }}</ps-label> -->
            <ps-dropdown horizontalAlign="right" h="h-auto" class="h-10 w-auto">
                <template #select>
                    <ps-button class=" h-10 " :disabled="activeProductsArrId != 1 && activeProductsArrId != 0"
                        colors="bg-feAchromatic-50 dark:bg-feAchromatic-900" border="border dark:border-feSecondary-400"
                        focus="focus:outline-none focus:bg-fePrimary-500 focus:ring focus:ring-fePrimary-300 focus:text-feAchromatic-50"
                        hover="hover:outline-none hover:bg-fePrimary-600 hover:text-feAchromatic-50">
                        <span class="me-1 font-medium"> {{ activeSortingArrName ?
                            $t(activeSortingArrName) : $t('core_fe__default') }} </span>
                        <ps-icon class="flex" name="downChervon" />
                    </ps-button>
                </template>
                <template #list v-if="activeProductsArrId == 1 || activeProductsArrId == 0">
                    <div class="rounded-md bg-feAchromatic-50 dark:bg-feSecondary-800 shadow-xs w-44 ">
                        <div class="pt-2 z-30">
                            <div>
                                <div v-for="sort in sortingArr" :key="sort.id"
                                    class="w-72 flex py-4 px-2 hover:bg-fePrimary-50 dark:hover:bg-feSecondary-500 cursor-pointer items-center"
                                    @click="orderingFilterClicked(sort)">
                                    <ps-label v-if="(sort.id == 1 || sort.id == 2)" class="ms-2"
                                        :class="sort.id == activeSortingArrId ? ' font-medium' : 'font-light'">{{
                                            $t("review_entry__title") }}:
                                        {{ $t(sort.title) }} </ps-label>
                                    <ps-label v-else-if="(sort.id == 3 || sort.id == 4)" class="ms-2"
                                        :class="sort.id == activeSortingArrId ? ' font-medium' : 'font-light'">{{
                                            $t("item_entry__price") }}:
                                        {{ $t(sort.title) }} </ps-label>
                                    <ps-label v-else class="ms-2"
                                        :class="sort.id == activeSortingArrId ? ' font-medium' : 'font-light'">
                                        {{ $t(sort.title) }} </ps-label>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
            </ps-dropdown>
        </div>
        <div class="flex items-center">
            <!-- <ps-label textColor="text-xs font-medium me-2 dark:text-feSecondary-200">{{ $t('core_fe__products_by')
            }}</ps-label> -->
            <ps-dropdown horizontalAlign="right" h="h-auto" class="h-10 w-auto">
                <template #select>
                    <ps-button class=" h-10 " colors="bg-feAchromatic-50 dark:bg-feAchromatic-900"
                        border="border dark:border-feSecondary-400"
                        focus="focus:outline-none focus:bg-fePrimary-500 focus:ring focus:ring-fePrimary-300 focus:text-feAchromatic-50"
                        hover="hover:outline-none hover:bg-fePrimary-600 hover:text-feAchromatic-50">
                        <span class="me-2 font-medium"> {{ activeProductsArrName ?
                            $t(activeProductsArrName) : $t('core_fe__relevance') }} </span>
                        <ps-icon class="flex" name="downChervon" />
                    </ps-button>
                </template>
                <template #list>
                    <div class="rounded-md bg-feAchromatic-50 dark:bg-feSecondary-800 shadow-xs w-44 ">
                        <div class="pt-2 z-30">
                            <div>
                                <div v-for="sort in productsArr" :key="sort.id"
                                    class="w-72 flex py-4 px-2 hover:bg-fePrimary-50 dark:hover:bg-feSecondary-500 cursor-pointer items-center"
                                    @click="orderingByProductsClicked(sort)">
                                    <ps-label class="ms-2"
                                        :class="sort.id == activeProductsArrId ? ' font-medium' : 'font-light'">{{
                                            $t(sort.title) }}</ps-label>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
            </ps-dropdown>
        </div>
    </div>
    <ps-loading-dialog ref="ps_loading_dialog" class='z-40' />
</template>

<script>
// Libs
import { ref, reactive, defineAsyncComponent } from 'vue';
// Components
import PsLabel from '@template1/vendor/components/core/label/PsLabel.vue';
const PsDropdown = defineAsyncComponent(() => import('@template1/vendor/components/core/dropdown/PsDropdown.vue'));
import PsIcon from '@template1/vendor/components/core/icons/PsIcon.vue';
import PsButton from '@template1/vendor/components/core/buttons/PsButton.vue';
import PsLoadingDialog from '@template1/vendor/components/core/dialog/PsLoadingDialog.vue';
// Providers
import { PsValueStore } from "@templateCore/store/modules/core/PsValueStore";
import { useProductStore } from "@templateCore/store/modules/item/ProductStore";
//Utils
import PsUtils from '../../../../../../../../../TemplateCore/utils/PsUtils';

export default {
    name: 'ItemFilterSortAndTypeSection',
    components: {
        PsLabel,
        PsDropdown,
        PsIcon,
        PsButton,
        PsLoadingDialog
    },
    props: {
        isNoPriceSettingOn : String
    },
    setup(props) {
        const psValueStore = PsValueStore();
        const itemProvider = useProductStore('list');
        const ps_loading_dialog = ref();

        const sortingArr = reactive([
            {
                id: 0,
                title: "core_fe__default",
                orderBy: "added_date",
                type: 'desc'
            },
            {
                id: 1,
                title: "A_to_Z",
                orderBy: "title",
                type: 'asc'
            },
            {
                id: 2,
                title: "Z_to_A",
                orderBy: "title",
                type: 'desc'
            }
        ]);

        if (!props.isNoPriceSettingOn) {
            sortingArr.push(
                {
                id: 3,
                title: "core_fe__high_to_low",
                orderBy: "price",
                type: 'desc'
            }
            );
            sortingArr.push(
                {
                id: 4,
                title: "core_fe__low_to_high",
                orderBy: "price",
                type: 'asc'
            }
            );
        }

        let activeSortingArrName = ref('');
        let activeSortingArrId = ref('');

        const productsArr = reactive([
            {
                id: 0,
                title: "core_fe__relevance",
                orderBy: "added_date",
                type: 'desc'
            },
            {
                id: 1,
                title: "core_fe__featured",
                orderBy: "title",
                type: 'desc'
            },
            {
                id: 2,
                title: "core_fe__recently_added",
                orderBy: "added_date",
                type: 'desc'
            },
            {
                id: 3,
                title: "core_fe__popular",
                orderBy: "item_touch_count",
                type: 'desc'
            },
            {
                id: 4,
                title: "core_fe__most_favorited",
                orderBy: "favourite_count",
                type: 'desc'
            }
        ]);

        let activeProductsArrName = ref('');
        let activeProductsArrId = ref('');

        function orderingFilterClicked(value) {
            activeSortingArrId.value = value.id;
            activeSortingArrName.value = value.title;
            itemProvider.paramHolder.orderBy = value.orderBy;
            itemProvider.paramHolder.orderType = value.type;

            loadDataList();
        }

        function orderingByProductsClicked(value) {
            activeProductsArrId.value = value.id;
            activeProductsArrName.value = value.title;
            itemProvider.paramHolder.paidStatus = value.id == "1" ? "only_paid_item" : '';
            itemProvider.paramHolder.orderBy = value.orderBy;
            itemProvider.paramHolder.orderType = value.type;

            loadDataList();
        }


        async function loadDataList(value) {
            ps_loading_dialog.value.openModal();
            PsUtils.addParamToCurrentUrl(itemProvider.getURLforListByParam(itemProvider.paramHolder));
            await itemProvider.resetProductList(psValueStore.getLoginUserId(), itemProvider.paramHolder);
            ps_loading_dialog.value.closeModal();
            //hide popup filter
            itemProvider.showPopUpFilter = false;
        }

        return {
            sortingArr,
            productsArr,
            itemProvider,
            activeSortingArrId,
            activeSortingArrName,
            activeProductsArrId,
            activeProductsArrName,
            orderingFilterClicked,
            orderingByProductsClicked,
            ps_loading_dialog
        }
    }

}

</script>
