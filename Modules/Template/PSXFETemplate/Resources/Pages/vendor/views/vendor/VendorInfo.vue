<template>
    <Head :title="vendorStore.vendor.data?.name"/>
    <ps-content-container>
        <template #content>
            <div class="mt-32 mb-10">
                <ps-breadcrumb-2 :items="breadcrumb" class="mb-6" />
                <!-- vendor -->
                <div class="rounded-lg border dark:border-feAchromatic-800 dark:bg-feAchromatic-800 overflow-hidden">
                    <img class="w-full h-52 object-cover" v-lazy="{ src: $page.props.uploadUrl + '/' + vendorStore.vendor.data?.banner1?.imgPath, loading: $page.props.sysImageUrl+'/loading_gif.gif', error: $page.props.sysImageUrl + '/default_photo.png' }">
                    <div class="flex sm:flex-row flex-col items-center gap-4 sm:gap-6 px-6 py-4">
                        <div class="w-20 h-20 relative rounded-full overflow-hidden">
                            <img class="w-20 h-20 rounded-full object-cover" v-lazy="{ src: $page.props.uploadUrl + '/' + vendorStore.vendor.data?.logo?.imgPath, loading: $page.props.sysImageUrl+'/loading_gif.gif', error: $page.props.sysImageUrl + '/default_vendor_shop.png' }" alt="">
                            <div v-if="vendorStore.vendor?.data?.expireStatus == PsConst.VendorExpiryExpiredStatus" class="w-20 h-20 flex items-center justify-center absolute top-0 left-0">
                                <div class="w-20 h-20 absolute top-0 left-0 bg-feAchromatic-900 opacity-60"></div>
                                <div class="w-20 h-20 flex items-center justify-center absolute">
                                    <ps-label textColor="text-feAchromatic-50 text-sm font-semibold">{{$t('Closed')}}</ps-label>
                                </div>
                            </div>
                        </div>
                        <div class="flex flex-col items-center sm:items-start gap-2 justify-center">
                            <ps-label textColor="text-xl font-semibold text-feSecondary-800 dark:text-feAchromatic-50 flex items-center gap-2">{{vendorStore.vendor.data?.name}} <span class="text-xs font-normal px-1 py-[2px] rounded bg-feWarning-500 text-feAchromatic-50 ">{{ $t('core_fe__vendor_indicator') }}</span> </ps-label>
                            <div class="flex gap-4">
                                <ps-label textColor="text-base font-semibold text-feSecondary-800 dark:text-feAchromatic-200">{{ vendorStore.vendor.data?.productCount }} <span class="text-base text-feSecondary-500 dark:text-feAchromatic-400 font-normal">{{ $t('core_fe__products') }}</span></ps-label>
                                <span class="text-base text-feSecondary-500 dark:text-feAchromatic-600 font-normal">|</span>
                                <ps-label textColor="text-base text-feSecondary-500 dark:text-feAchromatic-400 font-normal">{{ $t('core_fe__joined_on') }} {{ JoinedDate }}</ps-label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex mt-6">
                    <ps-label @click="navBarClick('home')" :class="isHome ? 'border-b-4 border-fePrimary-500 text-fePrimary-500' : 'border-b-2 text-feSecondary-500 dark:text-feAchromatic-400'" textColor="text-sm font-medium h-10 py-1 px-3 flex items-center cursor-pointer">{{ $t('core_fe__vendor_home') }}</ps-label>
                    <ps-label @click="navBarClick('profile')" :class="isProfile ? 'border-b-4 border-fePrimary-500 text-fePrimary-500' : 'border-b-2 text-feSecondary-500 dark:text-feAchromatic-400'" textColor="text-sm font-medium h-10 py-1 px-3 flex items-center cursor-pointer">{{ $t('core_fe__vendor_profile') }}</ps-label>
                </div>

                <div v-if="vendorStore.vendor?.data?.expireStatus == PsConst.VendorExpiryExpiredStatus" class="border border-yellow-500 bg-yellow-50 rounded-lg flex gap-4 p-4 mt-6 dark:bg-yellow-800">
                    <div class="w-6">
                        <ps-icon textColor="text-yellow-500" name="warning-triangle" w="24" h="24"/>
                    </div>
                    <ps-label textColor="dark:text-feAchromatic-50">{{$t('core_fe__vendor_temp_close')}}</ps-label>
                </div>

                <!-- item list -->
                <div v-if="isHome" class="mt-6">
                    <div v-if="productStore.itemList.data != null">
                        <div class="grid xl:grid-cols-4 lg:grid-cols-3 md:grid-cols-2 sm:grid-cols-1 gap-6">
                            <div v-for="(item, index) in productStore.itemList.data" :key="index">
                                <item-horizontal-item :item="item"/>
                            </div>
                        </div>
                        <!-- <div v-if="!productStore.isNoMoreRecord.value" class="w-full flex justify-center mt-6">
                            <ps-button @click="loadMore()">{{ $t('list__load_more') }}</ps-button>
                        </div> -->
                        <div class="flex flex-col items-center">
                            <ps-button v-if="productStore.loading.value == false" class="font-medium mx-auto mt-6" @click="loadMore" :class="productStore.isNoMoreRecord.value ? 'hidden' : ''"> {{ $t("follower_list__load_more") }} </ps-button>
                            <ps-button v-else class="font-medium mx-auto mt-6" @click="loadMore" :disabled="true"> {{ $t("follower_list__loading") }} </ps-button>
                        </div>
                    </div>
                    <ps-no-result v-if="productStore.loading.value == false && productStore.itemList?.data == null" @onClick="loadMore()" />
                </div>

                <!-- vendor info -->
                <div v-if="isProfile" class="mt-6">
                    <ps-label textColor="text-2xl font-semibold text-feSecondary-800 dark:text-feAchromatic-50">{{ $t('core_fe__vendor_info') }}</ps-label>
                    <div class="px-6 py-8 flex flex-col items-center sm:flex-row sm:items-start gap-6 mt-4 border dark:border-feAchromatic-800 dark:bg-feAchromatic-800 rounded-lg">
                        <img class="w-20 h-20 rounded-full" v-lazy="{ src: $page.props.uploadUrl + '/' + vendorStore.vendor.data?.logo?.imgPath, loading: $page.props.sysImageUrl+'/loading_gif.gif', error: $page.props.sysImageUrl + '/default_vendor_shop.png' }" alt="">
                        <div class="">
                            <ps-label textColor="text-xl font-semibold text-feSecondary-800 dark:text-feAchromatic-50 flex items-center justify-center sm:justify-start gap-2">{{vendorStore.vendor.data?.name}} <span class="text-xs font-normal px-1 py-[2px] rounded bg-feWarning-500 text-feAchromatic-50 ">{{ $t('core_fe__vendor_indicator') }}</span> </ps-label>
                            <div class="flex justify-center sm:justify-start gap-4 mt-2">
                                <ps-label textColor="text-base font-semibold text-feSecondary-800 dark:text-feAchromatic-200">{{ vendorStore.vendor.data?.productCount }} <span class="text-base text-feSecondary-500 dark:text-feAchromatic-400 font-normal">{{ $t('core_fe__products') }}</span></ps-label>
                                <span class="text-base text-feSecondary-500 dark:text-feAchromatic-600 font-normal">|</span>
                                <ps-label textColor="text-base text-feSecondary-500 dark:text-feAchromatic-400 font-normal">{{ $t('core_fe__joined_on') }} {{ JoinedDate }}</ps-label>
                            </div>
                            <ps-label textColor="text-sm font-normal text-feSecondary-800 dark:text-feAchromatic-400 mt-4">{{vendorStore.vendor.data?.description }}</ps-label>
                            <div class="mt-4 flex flex-col gap-3">
                                <ps-label v-if="isInclude('website')" textColor="text-sm font-normal text-feSecondary-800 dark:text-feAchromatic-400 flex items-center">
                                    <ps-icon textColor="dark:text-feAchromatic-400" class="me-2" name="website" h="20" w="20" viewBox="0 0 19 19"/> {{ vendorStore.vendor.data?.website }}
                                </ps-label>
                                <ps-label v-if="isInclude('phone')" textColor="text-sm font-normal text-feSecondary-800 dark:text-feAchromatic-400 flex items-center">
                                    <ps-icon textColor="dark:text-feAchromatic-400" class="me-2" name="phoneOutline" h="20" w="20" viewBox="0 0 19 19"/> {{ vendorStore.vendor.data?.phone }}
                                </ps-label>
                                <ps-label v-if="isInclude('address')" textColor="text-sm font-normal text-feSecondary-800 dark:text-feAchromatic-400 flex items-center">
                                    <ps-icon textColor="dark:text-feAchromatic-400" class="me-2" name="location" h="20" w="20" viewBox="-1 0 14 14"/> {{ vendorStore.vendor.data?.address }}
                                </ps-label>
                            </div>
                            <div class="flex gap-2 mt-4">
                                <a v-if="isInclude('facebook')" :href="vendorStore.vendor.data?.facebook" target="_blank" class="cursor-pointer">
                                    <img v-lazy="{ src: $page.props.sysImageUrl + '/facebook_icon.png' }" class="w-6 h-6">
                                </a>
                                <a v-if="isInclude('instagram')" :href="vendorStore.vendor.data?.facebook" target="_blank" class="cursor-pointer">
                                    <img v-lazy="{ src: $page.props.sysImageUrl + '/instagram_icon.png' }" class="w-6 h-6">
                                </a>
                                <div v-for="vendorRelation in vendorStore.vendor.data?.vendorRelation" :key="vendorRelation.coreKeysId">
                                    <a v-if="vendorRelation.coreKeysId == 'ps-ven00001'"
                                        :href="vendorRelation.value" target="_blank" class="cursor-pointer">
                                        <img v-lazy="{ src: $page.props.sysImageUrl + '/whatsapp_icon.png' }" class="w-6 h-6">
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-10" v-if="vendorStore.vendorBranches.data != null" >
                        <ps-label class="mb-4" textColor="text-2xl font-semibold text-feSecondary-800 dark:text-feAchromatic-50">{{ $t('core_fe__vendor_branches') }}</ps-label>
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6">
                            <div v-for="(branch, index) in vendorStore.vendorBranches.data.filter(branch => branch.name != '')" :key="index" class="p-6 border dark:border-feAchromatic-800 rounded-lg flex flex-col dark:bg-feAchromatic-800">
                                <ps-label textColor="text-lg font-semibold text-feSecondary-800 dark:text-feAchromatic-50">{{branch.name}}</ps-label>
                                <ps-label textColor="text-sm font-normal text-feSecondary-800 dark:text-feAchromatic-400 mt-4 grow">{{ branch.description }}</ps-label>
                                <div class="flex flex-col gap-3 mt-4">
                                    <ps-label v-if="branch.phone != ''" textColor="text-sm font-normal text-feSecondary-800 dark:text-feAchromatic-400 flex items-center">
                                        <ps-icon textColor="dark:text-feAchromatic-400" class="me-2" name="phoneOutline" h="20" w="20" viewBox="0 0 19 19"/> {{ branch.phone }}
                                    </ps-label>
                                    <ps-label v-if="branch.address != ''" textColor="text-sm font-normal text-feSecondary-800 dark:text-feAchromatic-400 flex items-center">
                                        <ps-icon textColor="dark:text-feAchromatic-400" class="me-2" name="location" h="20" w="20" viewBox="-1 0 14 14"/> {{ branch.address }}
                                    </ps-label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </ps-content-container>
