<template>
    <ps-label class="mt-4 mb-2 lg:text-base font-medium text-sm"
                            textColor="text-feSecondary-800 dark:text-feSecondary-300"> {{ $t("view_by") }} </ps-label>
    <ps-dropdown horizontalAlign="right" h="h-auto" class="h-10 w-auto">
        <template #select>
            <ps-dropdown-select placeholderLang='item_list__all' border="border dark:border-feSecondary-200"
                :selectedValue="activeViewArrName ? $t(activeViewArrName) : $t('Latest Joined')" />
        </template>
        <template #list>
            <div class="rounded-md shadow-xs w-56">
                <div class="pt-2 z-30">
                    <div>
                        <div v-for="sort in ViewArr" :key="sort.id"
                            class="w-72 flex py-4 px-2 hover:bg-fePrimary-50 dark:hover:bg-feSecondary-500 cursor-pointer items-center"
                            @click="orderingByViewClicked(sort)">
                            <ps-label class="ms-2"
                                :class="sort.id == activeViewArrId ? ' font-medium' : 'font-light'">{{ $t(sort.title) }}</ps-label>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </ps-dropdown>
</template>

<script>
import {  ref ,reactive} from 'vue';

import PsDropdown from '@template1/vendor/components/core/dropdown/PsDropdown.vue';
import PsDropdownSelect from '@template1/vendor/components/core/dropdown/PsDropdownSelect.vue';
import PsLabel from '@template1/vendor/components/core/label/PsLabel.vue';

export default ({
    name : "VendorFilterViewBy",
    components : {
        PsLabel,
        PsDropdown,
        PsDropdownSelect
    },
    setup() {
        const ViewArr = reactive([
            {
                id: 0,
                title: "core_fe__relevance",
                orderBy: "added_date",
                type: 'desc'
            },
            {
                id: 1,
                title: "Top Engaged",
                orderBy: "added_date",
                type: 'desc'
            },
            {
                id: 2,
                title: "Latest Joined",
                orderBy: "added_date",
                type: 'desc'
            },
        ]);

        let activeViewArrName = ref('');
        let activeViewArrId = ref();

        function orderingByViewClicked(value) {
            activeViewArrId.value = value.id;
            activeViewArrName.value = value.title;

            loadDataList();
        }

        function loadDataList(){
            // vendorSearchStore.showPopUpFilter = false;
        }
        return {
            ViewArr,
            activeViewArrId,
            activeViewArrName,
            orderingByViewClicked,
        }
    },
})
</script>
