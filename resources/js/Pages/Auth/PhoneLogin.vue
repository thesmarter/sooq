<template>
    <Head :title="$t('core__fe_sign_in')" />
    <div :class="isDarkMode ? 'dark' : ''">

        <div class="mb-6 bg-feAchromatic-50 dark:bg-feAchromatic-900 flex flex-row   mt-28  min-h-screen items-center justify-center">
            <div class="h-176 my-6 flex flex-row w-98 lg:w-202 rounded-md overflow-hidden shadow-md ">
                <div class="hidden w-100  h-auto bg-feSecondary-900  lg:flex flex-col items-center justify-center">
                    <div class="w-65 h-50">
                        <welcome-image />
                        <!-- <img  src="/images/assets/welcome_image.png" width="44" height="48" class="w-44 h-48 object-cover"/> -->
                    </div>
                    <ps-label class="text-lg font-semibold mt-6 mb-2" textColor="text-feSecondary-50">
                        {{ $t('login__fe_title_best_deal') }}
                    </ps-label>
                    <ps-label class="text-base font-normal text-center mb-2 mx-10" textColor="text-feSecondary-200">
                        {{ $t('login__fe_description_best_deal') }}
                    </ps-label>


                </div>
                <div class="flex-grow bg-feAchromatic-50 dark:bg-feSecondary-800 px-10 pb-10">




                    <!-- <div class="flex items-center justify-end px-2">


                    <ps-label-title class="text-xs font-semibold"> {{ $t("phone_login__phone_login") }} </ps-label-title>
                </div> -->
                    <form @submit.prevent="clicked">
                        <div class="px-6">
                            <!-- <img v-if="$page.props.backendLogo"
                            v-lazy="{ src: $page.props.uploadUrl + '/' + $page.props.backendLogo.img_path, loading: $page.props.sysImageUrl + '/loading_gif.gif', error: $page.props.sysImageUrl + '/default_photo.png' }"
                            width="50" height="50" class="m-auto my-2.5" />
                        <img v-else :src="$page.props.uploadUrl + '/no_logo.png'" width="200" height="100"
                            class="m-auto my-2.5" /> -->



                            <ps-label-title class="text-center m-auto pb-8 pt-2 mt-10"
                                textColor="text-feSecondary-600 dark:text-feSecondary-50">{{
                                    $t('phone_login__phone_login') }}</ps-label-title>
                            <ps-label class="mt-4  "> {{ $t("core__fe_phone_login__display_name") }} : </ps-label>
                            <ps-input class="mt-2 placeholder-feSecondary-800 dark:placeholder-feSecondary-500" type="text" v-bind:placeholder="$t('core__fe_phone_login__display_name_placeholder')"
                                theme="text-feSecondary-500 dark:bg-feSecondary-800"
                                defaultBorder="border border-feSecondary-200 dark:border-feSecondary-400 focus:outline-none focus:ring-1 focus:ring-fePrimary-500"
                                v-model:value="form.displayName"></ps-input>
                            <ps-label-caption v-if="isValidUserName" class="mt-2 mx-8"
                                textColor="text-fePrimary-500 dark:text-feAccent-500">{{
                                    $t("phone_login__user_name_required")
                                }}</ps-label-caption>

                            <ps-label-caption textColor="text-feError-500 " class="mt-2 block">{{ displayName
                            }}</ps-label-caption>
                            <!-- Username -->
                            <!-- <ps-label class="mt-4  "> {{ $t("phone_login__user_name") }} : </ps-label>
                        <ps-input class="mt-2 " v-on:keyup.enter="clicked" @keypress="validateName" type="text"
                            v-bind:placeholder="$t('phone_login__user_name')" v-model:value="name"></ps-input>
                        <ps-label-caption v-if="isValidUserName" class="mt-2 mx-8"
                            textColor="text-fePrimary-500 dark:text-feAccent-500">{{
                                $t("phone_login__user_name_required")
                            }}</ps-label-caption>

                        <ps-label-caption textColor="text-feError-500 " class="mt-2 block">{{ usernameError
                        }}</ps-label-caption> -->
                            <!-- <ps-label-caption class="mt-2 mx-8">{{ $t("phone_login__user_name_format") }}</ps-label-caption> -->

                            <ps-label class="mt-4  "> {{ $t("phone_label") }} : </ps-label>
                            <div class="flex">

                                <ps-dropdown align="left" class=" mt-2 w-20">
                                    <template #select>
                                        <ps-dropdown-select border="border border-feSecondary-200 dark:border-feSecondary-400 focus:outline-none focus:ring-1 focus:ring-fePrimary-500" placeholder="phone" :showCenter="true"
                                            :selectedValue="form.phone_code == '' ? '' : phoneCodes.filter((phoneCode) => phoneCode.id == form.phone_code)[0].country_code"
                                            @change="[(form.phone_code = phoneCode.id)]" />
                                    </template>
                                    <template #list>
                                        <div class="rounded-md shadow-xs w-16 bg-feAchromatic-50 dark:bg-feAchromatic-900">
                                            <div class="pt-2 z-20">
                                                <div v-if="phoneCodes.length === 0">
                                                    <ps-label class="p-2 flex cursor-pointer">{{
                                                        $t('core_fe_empty_phone_list') }}</ps-label>
                                                </div>
                                                <div v-else>
                                                    <div v-for="cat in phoneCodes" :key="cat.id"
                                                        class="w-96 flex py-4 px-2 hover:bg-fePrimary-50 dark:hover:bg-feSecondary-700 cursor-pointer items-center"
                                                        @click="[(form.phone_code = cat.id)]">
                                                        <ps-label class="ms-2"
                                                            :class="cat.id == form.phone_code ? ' font-bold' : ''">{{
                                                                cat.country_code }}</ps-label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                </ps-dropdown>
                                <ps-input class="mt-2 " v-on:keyup.enter="clicked" @keypress="validatePhone" type="text"
                                theme="text-feSecondary-500 dark:bg-feSecondary-800"
                                defaultBorder="border border-feSecondary-200 dark:border-feSecondary-400 focus:outline-none focus:ring-1 focus:ring-fePrimary-500"
                                    v-bind:placeholder="$t('phone_login__fe_phone_number_placeholderv2')"
                                    v-model:value="form.phone_string"></ps-input>


                                <!-- <ps-button class="mt-6 mx-2 truncate " textSize="text-xxs" @click="clicked"
                                    :disabled="false">
                                    {{ $t("send sms") }} </ps-button> -->

                            </div>
                            <ps-label-caption textColor="text-feError-500 " class="mt-2 block">{{ errors.user_ban
                            }}</ps-label-caption>
                            <ps-label-caption v-if="isValidPhone" class="mt-2 mx-8"
                                textColor="text-fePrimary-500 dark:text-feAccent-500">{{
                                    $t("phone_login__phone_required")
                                }}</ps-label-caption>
                            <ps-label-caption textColor="text-feError-500 " class="mt-2 block">{{ phoneError
                            }}</ps-label-caption>

                            <ps-input v-if="code_data_ready" class="mt-4 " type="text"
                                v-bind:placeholder="$t('phone_login__code')" v-model:value="code"></ps-input>
                            <ps-label-caption textColor="text-feError-500 " class="mt-2 block">{{ codeError
                            }}</ps-label-caption>

                            <div id="recaptcha-container"></div> <br>

                            <div class="flex items-center justify-center mb-4 lg:mt-56">

                                <!-- Loading Button -->
                                <ps-button class="mt-6  w-full" v-if="authStore.loading.value" :disabled="true">
                                    {{ $t("phone_login__loading") }} </ps-button>

                                <!-- Submit Button -->
                                <ps-button class="mt-6  w-full" @click="clicked" :disabled="false" v-else>
                                    {{ $t("phone_login__login") }} </ps-button>


                                <!-- <ps-button class="mt-6 mx-8" v-if="authStore.loading.value || !code_data_ready"
                                    :disabled="true">
                                    {{ $t("login") }} </ps-button>



                                <ps-button class="mt-6 mx-8" @click="codeClicked" v-else :disabled="false">
                                    {{ $t("login") }} </ps-button> -->

                            </div>
                            <div class="flex flex-auto justify-center mt-4 mb-10">


                                <ps-button colors="bg-feAchromatic-50 dark:bg-feSecondary-800 dark:text-feSecondary-50 " border="border" class="mt-6  w-full" @click="loginClicked" :disabled="false" >
                                    {{ $t("core__fe_btn_cancel") }} </ps-button>
                            </div>
                        </div>
                        <div v-if="code_data_ready">



                            <!-- <ps-button class="mt-6 mx-8" @click="codeClicked" :disabled="false">
                            {{ $t("submit") }} </ps-button> -->
                        </div>
                    </form>



                </div>

            </div>
        </div>

    </div>
    <user-phone-login-verification-modal ref="user_phone_login_verification_modal" />
    <ps-error-dialog ref="ps_error_dialog" />
    <ps-danger-dialog ref="ps_danger_dialog" />
