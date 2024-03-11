<template>
    <div class="w-full h-full" v-on:click="onClick != null ? onClick(vendor) : null">
        <!-- Pscard -->
        <ps-card class="border flex w-full bg-feAchromatic-50 flex-col lg:rounded-lg shadow-sm px-4 py-6 rounded items-center" :enabledHover="true">
            <div class="w-20 h-20 relative rounded-full overflow-hidden">
                <img alt="Placeholder" class="w-20 h-20 rounded-full object-cover"
                v-lazy="{ src: $page.props.thumb2xUrl + '/' + vendor?.logo.imgPath, loading: $page.props.sysImageUrl + '/loading_gif.gif', error: $page.props.sysImageUrl + '/default_vendor.png' }" />
                <div v-if="vendor?.expireStatus == PsConst.VendorExpiryExpiredStatus" class="w-20 h-20 flex items-center justify-center absolute top-0 left-0">
                    <div class="w-20 h-20 absolute top-0 left-0 bg-feAchromatic-900 opacity-60"></div>
                    <div class="w-20 h-20 flex items-center justify-center absolute">
                        <ps-label textColor="text-feAchromatic-50 text-sm font-semibold">{{$t('Closed')}}</ps-label>
                    </div>
                </div>
            </div>
            <div class="h-14 flex flex-col items-center justify-center">
                <div class="mt-3 flex gap-2 items-center">
                    <ps-label textColor="text-lg font-semibold text-feSecondary-800 dark:text-feAchromatic-50">{{ vendor.name.length>15 ? vendor.name.slice(0,14)+'...' : vendor.name }}</ps-label>
                    <span class="px-1 py-[2px] text-xs rounded-sm bg-feWarning-500 text-feAchromatic-50">{{ $t('core_fe__vendor_indicator') }}</span>
                </div>
                <div v-if="vendor?.expireStatus == PsConst.VendorExpiryExpiredStatus">
                    <p class="text-sm text-red-500">
                        {{ $t("core_fe__vendor_subscription_expired") }}
                    </p>
                </div>
            </div>
            <ps-button class="mt-4 w-full h-10" :colors="color" :border="border" :hover="hover" @click="clickVendor(vendor.status, vendor.id)">
                <ps-icon v-if="icon" :name="icon" textColor="" w="24" h="24"/>
                <span class="ms-2">{{ $t(text) }}</span>
            </ps-button>
        </ps-card>
        <vendor-pending-vendor-modal ref="vendor_pending_vendor_modal"/>
        <vendor-application-modal :vendorId="vendor.id" ref="vendor_application_modal" />
        <vendor-rejected-modal ref="vendor_rejected_modal" />
    </div>
</template>

<script>
import {defineComponent, ref } from 'vue';
import { router } from '@inertiajs/vue3';
import PsCard from '@template1/vendor/components/core/card/PsCard.vue';
import PsLabel from '@template1/vendor/components/core/label/PsLabel.vue';
import PsButton from '@template1/vendor/components/core/buttons/PsButton.vue';
import PsIcon from '@template1/vendor/components/core/icons/PsIcon.vue';
import VendorPendingVendorModal from './VendorPendingVendorModal.vue';
import VendorApplicationModal from '../vendor/VendorApplicationModal.vue';
import VendorRejectedModal from '@template1/vendor/components/modules/vendor/VendorRejectedModal.vue';
import PsConst from '@templateCore/object/constant/ps_constants';

    export default defineComponent({
        name: 'VendorHorizontalVendor',
        components: {
            PsCard,
            PsLabel,
            PsButton,
            PsIcon,
            VendorPendingVendorModal,
            VendorApplicationModal,
            VendorRejectedModal,
        },
        props: {
            vendor: Object,
        },
        setup(props){
            const color = ref();
            const border = ref();
            const hover = ref();
            const icon = ref();
            const text = ref();
            const currentStatus = ref();

            const vendor_pending_vendor_modal = ref();
            const vendor_application_modal = ref();
            const vendor_rejected_modal = ref();
            console.log(props.vendor);
            const status = {
                pending: {
                    color: "text-feWarning-500 bg-feWarning-50 dark:bg-feWarning-100",
                    border: "border border-feWarning-500 dark:border-feWarning-500",
                    hover: "hover:outline-none hover:bg-feWarning-500 dark:hover:bg-feWarning-500 hover:text-feAchromatic-50 active:bg-feWarning-600",
                    icon: "pending",
                    text: "core_fe__pending"
                },
                rejected: {
                    color: "text-fePrimary-500 bg-fePrimary-50 dark:bg-fePrimary-100",
                    border: "border border-fePrimary-500",
                    hover: "hover:outline-none hover:bg-fePrimary-500 dark:hover:bg-fePrimary-500 hover:text-feAchromatic-50 active:bg-fePrimary-600",
                    icon: "rejected",
                    text: "core_fe__rejected"
                },
                success: {
                    color: "text-feSecondary-800 dark:text-feSecondary-200 bg-feAchromatic-50 dark:bg-feAchromatic-500",
                    border: "border border-feSecondary-500 dark:border-feAchromatic-100 hover:border-fePrimary-500",
                    hover: "hover:outline-none hover:bg-fePrimary-500 hover:text-feAchromatic-50 active:bg-fePrimary-600",
                    text: "core_fe__visit_vendor"
                },
            };

            if(props.vendor.status == 0){
                currentStatus.value = status.pending;
            }else if(props.vendor.status == 1){
                currentStatus.value = status.rejected;
            }else{
                currentStatus.value = status.success;
            }

            color.value = currentStatus.value.color;
            border.value = currentStatus.value.border;
            hover.value = currentStatus.value.hover;
            icon.value = currentStatus.value.icon;
            text.value = currentStatus.value.text;

            function clickVendor(status, id) {
                if(status == 0){
                    vendor_pending_vendor_modal.value.openModal();
                }else if(status == 1){
                    // vendor_application_modal.value.openModal();
                    vendor_rejected_modal.value.openModal(() => {
                        vendor_application_modal.value.openModal();
                    });
                }else{
                    router.get(route('fe_vendor_info'),{vendorId:id});
                }
            }

            return {
                icon,
                color,
                border,
                hover,
                icon,
                text,
                vendor_pending_vendor_modal,
                vendor_application_modal,
                vendor_rejected_modal,
                clickVendor,
                PsConst
            }
        }
    })
</script>
