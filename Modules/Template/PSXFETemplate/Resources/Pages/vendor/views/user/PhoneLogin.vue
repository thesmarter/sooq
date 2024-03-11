<template>
    <Head :title="$t('phone_login__phone_login')"/>
    <div class=" sm:mt-28 lg:mt-28 mt-28 bg-fePrimary-500 dark:bg-feAccent-500  h-screen flex flex-col">

        <div class=" mt-56 w-96 flex flex-col mx-auto bg-feAchromatic-50 dark:bg-feAchromatic-900  m-auto rounded-lg">
            <ps-label-title class="mt-8 mx-auto text-2xl" > {{ $t("phone_login__phone_login") }} </ps-label-title>

            <ps-label class="mt-4 mx-8 "> {{ $t("phone_login__user_name") }} : </ps-label>
            <ps-input class="mt-2 mx-8" type="text" v-bind:placeholder="$t('phone_login__user_name')" v-on:keyup.enter="clicked" @keypress="validateName" v-model:value="name"></ps-input>
            <ps-label-caption v-if="isValidUserName" class="mt-2 mx-8" textColor="text-fePrimary-500 dark:text-feAccent-500" >{{ $t("phone_login__user_name_required") }}</ps-label-caption>
            <ps-label-caption class="mt-2 mx-8">{{ $t("phone_login__user_name_format") }}</ps-label-caption>

            <ps-label class="mt-4 mx-8 "> {{ $t("phone_login__phone_number") }} : </ps-label>
            <ps-input class="mt-2 mx-8" type="text" v-bind:placeholder="$t('phone_login__phone_number_placeholder')" v-on:keyup.enter="clicked" @keypress="validatePhone" v-model:value="phone"></ps-input>
            <ps-label-caption v-if="isValidPhone" class="mt-2 mx-8" textColor="text-fePrimary-500 dark:text-feAccent-500" >{{ $t("phone_login__phone_required") }}</ps-label-caption>
            <div id="recaptcha-container"></div> <br>
            <div class="flex items-center justify-center mb-4">

                <!-- Loading Button -->
                <ps-button class="mt-6 mx-8" v-if="userProvider.loading.value" :disabled="true">
                    {{ $t("phone_login__loading") }} </ps-button>

                <!-- Submit Button -->
                <ps-button class="mt-6 mx-8" @click="clicked" :disabled="false" v-else>
                    {{ $t("phone_login__login") }} </ps-button>
            </div>
            <div class="flex flex-auto justify-center mt-4 mb-10">
                <ps-label-caption class='cursor-pointer' @click="loginClicked"> {{ $t("phone_login__back_to_login") }} </ps-label-caption>
            </div>
        </div>
    </div>

    <user-phone-login-verification-modal ref="user_phone_login_verification_modal" />

    <ps-error-dialog ref="ps_error_dialog" />

</template>

<script lang='ts'>
import { Head } from '@inertiajs/vue3';
//Vue
import { ref } from 'vue';
// import firebaseApp from 'firebase/app';
// import "firebase/auth";
import { onMounted } from 'vue';
// import { useRoute } from 'vue-router';
// import router from '@template1/router';

// Components
import PsLabel from '@template1/vendor/components/core/label/PsLabel.vue';
import PsLabelTitle from '@template1/vendor/components/core/label/PsLabelTitle.vue';
import PsLabelCaption from '@template1/vendor/components/core/label/PsLabelCaption.vue';
import PsButton from '@template1/vendor/components/core/buttons/PsButton.vue';
import PsInput from '@template1/vendor/components/core/input/PsInput.vue';
import UserPhoneLoginVerificationModal from '@template1/vendor/components/modules/user/UserPhoneLoginVerificationModal.vue';
import PsErrorDialog from '@template1/vendor/components/core/dialog/PsErrorDialog.vue';

// Providers
import { PsValueStore } from "@templateCore/store/modules/core/PsValueStore";
import { useUserProviderState } from '@templateCore/store/modules/user/UserProvider';

// Params Holders
import PhoneLoginParameterHolder from '@templateCore/object/holder/PhoneLoginParameterHolder';
import PsStatus from '@templateCore/api/common/PsStatus';

//language
import { trans } from 'laravel-vue-i18n';import PsUtils from '@templateCore/utils/PsUtils';

