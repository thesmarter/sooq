<template>
    <div class="w-full lg:rounded-lg sm:rounded-xl rounded-2xl sm:flex mt-4 sm:mt-0 gap-5">
        <div class="flex gap-x-5 flex-col md:flex-row gap-y-2">

                <ps-input-with-left-icon theme="dark:text-feSecondary-200" placeholderColor="" rounded="rounded" defaultBorder="bg-feSecondary-50 dark:bg-feSecondary-800 border border-feSecondary-200 hover:border-feSecondary-400 dark:border-feSecondary-200 hover:dark:border-feSecondary-50 focus:outline-none focus:border-none focus:ring-2 focus:ring-fePrimary-300 ring-fePrimary-300 placeholder-feSecondary-500 dark:placeholder-feSecondary-200" padding="h-10" v-on:keyup.enter="searchClicked"  v-model:value="searchKeyword" :placeholder="$t('dashboard__enter_keyword')" >
                    <template #icon>
                        <ps-icon textColor="text-feSecondary-500 dark:text-feSecondary-200" name="search" class='cursor-pointer' />
                    </template>
                </ps-input-with-left-icon>


                <ps-dropdown h="h-64" align="left" class='w-full md:w-52 lg:rounded-2xl rounded' @onClick="loadSubLocation()">
                    <template #select>
                        <ps-label class="lg:rounded-2xl rounded-xshadow-sm h-full">
                            <button
                            type="button"
                            class="focus:ring-2 ring-fePrimary-200 inline-flex items-center content-start justify-between w-full h-10 lg:rounded rounded-x
                            px-4  bg-feSecondary-000 text-sm leading-5 font-medium text-feSecondary-500 dark:text-feSecondary-200
                            hover:text-feSecondary-400 focus:outline-none border border-feSecondary-200 focus:border-fePrimary-500
                            focus:shadow-outline-blue active:bg-feAchromatic-100 active:text-feSecondary-600 transition
                            ease-in-out duration-150 btn-focus
                            dark:bg-feSecondary-800 dark:border-feSecondary-200"
                            aria-haspopup="true"
                            aria-expanded="true">
                            <ps-label v-if="categoryName" textColor="font-normal text-feSecondary-500 dark:text-feSecondary-200" class=" text-start"> {{categoryName}} </ps-label>
                            <ps-label v-else textColor="font-normal text-feSecondary-500 dark:text-feSecondary-200"> {{ $t('choose_categories') }} </ps-label>
                            <ps-icon textColor="text-feSecondary-400 dark:text-feSecondary-200" name="downArrow" />
                            </button>
                        </ps-label>
                    </template>
                    <template #filter>
                        <div>
                            <ps-input-with-left-icon class="bg-feSecondary-100 dark:bg-transparent shadow-none" rounded="rounded-none" theme="border-none focus:ring-0 focus:ring-transparent text-feSecondary-500 dark:text-feAchromatic-50" 
                                height="h-10" @keyup.capture="filtersubLocationUpdate(categoryStore.filterKeyword)" v-model:value="categoryStore.filterKeyword" v-bind:placeholder= "$t('search_categories')">
                                <template #icon>
                                    <ps-icon textColor="text-fePrimary-500 dark:text-feAchromatic-500" name="search" class='cursor-pointer'  />
                                </template>
                               
                                    <template #removeIcon>
                                       <div v-if="categoryStore.filterKeyword !=''">
                                        <ps-icon textColor="text-feAchromatic-500 dark:text-feAchromatic-500" @click="[categoryStore.filterKeyword = '', filtersubLocationUpdate(categoryStore.filterKeyword)]" name="close" class='cursor-pointer' />

                                       </div> 
                                </template>
                               
                            </ps-input-with-left-icon>

                        </div>
                      <hr class="bg-feSecondary-400 h-0.5 ">
                       
                    </template>

                    <template #list >

                        <div class="pt-2 z-30">
                            <div  v-if="categoryStore.loading.value == true" class='mt-4 ms-4 mb-4 flex'>
                                <ps-label-caption > {{ $t("search_for_large_screem__loading") }} </ps-label-caption>
                            </div>
                            <div v-if="categoryStore.itemList.data == null" class='w-56 flex py-4 px-2 items-center'>
                                <ps-label class="ms-2" :class="categoryId =='' ? ' text-fePrimary-700' : ''"> {{ $t('no_result_found') }} </ps-label>
                            </div>
                            <div v-else class="w-56 flex py-4 px-2 hover:bg-fePrimary-50 dark:hover:bg-feAchromatic-800 cursor-pointer items-center"  @click="categoryFilterClicked('',$t('item_list__all'))" >
                                <ps-label class="ms-2" :class="categoryId == '' ? 'font-bold text-fePrimary-700' : ''"  > All </ps-label>
                            </div>
                            <div v-for="selectData in categoryStore.itemList.data" :key="selectData.catId" class="w-56 flex py-4 px-2 hover:bg-fePrimary-50 dark:hover:bg-feAchromatic-800 cursor-pointer items-center"  @click="categoryFilterClicked(selectData.catId, selectData.catName)" >
                                <ps-label class="ms-2" :class="selectData.catId==categoryId ? 'font-bold text-fePrimary-700' : ''"  > {{selectData.catName}} </ps-label>
                            </div>
                            <div class="border-t border-feAchromatic-100 "></div>
                        </div>
                    </template>
                    <template #loadmore>

                        <div class="mb-2 w-56">
                            <div  v-if="categoryStore.itemList.data != null
                            && categoryStore.loading.value == true" class='mt-4 ms-4 flex'>
                            <ps-label-caption >{{ $t("search_for_large_screem__loading") }} </ps-label-caption>
                            </div>
                            <div v-if="categoryStore.itemList.data != null && categoryStore.isNoMoreRecord.value == false" class="">
                                <ps-label class="flex mt-4 ms-4 mb-2 underline font-bold cursor-pointer" @click="loadSubLocation(categoryId)" > {{ $t("search_for_large_screem__load_more") }} </ps-label>
                            </div>
                        </div>
                    </template>
                </ps-dropdown>

                <ps-dropdown h="h-64" :id="locationId ?? 'locaitonid'" align="left" class='w-full md:w-52 h-full lg:rounded-2xl rounded' @onClick="loadLocationList" >
                    <template #select>
                        <ps-label class="lg:rounded-2xl rounded-x h-full">
                            <button
                            type="button"
                            class="focus:ring-2 ring-fePrimary-200 inline-flex items-center content-start justify-between w-full h-10 lg:rounded rounded-x
                            px-4  bg-feSecondary-000 text-sm leading-5 font-medium text-feSecondary-500 dark:text-feSecondary-200
                            hover:text-feSecondary-400 focus:outline-none border border-feSecondary-200 focus:border-fePrimary-500
                            focus:shadow-outline-blue active:bg-feAchromatic-100 active:text-feSecondary-600 transition
                            ease-in-out duration-150 btn-focus
                            dark:bg-feSecondary-800 dark:border-feSecondary-200   "
                            aria-haspopup="true"
                            aria-expanded="true">
                            <ps-label v-if="locationName" textColor="font-normal text-feSecondary-500 dark:text-feSecondary-200" class=" text-start"> {{locationName}} </ps-label>
                            <ps-label v-else textColor="font-normal text-feSecondary-500 dark:text-feSecondary-200"> {{ $t('choose_location') }} </ps-label>
                            <ps-icon textColor="text-feSecondary-400 dark:text-feSecondary-200" name="downArrow"   />
                            </button>
                        </ps-label>
                    </template>
                    <template #filter >
                        <div>
                            <ps-input-with-left-icon class="bg-feSecondary-50 dark:bg-transparent shadow-none" rounded="rounded-none" theme="border-none  focus:ring-0 focus:ring-transparent text-feSecondary-500 dark:text-feAchromatic-50" height="h-10" 
                                @keyup.capture="filterKeywordUpate(itemLocationStore.filterKeyword)"  v-model:value="itemLocationStore.filterKeyword" v-bind:placeholder= "$t('search_location')" style="width: 14.5rem;">
                                <template #icon>
                                    <ps-icon textColor="text-fePrimary-500 dark:text-feAchromatic-500" name="search" class='cursor-pointer'  />
                                </template>
                               
                                    <template #removeIcon>
                                       <div v-if="itemLocationStore.filterKeyword !=''">
                                        <ps-icon textColor="text-feAchromatic-500 dark:text-feAchromatic-500" @click="[itemLocationStore.filterKeyword = '', filterKeywordUpate(itemLocationStore.filterKeyword)]" name="cross" />

                                       </div> 
                                </template>
                               
                                
                            </ps-input-with-left-icon>
                            

                        </div>
                      <hr class="bg-feSecondary-300 h-0.5">
                       
                    </template>
                    <template #list >

                        <div class="pt-2 z-30">
                            <div  v-if="itemLocationStore.loading.value == true" class='w-56 flex py-4 px-2 items-center'>
                                <ps-label-caption class="ms-2" > {{ $t("search_for_large_screem__loading") }} </ps-label-caption>
                            </div>
                            <div v-if="itemLocationStore.itemLocationList.data == null" class='w-56 flex py-4 px-2 items-center'>
                                <ps-label class="ms-2" :class="locationId =='' ? ' text-fePrimary-700' : ''">{{ $t('no_result_found') }} </ps-label>
                            </div>    
                            <div v-else class="w-56 flex py-4 px-2 hover:bg-fePrimary-50 dark:hover:bg-feAchromatic-800 cursor-pointer items-center"  @click="locationFilterClicked('',$t('item_list__all'),'','')" >
                                <ps-label class="ms-2" :class="locationId =='' ? ' font-bold text-fePrimary-700' : ''"  > All </ps-label>
                            </div>
                            <div v-for="selectData in itemLocationStore.itemLocationList.data" :key="selectData.id" class="w-56 flex py-4 px-2 hover:bg-fePrimary-50 dark:hover:bg-feAchromatic-800 cursor-pointer items-center"  @click="locationFilterClicked(selectData.id,selectData.name,selectData.lat,selectData.lng)" >
                                <ps-label class="ms-2" :class="selectData.id==locationId ? ' font-bold text-fePrimary-700' : ''"  > {{selectData.name}} </ps-label>
                            </div>
                            <div class="border-t border-feAchromatic-100 "></div>
                        </div>
                    </template>
                    <template #loadmore>

                        <div class=" w-56">
                            <div  v-if="itemLocationStore.itemLocationList.data != null
                            && itemLocationStore.loading.value == true" class='py-4 px-2 flex'>
                            <ps-label-caption >{{ $t("search_for_large_screem__loading") }} </ps-label-caption>
                            </div>
                            <div v-if="itemLocationStore.itemLocationList.data != null && itemLocationStore.isNoMoreRecord.value == false" class="">
                                <ps-label class="flex py-4 px-2 underline font-bold cursor-pointer ms-2" @click="loadLocationList" > {{ $t("search_for_large_screem__load_more") }} </ps-label>
                            </div>                  
                        </div>
                    </template>
                </ps-dropdown>

                <div class="flex justify-center">
                    <ps-button v-bind:title="$t('search_for_large_screem__search')" class="px-8 py-2 md:px-5 lg:px-8" rounded="rounded" theme="bg-fePrimary-500" @click="searchClicked()">
                        <ps-label class="text-sm font-medium" textColor="text-feAchromatic-50 dark:text-feSecondary-50"> {{ $t("search_for_large_screem__search") }} </ps-label>
                    </ps-button>
                </div>


        </div>
    </div>

    <ps-loading-dialog ref="ps_loading_dialog"  :isClickOut='false'/>
