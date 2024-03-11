<template>
    <Head :title="$t('category_list__title')" />
    <ps-content-container>
        <template #content>
            <div class="sm:mt-32 lg:mt-36 mt-28">
                <!--Start search flex mobile and desktop -->
                <div class="flex flex-col sm:flex-row sm:justify-between items-start sm:items-center">
                    <div class="mb-6 sm:mb-0">
                        <ps-breadcrumb-2 :items="breadcrumb" class="" />
                    </div>
                    <!-- start keyword search -->
                    <div class="flex justify-end content-center items-center w-full">
                        <ps-input-with-right-icon v-on:keyup.enter="searchClicked" v-model:value="categoryStore.paramHolder.keyword" class="sm:w-80 w-full" padding="py-2 px-4 h-10" v-bind:placeholder="$t('category__fe_search')" >
                            <template #icon>
                                
                                <ps-icon v-if="categoryStore.paramHolder.keyword == ''" name="search" class='cursor-pointer'/>
                                <ps-icon v-else @click="[categoryStore.paramHolder.keyword = '',searchClicked()]" name="cross" class='cursor-pointer'/>
                            </template>
                        </ps-input-with-right-icon>



                        <ps-dropdown horizontalAlign="right" h="h-auto" class="ms-4" rounded="rounded-lg" >
                            <template #select>
                                <ps-dropdown-select  class="h-10 w-10 sm:w-auto sm:ps-4 ps-2.5" border="border border-1 border-feSecondary-200 dark:border-feSecondary-400" text="text-sm font-medium text-feAchromatic-800 dark:text-feAchromatic-100 hidden sm:inline" iconTheme="text-feAchromatic-800 ms-2 hidden sm:inline" leftIcon="filter" leftIconTheme="inline sm:hidden" :selectedValue="activeSortingName" :placeholder="$t('sort_by')" />
                            </template>
                            <template #list>
                                <div
                                    class="bg-feAchromatic-50 dark:bg-feSecondary-800 shadow-xs w-44"
                                    role="menu"
                                    aria-orientation="vertical"
                                    aria-labelledby="options-menu">
                                    <div v-for="sort in currentsorting" :key="sort.id" class="flex py-4 px-2 hover:bg-fePrimary-50 dark:hover:bg-feSecondary-500 cursor-pointer items-center"  @click="sortingFilterClicked(sort)" >
                                            <span class="ms-2 text-feSecondary-800 dark:text-feSecondary-200" :class="sort.id==activeSortingId ? 'font-semibold' : ''"  >{{ $t("core__fe_name") }}: {{sort.name}} </span>
                                    </div>
                                </div>
                            </template>
                        </ps-dropdown>
                    </div>
                    <!-- end keyword search -->
                </div>
                <!-- End search flex mobile and desktop -->
                <div class="flex flex-row mb-8 mt-8">
                    <div class='w-full' >
                        <div v-if="categoryStore.itemList?.data != null" class="grid grid-cols-4 sm:grid-cols-4 md:grid-cols-9 lg:grid-cols-12 xl:gap-6 gap-4 sm:gap-6 ">
                            <!-- Column -->
                            <div class="w-full col-span-4 sm:col-span-2 md:col-span-3 lg:col-span-4 xl:col-span-3" v-for="category in categoryStore.itemList.data" :key="category.catId">
                                <ps-route-link
                                    :to="{name: appInfoStore?.isShowSubCategory() ? 'fe_sub_category' : 'fe_item_list',
                                    query: {
                                        cat_id: category.catId,
                                        cat_name: category.catName,
                                        status: 1} }"
                                    @click="updateCategoryTouchCount(category.catId)">
                                    <category-horizontal-item  :category="category" />
                                </ps-route-link>
                            </div>
                            <!-- END Column -->
                        </div>

                        <ps-no-result v-if="categoryStore.loading.value == false && categoryStore.itemList?.data == null && initial == false" @onClick="loadMore"  />

                        <ps-button v-if="categoryStore.loading.value == false && initial == false" class="font-medium mx-auto mt-6" @click="loadMore" :class="categoryStore.isNoMoreRecord.value ? 'hidden' : ''"> {{ $t("category_list__load_more") }} </ps-button>
                        <ps-button v-else-if="initial == false" class="font-medium mx-auto mt-8" @click="loadMore" :disabled="true"> {{ $t("category_list__loading") }} </ps-button>
                        <!-- <div v-if="loading">
                            refreshing...
                        </div> -->
                    </div>
                </div>
            </div>
            <ps-loading-dialog ref="ps_loading_dialog" class='z-40' />
        </template>
    </ps-content-container>
</template>

<script lang="ts">
import { Head } from '@inertiajs/vue3';
import PsContentContainer from '@template1/vendor/components/layouts/container/PsContentContainer.vue';
import { useCategoryStoreState } from "@templateCore/store/modules/category/CategoryStore";
import { useTouchCountStoreState } from '@templateCore/store/modules/category/TouchCountStore';
import { usePsAppInfoStoreState } from '@templateCore/store/modules/appinfo/AppInfoStore';
// Holders
import AppInfoParameterHolder from '@templateCore/object/holder/AppInfoParameterHolder';
import TouchCountParameterHolder from '@templateCore/object/holder/TouchCountParameterHolder';

