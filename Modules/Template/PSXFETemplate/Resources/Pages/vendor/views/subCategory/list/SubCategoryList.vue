<template>
    <Head :title="$t('sub_category_list__category')"/>
 <ps-content-container>
    <template #content>

    <div class="sm:mt-32 lg:mt-36 mt-28">
        <div class=" flex flex-col sm:flex-row items-center justify-between mt-24 mb-5 sm:mb-6 lg:mb-8">

            <ps-breadcrumb-2 :items="breadcrumb" class="" />

            <div class="w-full flex content-center items-center mt-7 sm:mt-0">
                <!-- keyword search -->
                <div class="flex-grow lg:w-72 xl:w-76 h-10 w-full sm:w-56 md:w-60">
                    <ps-input-with-right-icon v-on:keyup.enter="searchClicked" v-model:value="subCategoryStore.paramHolder.keyword" class=""  v-bind:placeholder="$t('sub_category_list__search')" >
                        <template #icon>
                            <ps-icon v-if="subCategoryStore.paramHolder.keyword == ''" name="search" class='cursor-pointer'/>
                            <ps-icon v-else @click="[subCategoryStore.paramHolder.keyword = '',searchClicked()]" name="cross" class='cursor-pointer'/>
                        </template>
                    </ps-input-with-right-icon>
                </div>

                <!-- sorting -->
                <ps-dropdown horizontalAlign="right" h="h-auto" class="ms-4" rounded="rounded-lg" >
                        <template #select>
                            <ps-dropdown-select class="h-10 w-10 sm:w-auto sm:ps-4 ps-2.5" border="border border-1 border-feSecondary-200 dark:border-feSecondary-400" text="text-sm font-medium text-feAchromatic-800 dark:text-feAchromatic-100 hidden lg:inline" iconTheme="text-feAchromatic-800 ms-2 hidden lg:inline" leftIcon="filter" leftIconTheme="inline lg:hidden" :selectedValue="activeSortingName" :placeholder="$t('sort_by')" />
                        </template>
                        <template #list >
                            <div class="rounded-md bg-feAchromatic-50 dark:bg-feSecondary-800 shadow-xs w-44 " >
                                <div class="z-30">
                                    <div>
                                        <div v-for="sort in currentsorting" :key="sort.id"
                                            class="w-72 flex py-4 px-2 hover:bg-fePrimary-50 dark:hover:bg-feSecondary-500 cursor-pointer items-center"
                                                @click="sortingFilterClicked(sort)" >
                                            <ps-label class="ms-2" :class="sort.id==activeSortingId ? ' font-medium' : 'font-light'"  >{{ $t("review_entry__title") }}: {{sort.name}} </ps-label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>
                </ps-dropdown>

                <!-- subscription -->
                <div v-if="appInfoStore?.appInfo?.data?.psAppSetting?.isSubcatSubscription == PsConst.ONE" class="ml-4">
                    <ps-button v-if="!scribe" padding="p-2" @click="subCatScribe">
                        <ps-icon class="cursor-pointer" textColor="text-feAchromatic-50" name="bell-outline" h="24" w="24" viewBox="0 -2 18 23"/>
                    </ps-button>
                    <ps-button v-if="scribe" padding="p-2" @click="submitScribe">
                        <ps-icon class="cursor-pointer" textColor="text-feAchromatic-50" name="bluemark" h="24" w="24" />
                    </ps-button>
                </div>
            </div>

        </div>

        <div class="flex flex-col ">


            <div class="flex flex-row mb-8 ">
                <div class='w-full flex flex-col' >
                    <div v-if="subCategoryStore.subCategoryList && subCategoryStore.subCategoryList.data != null" class="grid grid-cols-4 sm:grid-cols-4 md:grid-cols-9 lg:grid-cols-12 md:gap-6 gap-4 ">

                        <!-- Column -->
                        <div  class="relative w-full col-span-4 sm:col-span-2 md:col-span-3 lg:col-span-4 xl:col-span-3" v-for="subCategory in subCategoryStore.subCategoryList?.data" :key="subCategory.id">
                            <ps-route-link class="flex-grow"
                                            :to="{name: 'fe_item_list',
                                            query: {
                                                cat_id: catId,
                                                cat_name: catName,
                                                sub_cat_id: subCategory.id,
                                                sub_cat_name: subCategory.name,
                                                status: 1 } }" >
                                <div class="text-center cursor-pointer w-full overflow-hidden bg-feAchromatic-50 border border-feAchromatic-100 dark:border-feAchromatic-700
                                            dark:bg-feSecondary-800 rounded-x sm:rounded-xl lg:rounded-lg hover:shadow-md" 
                                    :class="scribe && subscribedSubCatList.filter((subcat)=>subcat == subCategory.id+'_FE').length == 1 ? 'border-2 border-fePrimary-500' : ''" >
                                    <div class="h-44 relative flex justify-center">
                                        <img class="transform transition duration-500 hover:scale-110 w-full h-full object-cover" v-lazy="{ src: $page.props.thumb3xUrl + '/' + subCategory?.defaultPhoto?.imgPath, loading: $page.props.sysImageUrl+'/loading_gif.gif', error: $page.props.sysImageUrl+'/default_photo.png' }" alt="">

                                        <div class="w-14 h-14 absolute -bottom-7 bg-feAchromatic-50 p-3 items-center
                                                            rounded-full border-2 border-white dark:bg-feSecondary-800 shadow-md">
                                                <img alt="Placeholder" class="transform transition duration-500 hover:scale-110 "
                                                v-lazy=" { src: $page.props.thumb2xUrl + '/' + subCategory?.defaultIcon?.imgPath, loading: $page.props.sysImageUrl+'/loading_gif.gif', error: $page.props.sysImageUrl+'/default_photo.png' }"
                                                >
                                            </div>
                                    </div>

                                    <div class="h-20">
                                        <ps-label class="peer-checked/draft:text-sky-500 pt-9 lg:text-base sm:text-sm text-base font-medium" textColor=""> {{ subCategory.name.length > 20 ? subCategory.name.slice(0,20)+'...' : subCategory.name }}  </ps-label>
                                    </div>
                                </div>
                            </ps-route-link >
                            <!-- subscribe starts -->
                            <div class="absolute -top-3 -right-3 w-6 h-6">
                                <custom-checkbox :isScribe="subCategory.isSubScribe" :checked="subCategory.id+'_FE'" :value="subCategory.id+'_FE'"
                                    @updateSelectedValue="updateValue" v-model:selectedValue="checkedSelectedList" v-if="scribe" @click="subCategoryClick(subCategory.id+'_FE')"></custom-checkbox>
                            </div>
                            <!-- subscribe ends -->
                        </div>
                        <!-- END Column -->

                    </div>

                    <ps-no-result v-if="subCategoryStore.loading.value == false && subCategoryStore.subCategoryList?.data == null && initial == false" @onClick="loadMore" class="my-6" />

                    <ps-button v-if="subCategoryStore.loading.value == false && initial == false" class="w-60 mx-auto mt-8" @click="loadMore" :class="subCategoryStore.isNoMoreRecord.value ? 'hidden' : ''"> {{ $t("sub_category_list__load_more") }} </ps-button>
                    <ps-button v-else-if="initial == false" class="w-60 mx-auto mt-8" @click="loadMore" :disabled="true"> {{ $t("sub_category_list__loading") }} </ps-button>

                </div>

            </div>
        </div>
    </div>

    </template>
    </ps-content-container>

    <ps-loading-dialog ref="ps_loading_dialog" class='z-40' />
    <ps-success-dialog-2 ref="success" />
    <ps-error-dialog ref="ps_error_dialog" />
    <ps-warning-dialog-3 ref="ps_warning_dialog" />
