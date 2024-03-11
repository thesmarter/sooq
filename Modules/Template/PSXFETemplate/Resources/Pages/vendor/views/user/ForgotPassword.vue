<template>
    <Head :title="$t('forgot_password__forgot_password')"/>
    <div class="sm:mt-32 lg:mt-36 mt-28 h-screen flex flex-col">

        <div class="mt-4 content-center mx-auto w-96"  >
            <ps-label class="content-center  px-4 py-4 rounded-md" v-if="userProvider.userResource.message != ''"> {{ userProvider.userResource.message }} </ps-label>
        </div>

        <div class="mt-8 w-96 flex flex-col mx-auto  m-auto rounded-lg">

            <!-- Forget Password Title -->
            <ps-label-title class="mt-8 mx-auto text-2xl" > {{ $t("forgot_password__forgot_password") }} </ps-label-title>

            <!-- Email Entry -->
            <ps-label class="mt-4 mx-8 "> {{ $t("forgot_password__email") }} </ps-label>
            <ps-input class="mt-2 mx-8" type="email" v-bind:placeholder="$t('forgot_password__email_placeholder')" v-on:keyup.enter="clicked" v-model:value="email"></ps-input>

            <!-- Loading Button -->
            <ps-button class="mt-4 mb-8 mx-8" @click="clicked" v-if="userProvider.loading.value" :disabled="true">
                {{ $t("forgot_password__loading") }} </ps-button>

            <!-- Login Button -->
            <ps-button class="mt-4 mx-8" @click="clicked" :disabled="false" v-else>
                {{ $t("forgot_password__send") }} </ps-button>

            <div class="flex flex-auto justify-center mt-4">
                <ps-label-caption class='cursor-pointer' @click="loginClicked"> {{ $t("forgot_password__already_member_login") }} </ps-label-caption>
            </div>

            <div class="my-4" />

        </div>

    </div>
    <ps-loading-dialog ref="ps_loading_dialog"  :isClickOut='false'/>
    <ps-success-dialog ref="ps_success_dialog" />
    <ps-error-dialog ref="ps_error_dialog" />
</template>

<script lang="ts">
import { Head } from '@inertiajs/vue3';
import PsLabel from '@template1/vendor/components/core/label/PsLabel.vue';
import PsLabelTitle from '@template1/vendor/components/core/label/PsLabelTitle.vue';
import PsLabelCaption from '@template1/vendor/components/core/label/PsLabelCaption.vue';
import PsButton from '@template1/vendor/components/core/buttons/PsButton.vue';
import PsInput from '@template1/vendor/components/core/input/PsInput.vue';
import PsLoadingDialog from '@template1/vendor/components/core/dialog/PsLoadingDialog.vue';
import PsSuccessDialog from '@template1/vendor/components/core/dialog/PsSuccessDialog.vue';
import PsErrorDialog from '@template1/vendor/components/core/dialog/PsErrorDialog.vue';
import { useUserProviderState } from '@templateCore/store/modules/user/UserProvider';
import { PsValueStore } from '@templateCore/store/modules/core/PsValueStore';
// import router from '@template1/router';
import { ref } from 'vue';
import PsStatus from '@templateCore/api/common/PsStatus';
// import { useRoute } from 'vue-router';

// Params Holders
import ForgotpasswordParameterHolder from '@templateCore/object/holder/ForgotpasswordParameterHolder';

export default {
    name : "ForgetPasswordView",
    components : {
        PsButton,
        PsInput,
        PsLabel,
        PsLabelTitle,
        PsLoadingDialog,
        PsSuccessDialog,
        PsLabelCaption,
        PsErrorDialog,
        Head
    },
    setup() {

        // const route = useRoute();

        const holder = new ForgotpasswordParameterHolder();
        const ps_loading_dialog = ref();
        const ps_success_dialog = ref();
        const ps_error_dialog = ref();
        if(PsValueStore.psValueStore.isUserLoggedIn()) {
            // router.push({name: "dashboard"});
        }

        const email = ref('');
        const password = ref('');

        // Inject Provider
        const userProvider = useUserProviderState();

        async function clicked() {
            ps_loading_dialog.value.openModal();
            // forgot password
            holder.userEmail = email.value;
            const returnData = await userProvider.postForgotPassword(holder);

            ps_loading_dialog.value.closeModal();
            if(returnData.status == PsStatus.SUCCESS) {
                ps_success_dialog.value.openModal('', returnData.message);
            } else {
                ps_error_dialog.value.openModal('', returnData.message);
            }
        }

        function loginClicked() {
            // Redirect
            // userProvider.navigateUserLoginTo("login", router, route.query.redirect, route.query, route.params);
        }

        return { clicked, email, password, userProvider, loginClicked, ps_loading_dialog, ps_success_dialog, ps_error_dialog };
    }
}
</script>
