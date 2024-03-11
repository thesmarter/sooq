<template>
    <div>
       <ps-modal ref="psmodal"  line="hidden" maxWidth="534px" :isClickOut='false' class='h-full  ' bodyHeight="max-h-75" theme="px-4 py-8 sm:p-8 rounded-lg">
        <template #title>
          <div class="w-full flex justify-end h-6">
              <ps-icon class="cursor-pointer dark:text-feSecondary-500" name="close" w="24" h="24" @click="psmodal.toggle(false)"/>
          </div>
      </template>
        <template #body>
            <ps-label class="mt-6 sm:mt-7 font-medium text-base"> {{ $t('promote_item_modal__pay_with') }}  </ps-label>
            <div class="grid grid-cols-3 sm:grid-cols-3 gap-4 mt-6">
                <ps-button colors="bg-transparent dark:bg-feSecondary-50" border="border hover:shadow" hover="none" v-if="appInfoStore.appInfo.data?.paypalEnable == '1'" class=""  rounded="rounded-lg" @click="paymentClicked('PAYPAL')" >
                    <img v-lazy="{ src: $page.props.sysImageUrl + '/paypal.png' }" alt=""
                               class="w-full h-full object-contain bottom-0"
                                >
                </ps-button>

                <ps-button colors="bg-transparent  dark:bg-feSecondary-50" border="border hover:shadow" hover="none" v-if="appInfoStore.appInfo.data?.stripeEnable == '1'" class=""  rounded="rounded-lg" @click="paymentClicked('STRIPE')" >
                    <img v-lazy="{ src: $page.props.sysImageUrl + '/stripe.png' }" alt=""
                               class="w-full h-full object-contain bottom-0"
                                >
                </ps-button>
                <ps-button colors="bg-transparent dark:bg-feSecondary-50" border="border hover:shadow" hover="none" v-if="appInfoStore.appInfo.data?.razorEnable == '1'" class="" rounded="rounded-lg" @click="paymentClicked('RAZOR')" >
                    <img v-lazy="{ src: $page.props.sysImageUrl + '/razorpay.png' }" alt=""
                               class="w-full h-full object-contain bottom-0"
                                >
                </ps-button>
                <ps-button colors="bg-transparent dark:bg-feSecondary-50" border="border hover:shadow" hover="none" v-if="appInfoStore.appInfo.data?.payStackEnabled == '1'" class=""  rounded="rounded-lg" @click="paymentClicked('PAYSTACK')" >
                    <img v-lazy="{ src: $page.props.sysImageUrl + '/paystack.png' }" alt=""
                               class="w-full h-full object-contain bottom-0"
                                >
                </ps-button>
                <ps-button v-show="false" colors="bg-transparent dark:bg-feSecondary-50" v-if="appInfoStore.appInfo.data?.offlineEnabled == '1'" class="" border="border hover:shadow" hover="none" padding="px-8 py-4" rounded="rounded-lg" @click="paymentClicked('OFFLINE')" >
                    <ps-icon class=" dark:text-feSecondary-800 pr-1" name="wallet" w="20" h="20"/>
                                <ps-label textColor=" font-semibold" >Offline</ps-label>
                </ps-button>
            </div>
        </template>
    </ps-modal>
    <stripe-payment-modal ref="stripe_payment_modal" />

    <paypal-payment-modal ref="paypal_payment_modal" />

    <ps-warning-dialog-2 ref="ps_warning_dialog" />

    <offline-payment-modal ref="offline_payment_modal" />

    <ps-error-dialog ref="ps_error_dialog" />
    <input-email-modal ref="input_email" />
 </div>

</template>

<script lang='ts'>

/// Razorpay
import 'https://checkout.razorpay.com/v1/checkout.js';

// Libs
import {defineComponent, ref,onMounted } from 'vue';


// Compone
import PsModal from '@template1/vendor/components/core/modals/PsModal.vue';
import PsLabel from '@template1/vendor/components/core/label/PsLabel.vue';
import PsButton from '@template1/vendor/components/core/buttons/PsButton.vue';
import PsRadio from '@template1/vendor/components/core/radio/PsRadio.vue';
import PsRadio2 from '@template1/vendor/components/core/radio/PsRadio2.vue';
import PsErrorDialog from '@template1/vendor/components/core/dialog/PsErrorDialog.vue';
import PsWarningDialog2 from '@template1/vendor/components/core/dialog/PsWarningDialog2.vue';
import PsIcon from '@template1/vendor/components/core/icons/PsIcon.vue';

