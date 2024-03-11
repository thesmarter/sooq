<template>
    <Head :title="$t('core__fe_sign_in')" />
    <div :class="isDarkMode ? 'dark' : ''">
        <div
            class="min-h-screen flex flex-col lg:mt-36 mt-28   items-center pt-6 sm:pt-0 dark:bg-feAchromatic-900 dark:text-feSecondary-50">



            <ps-card class="w-full sm:max-w-md mt-6 py-4  dark:bg-feSecondary-800" >


                <div class="px-6">

                    <ps-label-header-5 class="pt-2 text-center font-semibold">{{ $t('core_fe_create_account') }}</ps-label-header-5>
                    <!-- <ps-label-title class="text-center m-auto pb-8 pt-2"
                        textColor="text-feSecondary-600 dark:text-feSecondary-50">{{ $t('please_create_account') }}</ps-label-title> -->

                    <form class='mt-6' @submit.prevent="submit">
                        <div class="mb-4" v-if="userLoginData.loginMethod == 'phone'">
                            <ps-label class="mb-2" textColor="text-feSecondary-800 dark:text-feSecondary-200">{{ $t('core__fe_user_phone') }}</ps-label>
                            <ps-input ref="phone" type="text" v-model:value="form.user_phone"
                            class="placeholder-feSecondary-800 dark:placeholder-feSecondary-500"
                                theme="text-feSecondary-500 dark:bg-feSecondary-800"
                                defaultBorder="border border-feSecondary-200 dark:border-feSecondary-400 focus:outline-none focus:ring-1 focus:ring-fePrimary-500"
                                :placeholder="$t('core__fe_phone_placeholder')" :disabled="true" />
                            <ps-label-caption textColor="text-feError-500 "
                                class="mt-2 block">{{ errors.phone }}</ps-label-caption>

                        </div>
                        <div class="mb-4">
                            <ps-label class="mb-2" textColor="text-feSecondary-800 dark:text-feSecondary-200">{{ $t('core__fe_username') }}</ps-label>
                            <ps-input ref="email" type="text" v-model:value="form.newUser.username"
                            class="placeholder-feSecondary-800 dark:placeholder-feSecondary-500"
                                theme="text-feSecondary-500 dark:bg-feSecondary-800"
                                defaultBorder="border border-feSecondary-200 dark:border-feSecondary-400 focus:outline-none focus:ring-1 focus:ring-fePrimary-500"
                                :placeholder="$t('core__fe_username_placeholder')" autofocus />
                                 <ps-label-title-3 class="mt-2 " textColor="text-feSecondary-400 dark:text-feSecondary-500">{{ $t('username__fe_valid_description') }}</ps-label-title-3>
                            <ps-label-caption textColor="text-feError-500 "
                                class="mt-2 block">{{ errors.email }}</ps-label-caption>
                        </div>
                        <div class="mb-8">
                            <ps-label class="mb-2" textColor="text-feSecondary-800 dark:text-feSecondary-200">{{ $t('core__fe_password') }}</ps-label>
                            <ps-input-with-right-icon v-model:value="form.newUser.password" ref="password"
                                placeholderColor="placeholder-feSecondary-400 dark:placeholder-feSecondary-500"
                                theme="bg-feAchromatic-50 dark:bg-feSecondary-800"
                                :type="(isHide ? 'password' : 'text')" @keyup="validateEmptyInput('password', form.password)"
                                @blur="validateEmptyInput('password', form.password)"
                                :placeholder="$t('core__fe_password_placeholder2')" autocomplete="current-password">
                                <template #icon>
                                    <ps-icon @click="isHide = !isHide" class="cursor-pointer"
                                        :name="isHide ? 'eyeOff' : 'eye-on'" />
                                </template>
                            </ps-input-with-right-icon>
                            <ps-label-caption textColor="text-feError-500 "
                                class="mt-2 block">{{ errors.password }}</ps-label-caption>
                        </div>


                        <div class="block mt-4">
                            <ps-button class="w-full mb-2">
                                <ps-loading v-if="form.processing"
                                    theme="border-2 border-t-2 border-text-8 border-t-fePrimary-500 me-1"
                                    loadingSize="h-5 w-5" />
                                {{ $t('core__fe_btn_done') }}
                            </ps-button>
                        </div>
                    </form>



                    {{ authStore.errorMessage }}
                </div>

            </ps-card>
        </div>
    </div>
</template>

