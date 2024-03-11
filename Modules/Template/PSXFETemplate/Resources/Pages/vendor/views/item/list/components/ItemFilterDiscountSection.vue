
<template>
    <div class="w-full flex flex-col">
        <ps-label class="lg:mb-2 mb-1 lg:text-base font-medium text-sm "
            textColor="text-feSecondary-800 dark:text-feSecondary-300">{{ $t("item_list__is_discount") }}</ps-label>
        <div v-if="spaceWrap" class="sm:block md:flex flex-col w-full text-sm font-medium ">
            <ps-radio v-for=" selectData  in soldOutItem" :key="selectData.id" :value="selectData"
                v-model:selectedValue="itemProvider.currentstatus" :title="selectData.name" :onChange="isDiscountFilterClicked"
                class="text-feSecondary-800 whitespace-nowrap text-xs lg:text-sm me-2 py-1"></ps-radio>
        </div>
        <div v-else class="flex flex-col w-full text-sm font-medium">
            <ps-radio v-for=" selectData  in soldOutItem" :key="selectData.id" :value="selectData"
                v-model:selectedValue="itemProvider.currentstatus" :title="selectData.name" :onChange="isDiscountFilterClicked"
                class="text-feSecondary-800 text-xs lg:text-sm lg:me-6 me-2 py-1"></ps-radio>
        </div>
    </div>

    <ps-loading-dialog ref="ps_loading_dialog" class='z-40' />
</template>

<script>
// Libs
import { ref, onMounted } from 'vue';
// Components
import PsLabel from '@template1/vendor/components/core/label/PsLabel.vue';
import PsRadio from '@template1/vendor/components/core/radio/PsRadio.vue';
import PsLoadingDialog from '@template1/vendor/components/core/dialog/PsLoadingDialog.vue';
// Providers
import { PsValueStore } from "@templateCore/store/modules/core/PsValueStore";
import { useProductStore } from "@templateCore/store/modules/item/ProductStore";
//Utils
import PsUtils from '../../../../../../../../../TemplateCore/utils/PsUtils';

export default {
    name: 'ItemFilterDiscountSection',
    components: {
        PsLabel,
        PsRadio,
        PsLoadingDialog
    },
    setup() {
        const psValueStore = PsValueStore();
        const itemProvider = useProductStore('list');
        const ps_loading_dialog = ref();
        const soldOutItem = ref([
        {
                id: "", name:"All"
            },
            {
                id: "1", name: "Discounts only"
            },
            {
                id: "0", name: "No discounts only"
            },
          
        ]);

        async function isDiscountFilterClicked(value) {
            
            // itemProvider.paramHolder.isDiscount = itemProvider.isDiscount == false ? '1' : '';
            itemProvider.paramHolder.isDiscount = value.id;
            itemProvider.currentstatus.value = value.id;
            itemProvider.currentstatus.value = value.name;
          
            ps_loading_dialog.value.openModal();
            PsUtils.addParamToCurrentUrl(itemProvider.getURLforListByParam(itemProvider.paramHolder));
            await itemProvider.resetProductList(psValueStore.getLoginUserId(), itemProvider.paramHolder);
            ps_loading_dialog.value.closeModal();

            //hide popup filter
            itemProvider.showPopUpFilter = false;

        }

        onMounted(() => {
           
            if (itemProvider.paramHolder.isDiscount == '1') {
                itemProvider.currentstatus.id = "1";
                itemProvider.currentstatus.name = "Discounts only";

            } else  if(itemProvider.paramHolder.isDiscount == '0'){
                itemProvider.currentstatus.id = "0";
                itemProvider.currentstatus.name = "No discounts only";
                itemProvider.paramHolder.isDiscount == '0';
            }else{
                itemProvider.currentstatus.id = "";
                itemProvider.currentstatus.name = "All";
                itemProvider.paramHolder.isDiscount == '';
            }
        })

        return {
            itemProvider,
            isDiscountFilterClicked,
            ps_loading_dialog,
            soldOutItem
        }
    }

}

</script>
