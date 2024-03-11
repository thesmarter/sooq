<template>
    <ps-modal ref="psmodal" maxWidth="480px" line="hidden" :isClickOut='false' theme=" px-6 py-6 rounded-lg shadow-xl"
        class=' z-50 h-56 bg-feAchromatic-50'>
        <template #title>
            <div class="w-full text-start">
                <ps-label class="text-lg font-semibold"> {{ title }} </ps-label>
            </div>
        </template>
        <template #body>
            <div class="w-full text-start mt-2">

                    <div class="mb-4">
                        <ps-label>{{ $t('core__be_username') }}</ps-label>
                        <ps-input ref="username" type="text" v-model:value="form.username"
                            :placeholder="$t('core__be_username_placeholder')" autofocus />
                        <ps-label-caption textColor="text-fePrimary-500 " class="mt-2 block">{{ usernameError }}</ps-label-caption>
                        <!-- <ps-label-caption textColor="text-fePrimary-500 " class="mt-2 block">{{ "errors" }}</ps-label-caption> -->
                    </div>
                    <div class="mb-4" v-if="hasPassword == false">
                        <ps-label>{{ $t('core__be_password') }}</ps-label>
                        <ps-input-with-right-icon v-model:value="form.password" ref="password"
                            :type="(isHide ? 'password' : 'text')" @keyup="validateEmptyInput('password', form.password)"
                            @blur="validateEmptyInput('password', form.password)"
                            :placeholder="$t('core__be_password_placeholder')" autocomplete="current-password">
                            <template #icon>
                                <ps-icon @click="isHide = !isHide" class="cursor-pointer"
                                    :name="isHide ? 'eyeOff' : 'eye-on'" />
                            </template>
                        </ps-input-with-right-icon>
                        <ps-label-caption textColor="text-fePrimary-500 " class="mt-2 block">{{  passwordError }}</ps-label-caption>
                        <!-- <ps-label-caption textColor="text-fePrimary-500 " class="mt-2 block">{{ errors.password
                        }}</ps-label-caption> -->
                    </div>

                    <!-- <div class="flex justify-between mb-4">
                            <ps-checkbox-value name="remember" v-model:checked="form.remember"
                                :title="$t('core__be_remember_me')" />
                            <ps-label class="cursor-pointer" textColor="text-fePrimary-500" v-if="canResetPassword"
                                @click="handleReset">
                                {{ $t('core__be_forgot_password') }}
                            </ps-label>
                        </div> -->



            </div>
        </template>
        <template #footer>
            <div class=" flex flex-row justify-end">
                <ps-button rounded="rounded" @click="actionClicked('no')" textSize="text-xs lg:text-sm" class=" me-4"
                    border="border border-feAchromatic-200" colors="bg-none"
                    hover="hover:outline-none hover:ring hover:ring-feAchromatic-100"> {{ cancelButton }} </ps-button>
                <ps-button rounded="rounded" @click="actionClicked('yes')" textSize="text-xs lg:text-sm" class="">
                    {{ okButton }} </ps-button>
            </div>
        </template>
    </ps-modal>
</template>

<script lang="ts">
import { defineComponent, ref, computed,onMounted } from 'vue';
import PsModal from '../modals/PsModal.vue';
import PsLabel from '../label/PsLabel.vue';
import PsButton from '../buttons/PsButton.vue';


import { router } from '@inertiajs/vue3';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { trans } from 'laravel-vue-i18n';

import PsCard from "@/Components/Core/Card/PsCard.vue";
import useValidators from "@/Validation/Validators";
import PsInput from "@/Components/Core/Input/PsInput.vue";
import PsInputWithRightIcon from "@/Components/Core/Input/PsInputWithRightIcon.vue";
import PsIcon from "@/Components/Core/Icons/PsIcon.vue";
import PsCheckboxValue from "@/Components/Core/Checkbox/PsCheckboxValue.vue";
// import { useProductStore } from "@templateCore/store/modules/item/ProductStore";
import { useAuthStore } from '../../../../../../../../../resources/js/store/AuthStore'
import UserExistParameterHolder from '@templateCore/object/holder/UserExistParameterHolder';
import PsLabelCaption from "@/Components/Core/Label/PsLabelCaption.vue";


