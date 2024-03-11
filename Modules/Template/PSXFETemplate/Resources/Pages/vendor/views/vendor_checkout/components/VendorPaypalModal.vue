<template>

    <ps-modal ref="psmodal"  line="hidden"  :isClickOut='false' >
        <template #body>
            <div id="dropin-container"></div>
            <div class="flex justify-center mx-auto">
                <ps-button id="submit-button">{{ $t('paypal_credit_card_modal__submit_payment') }}</ps-button>
                <ps-button theme="btn-second" class="text-center mx-4" @click="actionClicked('no')" > {{ $t('paypal_credit_card_modal__close') }} </ps-button>
            </div>
        </template>
    </ps-modal>
    <!-- <ps-loading-dialog ref="psloading"  :isClickOut='false'/> -->

    <ps-error-dialog ref="ps_error_dialog" />
</template>

<script lang='ts'>
import { defineComponent, ref} from 'vue';

import PsModal from '@template1/vendor/components/core/modals/PsModal.vue';
import PsButton from '@template1/vendor/components/core/buttons/PsButton.vue';

import { PsValueStore } from '@templateCore/store/modules/core/PsValueStore';
import PsErrorDialog from '@template1/vendor/components/core/dialog/PsErrorDialog.vue';
import { useVendorItemBoughtStore } from "@templateCore/store/modules/vendor_checkout/VendorItemBoughtStore";
import { trans } from 'laravel-vue-i18n';
import * as dropins from 'braintree-web-drop-in';
export default defineComponent({
    name : 'VendorPaypalModal',
    components : {
        PsModal,
        PsButton,
        PsErrorDialog,
    },
     setup() {
        const psmodal = ref();
        const ps_error_dialog = ref();
        const psValueStore = PsValueStore();
        const loginUserId = psValueStore.getLoginUserId();
        const vendorItemBoughtStore = useVendorItemBoughtStore();

        let okClicked: Function;
        let cancelClicked: Function;

        function actionClicked(status) {
            if(status == 'yes') {
                okClicked();
            }else {
                cancelClicked();
            }
            psmodal.value.toggle(false);
        }

        async function openModal(
                okClickedAction: Function,
                cancelClickedAction: Function,
                vendorId : string) {

                psmodal.value.toggle(true);
                okClicked = okClickedAction;
                cancelClicked= cancelClickedAction;
                //for braintree paypal dropin
                const returnData = await vendorItemBoughtStore.getVendorPaypalToken(vendorId,loginUserId);

                const button = document.querySelector('#submit-button');
                const dropin = dropins;

                dropin.create({
                    authorization: returnData.data.message,
                    container: '#dropin-container',
                    paypal: {
                        flow: 'vault',
                    }
                }, function (createErr, instance) {
                    button?.addEventListener('click', function () {
                        if (instance.isPaymentMethodRequestable()) {
                            setTimeout(function() {
                                instance.requestPaymentMethod(async function (err, payload) {
                                    console.log(payload);
                                    if(err) {
                                        ps_error_dialog.value.openModal('',
                                        trans('paypal_credit_card_modal__error_paid_ad'));
                                        return;
                                    }
                                    // Submit payload.nonce to your server
                                    vendorItemBoughtStore.paymentNonce = payload.nonce;
                                    actionClicked('yes');

                                });
                            }, 200);
                        }

                    });
                });
        }

        return {
            psmodal,
            ps_error_dialog,
            openModal,
            actionClicked,
        }
    },
})
</script>