</template>


<script type="ts">
//Vue
import { defineComponent, ref, computed } from 'vue'
import { router } from '@inertiajs/vue3';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import firebaseApp from 'firebase/app';
import "firebase/auth";
import { onMounted } from 'vue';
import { useStore } from 'vuex'
import PsDropdown from "@/Components/Core/Dropdown/PsDropdown.vue";
import PsDropdownSelect from "@/Components/Core/Dropdown/PsDropdownSelect.vue";




// Components
import PsLabel from '@template1/vendor/components/core/label/PsLabel.vue';
import PsLabelHeader3 from "@template1/vendor/components/core/label/PsLabelHeader3.vue";
import PsLabelTitle from '@template1/vendor/components/core/label/PsLabelTitle.vue';
import PsLabelCaption from '@template1/vendor/components/core/label/PsLabelCaption.vue';
import PsButton from '@template1/vendor/components/core/buttons/PsButton.vue';
import PsInput from '@/Components/Core/Input/PsInput.vue';
// import PsInput from '@template1/vendor/components/core/input/PsInput.vue';
import PsCard from "@/Components/Core/Card/PsCard.vue";
import PsDangerDialog from "@/Components/Core/Dialog/PsDangerDialog.vue";
import firebase from "firebase/app";
import UserPhoneLoginVerificationModal from '@template1/vendor/components/modules/user/UserPhoneLoginVerificationModal.vue';
import PsFrontendLayout from '@template1/vendor/components/layouts/container/PsFrontendLayout.vue';
import PsLayout from "@/Components/PsLayout.vue";
import WelcomeImage from "@/Components/Svgs/WelcomeImage.vue";
import { usePsAppInfoStoreState } from '@templateCore/store/modules/appinfo/AppInfoStore';
import AppInfoParameterHolder from '@templateCore/object/holder/AppInfoParameterHolder';