</template>

<script lang="ts">

// Libs
import {  ref } from 'vue';

// import router from "@template1/router";
import { router } from '@inertiajs/vue3';
// Components
import PsButton from '@template1/vendor/components/core/buttons/PsButton.vue';
import PsLabel from '@template1/vendor/components/core/label/PsLabel.vue';
import PsLabelCaption from '@template1/vendor/components/core/label/PsLabelCaption.vue';
import PsInputWithLeftIcon from '@template1/vendor/components/core/input/PsInputWithLeftIcon.vue';
import PsIcon from '@template1/vendor/components/core/icons/PsIcon.vue';
import PsDropdown from '@template1/vendor/components/core/dropdown/PsDropdown.vue';
import PsInputWithRightIcon from '@template1/vendor/components/core/input/PsInputWithRightIcon.vue';
import PsLoadingDialog from '@template1/vendor/components/core/dialog/PsLoadingDialog.vue';

// Providers
import { useItemLocationStoreState } from '@templateCore/store/modules/itemlocation/ItemLocationStore';
import { useCategoryStoreState } from '@templateCore/store/modules/category/CategoryStore';
import { useProductStore } from '@templateCore/store/modules/item/ProductStore';
import { PsValueStore } from "@templateCore/store/modules/core/PsValueStore";
import AppInfoParameterHolder from '@templateCore/object/holder/AppInfoParameterHolder';
import { usePsAppInfoStoreState } from '@templateCore/store/modules/appinfo/AppInfoStore';
import PsRouteLink from '@template1/vendor/components/core/link/PsRouteLink.vue';
import LocationParameterHolder from '@templateCore/object/holder/LocationParameterHolder';
import CategoryListParameterHolder from '@templateCore/object/holder/CategoryListParameterHolder';

