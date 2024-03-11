<template>
    <ps-content-container>
        <template #content>
            <div class="sm:mt-32 lg:mt-36 mt-28">
                <div class="flex flex-col sm:flex-row">
                    <div class="flex flex-row sm:mt-0 mt-6">
                        <ps-breadcrumb-2 :items="breadcrumb" /></div>
                    <!-- <item-filter-title :query="query" /> -->
                </div>

                <div class="relative mt-4 sm:mt-10 flex flex-row">
                    <!-- Filter For Normal and Large Screen -->
                    <div class='w-64 me-7 hidden sm:flex flex-col shadow-md dark:bg-feSecondary-800 h-full p-4 rounded-lg'>
                        <clear-button />
                        <category-drop-down/>
                        <!-- <sub-category-drop-down
                            v-if="appInfoStore?.isShowSubCategory()" /> -->
                        <discount-section
                            v-if="appInfoStore?.isShowDiscount()" />
                        <stock-section />
                        <price-range-input 
                            v-if="!appInfoStore?.isNoPriceSettingOn()" />
                        <custom-field-section />
                        <city-dropdown />
                        <township-dropdown
                            v-if="appInfoStore?.isShowSubLocation()" />
                        <pick-location-button
                            v-if="appInfoStore?.isFilterLocationOn()" />
                  
                    </div>
                    <!-- End Filter For Normal and Large Screen -->
                    <!-- Content -->
                    <div class='w-full'>

                        <div class="flex flex-row justify-end">
                            <!-- <div class=" w-34">
                                <ps-label >Search results</ps-label>
                                <ps-label v-if="itemProvider.itemList.data ==null">loading</ps-label>
                                
                                <ps-label v-else>{{itemProvider.itemList.data.length}}items found</ps-label>
                            </div> -->
                            
                            <div class="flex flex-row ">
                                <keyword-input class="sm:block hidden mx-3 " />
                            <div class="sm:block hidden ">
                                <sort-and-type-section 
                            :isNoPriceSettingOn="appInfoStore?.isNoPriceSettingOn()"/>
                            </div>
                            <mobile-filter-button />
                            </div>
                           
                        </div>
                       
                            <keyword-input class="sm:hidden mb-4 mt-3 sm:mb-0" />
                         <div class="sm:hidden block ">
                                <sort-and-type-section 
                            :isNoPriceSettingOn="appInfoStore?.isNoPriceSettingOn()"/>
                            </div>
                        <item-vertical-list :initial="initial" />

                    </div>
                    <!-- End Content -->

                    <!-- Filter For Small Screen -->
                    <div v-if="itemProvider.showPopUpFilter" class='flex justify-between sm:hidden flex-col absolute top-10 right-[40px]' >
                        <transition @enter="enter" @leave="leave">
                            <div class='flex flex-col w-68 p-8 h-auto bg-feAchromatic-50 dark:bg-feSecondary-800 shadow-xl rounded-lg'>
                                <clear-button />
                                <category-drop-down class="mb-12" />
                                <!-- <sub-category-drop-down
                                    v-if="appInfoStore?.isShowSubCategory()" /> -->
                                <discount-section
                                    v-if="appInfoStore?.isShowDiscount()" />
                                <stock-section
                                    :spaceWrap="false" />
                                <price-range-input 
                                    v-if="!appInfoStore?.isNoPriceSettingOn()" />
                                <custom-field-section />
                                <city-dropdown />
                                <township-dropdown 
                                    v-if="appInfoStore?.isShowSubLocation()" />
                                <pick-location-button
                                    v-if="appInfoStore?.isFilterLocationOn()" />
                                <!-- <apply-button /> -->
                            </div>
                        </transition>
                    </div>
                    <!-- End Filter For Small Screen -->

                </div>
            </div>
            <!-- google adsense desktop view -->
            <div class="mx-auto">
                <ps-ad-sense adFormat="horizontal"></ps-ad-sense>
            </div>

            <ps-loading-dialog ref="ps_loading_dialog" class='z-40' />

        </template>
    </ps-content-container>
</template>

<script>
// Libs
import { onMounted, onUnmounted, ref} from 'vue';
import { trans } from 'laravel-vue-i18n';
//Componets
import PsContentContainer from '@template1/vendor/components/layouts/container/PsContentContainer.vue';
import PsLoadingDialog from '@template1/vendor/components/core/dialog/PsLoadingDialog.vue';
import PsAdSense from '@template1/vendor/components/core/adsense/PsAdSense.vue';
import PsFrontendLayout from '@template1/vendor/components/layouts/container/PsFrontendLayout.vue';
import PsBreadcrumb2 from "@template1/vendor/components/core/breadcrumbs/PsBreadcrumb2.vue";
import CategoryDropDown from './components/ItemFilterCategoryDropdown.vue';
import SubCategoryDropDown from './components/ItemFilterSubCategoryDropdown.vue';
import DiscountSection from './components/ItemFilterDiscountSection.vue';
import StockSection from './components/ItemFilterStockSection.vue';
import PriceRangeInput from './components/ItemFilterPriceRangeInput.vue';
import CustomFieldSection from './components/ItemFilterCustomFieldSection.vue';
import CityDropdown from './components/ItemFilterCityDropdown.vue';
import TownshipDropdown from './components/ItemFilterTownshipDropdown.vue';
import PickLocationButton from './components/ItemFilterPickLocationButton.vue';
import ItemVerticalList from './components/ItemFilterItemVerticalList.vue';
import SortAndTypeSection from './components/ItemFilterSortAndTypeSection.vue';
import ItemFilterTitle from './components/ItemFilterTitle.vue';
import KeywordInput from './components/ItemFilterKeywordInput.vue';
import MobileFilterButton from './components/ItemFilterMobileFilterButton.vue';
import ClearButton from './components/ItemFilterClearButton.vue';
import PsLabel from '@template1/vendor/components/core/label/PsLabel.vue';