<script>
import { defineComponent, ref, computed, onMounted } from 'vue'
import { Head, Link } from '@inertiajs/vue3';
import PsLabelHeader3 from "@template1/vendor/components/core/label/PsLabelHeader3.vue";
import PsLabelHeader5 from "@template1/vendor/components/core/label/PsLabelHeader5.vue";
import PsCard from "@/Components/Core/Card/PsCard.vue";
import useValidators from "@/Validation/Validators";
import PsInput from "@template1/vendor/components/core/input/PsInput.vue";
import PsInputWithRightIcon from "@/Components/Core/Input/PsInputWithRightIcon.vue";
import PsLabel from "@template1/vendor/components/core/label/PsLabel.vue";
import PsButton from '@template1/vendor/components/core/buttons/PsButton.vue';
import PsIcon from "@/Components/Core/Icons/PsIcon.vue";
import PsCheckboxValue from "@/Components/Core/Checkbox/PsCheckboxValue.vue";
import PsLabelCaption from "@template1/vendor/components/core/label/PsLabelCaption.vue";
import PsLabelTitle from "@template1/vendor/components/core/label/PsLabelTitle.vue";
import PsLabelTitle3 from "@template1/vendor/components/core/label/PsLabelTitle3.vue";
import PsLoading from "@/Components/Core/Loading/PsLoading.vue";
import { useStore } from 'vuex'
import { trans } from 'laravel-vue-i18n';
import firebase from "firebase/app";
import 'firebase/messaging'
import { useAuthStore } from '../../store/AuthStore';
import { getExistUser } from '@/Api/psApiService.js'
import PsFrontendLayout from '@template1/vendor/components/layouts/container/PsFrontendLayout.vue';

import UserCreateParameterHolder from '@templateCore/object/holder/UserCreateParameterHolder';