export default defineComponent({
    name: "PsAccountDialog",
    components: {
        PsModal,
        PsLabel,
        PsButton,
        PsCheckboxValue,
        PsIcon,
        PsInput,
        PsInputWithRightIcon,
        PsLabelCaption,
        PsCard
    },
    props: {
        usernameError: Object,
        passwordError: Object,
        loginUserId:String,

    },
    setup(props) {
        const isHide = ref(true);

        let form = useForm({
            email: '',
            password: '',
            remember: false,
            deviceToken: '',
            headerToken: '',
            loginMethod: '',
            google_id: '',
            profile_photo_url: '',
            name: '',
            displayName: '',
            registerMethod: '',
            user_phone: '',
            username: '',
            phone_id: '',
        });

        onMounted(async () => {
            // paramHolder.id = props.loginUserId
            // paramHolder.loginMethod="checkFromDashboard"
            //  const UserLogindata = await authStore.existUser(paramHolder);
            // //  alert(UserLogindata.data.message.user.username);
            //  if(UserLogindata.data.message.user.username != ''){
            //     alert("here")
            //     psmodal.value.toggle(false);
            //  }
        })


        // if(props.loginUserId == '1')
        // {
        //     alert("here");
        // }
        const psmodal = ref();
        const title = ref(trans('ps_confirm_dialog__confirm'));
        const message = ref(trans('ps_confirm_dialog__are_you_confirm'));
        const okButton = ref(trans('ps_confirm_dialog__yes'));
        const username=ref('');
        const hasPassword=ref(false);
        const cancelButton = ref(trans('ps_confirm_dialog__no'));
        const authStore = useAuthStore();
        const paramHolder = new UserExistParameterHolder();
        let okClicked: Function;
        let cancelClicked: Function;
        // let usernameError = props.errors?.username ? ref(props.errors.username[0]) : '';
        // let passowrdError = props.errors?.password ? ref(props.errors.password[0]) : '';

        const { isEmpty, minLength, isEmail } = useValidators();
        const validateEmptyInput = (fieldName, fieldValue) => {
            // props.errors[fieldName] = !fieldValue ? isEmpty(fieldName, fieldValue) : minLength(fieldName, fieldValue, 4);
            // if (fieldName == 'password') {
            //     password.value.isError = (props.errors.password == '') ? false : true;
            // }
        };

        const validateEmailInput = (fieldName, fieldValue) => {
            // props.errors[fieldName] = !fieldValue ? isEmpty(fieldName, fieldValue) : isEmail(fieldName, fieldValue);
            // if (fieldName == 'email') {
            //     email.value.isError = (props.errors.email == '') ? false : true;
            // }
        }


        function actionClicked(status) {
            if (status == 'yes') {
                okClicked();
            } else {
                cancelClicked();
                psmodal.value.toggle(false);
            }

        }
        function closeModal(){
           psmodal.value.toggle(false)
        }

        function openModal(
            titleString,
            messageString,
            okString,
            cancelString,
            usernameString,
            passwordPassword,
            okClickedAction: Function,
            cancelClickedAction: Function) {
            title.value = titleString;
            message.value = messageString.toString();
            form.username = usernameString.toString();
            hasPassword.value = passwordPassword.toString() == "true" ? true:false
            okButton.value = okString;
            cancelButton.value = cancelString;
            psmodal.value.toggle(true);
            okClicked = okClickedAction;
            cancelClicked = cancelClickedAction;
        }

        return {
            psmodal,
            title,
            message,
            openModal,
            actionClicked,
            okButton,
            cancelButton,
            form,
            isHide,
            validateEmptyInput,
            validateEmailInput,
            username,
            hasPassword,
            // usernameError,
            // passowrdError,
            closeModal
        }
    },
})
</script>