</template>

<script>

import { ref , onMounted, computed } from 'vue';
import { Head, usePage } from '@inertiajs/vue3';
import { trans } from 'laravel-vue-i18n';
import { useVendorStore } from '@templateCore/store/modules/vendor/VendorStore';
import { PsValueStore } from "@templateCore/store/modules/core/PsValueStore";
import { useProductStore } from "@templateCore/store/modules/item/ProductStore";
import ProductParameterHolder from '@templateCore/object/holder/ProductParameterHolder';
import VendorBranchesParameterHolder from '@templateCore/object/holder/VendorBranchesParameterHolder';
import moment from 'moment';
import PsConst from '@templateCore/object/constant/ps_constants';

import PsContentContainer from '@template1/vendor/components/layouts/container/PsContentContainer.vue';
import PsFrontendLayout from '@template1/vendor/components/layouts/container/PsFrontendLayout.vue';
import PsBreadcrumb2 from "@template1/vendor/components/core/breadcrumbs/PsBreadcrumb2.vue";
import PsLabel from '@template1/vendor/components/core/label/PsLabel.vue';
import PsIcon from '@template1/vendor/components/core/icons/PsIcon.vue';
import ItemHorizontalItem from "@template1/vendor/components/modules/item/ItemHorizontalItem.vue";
import PsButton from '@template1/vendor/components/core/buttons/PsButton.vue';
import PsNoResult from "@template1/vendor/components/modules/list/PsNoResult.vue";

