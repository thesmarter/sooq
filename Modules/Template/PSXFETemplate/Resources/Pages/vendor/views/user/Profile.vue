<template>
    <Head :title="$t('ps_nav_bar__profile')"/>
    <ps-content-container>
        <template #content>
            <!-- breadcrumb start-->
            <ps-breadcrumb-2 class="mb-7 mt-32" :items="breadcrumb"/>

            <div class="grid grid-cols-1 sm:grid-cols-3 lg:grid-cols-3 xl:grid-cols-4 gap-6 sm:gap-5 md:gap-6 pb-12">

                <!-- profile card -->
                <profile-info-card />

                <!-- Paid Ad OFF -->
                <div class="w-full sm:col-span-2 lg:col-span-2 xl:col-span-3">
                     <!-- vendor anouncement  -->
                    <!-- <profile-vendor-card v-if="appInfoStore?.isVendorSettingOn() && (vendorStore.vendorList.data?.length < 0)"/> -->

                    <!-- vendor list -->
                    <profile-vendor-horizontal-list v-if="appInfoStore?.isVendorSettingOn()"/>

                    <!-- Start Purchased Package -->
                    <profile-purchased-package-horizontal-list v-if="appInfoStore?.isPaidAppOn()"/>

                    <!-- paid & promote ads -->
                    <profile-promoted-item-horizontal-list />

                    <!-- active listing -->
                    <profile-active-item-horizontal-list/>

                    <!-- pending listing -->
                    <profile-pending-item-horizontal-list/>

                    <!-- rejected listing -->
                    <profile-rejected-item-horizontal-list/>

                    <!-- favourite listing -->
                    <profile-favourite-item-horizontal-list/>

                    <!-- soldout listing -->
                    <profile-sold-out-item-horizontal-list/>

                    <!-- disabled listing -->
                    <profile-disabled-item-horizontal-list/>
                </div>

            </div>

            <ps-loading-dialog ref="psloading"  :isClickOut='false'/>

        </template>
    </ps-content-container>
</template>

<script>

import { ref , onMounted } from 'vue';

import { Head } from '@inertiajs/vue3';
import PsContentContainer from '@template1/vendor/components/layouts/container/PsContentContainer.vue';
import PsFrontendLayout from '@template1/vendor/components/layouts/container/PsFrontendLayout.vue';
import PsBreadcrumb2 from '@template1/vendor/components/core/breadcrumbs/PsBreadcrumb2.vue';

import ProfileInfoCard from './components/ProfileInfoCard.vue';
import ProfilePurchasedPackageHorizontalList from './components/ProfilePurchasedPackageHorizontalList.vue';
import ProfilePromotedItemHorizontalList from './components/ProfilePromotedItemHorizontalList.vue';
import ProfileActiveItemHorizontalList from './components/ProfileActiveItemHorizontalList.vue';
import ProfileRejectedItemHorizontalList from './components/ProfileRejectedItemHorizontalList.vue';
import ProfilePendingItemHorizontalList from './components/ProfilePendingItemHorizontalList.vue';
import ProfileFavouriteItemHorizontalList from './components/ProfileFavouriteItemHorizontalList.vue';
import ProfileSoldOutItemHorizontalList from './components/ProfileSoldOutItemHorizontalList.vue';
import ProfileDisabledItemHorizontalList from './components/ProfileDisabledItemHorizontalList.vue';
import ProfileVendorHorizontalList from './components/ProfileVendorHorizontalList.vue';
import ProfileVendorCard from './components/ProfileVendorCard.vue';

import { usePsAppInfoStoreState } from '@templateCore/store/modules/appinfo/AppInfoStore';
import { useVendorStore } from '@templateCore/store/modules/vendor/VendorStore';
import { PsValueStore } from '@templateCore/store/modules/core/PsValueStore';

import { trans } from 'laravel-vue-i18n';

import PsLoadingDialog from '@template1/vendor/components/core/dialog/PsLoadingDialog.vue';

export default {
    name : "ProfileView",
    components : {
        PsContentContainer,
        PsLoadingDialog,
        PsBreadcrumb2,
        Head,
        ProfileInfoCard,
        ProfilePurchasedPackageHorizontalList,
        ProfilePromotedItemHorizontalList,
        ProfileActiveItemHorizontalList,
        ProfileRejectedItemHorizontalList,
        ProfilePendingItemHorizontalList,
        ProfileFavouriteItemHorizontalList,
        ProfileSoldOutItemHorizontalList,
        ProfileDisabledItemHorizontalList,
        ProfileVendorHorizontalList,
        ProfileVendorCard
    },
layout: PsFrontendLayout,
    setup() {

        //Modals
        const psloading = ref();
        let breadcrumb = ref([]);
        const appInfoStore = usePsAppInfoStoreState();
        const psValueStore = PsValueStore();
        const vendorStore = useVendorStore('profile');
        const loginUserId = psValueStore.getLoginUserId();



        onMounted(() => {
            if(appInfoStore?.isVendorSettingOn()){
                vendorStore.loadVendorList(loginUserId, loginUserId);
            }
            breadcrumb.value = [
                {
                    label: trans('ps_nav_bar__home'),
                    url: route('dashboard')
                },
                {
                    label: trans('ps_nav_bar__profile'),
                    color: "text-fePrimary-500"
                }
            ];
        })

        return {
            psloading,
            appInfoStore,
            breadcrumb,
            vendorStore
        };
    },
}
</script>