// Models
import { useProductStore } from "@templateCore/store/modules/item/ProductStore";
import { PsValueStore } from '@templateCore/store/modules/core/PsValueStore';
import { usePsAppInfoStoreState } from '@templateCore/store/modules/appinfo/AppInfoStore';
// Params Holders
import PsConst from '@templateCore/object/constant/ps_constants';
import ProductParameterHolder from '@templateCore/object/holder/ProductParameterHolder';
import AppInfoParameterHolder from '@templateCore/object/holder/AppInfoParameterHolder';
//Utils
import PsUtils from '../../../../../../../../TemplateCore/utils/PsUtils';

export default {
    name: "ItemListView",
    components: {
        PsBreadcrumb2,
        PsContentContainer,
        PsLoadingDialog,
        PsAdSense,
        CategoryDropDown,
        SubCategoryDropDown,
        DiscountSection,
        StockSection,
        PriceRangeInput,
        CustomFieldSection,
        CityDropdown,
        TownshipDropdown,
        PickLocationButton,
        ItemVerticalList,
        SortAndTypeSection,
        ItemFilterTitle,
        KeywordInput,
        MobileFilterButton,
        ClearButton,
        PsLabel
    },
    layout: PsFrontendLayout,
    props: ['query'],
    setup(props) {
        /**
         * Init Providers & Refs
         */
        const itemProvider = useProductStore('list');
        const appInfoStore = usePsAppInfoStoreState();
        const psValueStore = PsValueStore();
        const ps_loading_dialog = ref();

        const initial = ref(true);
        const appInfoParameterHolder = new AppInfoParameterHolder();
        itemProvider.paramHolder.parseParamsAndQuery(props.query);

        onMounted(async () => {
         
            appInfoParameterHolder.userId = psValueStore.getLoginUserId();
            appInfoStore.loadAppInfo(appInfoParameterHolder);

            setTimeout(async () => {
                // console.log(window.popStateDetected);
                if (!window.popStateDetected) {
                    if (initial.value == true && itemProvider.itemList.data == null) {
                        ps_loading_dialog.value.openModal();
                    }

                    if (itemProvider.paramHolder.orderBy != PsConst.FILTERING_TRENDING || itemProvider.paramHolder.orderType != PsConst.FILTERING__DESC) {
                        itemProvider.paramHolder.sortingName == 'Recent';
                        itemProvider.paramHolder.orderBy == PsConst.FILTERING__ADDED_DATE;
                        itemProvider.paramHolder.orderType == PsConst.FILTERING__DESC;
                    } 
                    await loadDataList();

                } else {
                    initial.value = false;
                    window.popStateDetected = false;
                }
            },1000 );
        });
        onUnmounted(() => {
            itemProvider.paramHolder = new ProductParameterHolder().getRecentParameterHolder();
        });

        async function loadDataList() {
            ps_loading_dialog.value.openModal();
            if (itemProvider.paramHolder.mile == '0' || itemProvider.paramHolder.mile.toString() == '') {
                itemProvider.paramHolder.lat = '';
                itemProvider.paramHolder.lng = '';
            } 
            PsUtils.addParamToCurrentUrl(itemProvider.getURLforListByParam(itemProvider.paramHolder));
            await itemProvider.resetProductList(psValueStore.getLoginUserId(), itemProvider.paramHolder);
            ps_loading_dialog.value.closeModal();
            initial.value = false;
            console.log(itemProvider.itemList.data.length)
        }

        const arrowIcon = ref("downArrow");
        function enter(el, done) {
            arrowIcon.value = "upArrow";
        }

        function leave(el, done) {
            arrowIcon.value = "downArrow";
        }

        return {
            ps_loading_dialog,
            itemProvider,
            appInfoStore,
            enter,
            leave,
            initial,
        }
    },
    computed: {
        breadcrumb() {
            if (this.itemProvider.paramHolder.catName && this.itemProvider.paramHolder.subCatName) {
                return [
                    {
                        label: trans('item_detail__home'),
                        url: route('dashboard')
                    },
                    {
                        label: trans('category_list__title'),
                        url: route('fe_category.index'),
                    },
                    {
                        label: this.itemProvider.paramHolder.catName,
                        url: route('fe_sub_category', {
                            cat_id: this.query.cat_id,
                            cat_name: this.query.cat_name
                        }),
                    },
                    {
                        label: this.itemProvider.paramHolder.subCatName,
                        color: "text-fePrimary-500"
                    }
                ]
            } else if (this.itemProvider.paramHolder.catName) {
                return [
                    {
                        label: trans('item_detail__home'),
                        url: route('dashboard')
                    },
                    {
                        label: trans('category_list__title'),
                        url: route('fe_category.index'),
                    },
                    {
                        label: this.itemProvider.paramHolder.catName,
                        color: "text-fePrimary-500"
                    }
                ]
            } else {
                return [
                    {
                        label: trans('item_detail__home'),
                        url: route('dashboard')
                    },
                    {
                        label: trans('fe__search_result'),
                        color: "text-fePrimary-500"
                    }
                ]
            }
        }
    },
}
</script>
<style scoped>
[v-cloak] {
    display: none;
}
</style>