export default {
    name : "PhoneLoginView",
    components : {
        PsLabel,
        PsLabelTitle,
        PsButton,
        PsInput,
        UserPhoneLoginVerificationModal,
        PsErrorDialog,
        PsLabelCaption,
        Head
    },
    setup() {

        // Inject Provider
        const userProvider = useUserProviderState();
        const holder = new PhoneLoginParameterHolder();

        // Init Values
        // const route = useRoute();
        const psValueStore = PsValueStore();
        const loginUserId = psValueStore.getLoginUserId();
        const name = ref();
        const phone = ref();
        const isValidUserName = ref(false);
        const isValidPhone = ref(false);
        const user_phone_login_verification_modal = ref();
        const ps_error_dialog = ref();
        let appVerifier;
        userProvider.loading.value = true;
        function loadAppVerifier()  {
            // Init recaptchaVerifier
            userProvider.loading.value = true;
            setTimeout(()=>{
                // (window as any).recaptchaVerifier = new firebaseApp.auth.RecaptchaVerifier('recaptcha-container', {
                //     'size': 'invisible',
                //     'callback': (response) => {
                //         PsUtils.log("Callback");
                //         PsUtils.log(response);
                //     },
                //     'expired-callback': () => {
                //         PsUtils.log("expiry callback")
                //     }
                // });

                appVerifier =  (window as any).recaptchaVerifier;
                userProvider.loading.value = false;
            },1000);
        }

        onMounted(() =>{
            loadAppVerifier();
        })

        function loginClicked() {
            // Redirect
            // userProvider.navigateUserLoginTo("login", router, route.query.redirect, route.query, route.params);
        }

        async function clicked() {

            let errorStatus = true;
            if (name.value == '' || name.value == undefined) {
                isValidUserName.value = true;
                errorStatus = false;
            }
            if (name.value == '' || name.value == undefined) {
                isValidPhone.value = true;
                errorStatus = false;
            }
            if(!errorStatus) {
                window.scrollTo(0, 0);
                return;
            }

            holder.phoneId = loginUserId;
            holder.userName = name.value;
            holder.userPhone = phone.value;

            userProvider.loading.value = true;

            const verifier = appVerifier;
            // const confirmationResult = await firebaseApp.auth().signInWithPhoneNumber(phone.value, verifier).catch((error) => {
            //     userProvider.loading.value = false;

            //     ps_error_dialog.value.openModal(
            //         trans('phone_login__error_in_sign_in'), error?.message);
            //     // loadAppVerifier();
            // });

            userProvider.loading.value = false;

            // if(confirmationResult != undefined) {
            //     user_phone_login_verification_modal.value.openModal(
            //         trans('phone_no_verification_modal__title'),
            //         // 'Phone No Verification',
            //         '',
            //         'Submit',
            //         'Cancel',
            //         async (code) => {
            //             userProvider.loading.value = true;
            //             confirmationResult.confirm(code).then(async (userCredential) => {

            //                 if(userCredential != null
            //                     && userCredential.user != null
            //                     && userCredential.user.uid != null
            //                     && userCredential.user.uid != '' ) {
            //                         // call backend server
            //                         const user = userCredential.user;
            //                         holder.phoneId = user.uid;
            //                         await userProvider.submitUserLoginWithPhoneId( user.uid, name.value, phone.value);

            //                         if(userProvider.userResource.status == PsStatus.SUCCESS) {
            //                             userProvider.navigateUserLoginRedirect(route.query.redirect, 'dashboard', router, route.query.redirect, route.query, route.params);
            //                         }else {
            //                             ps_error_dialog.value.openModal(trans('phone_login__error_in_sign_in'), userProvider.userResource.message);
            //                             userProvider.setLoading(false);
            //                         }


            //                     }else {

            //                         userProvider.setLoading(false);
            //                         // loadAppVerifier();

            //                     }
            //             }).catch((error) => {

            //                 userProvider.setLoading(false);
            //                 ps_error_dialog.value.openModal(trans('phone_login__error_in_sign_in'), error?.message);

            //             });
            //         },
            //         () => {
            //             PsUtils.log("Cancel");
            //         }
            //     );
            // }

        }
        function validateName(e) {
            const pattern = /^\/$|(\/[a-zA-Z_0-9-]+)+$/;
            const res = pattern.test(e.target.value);
            if (!res) {
                isValidUserName.value = false;
            } else {
                isValidUserName.value = true;
            }
        }
        function validatePhone(e) {
            const pattern = /^\/$|(\/[a-zA-Z_0-9-]+)+$/;
            const res = pattern.test(e.target.value);
            if (!res) {
                isValidPhone.value = false;
            } else {
                isValidPhone.value = true;
            }
        }
        return{
            userProvider,
            clicked,
            name,
            phone,
            user_phone_login_verification_modal,
            ps_error_dialog,
            loginClicked,
            isValidUserName,
            validateName,
            validatePhone,
            isValidPhone
        }
    }
}
</script>
