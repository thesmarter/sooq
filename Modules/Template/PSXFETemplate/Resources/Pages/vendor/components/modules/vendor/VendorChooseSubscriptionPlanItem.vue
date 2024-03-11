<template>
    <div :class="{ 'border-fePrimary-500 h-100': isHover , 'h-90' : !isHover}" class="w-2/5 mx-5 rounded overflow-hidden shadow-lg border border-inherit bg-feAchromatic-50 dark:bg-feAchromatic-800 cursor-pointer"
    @click="buyAdClick" @mouseover="mouseHover" @mouseleave="mouseUnHover">
    <div class="bg-feSecondary-100 dark:bg-feAchromatic-900 flex p-10 justify-start items-start flex-col">

        <div class="flex">
            <div class=" text-feSecondary-800 dark:text-feAchromatic-50 px-1 py-1 text-sm mb-2 mr-2">{{ $t("vendor_price") }}</div>
            <div v-if="plan.paymentAttributes.isMostPopularPlan != '0'" class="bg-fePrimary-500 text-white px-2 py-1 rounded text-sm mb-2">{{ $t("vendor_most_popular") }}</div>
        </div>

      <div class="flex items-end">
        <div v-if="plan.paymentAttributes.discountPrice != '0'" class="flex gap-2 items-end text-5xl font-bold text-fePrimary-500 text-feAchromatic-50 mr-2">
          {{plan ? plan.paymentAttributes.currencySymbol : ''}}{{plan ? plan.paymentAttributes.discountPrice : ''}}
            <div  class="text-gray-500 text-xl line-through">
            {{plan ? plan.paymentAttributes.currencySymbol : ''}}{{plan ? plan.paymentAttributes.salePrice : ''}}
            </div>
        </div>
        <div v-else class="text-5xl font-bold text-fePrimary-500 text-feAchromatic-50 mr-2">
          {{plan ? plan.paymentAttributes.currencySymbol : ''}}{{plan ? plan.paymentAttributes.salePrice : ''}}
        </div>

      </div>
    </div>
    <div  class="mt-8 w-full flex justify-center items-center flex-col mb-10">
        <ps-label :class="{ 'pt-5': isHover }" textColor="text-feSecondary-800 dark:text-feAchromatic-50 text-3xl font-semibold pb-3">{{plan ? plan.paymentInfo.value : ''}}</ps-label>
        <ps-label textColor="text-feSecondary-800 dark:text-feAchromatic-50 pb-6">{{plan ? plan.paymentAttributes.duration : ''}}</ps-label>
        <ps-button  :class="{ 'mt-5 bg-fePrimary-500 text-white': isHover }" colors="bg-feAchromatic-50 dark:bg-feAchromatic-800" border=" border border-gray-200" class="text-feSecondary-800 dark:text-feAchromatic-200 w-5/6 mx-10 px-4 py-2" >Purchase</ps-button>
    </div>
  </div>

  <vendor-plan-bought-modal ref="vendor_plan_bought_modal"/>
</template>

<script>
import vendorSubscriptionPlan from '@templateCore/object/VendorSubscriptionPlan';

import PsLabel from '@template1/vendor/components/core/label/PsLabel.vue'
import PsButton from '@template1/vendor/components/core/buttons/PsButton.vue';
import { ref , defineAsyncComponent } from 'vue';
import VendorPlanBoughtModal from './VendorPlanBoughtModal.vue';

export default{
  components: { PsLabel , PsButton , VendorPlanBoughtModal},
    name : "VendorChooseSubscriptionPlanItem",
    props : {
      key : Number,
      plan : {type : vendorSubscriptionPlan},
      vendorId : String
    },
    setup(props){
      const vendor_plan_bought_modal = ref();
      const isHover = ref(false);

      function mouseHover(){
        isHover.value = true;
      }

      function mouseUnHover(){
        isHover.value = false;
      }

      async function buyAdClick(){
        vendor_plan_bought_modal.value.openModal(props.vendorId,props.plan);
      }
      return {
        buyAdClick,
        vendor_plan_bought_modal,
        mouseHover,
        mouseUnHover,
        isHover
      }
    }
}


</script>