</template>

<script lang="ts">
import { Head } from '@inertiajs/vue3';
import {  ref,onMounted } from 'vue';
// import router from '@template1/router';
// import { useRoute } from 'vue-router';

import PsButton from '@template1/vendor/components/core/buttons/PsButton.vue';
import PsLabel from '@template1/vendor/components/core/label/PsLabel.vue';
import PsLabelHeader4 from '@template1/vendor/components/core/label/PsLabelHeader4.vue';
import PsIcon from '@template1/vendor/components/core/icons/PsIcon.vue';
import PsInput from '@template1/vendor/components/core/input/PsInput.vue';
import PsDropdown from '@template1/vendor/components/core/dropdown/PsDropdown.vue';
import CustomCheckbox from '@template1/vendor/components/core/checkbox/CustomCheckbox.vue';
import PsBreadcrumb2 from "@template1/vendor/components/core/breadcrumbs/PsBreadcrumb2.vue";
import PsWarningDialog3 from '@template1/vendor/components/core/dialog/PsWarningDialog3.vue';
import PsDropdownSelect from '@template1/vendor/components/core/dropdown/PsDropdownSelect.vue';

//import SubCategoryHorizontalItem from '@template1/vendor/components/modules/subCategory/SubCategoryHorizontalItem.vue';
import PsRouteLink from '@template1/vendor/components/core/link/PsRouteLink.vue';
import PsLoadingDialog from '@template1/vendor/components/core/dialog/PsLoadingDialog.vue';
import PsSuccessDialog2 from '@template1/vendor/components/core/dialog/PsSuccessDialog2.vue';
import PsErrorDialog from '@template1/vendor/components/core/dialog/PsErrorDialog.vue';
import PsConst from '@templateCore/object/constant/ps_constants';