import PsErrorDialog from '@template1/vendor/components/core/dialog/PsErrorDialog.vue';


// Providers
// import { PsValueProvider } from "@/store/modules/core/PsValueProvider";
// import { useUserProviderState } from '@/store/modules/user/UserProvider';
import { useAuthStore } from '../../store/AuthStore';

// Params Holders
// import PhoneLoginParameterHolder from '../object/holder/PhoneLoginParameterHolder';
import PhoneLoginParameterHolder from '@templateCore/object/holder/PhoneLoginParameterHolder';

import { trans } from 'laravel-vue-i18n';

// import PsStatus from '@/api/common/PsStatus';

//language
// import { i18n } from '@/assets/locales/index';


export default defineComponent({
    name: "PhoneLoginView",
    components: {
        Head,
        Link,
        PsLabel,
        PsLabelTitle,
        PsButton,
        PsInput,
        UserPhoneLoginVerificationModal,
        PsErrorDialog,
        PsLabelCaption,
        PsCard,
        PsDangerDialog,
        WelcomeImage,
        PsDropdown,
        PsDropdownSelect
    },

    props: {
        backendSetting: Object,

        status: String,
        error: String,
        errors: Object,
        phoneCodes: Object,
        firebaseConfig: String
    },
    layout: PsFrontendLayout,

    setup(props) {

        // Inject Provider
        const authStore = useAuthStore();
        const holder = new PhoneLoginParameterHolder();
        const store = useStore();
        const isDarkMode = computed(() => store.getters.isDarkMode);
        const dir = computed(() => store.getters.dir);
        const usernameError = ref();
        const phoneError = ref();
        const displayNameError = ref();
        const codeError = ref();
        const ps_danger_dialog = ref();
        const appInfoStore = usePsAppInfoStoreState();



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
            phone_code: appInfoStore?.appInfo?.data?.defaultPhoneCountryCode?.id ?? '1',
            phone_string: '',
        });

        const firebaseConfiguration = JSON.parse(props.firebaseConfig);
        if (firebase.apps.length < 1) {
            firebase.initializeApp(firebaseConfiguration);
        }

        // Init Values
        // const route = useRoute();
        // const psValueHolder = PsValueProvider.psValueHolder;
        // const loginUserId = psValueHolder.getLoginUserId();
        const name = ref();
        const phone = ref();
        const phoneFormatEdit = ref();
        const code = ref();
        const isValidUserName = ref(false);
        const isValidPhone = ref(false);
        const user_phone_login_verification_modal = ref();
        const ps_error_dialog = ref();
        const code_data_ready = ref(false);


        let confirmationResult;
        let UserLogindata;
        let appVerifier;
        authStore.loading.value = false;
        function loadAppVerifier() {
            // Init recaptchaVerifier
            authStore.loading.value = true;
            setTimeout(() => {
                window.recaptchaVerifier = new firebaseApp.auth.RecaptchaVerifier('recaptcha-container', {
                    'size': 'invisible',
                    'callback': (response) => {
                        // clicked();
                        console.log("Callback");
                        console.log(response);
                    },
                    'expired-callback': () => {
                        console.log("expiry callback")
                    }
                });

                appVerifier = window.recaptchaVerifier;
                authStore.loading.value = false;
                
            }, 1000);
        }

        onMounted(async () => {
            loadAppVerifier();
            const appInfoParameterHolder = new AppInfoParameterHolder();
            appInfoParameterHolder.userId = 'nologinuser';
            await appInfoStore.loadAppInfo(appInfoParameterHolder); 
            form.phone_code = appInfoStore?.appInfo?.data?.defaultPhoneCountryCode?.id ?? '1';
        })

        function loginClicked() {
            // Redirect
            // userProvider.navigateUserLoginTo("login", router, route.query.redirect, route.query, route.params);
            router.get(route('login'));
        }

        async function codeClicked() {

            codeError.value = '';

            if (confirmationResult != undefined) {


                let data = await confirmationResult.confirm(code.value).then(async (userCredential) => {

                    if (userCredential != null
                        && userCredential.user != null
                        && userCredential.user.uid != null
                        && userCredential.user.uid != '') {

                        // call backend server
                        const user = userCredential.user;
                        holder.phoneId = user.uid;





                        authStore.loading.value = true;


                        if (UserLogindata.data.message.user_isexisted == "2") {
                            form.loginMethod = 'phone';
                            form.username = name.value;
                            form.user_phone = phone.value;
                            form.phone_id = user.uid;

                            router.post(route("CreateLogin"), form, {});


                        }
                        if (UserLogindata.data.message.user_isexisted == "1") {
                            form.loginMethod = 'phone';
                            form.username = name.value;
                            form.email = "admin@gmail.com";
                            form.user_phone = phone.value;
                            form.password = 'admin@123';
                            form.phone_id = user.uid;
                            form.name = name.value

                            router.post(route("login"), form, {});


                        }














                        // await authStore.submitUserLoginWithPhoneId( user.uid, name.value, phone.value);

                        // if(userProvider.userResource.status == PsStatus.SUCCESS) {
                        //     userProvider.navigateUserLoginRedirect(route.query.redirect, 'dashboard', router, route.query.redirect, route.query, route.params);
                        // }else {
                        //     ps_error_dialog.value.openModal(i18n.global.t('phone_login__error_in_sign_in'), userProvider.userResource.message);
                        //     userProvider.setLoading(false);
                        // }
                        // alert("here");


                    } else {

                        authStore.setLoading(false);
                        // loadAppVerifier();

                    }



                }).catch((error) => {


                    authStore.setLoading(false);



                    codeError.value = error?.message;
                    // ps_error_dialog.value.openModal(trans('phone_login__error_in_sign_in'), error?.message);

                });
            }

        }

        async function clicked() {



            // let errorStatus = true;
            // if (name.value == '' || name.value == undefined) {
            //     isValidUserName.value = true;
            //     errorStatus = false;
            // }
            // if (name.value == '' || name.value == undefined) {
            //     isValidPhone.value = true;
            //     errorStatus = false;
            // }
            // if (!errorStatus) {
            //     window.scrollTo(0, 0);
            //     return;
            // }

            // holder.phoneId = loginUserId;
            let phone_country_code = props.phoneCodes.filter((phoneCode) => phoneCode.id == form.phone_code)[0].country_code
            phone.value = phone_country_code + form.phone_string
            phoneFormatEdit.value = phone_country_code + '-' + form.phone_string;
            holder.userName = name.value;
            holder.userPhone = phoneFormatEdit.value;
            holder.displayName = form.displayName;

            authStore.loading.value = true;


            const verifier = appVerifier;
            UserLogindata = await authStore.existUser(holder);

            if (UserLogindata.data.status == 'error') {
                usernameError.value = ''
                phoneError.value = ''
                displayNameError.value = ''

                usernameError.value = UserLogindata.data.message['username'] ? UserLogindata.data.message['username'][0] : ''
                phoneError.value = UserLogindata.data.message['user_phone'] ? UserLogindata.data.message['user_phone'][0] : ''
                displayNameError.value = UserLogindata.data.message['displayName'] ? UserLogindata.data.message['displayName'][0] : ''
                authStore.loading.value = false


            }


            if (UserLogindata.data.status == "success") {
                confirmationResult = await firebaseApp.auth().signInWithPhoneNumber(phone.value, verifier).catch((error) => {
                    authStore.loading.value = false;


                    codeError.value = error?.message;
                    // ps_error_dialog.value.openModal(
                    //     trans('phone_login__error_in_sign_in'), error?.message);
                    // loadAppVerifier();
                });
                // if (confirmationResult != undefined) {

                //     code_data_ready.value = true;
                //     authStore.loading.value = false;

                // }




                if (confirmationResult != undefined) {
                    user_phone_login_verification_modal.value.openModal(
                        trans('phone_no_verification_modal__title'),
                        // 'Phone No Verification',
                        '',
                        'Submit',
                        'Cancel',
                        async (code) => {
                            authStore.loading.value = true;
                            let data = await confirmationResult.confirm(code).then(async (userCredential) => {

                                if (userCredential != null
                                    && userCredential.user != null
                                    && userCredential.user.uid != null
                                    && userCredential.user.uid != '') {
                                    // call backend server
                                    const user = userCredential.user;
                                    holder.phoneId = user.uid;


                                    user_phone_login_verification_modal.value.actionClicked('no')




                                    if (UserLogindata.data.message.user_isexisted == "2") {
                                        form.loginMethod = 'phone';
                                        form.username = name.value;
                                        form.user_phone = phoneFormatEdit.value;
                                        form.phone_id = user.uid;

                                        router.post(route("CreateLogin"), form, {});


                                    }
                                    if (UserLogindata.data.message.user_isexisted == "1") {
                                        form.loginMethod = 'phone';
                                        form.username = name.value;
                                        form.email = "admin@gmail.com";
                                        form.user_phone = phoneFormatEdit.value;
                                        form.password = 'admin@123';
                                        form.phone_id = user.uid;
                                        form.name = name.value

                                        router.post(route("login"), form, {});


                                    }













                                    // await authStore.submitUserLoginWithPhoneId( user.uid, name.value, phone.value);

                                    // if(userProvider.userResource.status == PsStatus.SUCCESS) {
                                    //     userProvider.navigateUserLoginRedirect(route.query.redirect, 'dashboard', router, route.query.redirect, route.query, route.params);
                                    // }else {
                                    //     ps_error_dialog.value.openModal(i18n.global.t('phone_login__error_in_sign_in'), userProvider.userResource.message);
                                    //     userProvider.setLoading(false);
                                    // }
                                    // alert("here");


                                } else {

                                    authStore.setLoading(false);
                                    // loadAppVerifier();

                                }



                            }).catch((error) => {

                                authStore.setLoading(false);
                                ps_error_dialog.value.openModal(trans('phone_login__error_in_sign_in'), error?.message);

                            });





                        },
                        () => {
                            console.log("Cancel");
                            // router.get(route("dashboard"));
                        }
                    );




                }
            }


        }

        function test() {
            router.post(route("login"));
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
        return {
            authStore,
            clicked,
            name,
            phone,
            user_phone_login_verification_modal,
            ps_error_dialog,
            loginClicked,
            isValidUserName,
            validateName,
            validatePhone,
            isValidPhone,
            dir,
            isDarkMode,
            test,
            usernameError,
            phoneError,
            ps_danger_dialog,
            code,
            code_data_ready,
            codeClicked,
            codeError,
            form,
            displayNameError,
        }
    },
    watch: {
        async errors(value) {
            if (this.errors.user_ban) {

               this.authStore.loading.value = false

            }
            // alert(this.errors.user_need_verify)
        }
    }
})
</script>
