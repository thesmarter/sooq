<template>
    <Head :title="$t('verify_email__verify_your_email')"/>
    <div class="sm:mt-32 lg:mt-36 mt-28 h-screen flex flex-col">
        <div class=" w-96 flex flex-col mx-auto  m-auto rounded-lg">
            <ps-label-title class="mt-8 mx-auto text-2xl" > {{ $t("verify_email__verify_your_email") }} </ps-label-title>

            <ps-label class="mt-4 mx-8 "> {{ $t("verify__verification_code") }} : </ps-label>
            <ps-input class="mt-2 mx-8" type="text"  v-bind:placeholder="$t('verify__verification_code')" v-on:keyup.enter="clicked" v-model:value="code"></ps-input>

            <div class="flex items-center justify-center mb-4">
                <!-- Loading Button -->
                <ps-button class="mt-6 mx-8" @click="clicked" v-if="userProvider.loading.value" :disabled="true">
                    {{ $t("verify__loading") }} </ps-button>

                <!-- Submit Button -->
                <ps-button class="mt-6 mx-8" @click="clicked" :disabled="false" v-else>
                    {{ $t("verify__submit") }} </ps-button>
            </div>
            <div class="flex items-center justify-center mb-4">
                <ps-route-link class="mx-8" :to="{name: 'login' }" > {{ $t("verify__back_to_login") }} </ps-route-link>
            </div>
        </div>
    </div>

    <ps-error-dialog ref="ps_error_dialog" />
</template>

<script lang="ts">
import { Head } from '@inertiajs/vue3';
//Vue
import { ref } from 'vue';
// import router from '@template1/router';

import PsLabel from '@template1/vendor/components/core/label/PsLabel.vue';
import PsLabelTitle from '@template1/vendor/components/core/label/PsLabelTitle.vue';
import PsButton from '@template1/vendor/components/core/buttons/PsButton.vue';
import PsInput from '@template1/vendor/components/core/input/PsInput.vue';
import PsRouteLink from '@template1/vendor/components/core/link/PsRouteLink.vue';
import PsErrorDialog from '@template1/vendor/components/core/dialog/PsErrorDialog.vue';

//Modules
import { PsValueStore } from "@templateCore/store/modules/core/PsValueStore";
import { useUserProviderState } from '@templateCore/store/modules/user/UserProvider';

// Params Holders
import UserEmailVerifyParameterHolder from '@templateCore/object/holder/UserEmailVerifyParameterHolder';
import PsStatus from '@templateCore/api/common/PsStatus';

//language
import { trans } from 'laravel-vue-i18n';import PsConst from '@templateCore/object/constant/ps_constants';

export default {
    name : "VerifyEmailView",
    components : {
        PsLabel,
        PsLabelTitle,
        PsButton,
        PsInput,
        PsRouteLink,
        PsErrorDialog,
        Head
    },
    setup() {

        // Inject Provider
        const userProvider = useUserProviderState();
        const holder = new UserEmailVerifyParameterHolder();
        const psValueStore = PsValueStore();
        const loginUserId = psValueStore.getVerifyUserId();
        const ps_error_dialog = ref();

        if(loginUserId == null || loginUserId == '' || loginUserId == PsConst.NO_LOGIN_USER) {
            // router.push({ name : "login"});
        }
        const code = ref();

        async function clicked() {
            holder.userId = loginUserId;
            holder.code = code.value;

            const returnData = await userProvider.postUserEmailVerify(holder);
            if(returnData.status == PsStatus.SUCCESS) {

                PsValueStore.psValueStore.login(returnData.data.userId, returnData.data.userName,returnData.data.deviceToken);
                // router.push({ name : "dashboard"});
            }else {
                ps_error_dialog.value.openModal('',
                trans('verify_email__incorrect_code'));
            }

        }
        return{
            userProvider,
            clicked,
            code,
            ps_error_dialog
        }
    }
}
</script>