import { usePsAppInfoStoreState } from '@templateCore/store/modules/appinfo/AppInfoStore';
import AppInfoParameterHolder from '@templateCore/object/holder/AppInfoParameterHolder';
import { useSubCategoryStoreState } from '@templateCore/store/modules/subCategory/SubCategoryStore';
import { useSubCategoryScribeStoreState } from '@templateCore/store/modules/subCategory/SubCategoryScribeStore';
import SubCategoryScribeParameterHolder from '@templateCore/object/holder/SubCategoryScribeParameterHolder';
import { PsValueStore } from '@templateCore/store/modules/core/PsValueStore';
import PsStatus from '@templateCore/api/common/PsStatus';
import  firebase  from 'firebase/app';
import 'firebase/messaging'
import { trans } from 'laravel-vue-i18n';
import PsContentContainer from '@template1/vendor/components/layouts/container/PsContentContainer.vue';

import PsFrontendLayout from '@template1/vendor/components/layouts/container/PsFrontendLayout.vue';
import PsInputWithRightIcon from '@template1/vendor/components/core/input/PsInputWithRightIcon.vue';
import PsNoResult from "@template1/vendor/components/modules/list/PsNoResult.vue";
import { router } from '@inertiajs/vue3';
import PsUtils from '@templateCore/utils/PsUtils';

