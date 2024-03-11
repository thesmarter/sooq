<template>
    <div v-if="vendorSearchStore.vendorSearchList?.data?.length != 0 && vendorSearchStore.vendorSearchList?.data?.length != null" class="xl:w-feLarge lg:w-large md:w-full md:px-6 lg:px-0 mx-auto">
        <div class="flex justify-between items-center mt-10 mb-5">
            <ps-label-header-5 class="font-semibold"> {{ $t("latest_vendor") }} </ps-label-header-5>
            <ps-route-link :to="{ name: 'fe_vendor_filter'}">
                <ps-button class="flex flex-row" padding="p-2 sm:px-4 sm:py-2" shadow="shadow-sm" rounded="rounded" hover=""
                    focus="" border="border border-feSecondary-200 dark:border-feSecondary-800"
                    colors="bg-feAchromatic-50 text-feSecondary-800 dark:bg-feSecondary-800 dark:text-feSecondary-200">
                    <ps-label class="hidden sm:inline">{{ $t("list_fe__view_all_label") }}</ps-label>
                    <ps-icon class="sm:ms-2 block rtl:hidden" textColor="dark:text-feSecondary-200" name="rightChervon"
                        h="24" w="24" />
                    <ps-icon class="sm:ms-2 hidden rtl:block" textColor="dark:text-feSecondary-200" name="leftChervon"
                        h="24" w="24" />
                </ps-button>
            </ps-route-link>
        </div>

        <vendor-horizontal-swiper :itemList="vendorSearchStore.vendorSearchList?.data"/>
    </div>
</template>

<script>
import {defineComponent, ref, onMounted } from 'vue';

import { useVendorStore } from '@templateCore/store/modules/vendor/VendorStore';
import { PsValueStore } from '@templateCore/store/modules/core/PsValueStore';
import { useVendorSearchStoreState } from "@templateCore/store/modules/vendor/VendorSearchStore";

import PsLabelHeader5 from '@template1/vendor/components/core/label/PsLabelHeader5.vue';
import PsRouteLink from '@template1/vendor/components/core/link/PsRouteLink.vue';
import PsButton from '@template1/vendor/components/core/buttons/PsButton.vue';
import VendorHorizontalSwiper from '../../../components/modules/vendor/VendorHorizontalSwiper.vue';
import VendorApplicationModal from '@template1/vendor/components/modules/vendor/VendorApplicationModal.vue';
import PsIcon from '@template1/vendor/components/core/icons/PsIcon.vue';
import VendorSearchParameterHolder from '@templateCore/object/holder/VendorSearchParameterHolder';

    export default defineComponent({
        name: 'DashboardVendorHorizontalList',
        components: {
            PsLabelHeader5,
            PsRouteLink,
            PsButton,
            PsIcon,
            VendorHorizontalSwiper,
            VendorApplicationModal,
        },
        setup() {
            const psValueStore = PsValueStore();
            const vendorSearchStore = useVendorSearchStoreState('dashboard_vendor_list');

            const loginUserId = psValueStore.getLoginUserId();
            const vendorSearchParameterHolder = new VendorSearchParameterHolder().getAllVendorParameterHolder();

            onMounted(() => {
                vendorSearchStore.getVendorSearchList(loginUserId,vendorSearchParameterHolder);
                console.log(vendorSearchStore)
            });

            return {
                vendorSearchStore,
            }
        }
    })
</script>
