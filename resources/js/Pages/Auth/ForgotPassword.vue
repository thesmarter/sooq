<template>
    <Head :title="$t('core__fe_forgot_password_title')" />
    <div :class="isDarkMode ? 'dark' : ''">
        <div class="mb-6 bg-feAchromatic-50 dark:bg-feAchromatic-900 flex flex-row  min-h-screen items-center justify-center">
            <div class="h-auto  sm:mt-32 lg:mt-36 mt-28 flex flex-row w-98 lg:w-202 rounded-md overflow-hidden shadow-md ">
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

                    <div class="flex flex-row text-feSecondary-50  dark:text-feSecondary-900  rtl:space-x-reverse space-x-4">
                        <ps-icon name="facebookCircle" />
                        <ps-icon name="instagramCircle" />
                        <ps-icon name="twitter" />
                        <!-- <ps-icon textColor="text-fePrimary-500 dark:text-feAchromatic-50" class="" name="apple-icon"
                            h="24" w="24" /> -->
                        <ps-icon name="pinterest" />
                        <ps-icon name="linkedIn" />
                    </div>
                </div>
                <div class="w-full lg:w-100 bg-feAchromatic-50 dark:bg-feSecondary-800 px-4 sm:px-10 pb-10">
                    <!-- <img v-if="$page.props.backendLogo" :src="$page.props.uploadUrl + '/' + $page.props.backendLogo.img_path" width="50" height="50" class="m-auto my-2.5"/> -->

                    <div class="flex  justify-center">
                        <ps-label class="mt-6 mb-8 text-2xl font-senibold justify-center">{{ $t('core__fe_forgot_password_title') }}</ps-label>
                    </div>
                    <ps-label class=" mb-12 text-sm" textColor="text-feSecondary-500 ">{{
                            $t('core__fe_forgot_password_description') }}</ps-label>

                    <div>
                        <div class="mb-4">
                            <ps-label class="mb-2 text-sm" textColor="text-feSecondary-800 dark:text-feSecondary-200">{{ $t('core__fe_forgot_password_email_or_user_name') }}</ps-label>
                            <ps-input type="text" v-on:keyup.enter="clicked" v-model:value="email"
                                class="placeholder-feSecondary-800 dark:placeholder-feSecondary-500"
                                theme="text-feSecondary-500 bg-feAchromatic-50 dark:bg-feSecondary-800"
                                defaultBorder="border border-feSecondary-200 dark:border-feSecondary-400 focus:outline-none focus:ring-1 focus:ring-fePrimary-500"
                                :placeholder="$t('core__fe_forgot_password_email_or_user_name_placeholder')" autofocus />
                            <!-- <ps-label-caption textColor="text-feError-500 " class="mt-2 block">{{ errors.email
                            }}</ps-label-caption> -->
                        </div>

                        <div class="block mt-12">
                            <ps-button class="w-full mb-2"  @click="clicked">
                                <ps-loading v-if="authStore.loading.value"
                                    theme="border-2 border-t-2 border-text-8 border-t-fePrimary-500 me-1"
                                    loadingSize="h-5 w-5" />
                                {{ $t('core__fe_forgot_password_send') }}
                            </ps-button>
                        </div>

                        <div class="block mt-6 mb-2 lg:mb-44">
                            <ps-feSecondary-button 
                            colors="bg-feAchromatic-50 dark:bg-feSecondary-800 text-feSecondary-800 dark:text-feSecondary-50"
                            @click="loginClicked" class="w-full">
                                {{ $t('core__fe_forgot_password_cancel') }}
                            </ps-feSecondary-button>
                        </div>

                    </div>

                </div>

            </div>
        </div>
    </div>
    <ps-loading-dialog ref="ps_loading_dialog"  :isClickOut='false'/>
    <ps-success-dialog ref="ps_success_dialog" />
    <ps-error-dialog ref="ps_error_dialog" />
</template>

