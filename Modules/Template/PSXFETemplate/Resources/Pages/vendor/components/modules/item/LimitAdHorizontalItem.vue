<template>

    <!-- Start Pscard -->
    <div class="sm:w-full md:w-full shadow-sm relative border border-feSecondary-100 dark:border-feSecondary-800 rounded-lg py-5 " v-on:click="onClick != null ? onClick(buyad) : null">
        
        <!-- Package -->
        <ps-label textColor="text-base md:text-lg font-semibold text-center text-fePrimary-500">{{buyad ? buyad.package.paymentInfo.value : ''}} </ps-label>
        
        <!-- Price -->
        <ps-label textColor="text-base md:text-lg font-semibold text-center text-feSecondary-800 dark:text-feSecondary-300 mt-2">{{buyad ? buyad.package.paymentAttributes.currencySymbol : ''}}{{buyad ? buyad.package.paymentAttributes.price : ''}}</ps-label>

        <hr class="mt-4 mb-4 mx-6">
        
            <!-- Posts -->
            <ps-label class="flex justify-center items-center text-sm font-medium mt-2 text-feSecondary-700">
                <span>{{buyad ? buyad.user.remainingPost : ''}} {{ $t("package__posts") }} {{ "Available" }}</span>
            </ps-label>

            <!-- Purchased Date -->
            <ps-label class="flex justify-center items-center text-sm font-medium mt-2 text-feSecondary-700">
                <span>{{ buyad ?  moment(buyad.addedDate.split(" ")[0]).format($page.props.dateFormat)  : ''}}</span>
            </ps-label>

            <!-- Purchased Time -->
            <ps-label class="flex justify-center items-center text-sm font-medium mt-2 text-feSecondary-700">
                <span>{{buyad ? buyad.addedDate.split(" ")[1] : ''}}</span>
            </ps-label>

            <!-- Payment Type -->
            <ps-label class="flex justify-center items-center text-sm font-medium mt-2 text-feSecondary-700">
                <span>{{buyad ? buyad.paymentMethod : ''}}</span>
            </ps-label>   
    
    </div>

    <!-- END Pscard -->
</template>

<script lang="ts">

// Components
import PsLabel from '@template1/vendor/components/core/label/PsLabel.vue';

//language

// test

import { trans } from 'laravel-vue-i18n';
import format from 'number-format.js';

// Objects
import LimitAdTransaction from '@templateCore/object/LimitAdTransaction';

// Providers
import { usePsAppInfoStoreState } from '@templateCore/store/modules/appinfo/AppInfoStore';
import AppInfoParameterHolder from '@templateCore/object/holder/AppInfoParameterHolder';
import { PsValueStore } from "@templateCore/store/modules/core/PsValueStore";
import { usePsValueHolderState } from '@templateCore/object/core/PsValueHolder';
import moment from "moment";

export default {
    name : "LimitAdHorizontalItem",
    components : {
        PsLabel
    },
    props : {
        buyad : { type : LimitAdTransaction } ,
        onClick : Function,
        padding: {
            type: String,
            default: "py-10 px-10 lg:px-3"
        }   
    },
    setup() {
        // Inject Provider
        PsValueStore.psValueStore = usePsValueHolderState();

        const psValueStore = PsValueStore();
        const loginUserId = psValueStore.getLoginUserId();
        const appInfoParameterHolder = new AppInfoParameterHolder();
        appInfoParameterHolder.userId = loginUserId;
        const appInfoStore = usePsAppInfoStoreState();
        
        function formatPrice(value) {
            if(value.toString() == '0') {
                return trans('item_price__free');
            }else {
                if(appInfoStore?.appInfo?.data?.mobileSetting?.price_format === "###,###"){
                        const formattedNumber = new Intl.NumberFormat('en-FR', {
                                                                        style: 'decimal',
                                                                        useGrouping: true,
                                                                        minimumFractionDigits: 0,
                                                                        }).format(value);
                        return formattedNumber.replace(",", ' ');
                    }else if(appInfoStore?.appInfo?.data?.mobileSetting?.price_format === "##,####"){
                        const formattedValue = parseFloat(value).toFixed(4);
                        let formattedNumberArr = formattedValue.split('.');
                        
                        let formattedNumber = formattedNumberArr.pop();
                        
                        formattedNumber = formattedNumberArr[0];
                        return formattedNumber.replace(/(\d)(?=(\d{4})+$)/g, '$1,');
                    }else{
                        return format(appInfoStore?.appInfo?.data?.mobileSetting?.price_format, value);
                    }
            }
        }
        return {
            formatPrice,
            psValueStore,
            moment
        }
    }
}
</script>
