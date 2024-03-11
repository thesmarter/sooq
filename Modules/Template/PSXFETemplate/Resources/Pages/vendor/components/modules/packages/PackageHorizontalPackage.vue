<template>
    <div :class="'border-feSecondary-600'" class="bg-feSecondary-800 border-2 rounded-lg py-5 hover:bg-feAchromatic-900 cursor-pointer ">
        <div class="text-center">
            <ps-label textColor="text-base md:text-lg font-semibold text-feSecondary-50 ">{{packageList.paymentInfo.value}}</ps-label>
        </div>
        <div class="flex gap-1 mt-1 place-content-center ">
            <ps-icon class="text-feSecondary-50" name="user-fill" w="20" h="20" viewBox="0 0 20 20"/>
            <ps-label textColor="text-sm font-medium text-feSecondary-50">({{packageList.purchasedCount}})</ps-label>
            
        </div>
        <hr class="mt-4 mb-4 mx-10">
        <div class="mt-1 lg:mt-2 text-center">
            <ps-label textColor="text-sm md:text-base font-medium text-feSecondary-50">{{ packageList.paymentAttributes.count }} posts</ps-label>
          
        </div>
        <div class="flex justify-center ">
            <ps-label textColor="text-sm sm:text-base font-semibold text-feSecondary-50">{{ packageList.paymentAttributes.currencySymbol }} {{ formatPrice(packageList.paymentAttributes.price) }}</ps-label>
        </div>
    </div>
</template>


<script>
import format from 'number-format.js';

import '@splidejs/vue-splide/css';
import { trans } from 'laravel-vue-i18n';
import { usePsAppInfoStoreState } from '@templateCore/store/modules/appinfo/AppInfoStore';
import PsLabel from '@template1/vendor/components/core/label/PsLabel.vue';
import PsIcon from '@template1/vendor/components/core/icons/PsIcon.vue';

    export default {
        name: "PackageHorizontalPackage",
        components: {
            PsLabel,
            PsIcon
        },
        props: {
            packageList: Object,
        },
        setup(){
            const appInfoStore = usePsAppInfoStoreState();
            function formatPrice(value) {
                if(value.toString() == '0') {
                    return trans('item_price__free');
                }else {
                    // return format(appInfoStore?.appInfo?.data?.mobileSetting?.price_format, value)
                    if(appInfoStore?.appInfo?.data?.mobileSetting?.price_format === "###,###"){
                        const formattedNumber = new Intl.NumberFormat('en-FR', {
                                                                        style: 'decimal',
                                                                        useGrouping: true,
                                                                        minimumFractionDigits: 0,
                                                                        }).format(value);
                        return formattedNumber.replace(",", ' ');
                    }else{
                        return format(appInfoStore?.appInfo?.data?.mobileSetting?.price_format, value);
                    }
                }
            }

            return{
                formatPrice
            }
        }
    }
</script>
