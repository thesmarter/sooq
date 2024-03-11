<template>
  <ps-modal ref="psmodal" line="hidden" maxWidth="1200px" :isClickOut='false' class='h-full' bodyHeight="max-h-[600px]" theme="p-6 border rounded" bgColor="bg-feAchromatic-50 dark:bg-feAchromatic-800">
      <template #title>
          <div class="w-full flex justify-end h-6">
              <ps-icon class="cursor-pointer dark:text-feSecondary-500" name="close" w="24" h="24" @click="psmodal.toggle(false)"/>
          </div>
      </template>
      <template #body>
        <div class="w-full flex justify-center">
            <ps-label textColor="text-4xl font-semibold text-fePrimary-500 text-feAchromatic-50">{{ $t("vendor_choose_subscription") }}</ps-label>
        </div>
        <div class="flex flex-wrap justify-center mt-5 mb-10">
            <div class="w-140 font-normal text-center text-xl text-feSecondary-800 dark:text-feAchromatic-200">{{ $t("vendor_choose_subscription_description") }}</div>
        </div>

        <div class="flex justify-center h-96" >
            <vendor-choose-subscription-plan-item v-for="plan in vendorSubScriptionStore.subScriptionList?.data" :key="plan.id" :plan="plan" :vendorId="vendorId"/>
        </div>
      </template>
  </ps-modal>
</template>

<script>
import { defineComponent, ref } from 'vue';
import { PsValueStore } from '@templateCore/store/modules/core/PsValueStore';
import { useVendorSubScriptionStoreState } from '@templateCore/store/modules/vendorSubScription/VendorSubScriptionStore';
import PsModal from '@template1/vendor/components/core/modals/PsModal.vue';
import PsIcon from '@template1/vendor/components/core/icons/PsIcon.vue';
import PsLabel from '@template1/vendor/components/core/label/PsLabel.vue';
import PsButton from '@template1/vendor/components/core/buttons/PsButton.vue';
import PsInput from '@template1/vendor/components/core/input/PsInput.vue';
import PsTextarea from '@template1/vendor/components/core/textarea/PsTextarea.vue';
import PsFileUpload from '../item/PsFileUpload.vue';
import PrivacyModal from '@template1/vendor/components/modules/privacy/PrivacyModal.vue';
import PsLoadingDialog from '@template1/vendor/components/core/dialog/PsLoadingDialog.vue';
import VendorChooseSubscriptionPlanItem from './VendorChooseSubscriptionPlanItem.vue';

export default defineComponent({
  name: 'VendorChooseSubscriptionPlanModal',
  components: {
      PsModal,
      PsIcon,
      PsLabel,
      PsButton,
      PsInput,
      PsTextarea,
      PsFileUpload,
      PrivacyModal,
      PsLoadingDialog,
      VendorChooseSubscriptionPlanItem
  },
  setup() {
      const psValueStore = PsValueStore();
      const loginUserId = psValueStore.getLoginUserId();
      const vendorSubScriptionStore = useVendorSubScriptionStoreState();

      const psmodal = ref();
      const isError = ref(false);
      const error = ref();
      const ps_loading_dialog = ref();
      const vendorId = ref('');

      async function openModal(id) {
          await vendorSubScriptionStore.resetVendorSubscriptionPlanList(loginUserId);
          vendorId.value = id;
          psmodal.value.toggle(true);
      }

      function closeModal() {
          psmodal.value.toggle(false);
      }

      return {
          vendorSubScriptionStore,
          psmodal,
          openModal,
          closeModal,
          isError,
          error,
          ps_loading_dialog,
          vendorId
      }
  },
})
</script>
