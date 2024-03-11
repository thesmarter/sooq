<template>
    <Head :title="$t('register__register_title')"/>
    <div>
        <div class=" sm:mt-32 lg:mt-36 mt-28  flex flex-col">

            <div class=" content-center mx-auto w-96"  >
                <ps-label class="content-center bg-feAchromatic-50 text-general-200  px-4 py-4 rounded-md" v-if="userProvider.userResource.message != ''"> {{ userProvider.userResource.message }} </ps-label>
            </div>

            <div class=" w-55 lg:w-80 flex flex-col mx-auto rounded-lg">

                <!-- Register Title -->
                <ps-label class="font-medium text-2xl mt-8 mx-auto lg:text-3xl mb-4" > {{ $t("register__register_title") }} </ps-label>

                <!-- User Entry -->
                <form class=" w-full">
                    <ps-label class="mt-4 lg:mt-5 font-medium text-base lg:text-xl w-full">{{ $t("register__user_name") }} </ps-label>
                    <ps-input class="lg:mt-2 mt-1 w-full" type="email" v-bind:placeholder="$t('register__user_name')" v-on:keyup.enter="clicked" v-model:value="name"></ps-input>
                    <ps-label class="mt-4 lg:mt-5 font-medium text-base lg:text-xl w-full"> {{ $t("register__email") }} </ps-label>
                    <ps-input class="lg:mt-2 mt-1 w-full" type="email" v-bind:placeholder="$t('register__email')" v-on:keyup.enter="clicked" v-model:value="email" @blur="validateEmail"></ps-input>
                    <ps-label class="lg:mt-2 mt-1  w-full" textColor="text-fePrimary-500" v-if="validationEmail"> {{ $t("login__email_required_error") }} </ps-label>
                    <ps-label class="mt-4 lg:mt-5 font-medium text-base lg:text-xl w-full"> {{ $t("register__password") }} </ps-label>
                    <ps-input class="lg:mt-2 mt-1 w-full" type="password" v-bind:placeholder="$t('register__password')" v-on:keyup.enter="clicked" v-model:value="password" autocomplete='off'></ps-input>
                    <ps-label class="mt-4 lg:mt-5 font-medium text-base lg:text-xl w-full"> {{ $t("register__confirm_password") }} </ps-label>
                    <ps-input class="lg:mt-2 mt-1 w-full" type="password" v-bind:placeholder="$t('register__confirm_password')" v-on:keyup.enter="clicked" v-model:value="confirmPassword" autocomplete='off'></ps-input>
                </form>


                <!-- Loading Button -->
                <ps-button textSize="text-xs lg:text-base" class="mt-4 lg:mt-5 w-full py-3" v-if="userProvider.loading.value" :disabled="true">
                    {{ $t("register__loading") }} </ps-button>

                <!-- Register Button -->
                <ps-button textSize="text-xs lg:text-base" class="mt-4 lg:mt-5 py-3" @click="clicked" :disabled="false" v-else>
                    {{ $t("register__register") }} </ps-button>

                <!-- Privacy Policy -->
                <div class='flex flex-row items-start mt-2 lg:mt-3'>
                    <ps-checkbox-value title=""  @click="agreePrivacyPolicyClicked"
                        class="lg:mt-1 mt-0.5" v-model:value="checkedterms" />
                    <ps-label class='me-2 font-medium text-sm lg:text-base'> {{ $t("register__check_privacy_policy") }} </ps-label>
                    <ps-route-link :to="{name: 'privacy' }" class="underline cursor-pointer text-sm" url="https://smart.sd/privacy" >
                        <ps-label class='font-medium text-sm lg:text-base' > {{ $t("register__privacy_policy") }} </ps-label>
                    </ps-route-link>
                </div>

                <div class="flex flex-auto justify-center mt-3">
                    <ps-label textColor="text-feSecondary-400 dark:text-feAchromatic-500" class='cursor-pointer  font-light text-xs lg:text-sm' @click="loginClicked">  {{ $t("register__already_member_login") }}  </ps-label>
                </div>

                <div class="flex flex-row justify-center mt-4 ">
                    <ps-line class="w-full lg:h-3 h-2 border border-s-0 border-e-0 border-feSecondary-400 dark:border-feAchromatic-500" />
                    <ps-label class="px-1 font-medium text-sm lg:text-base"> {{ $t("login__or") }} </ps-label>
                    <ps-line class="w-full lg:h-3 h-2 border border-s-0 border-e-0 border-feSecondary-400 dark:border-feAchromatic-500" />
                </div>

                <ps-button textSize="text-xs lg:text-base" class="mt-3 lg:mt-4 py-3 flex justify-center" @click="phoneloginclicked">
                     <!-- <font-awesome-icon :icon="['fas', 'phone']" class="text-feAchromatic-50 dark:text-feAchromatic-900 text-xl me-2" /> -->
                    {{ $t("register__register_with_phone") }}
                </ps-button>

                <ps-button textSize="text-xs lg:text-base" class="mt-3 lg:mt-4 py-3 flex justify-center" theme="bg-feInfo-600 hover:bg-feInfo-700 text-feAchromatic-50 px-4 py-2" @click="facebookloginclicked">
                    <!-- <font-awesome-icon :icon="['fab', 'facebook-f']" class="text-feAchromatic-50 text-xl me-2"  /> -->
                    {{ $t("register__register_with_facebook") }}
                </ps-button>
                <ps-button textSize="text-xs lg:text-base" class="mt-3 lg:mt-4 py-3 flex justify-center mb-2" theme="bg-feWarning-600 hover:bg-feWarning-700 text-feAchromatic-50 px-4 py-2"  id="signinBtn" @click="googleloginclicked">
                    <!-- <font-awesome-icon :icon="['fab', 'google']" class="text-feAchromatic-50 text-xl me-2"  /> -->
                    {{ $t("register__register_with_google") }}
                </ps-button>
                <ps-button textSize="text-xs lg:text-base" class="mt-3 lg:mt-4 py-3 flex justify-center mb-5" theme="bg-feAchromatic-900 hover:bg-feAchromatic-700 text-feAchromatic-50 px-4 py-2"  @click="appleloginclicked">
                    <!-- <font-awesome-icon :icon="['fab', 'apple']" class="text-feAchromatic-50 text-xl me-2"  /> -->
                    {{ $t("register__register_with_apple") }}
                </ps-button>

                <div class="my-4" />
            </div>

            <ps-error-dialog ref="ps_error_dialog" />
            <ps-success-dialog-2 ref="ps_success_dialog" />

        </div>
        <privacy-modal ref="privacy_modal" />
    </div>