export default {
    name : "VendorInfo",
    components : {
        Head,
        PsContentContainer,
        PsBreadcrumb2,
        PsLabel,
        PsIcon,
        ItemHorizontalItem,
        PsButton,
        PsNoResult
    },
    layout: PsFrontendLayout,
    props: ['query'],
    setup(props) {
        const vendorStore = useVendorStore();
        const productStore = useProductStore('vendor-info');
        const productParameterHolder = new ProductParameterHolder();
        const vendorBranchesParameterHolder = new VendorBranchesParameterHolder();
        const psValueStore = PsValueStore();
        const loginUserId = psValueStore.getLoginUserId();
        const vendorId = props.query.vendorId;
        const JoinedDate = ref();
        const isHome = ref(true);
        const isProfile = ref(false);

        onMounted(async()=>{
            productParameterHolder.vendorId = vendorId;
            vendorBranchesParameterHolder.vendorId = vendorId;
            // productStore.limit = 3;
            await vendorStore.loadVendor(vendorId, loginUserId, loginUserId);
            await vendorStore.loadVendorBranches(loginUserId, vendorBranchesParameterHolder);
            await productStore.resetProductList(loginUserId, productParameterHolder);

            const temp = vendorStore.vendor.data ? new Date(vendorStore.vendor.data?.addedDate)  :  '' ;
            const joinedDate = moment(temp).format(usePage().props.dateFormat);
            JoinedDate.value = joinedDate;

            console.log(vendorStore);
            console.log(productStore);
        });


        function isInclude(value){
            return vendorStore.vendor.data?.[value] == '' ? false : true;
        }

        function navBarClick(value){
            isHome.value = value == 'home' ? true : false;
            isProfile.value = !isHome.value;
        }

        function loadMore() {
            productStore.loadItemList(loginUserId, productParameterHolder);
        }

        const breadcrumb = computed(() => {
            return [
                {
                    label: trans('ps_nav_bar__home'),
                    url: route('dashboard')
                },
                {
                    label: vendorStore.vendor.data?.name,
                    color: "text-fePrimary-500"
                }
            ];
        });

        return {
            PsConst,
            breadcrumb,
            vendorStore,
            productStore,
            JoinedDate,
            isInclude,
            navBarClick,
            loadMore,
            isHome,
            isProfile,
            loginUserId
        }
    },
}
</script>
