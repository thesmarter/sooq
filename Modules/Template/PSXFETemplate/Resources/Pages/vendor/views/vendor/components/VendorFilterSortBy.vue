<template>
    <ps-label class="mt-4 mb-2 lg:text-base font-medium text-sm"
                            textColor="text-feSecondary-800 dark:text-feSecondary-300"> {{ $t("core_fe__sort_by") }} </ps-label>
    <ps-dropdown horizontalAlign="right" h="h-auto" class="h-10 w-auto">
        <template #select>
            <ps-dropdown-select placeholderLang='item_list__all' border="border dark:border-feSecondary-200"
                :selectedValue="vendorSearchStore.sortByValue ? $t(vendorSearchStore.sortByValue) : $t('core_fe__default')" />
        </template>
        <template #list>
            <div class="rounded-md shadow-xs w-56">
                <div class="pt-2 z-30">
                    <div>
                        <div v-for="sort in sortingArr" :key="sort.id"
                            class="w-72 flex py-4 px-2 hover:bg-fePrimary-50 dark:hover:bg-feSecondary-500 cursor-pointer items-center"
                            @click="orderingFilterClicked(sort)">
                            <ps-label v-if="(sort.id == 1 || sort.id == 2)" class="ms-2"
                                :class="sort.id == activeSortingArrId ? ' font-medium' : 'font-light'">{{
                                    $t("item_list__name") }}:
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
</template>

<script>
import {  ref ,reactive} from 'vue';
import { PsValueStore } from "@templateCore/store/modules/core/PsValueStore";
import { useVendorSearchStoreState } from "@templateCore/store/modules/vendor/VendorSearchStore";

import PsDropdown from '@template1/vendor/components/core/dropdown/PsDropdown.vue';
import PsDropdownSelect from '@template1/vendor/components/core/dropdown/PsDropdownSelect.vue';
import PsLabel from '@template1/vendor/components/core/label/PsLabel.vue';

export default ({
    name : "VendorFilterSortBy",
    components : {
        PsLabel,
        PsDropdown,
        PsDropdownSelect
    },
    setup() {
        const vendorSearchStore = useVendorSearchStoreState();
        const psValueStore = PsValueStore();
        const loginUserId = psValueStore.getLoginUserId();
        
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
                orderBy: "name",
                type: 'asc'
            },
            {
                id: 2,
                title: "Z_to_A",
                orderBy: "name",
                type: 'desc'
            }
        ]);

        let activeSortingArrName = ref('');
        let activeSortingArrId = ref();

        function orderingFilterClicked(value) {
            activeSortingArrId.value = value.id;
            vendorSearchStore.sortByValue = value.title;

            loadDataList(value.id);
            vendorSearchStore.showPopUpFilter = false;
        }

        function loadDataList(id){
            vendorSearchStore.paramHolder.ownerUserId = loginUserId;
            if(id == 1){
                vendorSearchStore.paramHolder.orderType = 'asc';
                vendorSearchStore.getVendorSearchList(loginUserId,vendorSearchStore.paramHolder);
            }else if(id == 2){
                vendorSearchStore.paramHolder.orderType = 'desc';
                vendorSearchStore.getVendorSearchList(loginUserId,vendorSearchStore.paramHolder);
            }

            vendorSearchStore.showPopUpFilter = false;

        }
        return {
            vendorSearchStore,
            sortingArr,
            activeSortingArrId,
            activeSortingArrName,
            orderingFilterClicked
        }
    },
})
</script>