<script lang="ts">
import PsLabel from '@template1/vendor/components/core/label/PsLabel.vue';
import PsLabelTitle from '@template1/vendor/components/core/label/PsLabelTitle.vue';
import PsLabelCaption2 from '@template1/vendor/components/core/label/PsLabelCaption2.vue';
import PsButton from '@template1/vendor/components/core/buttons/PsButton.vue';
import PsSecondaryButton from '@template1/vendor/components/core/buttons/PsSecondaryButton.vue';
import PsInput from '@template1/vendor/components/core/input/PsInput.vue';
import PsLoadingDialog from '@template1/vendor/components/core/dialogs/PsLoadingDialog.vue';
import PsSuccessDialog from '@template1/vendor/components/core/dialogs/PsSuccessDialog.vue';
import PsErrorDialog from '@template1/vendor/components/core/dialogs/PsErrorDialog.vue';
import { useAuthStore } from '../../store/AuthStore';
// import { PsValueStore } from '@templateCore/store/modules/core/PsValueStore';
// import router from '@template1/router';
import { ref, computed} from 'vue';
import { useStore } from 'vuex'
import { trans } from 'laravel-vue-i18n';

import PsStatus from '@templateCore/api/common/PsStatus';
import { router } from '@inertiajs/vue3';
import PsConst from '@templateCore/object/constant/ps_constants';
import { Head, Link } from '@inertiajs/vue3';
import PsLabelHeader3 from "@template1/vendor/components/core/label/PsLabelHeader3.vue";
import PsCard from "@/Components/Core/Card/PsCard.vue";
// Params Holders
import ForgotpasswordParameterHolder from '@templateCore/object/holder/ForgotpasswordParameterHolder';
import PsIcon from "@/Components/Core/Icons/PsIcon.vue";
import PsLabelCaption from "@template1/vendor/components/core/label/PsLabelCaption.vue";
import PsLoading from "@/Components/Core/Loading/PsLoading.vue";
import WelcomeImage from "@/Components/Svgs/WelcomeImage.vue";
import PsFrontendLayout from '@template1/vendor/components/layouts/container/PsFrontendLayout.vue';

export default {
    name : "ForgetPassword",
    components : {
        PsLabelCaption,
        PsIcon,
        PsLabelHeader3,
        PsCard,
        Head,
        Link,
        PsButton,
        PsInput,
        PsLabel,
        PsLabelTitle,
        PsLoadingDialog,
        PsSuccessDialog,
        PsLabelCaption2,
        PsErrorDialog,
        PsLoading,
        WelcomeImage,
        PsSecondaryButton
    },
    layout: PsFrontendLayout,
    props : ['authUser'],
    setup(props) {
       
        // const route = useRoute();

        const holder = new ForgotpasswordParameterHolder();
        const ps_loading_dialog = ref();
        const ps_success_dialog = ref();
        const ps_error_dialog = ref();
        // const psValueStore = PsValueStore();
        // const loginUserId = ref(psValueStore.getLoginUserId());

         const store = useStore();
        const isDarkMode = computed(() => store.getters.isDarkMode);
        // const dir = computed(() => store.getters.dir);
        
        if(props.authUser != null){
            router.get(route('dashboard'))
        }

        const email = ref('');
        const password = ref('');

        // Inject Provider
        const authStore = useAuthStore();
        
        async function clicked() {
            ps_loading_dialog.value.openModal();
            // forgot password
            holder.userEmail = email.value;
            const returnData = await authStore.postForgotPassword(holder);
           
            ps_loading_dialog.value.closeModal(); 
            if(returnData.status == PsStatus.SUCCESS) {  
                ps_success_dialog.value.openModal(trans('ps_success_dialog__success'), returnData.data.message,trans('core__fe_enter_code'),()=>{
                    router.get(route('verifyCode',{'email':email.value }))
                }); 
            } else {
                ps_error_dialog.value.openModal(trans('ps_error_dialog__error'), returnData.message,trans('core__fe_btn_ok'),()=>{});
            }
        }

        function loginClicked() {
            // Redirect
            router.get(route('login'))
            // authStore.navigateUserLoginTo("login", router, route.query.redirect, route.query, route.params);
        }

        return { isDarkMode, clicked, email, password, authStore, loginClicked, ps_loading_dialog, ps_success_dialog, ps_error_dialog };
    }
}
</script>