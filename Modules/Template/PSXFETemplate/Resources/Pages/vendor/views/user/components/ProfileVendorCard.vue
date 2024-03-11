<template>
    <div class="w-full">
            <div class="w-full bg-feWarning-100 rounded-lg mx-auto py-0 p-3 mb-4 dark:bg-feWarning-700">
                <div class="w-full grid grid-cols-4 p-5">

                    <div class="w-full h-full flex justify-center items-center col-span-4 md:col-span-1 lg:col-span-1">
                        <img v-lazy="{ src: $page.props.sysImageUrl + '/VendorAnnouement-2.png' }" alt=""
                                class="w-36 object-contain">
                    </div>

                    <div class="w-full col-span-4 md:col-span-3 lg:col-span-3">


                        <div class="space-y-2 flex flex-col items-center md:items-start pt-3">
                            <ps-label-header6 textColor="text-feAchromatic-800 dark:text-feAchromatic-100">{{ $t("core__fe_profile_vendor_header") }}</ps-label-header6>
                            <span class="text-feAchromatic-500 whitespace-wrap text-center md:text-left dark:text-feAchromatic-200">{{ $t("core__fe_profile_vendor_text") }}</span>
                            <div class="mt-4">
                                <ps-button @click="addNewVendor()">{{ $t("core__fe_button_become_a_vendor") }}</ps-button>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    <vendor-application-modal ref="vendor_application_modal" />
</template>

<script>
import {defineComponent, ref, onMounted } from 'vue';

// Components
import PsLabel from '@template1/vendor/components/core/label/PsLabel.vue';
import PsLabelHeader3 from '@template1/vendor/components/core/label/PsLabelHeader3.vue';
import PsLabelHeader6 from '@template1/vendor/components/core/label/PsLabelHeader6.vue';
import PsButton from '@template1/vendor/components/core/buttons/PsButton.vue';
import VendorApplicationModal from '@template1/vendor/components/modules/vendor/VendorApplicationModal.vue';

export default defineComponent({
    name: 'DashboardHowItWorksCard',
    components: {
        PsLabel,
        PsLabelHeader3,
        PsLabelHeader6,
        PsButton,
        VendorApplicationModal
    },
    setup() {
        const vendor_application_modal = ref();

        function addNewVendor() {
            vendor_application_modal.value.openModal();
        }

        return {
            vendor_application_modal,
            addNewVendor
        }
    }
})

</script>