import StripePaymentModal from '@template1/vendor/components/modules/credit/StripePaymentModal.vue';
import PaypalPaymentModal from '@template1/vendor/components/modules/credit/PaypalPaymentModal.vue';
import OfflinePaymentModal from '@template1/vendor/components/modules/credit/OfflinePaymentModal.vue';
import InputEmailModal from '@template1/vendor/components/modules/email/InputEmailModal.vue';

// Providers
import { usePsAppInfoStoreState } from '@templateCore/store/modules/appinfo/AppInfoStore';
import { PsValueStore } from '@templateCore/store/modules/core/PsValueStore';
import { useUserStore } from "@templateCore/store/modules/user/UserStore";
import { useVendorPlanBoughtStoreState } from "@templateCore/store/modules/vendor/VendorPlanBoughtStore";
import VendorPlanBoughtParameterHolder from '@templateCore/object/holder/VendorPlanBoughtParameterHolder';
import AppInfoParameterHolder from '@templateCore/object/holder/AppInfoParameterHolder';

import PsStatus from '@templateCore/api/common/PsStatus';
import { trans } from 'laravel-vue-i18n';import PsConst from '@templateCore/object/constant/ps_constants';

import PaystackPop from '@paystack/inline-js';
import PsUtils from '@templateCore/utils/PsUtils';
import format from 'number-format.js';
export default defineComponent({
    name: "LimitItemModal",
    components: {
        PsModal,
        PsIcon,
        PsLabel,
        PsButton,
        PsErrorDialog,
        PsRadio2,
        PsRadio,
        StripePaymentModal,
        PaypalPaymentModal,
        OfflinePaymentModal,
        PsWarningDialog2,
        InputEmailModal,
    },
   setup() {
        const psmodal = ref();
        const stripe_payment_modal = ref();
        const paypal_payment_modal = ref();
        const offline_payment_modal = ref();
        const ps_error_dialog = ref();
        const input_email = ref();
        const ps_warning_dialog = ref();

        const vendorId = ref();
        const subScriptionPlan = ref();

        const vendorPlanBoughtStore = useVendorPlanBoughtStoreState();

        const vendorPlanBoughtParameterHolder = new VendorPlanBoughtParameterHolder();
        const appInfoStore = usePsAppInfoStoreState();
        const appInfoParameterHolder = new AppInfoParameterHolder();
        const userProvider = useUserStore();

        const psValueStore = PsValueStore();
        const loginUserId = psValueStore.getLoginUserId();

        // Price Per Day
        const pricePerDay = ref(0);
        const razorKey = ref('');
        const razorId = ref('');
        const price = ref(0);

        async function openModal(vId,plan) {

            vendorId.value = vId,
            subScriptionPlan.value = plan;
            price.value = subScriptionPlan.value.paymentAttributes.discountPrice == 0 ? subScriptionPlan.value.paymentAttributes.salePrice : subScriptionPlan.value.paymentAttributes.discountPrice;

            psmodal.value.toggle(true);
            const info = await appInfoStore.loadAppInfo(appInfoParameterHolder);

            razorKey.value = info.data.razorKey;
            if(info.status == PsStatus.SUCCESS) {
                pricePerDay.value = parseInt(info.data.oneDay, 10);
            }else {
                pricePerDay.value = 0;
            }

        }

        function paymentClicked(value){

            if(appInfoStore.appInfo.data.mobileSetting.is_demo_for_payment == 1){
                ps_warning_dialog.value.openModal(
                    trans('payment__warning_title'),
                    trans('payment__confirm_message'),
                    trans('payment__ok'),
                    trans('credit_card_modal__cancel'),
                    () => {
                        if(value == 'PAYPAL'){
                            paypalClicked();
                        }else if(value == 'STRIPE'){
                            stripeClicked();
                        }else if(value == 'RAZOR'){
                            razorClicked();
                        }else if(value == 'PAYSTACK'){
                            paystackClicked();
                        }else if(value == 'OFFLINE'){
                            offlineClicked();
                        }
                    },
                    () => {
                        PsUtils.log("Cancel");
                    });


            }else{
                if(value == 'PAYPAL'){
                    paypalClicked();
                }else if(value == 'STRIPE'){
                    stripeClicked();
                }else if(value == 'RAZOR'){
                    razorClicked();
                }else if(value == 'PAYSTACK'){
                    paystackClicked();
                }else if(value == 'OFFLINE'){
                    offlineClicked();
                }
            }


        }

        function stripeClicked() {

            psmodal.value.toggle(false);

            stripe_payment_modal.value.openModal(
                () => {
                    const payment = PsConst.PAYMENT_STRIPE_METHOD.toString();
                    doPayment(payment);
                },
                () => {
                    PsUtils.log("Cancel");
                }
            );

        }

        async function doPayment(payment) {
            vendorPlanBoughtParameterHolder.userId = loginUserId;
            vendorPlanBoughtParameterHolder.subscriptionPlanId = subScriptionPlan.value.paymentInfo?.id;
            vendorPlanBoughtParameterHolder.paymentMethod = payment;
            vendorPlanBoughtParameterHolder.paymentMethodNounce = localStorage.paymentNonce || '';
            vendorPlanBoughtParameterHolder.price = price.value;
            vendorPlanBoughtParameterHolder.razorId = razorId.value || '';
            vendorPlanBoughtParameterHolder.vendorId = vendorId.value;


            const subscriptionPlanItem = await vendorPlanBoughtStore.postSubscriptionPlanBought(loginUserId, vendorPlanBoughtParameterHolder);
            psmodal.value.toggle(false);
            if(subscriptionPlanItem.status == PsStatus.ERROR) {
                ps_error_dialog.value.openModal('', subscriptionPlanItem.message);
            } else {
                localStorage.paymentNonce = "";
                location.reload();
            }
        }

        function paypalClicked() {
            psmodal.value.toggle(false);
            paypal_payment_modal.value.openModal(
                () => {
                    const payment = PsConst.PAYMENT_PAYPAL_METHOD.toString();
                    doPayment(payment);
                },
                () => {
                    PsUtils.log("Cancel");
                }
            );
        }

        async function razorClicked() {
            const returnData = await userProvider.loadUser(loginUserId);
            const userInfo = returnData.data;
            //for razor ui
            const options =
            {
                "key": razorKey.value, // Enter the Key ID generated from the Dashboard
                "name": "Test Company",
                "prefill": {
                    "email": userInfo.userEmail,
                    "name": userInfo.userName,
                    "contact": userInfo.userPhone
                },
                "theme": {
                    "color": "#0e9f6e"
                },
                // This handler function will handle the success payment
                "handler": async function (response) {
                    razorId.value =  response.razorpay_payment_id;
                    // Submit response.razorpay_payment_id to your server
                    doPayment(PsConst.PAYMENT_RAZOR_METHOD.toString())

                },
            };

            const rzp1 = new (window as any).Razorpay(options);
            rzp1.open();
            rzp1.on('payment.failed', function (response)
            {
                // alert(response.error);
            });

        }

        async function paystackClicked() {
            const returnData = await userProvider.loadUser(loginUserId);
            const userInfo = returnData.data;
            let email = userInfo.userEmail;
            if(userInfo.userEmail == "" || userInfo.userEmail == null){
                input_email.value.openModal(
                    (emailStr) => {
                         email = emailStr;
                        const paystack = PaystackPop.setup({
                            key: appInfoStore?.appInfo?.data.payStackKey,
                            email: email,
                            amount: subScriptionPlan.value.paymentAttributes?.salePrice,
                            callback: async function(response){
                                PsUtils.log(response);
                                // Payment complete! Reference: ' + response.reference;
                                doPayment(PsConst.PAYMENT_PAY_STACK_METHOD.toString());

                            },
                            onClose: function(){
                                // alert("close");
                                // user closed popup
                            }

                        });
                        paystack.openIframe();
                    },
                     () => {
                         console.log('cancel');
                    }  );
            }else{
                const paystack = PaystackPop.setup({
                key: appInfoStore?.appInfo?.data.payStackKey,
                email: email,
                amount: subScriptionPlan.value.paymentAttributes?.salePrice,
                callback: async function(response){
                    PsUtils.log(response);
                    // Payment complete! Reference: ' + response.reference;
                    doPayment(PsConst.PAYMENT_PAY_STACK_METHOD.toString());

                },
                onClose: function(){
                    // alert("close");
                    // user closed popup
                }

            });
            paystack.openIframe();
            }

        }

        function offlineClicked() {
            psmodal.value.toggle(false);

            offline_payment_modal.value.openModal(
                () => {
                    const payment = PsConst.PAYMENT_OFFLINE_METHOD.toString();
                    doPayment(payment);
                },
                () => {
                    PsUtils.log("Cancel");
                }
            );
        }

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
                    }else{
                        return format(appInfoStore?.appInfo?.data?.mobileSetting?.price_format, value);
                    }
            }
        }

        return {
            psmodal,
            openModal,
            vendorId,
            appInfoStore,
            ps_error_dialog,
            stripe_payment_modal,
            paypal_payment_modal,
            offline_payment_modal,
            ps_warning_dialog,
            paymentClicked,
            input_email,
            formatPrice
        }
    },
})
</script>