</template>

<script lang="ts">
import { Head } from '@inertiajs/vue3';
import PsLabel from '@template1/vendor/components/core/label/PsLabel.vue';
import PsButton from '@template1/vendor/components/core/buttons/PsButton.vue';
import PsInput from '@template1/vendor/components/core/input/PsInput.vue';
import { useUserProviderState } from '@templateCore/store/modules/user/UserProvider';
import { PsValueStore } from '@templateCore/store/modules/core/PsValueStore';
// import router from '@template1/router';
import { ref } from 'vue';
import PsCheckboxValue from '@template1/vendor/components/core/checkbox/PsCheckboxValue.vue';
import PsErrorDialog from '@template1/vendor/components/core/dialog/PsErrorDialog.vue';
import PsSuccessDialog2 from '@template1/vendor/components/core/dialog/PsSuccessDialog2.vue';
// import { useRoute } from 'vue-router';
import PsRouteLink from '@template1/vendor/components/core/link/PsRouteLink.vue';
import PrivacyModal from '@template1/vendor/components/modules/privacy/PrivacyModal.vue';
import { trans } from 'laravel-vue-i18n'; import PsLine from '@template1/vendor/components/core/line/PsLine.vue';
 import PsStatus from '@templateCore/api/common/PsStatus';

export default {
    name : "RegisterView",
    components : {
        PsButton,
        PsLine,
        PsInput,
        PsLabel,
        PsCheckboxValue,
        PsErrorDialog,
        PsRouteLink,
        PrivacyModal,
        PsSuccessDialog2,
        Head
    },
    setup() {

        // const route = useRoute();

        if(PsValueStore.psValueStore.isUserLoggedIn()) {
            // router.push({name: "dashboard"});
        }

        const email = ref('');
        const password = ref('');
        const confirmPassword = ref('');
        const name = ref('');
        const validationEmail = ref(false);

        // For checkbox terms
        const checkedterms = ref(false);
        const ps_error_dialog = ref();
        const ps_success_dialog = ref();
        const privacy_modal = ref();
        // Inject Provider
        const userProvider = useUserProviderState();
        userProvider.userResource.message = '';

        function validateEmail(e) {
            const pattern = /^([a-zA-Z0-9]+)@([a-zA-Z0-9]+)\.([a-zA-Z]{2,5})$/;
            const res = pattern.test(e.target.value);
            if (!res) {
                validationEmail.value = true;
            } else {
                validationEmail.value = false;
            }
        }

        async function clicked() {
            // Login
            if(password.value == confirmPassword.value){
                if( checkedterms.value == true) {
                    await userProvider.signUpWithEmailId(name.value,email.value, password.value,
                        () => {

                            redirect();

                        },
                        () => {

                            // router.push({ name : "verify-email"});
                        }
                    );

                } else {
                    ps_error_dialog.value.openModal('',
                    trans('register__select_privacy_policy'));
                }

            }else{
                ps_error_dialog.value.openModal('',
                    trans('register__passwords_not_match_error'));

            }

        }

        function loginClicked() {
            // if(checkedterms.value == true) {
                // userProvider.navigateUserLoginTo("login", router, route.query.redirect, route.query, route.params);

            // }else {
            //     ps_error_dialog.value.openModal(
            //         trans('login__privacy_policy'),
            //         trans('login__need_to_agree_privacy'));
            // }

        }

        function isAcceptPrivacy(isAccept){
            checkedterms.value = isAccept;
        }
        function phoneloginclicked() {
            if(checkedterms.value == true) {
                // userProvider.navigateUserLoginTo("phone-login", router, route.query.redirect, route.query, route.params);

            }else {
                ps_error_dialog.value.openModal(
                    trans('login__privacy_policy'),
                    trans('login__need_to_agree_privacy'));
            }
        }

        function agreePrivacyPolicyClicked() {
            privacy_modal.value.openModal(isAcceptPrivacy);
        }
        async function facebookloginclicked() {
            if(checkedterms.value == true) {
                await userProvider.loginWithFacebookId();
                if(userProvider.userResource.status == PsStatus.SUCCESS) {
                    ps_success_dialog.value.openModal(
                    trans('register__success'),
                    trans('register__login_success'),
                    trans('register_ok'),
                    () => {

                            redirect();

                        }
                    );
                }

            }else {
                ps_error_dialog.value.openModal('',
                trans('register__select_privacy_policy'));
            }
        }

        async function appleloginclicked() {
            if(checkedterms.value == true) {
                await userProvider.loginWithAppleSignIn();
                if(userProvider.userResource.status == PsStatus.SUCCESS) {
                    redirect();
                }
            }else {
                ps_error_dialog.value.openModal(
                trans('register__privacy_policy'),
                trans('register__need_to_agree_privacy'));
            }
        }

        async function googleloginclicked() {
            if(checkedterms.value == true) {
                await userProvider.loginWithGoogleId();
                if(userProvider.userResource.status == PsStatus.SUCCESS) {
                    ps_success_dialog.value.openModal(
                    trans('register__success'),
                    trans('register__login_success'),
                    trans('register_ok'),
                    () => {

                            redirect();

                        }
                    );

                }

            }else {
                ps_error_dialog.value.openModal('',
                trans('register__select_privacy_policy'));
            }
        }
        function redirect(){
            // userProvider.navigateUserLoginRedirect(route.query.redirect, 'dashboard', router, route.query.redirect, route.query, route.params);
        }

        return {
        clicked,
        email,
        password,
        confirmPassword,
        name,
        userProvider,
        checkedterms,
        ps_error_dialog,
        ps_success_dialog,
        loginClicked,
        agreePrivacyPolicyClicked,
        privacy_modal,
        facebookloginclicked,
        googleloginclicked ,
        phoneloginclicked,
        validateEmail,
        validationEmail,
        appleloginclicked
        };
    }
}
</script>