export default {
    name: 'SubCategoryListView',
    components : {
        PsNoResult,
        PsButton,
        PsLabel,
        PsLabelHeader4,
       // SubCategoryHorizontalItem,
        PsIcon,
        PsRouteLink,
        PsInput,
        PsDropdown,
        PsLoadingDialog,
        PsErrorDialog,
        CustomCheckbox,
        PsSuccessDialog2,
        PsBreadcrumb2,
        PsContentContainer,
        PsInputWithRightIcon,
        Head,
        PsWarningDialog3,
        PsDropdownSelect
    },
    props: ['query','mobileSetting','backendSetting'],
    layout: PsFrontendLayout,
    setup (props){
        localStorage.catId = "";
        // const route = useRoute();
        const catId = props.query?.cat_id;
        const catName = props.query?.cat_name;
        // const catId = '';
        // const catName = '';
        const subCategoryStore = useSubCategoryStoreState();
        const subCategoryScribeProvider = useSubCategoryScribeStoreState();
        const holder = new SubCategoryScribeParameterHolder();
        const psValueStore = PsValueStore();
        const loginUserId = psValueStore.getLoginUserId();
        const appInfoParameterHolder = new AppInfoParameterHolder();
        appInfoParameterHolder.userId = loginUserId;
        const appInfoStore = usePsAppInfoStoreState();
        const initial = ref(true);

        const is_user_subscribed = ref(false);

        subCategoryStore.limit = props.mobileSetting?.default_loading_limit ?? 12;

        const ps_loading_dialog = ref();
        const ps_warning_dialog = ref();
        let activeSortingId = ref('');
        let activeSortingName = ref('');
        let scribe = ref(false);
        let checkedSelectedList = ref([]);
        let subscribedSubCatList = ref([]);
        const success = ref();
        const ps_error_dialog =ref();
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
        async function loadAppInfo(){
            // await appInfoStore.loadAppInfo(appInfoParameterHolder);
        }
        loadAppInfo();

        subCategoryStore.paramHolder.catId = catId;
        // subCategoryStore.paramHolder.keyword = catName;
        setTimeout(async ()=>{

            await loadDataList();


        },1000);

        function sortingFilterClicked(value) {
            activeSortingId.value = value.id;
            activeSortingName.value = value.name;
            subCategoryStore.paramHolder.orderBy = value.orderBy;
            subCategoryStore.paramHolder.orderType = value.orderType;
            loadDataList();

        }

        async function subCatScribe() {
            if( loginUserId != PsConst.NO_LOGIN_USER){
                if(is_user_subscribed.value == false){
                    ps_warning_dialog.value.openModal(
                        trans('subcat_subscribe__how_it_works'),
                        trans('subcat_subscribe__desc'),
                        trans('payment__ok'),
                        () => {
                            PsUtils.log("Ok");
                        },
                        () => {
                            PsUtils.log("Cancel");
                    });
                }
                scribe.value= true;
            }else{
                router.get(route('login'));
            }


        }

        ///start firebase subscribe
        const messaging = firebase.messaging.isSupported() ? firebase.messaging() : null;
        function subscribeTokenToTopic(token, topic) {
            if(!messaging) return;
            fetch('https://iid.googleapis.com/iid/v1/' + token + '/rel/topics/' + topic, {
                method: 'POST',
                headers: new Headers({
                    'Authorization': 'key='+props.backendSetting.fcm_api_key
                })
            }).then(response => {
                if (response.status < 200 || response.status >= 400) {

                    throw 'Error subscribing to topic: ' + response.status + ' - ' + response.text();
                }
                console.log('Subscribed to "' + topic + '"');
            }).catch(error => {
                console.error(error);
            })
        }
        ///end firebase

        async function submitScribe() {

            for (var i = 0; i < checkedSelectedList.value.length; i++) {
                subscribeTokenToTopic(localStorage.deviceToken,checkedSelectedList.value[i]);
            }
            holder.userId = loginUserId;
            holder.catId = catId;
            holder.subCatIds = checkedSelectedList.value;
            checkedSelectedList.value = [];
            const status = await subCategoryScribeProvider.subCategoryScription(loginUserId, holder);
             if(status.status == PsStatus.SUCCESS){

                await subCategoryStore.resetSearchSubCategoryList(loginUserId,subCategoryStore.paramHolder);
                is_user_subscribed.value = true;

                ps_loading_dialog.value.closeModal();
                if(status.data.message == "SUBSCRIBED"){
                    success.value.openModal(
                        trans('subcategory__subscription_status'),
                        trans('subcategory__subscription_message'),
                        trans('core_fe__done'),
                        () => {
                            PsUtils.log("ok");
                        });

                }else if(status.data.message == "UNSUBSCIRBED"){
                    success.value.openModal(
                        trans('subcategory__subscription_status'),
                        trans('subcategory__subscription_message'),
                        trans('core_fe__done'),
                        () => {
                            PsUtils.log("ok");
                        });
                }else{
                    success.value.openModal(
                        trans('subcategory__subscription_status'),
                        trans('subcategory__subscription_message'),
                        trans('core_fe__done'),
                        () => {
                            PsUtils.log("ok");
                        });
                }
                scribe.value= false;
                isSubscribed();
            }

        }

        function searchClicked() {
            loadDataList();
        }
        async function isSubscribed() {
            subscribedSubCatList.value = [];
            subCategoryStore.subCategoryList.data.forEach(e => {
                if(e.isSubScribe == '1'){
                    subscribedSubCatList.value.push(e.id+'_FE');
                }
            })
        }

        async function loadDataList() {
            ps_loading_dialog.value.openModal();

            await subCategoryStore.resetSearchSubCategoryList(loginUserId,subCategoryStore.paramHolder);

            const status = await subCategoryScribeProvider.isUserSubscribed('1', loginUserId);
            is_user_subscribed.value = status?.data?.status == 'success' ? true : false;
            isSubscribed();

            ps_loading_dialog.value.closeModal();
            initial.value = false;
        }

        function loadMore() {
            subCategoryStore.resetSearchSubCategoryList(loginUserId,subCategoryStore.paramHolder);
        }

        function subCategoryClick(value){
            if(subscribedSubCatList.value.includes(value)){
                let index = subscribedSubCatList.value.indexOf(value);
                subscribedSubCatList.value.splice(index, 1);
            }else{
                subscribedSubCatList.value.push(value);
            }
        }

        function updateValue(value){
            checkedSelectedList.value = value;
        }
        onMounted(() => {
            if(initial.value == true && subCategoryStore.subCategoryList?.data == null){
                ps_loading_dialog.value.openModal();
            }
        })


        return {
            updateValue,
            subCategoryStore,
            loadMore,
            subCategoryClick,
            catId,
            catName,
            currentsorting,
            sortingFilterClicked,
            activeSortingId,
            activeSortingName,
            ps_loading_dialog,
            searchClicked,
            subCatScribe,
            scribe,
            submitScribe,
            checkedSelectedList,
            subscribedSubCatList,
            success,
            ps_error_dialog,
            ps_warning_dialog,
            appInfoStore,
            PsConst,
            initial
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
                    label: trans('sub_category_list__category'),
                    url: route('fe_category.index'),
                },
                {
                    label: this.catName,
                    color: "text-fePrimary-500"
                }
            ]
        }
    },

}
</script>
