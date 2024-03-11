<template>
    <Head :title="$t('contact_us__contact_us')"/>
    <ps-content-container>
        <template #content>
            <div class="bg-feAchromatic-50 dark:bg-feAchromatic-900 rounded-lg w-auto sm:w-[700px]  sm:mt-32 lg:mt-36 mt-28 mb-16 mx-auto py-4 flex flex-row ">
                <div class=" w-full h-full">
                            <img v-lazy="{ src: $page.props.sysImageUrl + '/contact_us.png' }" alt=""
                             class="w-full h-full object-cover bottom-0">
                        </div>
                <div class="w-full flex flex-col justify-center">
                    <div class="flex flex-row justify-center items-center">
                    <ps-label-header-6 class="font-semibold ">{{$t("contact_us__get_in_touch_with_us")}}</ps-label-header-6>
               
                </div>
                    <div class="px-8">
                        <ps-label class="mt-5">{{ $t("contact_us__contact_name") }}<span style="font-size: 17px; color: red;">*</span> </ps-label>
                        <ps-input class="mt-1"  theme="text-feArchromatic-300 dark:text-feSecondary-400" type="text" v-bind:placeholder="$t('contact_us__enter_name')" v-model:value="name"></ps-input>
                        <ps-label class="mt-4" textColor="text-fePrimary-500" v-if="validation.nameStatus"> {{ $t("contact_us__name_required_error") }} </ps-label>
                    </div>
                    <div class="px-8">
                        <ps-label class="mt-5">{{ $t("contact_us__contact_phone") }}<span style="font-size: 17px; color: red;">*</span> </ps-label>
                        <ps-input class="mt-1 " theme="text-feArchromatic-300 dark:text-feSecondary-400"  type="text" v-bind:placeholder="$t('contact_us__enter_phone_number')" v-model:value="phone" @keypress="phoneNumber($event)"></ps-input>
                        <ps-label class="mt-4" textColor="text-fePrimary-500" v-if="validation.phoneStatus"> {{ $t("contact_us__phone_required_error") }} </ps-label>
                    </div>

                    <div class="px-8">
                        <ps-label class="mt-5"> {{ $t("contact_us__contact_email") }} <span style="font-size: 17px; color: red;">*</span> </ps-label>
                        <ps-input class="mt-1" theme="text-feArchromatic-300 dark:text-feSecondary-400" type="email" v-bind:placeholder="$t('contact_us__enter_email')" v-model:value="email" @keypress="validateEmail"></ps-input>
                        <ps-label class="mt-4" textColor="text-fePrimary-500" v-if="validation.emailStatus"> {{ $t("contact_us__email_required_error") }} </ps-label>
                    </div>

                    <div class="ml-8 mr-20">
                        <ps-label class="mt-5"> {{ $t("contact_us__contact_message") }} <span style="font-size: 17px; color: red;">*</span> </ps-label>
                        <ps-textarea rows="5"  class="mt-1"  theme="text-feArchromatic-300 dark:text-feSecondary-400" v-bind:placeholder="$t('contact_us__write_message')" v-model:value="message"></ps-textarea>
                        <ps-label class="mt-4" textColor="text-fePrimary-500" v-if="validation.messageStatus"> {{ $t("contact_us__message_required_error") }} </ps-label>
                    </div>

                    <div class="flex flex-col items-center w-full px-8 mb-4">
                        <ps-button class="mt-6 w-full" @click="submitclicked" :disabled="false"> {{ $t("contact_us__submit") }} </ps-button>
                    </div>

                </div>
            </div>
        </template>
    </ps-content-container>

    <ps-success-dialog ref="ps_success_dialog" />

</template>

<script lang="ts">
//Vue
import {ref} from 'vue';
import { Head } from '@inertiajs/vue3';

import PsContentContainer from '@template1/vendor/components/layouts/container/PsContentContainer.vue';
import PsLabelHeader6 from '@template1/vendor/components/core/label/PsLabelHeader6.vue';
import PsLabel from '@template1/vendor/components/core/label/PsLabel.vue';
import PsInput from '@template1/vendor/components/core/input/PsInput.vue';
import PsButton from '@template1/vendor/components/core/buttons/PsButton.vue';
import PsTextarea from '@template1/vendor/components/core/textarea/PsTextarea.vue';
import PsSuccessDialog from '@template1/vendor/components/core/dialog/PsSuccessDialog.vue';

import { useContactUsStoreState } from "@templateCore/store/modules/contact/ContactUsStore";

// Params Holders
import ContactUsPrameterHolder from '@templateCore/object/holder/ContactUsParameterHolder';
import { trans } from 'laravel-vue-i18n';
import PsFrontendLayout from '@template1/vendor/components/layouts/container/PsFrontendLayout.vue';
import { PsValueStore } from "@templateCore/store/modules/core/PsValueStore";

export default {
    name : "ContactView",
    components : {
        PsContentContainer,
        PsLabelHeader6,
        PsLabel,
        PsInput,
        PsButton,
        PsTextarea,
        PsSuccessDialog,
        Head
    },
    layout: PsFrontendLayout,
    setup() {

        // Inject Item Provider
        const contactUsStore = useContactUsStoreState();

        const paramHolder = new ContactUsPrameterHolder();
        const ps_success_dialog = ref();
        const name = ref('');
        const phone = ref('');
        const email = ref('');
        const message = ref('');
        const validation = ref({
            nameStatus : false,
            phoneStatus : false,
            emailStatus : false,
            messageStatus : false,
        });

        const psValueStore = PsValueStore();
        const loginUserId = psValueStore.getLoginUserId();

        function validateEmail(e) {
            const pattern = /^([a-zA-Z0-9]+)@([a-zA-Z0-9]+)\.([a-zA-Z]{2,5})$/;
            const res = pattern.test(e.target.value);
            if (!res) {
                validation.value.emailStatus = true;
            } else {
                validation.value.emailStatus = false;
            }
        }

        function phoneNumber(evt) {
            evt = (evt) ? evt : window.event;
            const charCode = (evt.which) ? evt.which : evt.keyCode;

            if ( charCode < 42 || charCode > 57 ) {
                evt.preventDefault();
            } else {
                return true;
            }
        }

        async function submitclicked() {
            paramHolder.name = name.value;
            paramHolder.phone = phone.value;
            paramHolder.email = email.value;
            paramHolder.message = message.value;
            if (paramHolder.name == '' || paramHolder.name == undefined) {
                validation.value.nameStatus = true;
                return;
            }

            if (paramHolder.phone == '' || paramHolder.phone == undefined) {
                validation.value.phoneStatus = true;
                return;
            }

            if (paramHolder.email == '' || paramHolder.email == undefined) {
                validation.value.emailStatus = true;
                return;
            }

            if (paramHolder.message == '' || paramHolder.message == undefined) {
                validation.value.messageStatus = true;
                return;
            }
            // contact us
            await contactUsStore.postContactUs(paramHolder,loginUserId);
            ps_success_dialog.value.openModal('Success!',  trans('contact_us__add_successed'));

           name.value = '';
            phone.value = '';
            email.value = '';
            message.value = '';

            validation.value.nameStatus = false;
            validation.value.phoneStatus = false;
            validation.value.emailStatus = false;
            validation.value.messageStatus = false;
        }

        return {
            contactUsStore,
            submitclicked,
            name,
            phone,
            email,
            message,
            validation,
            validateEmail,
            phoneNumber,
            ps_success_dialog
        }
    }
}
</script>