import {  ref,onMounted } from 'vue';
import PsRouteLink from '@template1/vendor/components/core/link/PsRouteLink.vue';
import PsBreadcrumb2 from '@template1/vendor/components/core/breadcrumbs/PsBreadcrumb2.vue';
import PsIcon from '@template1/vendor/components/core/icons/PsIcon.vue';

import PsButton from '@template1/vendor/components/core/buttons/PsButton.vue';
import PsLabel from '@template1/vendor/components/core/label/PsLabel.vue';
import PsInput from '@template1/vendor/components/core/input/PsInput.vue';
import PsDropdown from '@template1/vendor/components/core/dropdown/PsDropdown.vue';
import PsDropdownSelect from '@template1/vendor/components/core/dropdown/PsDropdownSelect.vue';
import PsLabelHeader4 from '@template1/vendor/components/core/label/PsLabelHeader4.vue';
import CategoryHorizontalItem from '@template1/vendor/components/modules/category/CategoryHorizontalItem.vue';
import PsConst from '@templateCore/object/constant/ps_constants';
import PsLoadingDialog from '@template1/vendor/components/core/dialog/PsLoadingDialog.vue';
import PsFrontendLayout from '@template1/vendor/components/layouts/container/PsFrontendLayout.vue';
import { PsValueStore } from "@templateCore/store/modules/core/PsValueStore";
import PsInputWithRightIcon from '@template1/vendor/components/core/input/PsInputWithRightIcon.vue';
import PsNoResult from "@template1/vendor/components/modules/list/PsNoResult.vue";
import { trans } from 'laravel-vue-i18n';
export default {
    name: 'CategoryListView',
    components : {
        PsButton,
        PsContentContainer,
        PsBreadcrumb2,
        PsLabel,
        PsLabelHeader4,
        CategoryHorizontalItem,
        PsRouteLink,
        PsIcon,
        PsInput,
        PsNoResult,
        PsDropdown,
        PsDropdownSelect,
        PsLoadingDialog,
        PsInputWithRightIcon,
        Head
    },
    props: ['mobileSetting'],
    layout: PsFrontendLayout,
    setup (props){

        const categoryStore = useCategoryStoreState('cat');
        const appInfoStore = usePsAppInfoStoreState();  
        const appInfoParameterHolder = new AppInfoParameterHolder();       


        categoryStore.limit = props.mobileSetting?.default_loading_limit ?? 12;

        const ps_loading_dialog = ref();
        const loading = ref(false);
        let activeSortingId = ref('');
        let activeSortingName = ref('');
        const psValueStore = PsValueStore();
        const loginUserId = psValueStore.getLoginUserId();
        const touchCountStore = useTouchCountStoreState();
        const touchCountHolder = new TouchCountParameterHolder();
        const currentsorting = [
            {
                id:"0",
                orderBy:"name",
                orderType:PsConst.FILTERING__ASC,
                name:"A to Z"
            },
            {
                id:"1",
                orderBy:"name",
                orderType:PsConst.FILTERING__DESC,
                name:"Z to A"
            }
        ];
        const initial = ref(true);

        setTimeout(async ()=>{

            await loadDataList();

        },1000);

        function sortingFilterClicked(value) {
            activeSortingId.value = value.id;
            activeSortingName.value = value.name;
            categoryStore.paramHolder.orderBy = value.orderBy;
            categoryStore.paramHolder.orderType = value.orderType;
            loadDataList();

        }

        function searchClicked() {
            loadDataList();
        }

        async function loadDataList() {

            loading.value = true;

            await categoryStore.resetCategoryList(loginUserId, categoryStore.paramHolder);

            ps_loading_dialog.value.closeModal();
            loading.value = false;
            initial.value = false;
        }


        function loadMore() {
            categoryStore.loadItemList(loginUserId, categoryStore.paramHolder);
        }

        async function updateCategoryTouchCount(catId){
            touchCountHolder.typeName = 'category';
            touchCountHolder.typeId = catId;
            touchCountHolder.userId = loginUserId;
            await touchCountStore.postTouchCount(loginUserId, touchCountHolder);
        }
        onMounted(() => {
            appInfoParameterHolder.userId = loginUserId;
            appInfoStore.loadAppInfo(appInfoParameterHolder);

            if(initial.value == true && categoryStore.itemList?.data == null){
                ps_loading_dialog.value.openModal();
            }
        })

        return {
            categoryStore,
            loadMore,
            currentsorting,
            sortingFilterClicked,
            activeSortingId,
            activeSortingName,
            ps_loading_dialog,
            searchClicked,
            loading,
            updateCategoryTouchCount,
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
                    label: trans('category_list__title'),
                    color: "text-fePrimary-500"
                }
            ]
        }
    },

}
</script>
