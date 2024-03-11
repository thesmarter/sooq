<template>
    <div class="mt-10 w-full mb-10">
            <div class="border rounded-lg mx-auto py-0 mb-5 sm:mb-10 bg-feAchromatic-50 dark:bg-feAchromatic-800">
                <div class="w-full h-full flex flex-wrap sm:flex-none">

                    <div class="w-full h-full sm:w-full md:w-1/2 lg:w-1/2">
                        <div class="flex flex-col mt-12 pl-8 pr-3">
                            <ps-label class="text-sm" textColor="text-fePrimary-500">{{ $t("core__fe_empower_bussiness") }}</ps-label>
                            <ps-label-header6 class=" text-xl font-semibold tracking-wide mt-2 mb-3 " textColor="text-feAchromatic-700 dark:text-feAchromatic-50">{{ $t("core__fe_dashboard_become_vendor") }}</ps-label-header6>
                            <ps-label class="text-feAchromatic-400 text-md mt-2 dark:text-feSecondary-50">{{ $t("core__fe_dashboard_vendor_text") }}</ps-label>
                            <ps-button class="w-48 flex flex-row mt-5" padding="p-2 sm:px-4 sm:py-2" shadow="shadow-sm" rounded="rounded" hover=""
                                focus="" border="border border-feSecondary-200 dark:border-feSecondary-800"
                                @click="addNewVendor()"
                                colors="bg-fePrimary-500 text-feAchromatic-100 mb-10">{{ $t("core__fe_button_become_a_vendor") }}</ps-button>
                        </div>
                    </div>

                    <div class="w-full h-full sm:w-full md:w-1/2 lg:w-1/2 bg-fePrimary-300">
                        <div class="h-full w-full mx-auto flex justify-center items-center">
                            <img v-lazy="{ src: $page.props.sysImageUrl + '/vendorAnnouncement.png' }" alt=""
                                class="w-full h-80 object-contain">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <vendor-application-modal ref="vendor_application_modal" />
</template>

<script>
import { defineComponent, ref } from 'vue';
import { router } from '@inertiajs/vue3';
import { PsValueStore } from '@templateCore/store/modules/core/PsValueStore';
import PsConst from '@templateCore/object/constant/ps_constants';

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
        VendorApplicationModal,
    },
    setup() {
        const vendor_application_modal = ref();
        const psValueStore = PsValueStore();
        const LoginUserId = psValueStore.getLoginUserId();

        function addNewVendor() {
            if(LoginUserId && LoginUserId != PsConst.NO_LOGIN_USER){
                vendor_application_modal.value.openModal();
            }else{
                router.get(route('login'));
            }
        }

        return {
            vendor_application_modal,
            addNewVendor,
        }
    }
})

</script>
