<template>
    <Head :title="$t('category_list__title')" />
    <ps-content-container>
        <template #content>
            <div class="sm:mt-32 lg:mt-36 mt-28">
                <div class="flex flex-col sm:flex-row">
                    <div class="flex flex-row sm:mt-0 mt-6">
                        <ps-breadcrumb-2 :items="breadcrumb" /></div>
                </div>

                <div class="relative mt-4 sm:mt-10 flex flex-row">
                    <!-- Filter For Normal and Large Screen -->
                    <div class='w-64 me-7 hidden sm:flex flex-col shadow-md dark:bg-feSecondary-800 h-full p-4 rounded-lg'>
                        <vendor-filter-clear/>

                        <vendor-filter-sort-by/>

                        <vendor-filter-view-by/>

                        <!-- Listing Start-->
                        <div class="w-full flex flex-col">
                            <ps-label class="mt-6 lg:mb-2 mb-1 lg:text-base font-medium text-sm"
                                textColor="text-feSecondary-800 dark:text-feSecondary-300"> {{ $t("listings") }} </ps-label>
                            
                            <vendor-filter-high-low :options="priorityOptions" v-model="priority" @change="handleRadioChange"/>
                        </div>    
                        <!-- Listing end-->
                    </div>

                    <div class='w-full'>

                        <div class="flex flex-row justify-end items-center">
                            <vendor-filter-search/>
                            <div class="sm:hidden ml-2">
                                <ps-button hover="" focus="" colors="bg-feAchromatic-50 dark:bg-feAchromatic-900 text-feSecondary-800 dark:text-feSecondary-200 "
                                    class="me-2 sm:hidden" padding="px-4 py-2" border="border border-1 border-feSecondary-200" @click="toggleShowFilter"
                                    :disabled="false">
                                    <ps-icon textColor="text-feSecondary-500 dark:text-feSecondary-200" name="filter" class='cursor-pointer me-1' />
                                    {{ $t("filter") }}
                                </ps-button>
                            </div>
                        </div>

                        <vendor-filter-vertical-list :loginUserId="loginUserId"/>
                    </div>

                    <div v-if="vendorSearchStore.showPopUpFilter" class='flex justify-between sm:hidden flex-col absolute top-10 right-[40px]' >
                        <transition @enter="enter" @leave="leave">
                            <div class='flex flex-col w-68 p-8 h-auto bg-feAchromatic-50 dark:bg-feSecondary-800 shadow-xl rounded-lg'>
                                <vendor-filter-clear/>

                                <vendor-filter-sort-by/>

                                <vendor-filter-view-by/>

                                <!-- Listing Start-->
                                <div class="w-full flex flex-col">
                                    <ps-label class="mt-6 lg:mb-2 mb-1 lg:text-base font-medium text-sm"
                                        textColor="text-feSecondary-800 dark:text-feSecondary-300"> {{ $t("Listings") }} </ps-label>

                                    <vendor-filter-high-low :options="priorityOptions" v-model="priority" @change="handleRadioChange"/>
                                </div>    
                                <!-- Listing end-->
                            </div>
                        </transition>
                    </div>
                    
                </div>
            </div>
            <ps-loading-dialog ref="ps_loading_dialog" class='z-40'/>
        </template>
    </ps-content-container>
</template>

<script lang="ts">
import { Head } from '@inertiajs/vue3';
import PsContentContainer from '@template1/vendor/components/layouts/container/PsContentContainer.vue';
import { usePsAppInfoStoreState } from '@templateCore/store/modules/appinfo/AppInfoStore';
import PsConst from '@templateCore/object/constant/ps_constants';

import {  ref,onMounted ,reactive} from 'vue';
import PsBreadcrumb2 from "@template1/vendor/components/core/breadcrumbs/PsBreadcrumb2.vue";
import PsButton from '@template1/vendor/components/core/buttons/PsButton.vue';
import PsLabel from '@template1/vendor/components/core/label/PsLabel.vue';
import PsLabelHeader4 from '@template1/vendor/components/core/label/PsLabelHeader4.vue';
import PsLoadingDialog from '@template1/vendor/components/core/dialog/PsLoadingDialog.vue';
import PsFrontendLayout from '@template1/vendor/components/layouts/container/PsFrontendLayout.vue';
import PsNoResult from "@template1/vendor/components/modules/list/PsNoResult.vue";

import { PsValueStore } from "@templateCore/store/modules/core/PsValueStore";
import { trans } from 'laravel-vue-i18n';
import { useVendorSearchStoreState } from "@templateCore/store/modules/vendor/VendorSearchStore";

import VendorFilterHighLow from './components/VendorFilterHighLow.vue';
import VendorFilterSortBy from './components/VendorFilterSortBy.vue';
import VendorFilterClear from './components/VendorFilterClear.vue';
import VendorFilterViewBy from './components/VendorFilterViewBy.vue';
import VendorFilterSearch from './components/VendorFilterSearch.vue';
import VendorFilterVerticalList from './components/VendorFilterVerticalList.vue';

export default {
    name: 'VendorFilter',
    components : {
        PsButton,
        PsContentContainer,
        PsBreadcrumb2,
        PsLabel,
        PsLabelHeader4,
        PsNoResult,
        PsLoadingDialog,
        Head,
        VendorFilterHighLow,
        VendorFilterSortBy,
        VendorFilterClear,
        VendorFilterViewBy,
        VendorFilterSearch,
        VendorFilterVerticalList
    },
    // props: ['mobileSetting'],
    layout: PsFrontendLayout,
    data() {
        return {
            priorityOptions: ['High', 'Low'],
            priority: 'High', // Set the default selected option
        };
    },
    setup (){

        const appInfoStore = usePsAppInfoStoreState();  
        const vendorSearchStore = useVendorSearchStoreState();

        const ps_loading_dialog = ref();
        const loading = ref(false);
        const psValueStore = PsValueStore();
        const loginUserId = psValueStore.getLoginUserId();

        function handleRadioChange(newSelectedValue) {
            // high or low = newSelectedValue
        console.log(newSelectedValue);
        }

        const initial = ref(true);

        setTimeout(async ()=>{

            await loadDataList();

        },1000);

        function toggleShowFilter() {
            vendorSearchStore.showPopUpFilter = !vendorSearchStore.showPopUpFilter;
        }

        async function loadDataList() {

            loading.value = true;

            ps_loading_dialog.value.closeModal();
            loading.value = false;
            initial.value = false;
        }

        const arrowIcon = ref("downArrow");
        function enter(el, done) {
            arrowIcon.value = "upArrow";
        }

        function leave(el, done) {
            arrowIcon.value = "downArrow";
        }

        onMounted(() => {
            
            if(initial.value == true && vendorSearchStore.vendorSearchList?.data == null){
                ps_loading_dialog.value.openModal();
            }
        })

        return {
            PsConst,
            loginUserId,
            enter,
            leave,
            vendorSearchStore,
            handleRadioChange,
            toggleShowFilter,
            ps_loading_dialog,
            loading,
            initial,
            appInfoStore
        }
    },
    computed: {
        breadcrumb() {
            return [
                {
                    label: trans('item_list__home'),
                    url: route('dashboard')
                },
                {
                    label: trans('latest_vendor'),
                    color: "text-fePrimary-500"
                }
            ]
        }
    },

}
</script>
