<template>
    <Head :title="$t('core_fe__my_vendors')"/>
    <ps-content-container>
        <template #content>
            <div class="mt-32 mb-10">
                <div class="mb-6">
                    <ps-breadcrumb-2 :items="breadcrumb" class="" />
                </div>

                <div class="flex justify-between mt-6">
                    <ps-label textColor="text-feSecondary-800 text-2xl font-semibold">{{ $t('core_fe__my_vendors') }}</ps-label>
                    <ps-button @click="addNewVendor()">{{ $t('core_fe__add_new_vendor') }}</ps-button>
                </div>
                <div class="mt-6">
                    <div class='w-full'  v-if="vendorStore.vendorList.data != null">
                        <div class="grid lg:grid-cols-4 md:grid-cols-3 sm:grid-cols-2 grid-cols-1 gap-6">
                            <div class="w-full" v-for="(vendor, index) in vendorStore.vendorList.data" :key="index">
                                <vendor-horizontal-vendor :vendor="vendor"/>
                            </div>
                        </div>
                        <div class="flex flex-col items-center">
                            <ps-button v-if="vendorStore.loading.value == false" class="font-medium mx-auto mt-6" @click="loadMore" :class="vendorStore.isNoMoreRecord.value ? 'hidden' : ''"> {{ $t("follower_list__load_more") }} </ps-button>
                            <ps-button v-else class="font-medium mx-auto mt-6" @click="loadMore" :disabled="true"> {{ $t("follower_list__loading") }} </ps-button>
                        </div>
                    </div>
                    <ps-no-result v-if="vendorStore.loading.value == false && vendorStore.vendorList?.data == null" @onClick="loadMore" />
                </div>
            </div>
        <vendor-application-modal ref="vendor_application_modal" />
        </template>
    </ps-content-container>
</template>

<script lang="ts">
import { Head } from '@inertiajs/vue3';
import {defineComponent, ref, onMounted } from 'vue';
import { useVendorStore } from '@templateCore/store/modules/vendor/VendorStore';
import PsContentContainer from '@template1/vendor/components/layouts/container/PsContentContainer.vue';
import PsLabelHeader4 from '@template1/vendor/components/core/label/PsLabelHeader4.vue';
import PsLabel from '@template1/vendor/components/core/label/PsLabel.vue';
import PsButton from '@template1/vendor/components/core/buttons/PsButton.vue';
import PsIcon from '@template1/vendor/components/core/icons/PsIcon.vue';
import PsRouteLink from '@template1/vendor/components/core/link/PsRouteLink.vue';
import PsBreadcrumb2 from '@template1/vendor/components/core/breadcrumbs/PsBreadcrumb2.vue';
import PsNoResult from "@template1/vendor/components/modules/list/PsNoResult.vue";
import { trans } from 'laravel-vue-i18n';

//Modules
import PsFrontendLayout from '@template1/vendor/components/layouts/container/PsFrontendLayout.vue';
import VendorHorizontalVendor from '@template1/vendor/components/modules/item/VendorHorizontalVendor.vue';
import VendorApplicationModal from '@template1/vendor/components/modules/vendor/VendorApplicationModal.vue';

export default defineComponent({
    name : "VendorListView",
    components : {
        Head,
        PsContentContainer,
        PsLabelHeader4,
        VendorHorizontalVendor,
        PsLabel,
        PsButton,
        PsIcon,
        PsRouteLink,
        PsBreadcrumb2,
        PsNoResult,
        VendorApplicationModal
    },
    layout: PsFrontendLayout,
    props: ['query'],
    setup(props) {
        const ownerUserId = props.query.ownerUserId;
        const vendorStore = useVendorStore('profile-vendor-list');

        const vendor_application_modal = ref();

        onMounted(async() => {
            await vendorStore.resetVendorList(ownerUserId, ownerUserId);
        });

        function addNewVendor() {
            vendor_application_modal.value.openModal();
        }

        function loadMore() {
            vendorStore.loadVendorList(ownerUserId, ownerUserId);
        }

        return {
            vendorStore,
            vendor_application_modal,
            addNewVendor,
            loadMore
        }
    },
    computed: {
        breadcrumb() {
            return [
                {
                    label: trans('ps_nav_bar__home'),
                    url: route('dashboard')
                },
                {
                    label: trans('ps_nav_bar__profile'),
                    url: route('fe_profile')
                },
                {
                    label: trans('core_fe__my_vendors'),
                    color: "text-fePrimary-500"
                }
            ]
        }
    },
})
</script>