export default defineComponent({
    components: {
        Head,
        Link,
        PsLabelHeader3,
        PsCard,
        PsInput,
        PsInputWithRightIcon,
        PsLabel,
        PsButton,
        PsIcon,
        PsCheckboxValue,
        PsLabelCaption,
        PsLabelTitle,
        PsLoading,
        PsLabelTitle3,
        getExistUser,
        PsLabelHeader5
    },

    props: {
        canResetPassword: Boolean,
        status: String,
        error: String,
        errors: Object,
        backendSetting: Object,
        userLoginData: Object,
        firebaseConfig: String,
        webPushKey: String,
        appUrl: String,

    },
    layout: PsFrontendLayout,

    data(props) {
        return {
            form: this.$inertia.form({
                email: props.userLoginData?.email,
                password: '',
                remember: false,
                deviceToken: '',
                headerToken: '',
                loginMethod: props.userLoginData.loginMethod,
                google_id: props.userLoginData.google_id ,
                facebook_id: props.userLoginData.facebook_id ,
                apple_id: props.userLoginData.apple_id ,
                profile_photo_url: props.userLoginData.profile_photo_url,
                name: props.userLoginData.name,
                registerForm: 1,
                registerMethod: props.userLoginData.registerMethod,
                newUser: {
                    username: props.userLoginData.username,
                    password: '',
                },
                user_phone:props.userLoginData.user_phone,
                phone_id:props.userLoginData.phone_id
            }),
            errors: {
                email: '',
                password: ''
            },
        }
    },
    setup(props) {


        const paramHolder = new UserCreateParameterHolder();


        const email = ref();
        const password = ref();
        const isHide = ref(true);

        const store = useStore();
        const isDarkMode = computed(() => store.getters.isDarkMode);
        // const dir = computed(() => store.getters.dir);

        const authStore = useAuthStore();

        // let errors = ref(props.errors);



        ///start firebase noti
        const firebaseConfiguration = JSON.parse(props.firebaseConfig);

        if (firebase.apps.length < 1) {
            firebase.initializeApp(firebaseConfiguration);
        }
        const messaging = firebase.messaging.isSupported() ? firebase.messaging() : null;
        if (messaging) {
            Notification.requestPermission().then((permission) => {
                if (permission === 'granted') {
                    console.log('Notification permission granted.');
                } else {
                    console.log('Unable to get permission to notify.');
                }
            });
        }


        function subscribeTokenToTopic(token, topic) {
            if (messaging) {
                fetch('https://iid.googleapis.com/iid/v1/' + token + '/rel/topics/' + topic, {
                    method: 'POST',
                    headers: new Headers({
                        'Authorization': 'key='+props.backendSetting.fcm_api_key
                    })
                }).then(response => {
                    if (response.status < 200 || response.status >= 400) {
                        throw 'Error subscribing to topic: ' + response.status + ' - ' + response.text();
                    }
                    console.log('Subscribed to "' + topic + '"');
                }).catch(error => {
                    console.error(error);
                })
            }
        }



        ///end firebase noti

        onMounted(async () => {
            // for dark or light mode local storage
            store.dispatch('loadIsDarkMode');

            // for rtl or ltr directory switch local storage
            // store.dispatch('loadDirectory');

            // await appInfoStore.loadAppInfo();
            if ("serviceWorker" in navigator) {

                if (messaging) {
                    let appUrl = props.appUrl;
                    let url=appUrl+"/firebase-messaging-sw.js";
                    // console.log(appUrl);
                    // console.log(appUrl.endsWith("/"));

                    if(appUrl.endsWith("/")){
                        url = appUrl+"firebase-messaging-sw.js";
                    }
                    navigator.serviceWorker .register(url)
                        .then(function (registration) {
                            messaging.getToken({ vapidKey: props.webPushKey, serviceWorkerRegistration: registration })
                                .then((currentToken) => {
                                    if (currentToken) {
                                        console.log('current token for client: ', currentToken);
                                        localStorage.deviceToken = currentToken;
                                        // psValueHolder.replacedeviceToken(localStorage.deviceToken);
                                        subscribeTokenToTopic(currentToken, 'fe_broadcast');

                                    }
                                }).catch((err) => {
                                    console.log('An error occurred while retrieving token. ', err);
                                    localStorage.deviceToken = 'errorToken';
                                    // catch error while creating client token
                                });
                        }).catch(function (err) {
                            localStorage.deviceToken = 'errorToken';
                            console.log("Service worker registration failed, error:", err);
                        });
                }
            } else {
                localStorage.deviceToken = 'errorToken';
                console.log('no serviceWorker in navigator');
            }
        });

        // Client Side Validation
        const { isEmpty, minLength, isEmail } = useValidators();

        const validateEmptyInput = (fieldName, fieldValue) => {
            props.errors[fieldName] = !fieldValue ? isEmpty(fieldName, fieldValue) : minLength(fieldName, fieldValue, 4);
            if (fieldName == 'password') {
                password.value.isError = (props.errors.password == '') ? false : true;
            }
        };

        const validateEmailInput = (fieldName, fieldValue) => {
            props.errors[fieldName] = !fieldValue ? isEmpty(fieldName, fieldValue) : isEmail(fieldName, fieldValue);
            if (fieldName == 'email') {
                email.value.isError = (props.errors.email == '') ? false : true;
            }
        }

        return {
            validateEmptyInput, validateEmailInput, email, password, isHide, authStore, paramHolder,
            isDarkMode
        };
    },
    methods: {
        async submit() {


            this.form.deviceToken = localStorage.deviceToken;
            const currentDate = new Date();
            localStorage.headerToken = localStorage.deviceToken + currentDate.getTime();
            this.form.headerToken = localStorage.headerToken;
            // this.form.loginMethod = 'normal';
            this.form.password = this.form.newUser.password;

            this.paramHolder.username = this.form.newUser.username;
            this.paramHolder.password = this.form.newUser.password;
            this.paramHolder.google_id = this.form.google_id;
            this.paramHolder.facebook_id = this.form.facebook_id;
            this.paramHolder.apple_id=this.form.apple_id;
            if(this.form.loginMethod == 'phone'){
                this.paramHolder.name = this.userLoginData.displayName;
            }else{
                this.paramHolder.name = this.form.name ? this.form.name : this.form.username;
            }
            this.paramHolder.email = this.form.email;
            this.paramHolder.profile_photo_url = this.form.profile_photo_url ? this.form.profile_photo_url : '';
            this.paramHolder.registerForm = this.form.registerForm;
            this.paramHolder.loginMethod = this.form.loginMethod;
            this.paramHolder.user_phone = this.form.user_phone;
            this.paramHolder.phone_id = this.form.phone_id;



            const UserLogindata = await this.authStore.createUserwithUsername(this.paramHolder);


            if (UserLogindata.data.status == 'error') {
                this.errors.email = ''
                this.errors.password = ''
                this.errors.email = UserLogindata.data.message['username'][0]
                this.errors.password = UserLogindata.data.message['password'][0]
            }

            if (UserLogindata.data.status == 'success') {

                if(this.form.loginMethod  == 'phone'){
                    this.form.email = this.form.user_phone.replace("-", "");;
                }
                this.form.loginMethod = 'normal'

                this.form
                    .transform(data => ({
                        ...data,
                        remember: this.form.remember ? 'on' : ''
                    }))
                    .post(this.route('login'), {
                        onFinish: () => this.form.reset('password'),
                    })

            }


        },
        async getCategoriesData(google_id) {
            category_loadmore_visible.value = true;
            is_loading.value = true
            getExistUser(google_id).then(response => {

                this.form.newUser = response.data
            });
        },
        async googleloginclicked() {
            // if(agreePrivacyPolicy.value) {
            const user = await this.authStore.loginWithGoogleId();
            this.form.deviceToken = localStorage.deviceToken;
            const currentDate = new Date();
            localStorage.headerToken = localStorage.deviceToken + currentDate.getTime();
            this.form.headerToken = localStorage.headerToken;
            this.form.email = user.email;
            this.form.password = 'admin123'
            this.form.loginMethod = 'google'
            this.form.google_id = user.uid
            this.form.name = user.displayName
            this.form.profile_photo_url = user.photoURL





            this.form
                .transform(data => ({
                    ...data,
                }))
                .post(this.route('createUser'), {
                    onFinish: () => this.form.reset('password'),
                })


        },

        handleReset() {
            this.$inertia.get(route('password.request'))
        }
    }
})
</script>