import { trans } from 'laravel-vue-i18n';
export default {
    name : "SearchForLargeScreenView",
    components: {
        PsButton,
        PsLabel,
        PsLabelCaption,
        PsInputWithLeftIcon,
        PsIcon,
        PsDropdown,
        PsInputWithRightIcon,
        PsLoadingDialog,
        PsRouteLink,
    },
    props : {
        data : {
            type: String,
            default: '0'
        },
    },
    emits: ['clicklocation','onSubclick'],
    setup( props, context ) {

        const psValueStore = PsValueStore();
        const ps_loading_dialog = ref();

        let timer =0;

        // psValueStore.updateExpiryTimeForAction(); // only call from dashboard
        const appInfoStore = usePsAppInfoStoreState();
        const searchKeyword = ref("");
        const locationName = ref();
        const locationId = ref(psValueStore.locationId);
        const sublocationId = ref();
        const sublocationName = ref();
        const categoryId = ref();
        const categoryName = ref();

        

        const itemLocationStore = useItemLocationStoreState();
        const categoryStore = useCategoryStoreState();

        const itemStore = useProductStore();


        const loginUserId = psValueStore.getLoginUserId();

        const paramHolder = new LocationParameterHolder().getDefaultParameterHolder();
        const subparamHolder = new CategoryListParameterHolder().CategoryListParameterHolder();

        async function searchClicked() {

            itemStore.paramHolder.searchTerm = searchKeyword.value;
            // itemStore.paramHolder.itemLocationName = locationName.value.toString();
            itemStore.paramHolder.itemLocationId = locationId.value;
            itemStore.paramHolder.itemLocationName = locationName.value;
            itemStore.paramHolder.catId = categoryId.value;
            itemStore.paramHolder.catName = categoryName.value;
            itemStore.paramHolder.itemLocationTownship = '';
            // itemStore.paramHolder.itemLocationTownship = psValueStore.subLocationId;
            // itemStore.paramHolder.itemLocationName = psValueStore.locationName;
            // itemStore.paramHolder.itemLocationTownshipName = psValueStore.subLocationName;
            itemStore.paramHolder.status = '1';
            const params = itemStore.paramHolder.getUrlParamsAndQuery();

            router.get(route('fe_item_list', params['query']));

        }

        function loadLocationList() {

            itemLocationStore.loadItemLocationList(psValueStore.getLoginUserId(), paramHolder);
        }

        function loadSubLocation() {
            categoryStore.loadItemList(psValueStore.getLoginUserId(), subparamHolder);
        }

        async function locationFilterClicked(id,name,lat,lng) {

            locationId.value = id;
            locationName.value = name;
            context.emit('clicklocation', {id: id,name: name,lat: lat,lng: lng});

        }


        async function categoryFilterClicked(catId,catName){
            categoryId.value = catId;
            categoryName.value = catName;
            context.emit('onSubclick', {id: locationId.value,name: locationName.value,subId: catId,subName: catName});
        }

        function filterKeywordUpate(value) {
            clearTimeout(timer);

            timer = window.setTimeout(async () => {
            await itemLocationStore.filterKeywordUpate(value,loginUserId, paramHolder);

            }, 400); 
            
        }

        function filtersubLocationUpdate(value) {
            subparamHolder.keyword = value;
            clearTimeout(timer);

            timer = window.setTimeout(async () => {
            await categoryStore.resetCategoryList(loginUserId,subparamHolder);

            }, 400); 
            
        }

        const appInfoParameterHolder = new AppInfoParameterHolder();
        appInfoParameterHolder.userId = loginUserId;
        // appInfoStore.loadAppInfo(appInfoParameterHolder);

        return {
            PsValueStore,
            itemLocationStore,
            categoryStore,
            // itemProvider,
            searchKeyword,
            searchClicked,
            locationName,
            locationId,
            sublocationId,
            sublocationName,
            categoryId,
            categoryName,
            loadLocationList,
            locationFilterClicked,
            ps_loading_dialog,
            filterKeywordUpate,
            // subLocationFilterClicked,
            loadSubLocation,
            appInfoStore,
            filtersubLocationUpdate,
            categoryFilterClicked,
            timer
        };
    },
};
</script>

