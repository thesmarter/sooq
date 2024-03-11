<template>
    <div v-if="alreadyVendor">
        <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4 ">
            <ps-label-header-4 class="font-medium"> {{ $t("core_fe__my_vendors") }} </ps-label-header-4>
            <div class="flex justify-between gap-5">
                <ps-button class="flex flex-row " @click="addNewVendor()">
                    {{ $t("core_fe__add_new_vendor") }}
                </ps-button>
                <ps-route-link :to="{ name: 'fe_vendor_list', query: {ownerUserId: loginUserId} }">
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
        </div>

        <div class="w-full mt-6 grid sm:grid-cols-2 md:grid-cols-2 lg:grid-cols-2 xl:grid-cols-3 gap-6 sm:gap-5 md:gap-6 mb-10">
            <div v-for="(vendor, index) in vendorStore.vendorList.data?.slice(0,3)" :key="index">
                <vendor-horizontal-vendor :vendor="vendor"/>
            </div>
        </div>
    </div>
    <profile-vendor-card v-else/>
    <vendor-application-modal ref="vendor_application_modal" storeName="profile-vendor"/>
</template>

<script>
import {defineComponent, ref, onMounted } from 'vue';

import { useVendorStore } from '@templateCore/store/modules/vendor/VendorStore';
import { PsValueStore } from '@templateCore/store/modules/core/PsValueStore';

import PsLabelHeader4 from '@template1/vendor/components/core/label/PsLabelHeader4.vue';
import PsRouteLink from '@template1/vendor/components/core/link/PsRouteLink.vue';
import PsButton from '@template1/vendor/components/core/buttons/PsButton.vue';
import VendorHorizontalVendor from '@template1/vendor/components/modules/item/VendorHorizontalVendor.vue';
import VendorApplicationModal from '@template1/vendor/components/modules/vendor/VendorApplicationModal.vue';
import ProfileVendorCard from './ProfileVendorCard.vue';
import PsIcon from '@template1/vendor/components/core/icons/PsIcon.vue';

    export default defineComponent({
        name: 'ProfileVendorHorizontalList',
        components: {
            PsLabelHeader4,
            PsRouteLink,
            PsButton,
            PsIcon,
            VendorHorizontalVendor,
            VendorApplicationModal,
            ProfileVendorCard
        },
        setup() {
            const psValueStore = PsValueStore();
            const vendorStore = useVendorStore('profile-vendor');

            const loginUserId = psValueStore.getLoginUserId();

            const vendor_application_modal = ref();
            const alreadyVendor = ref(false);

            onMounted(async () => {
                await vendorStore.resetVendorList(loginUserId, loginUserId);
                alreadyVendor.value = vendorStore.vendorList.data.length == 0 ? false : true;
            });

            function addNewVendor(){
                vendor_application_modal.value.openModal();
            }

            return {
                vendorStore,
                loginUserId,
                vendor_application_modal,
                alreadyVendor,
                addNewVendor,
            }
        }
    })
</script>
