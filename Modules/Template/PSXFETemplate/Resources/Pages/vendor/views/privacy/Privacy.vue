<template>
    <Head :title="$t('privacy_policy__privacy_policy')"/>
    <ps-content-container>
        <template #content>
            <div class='sm:mt-32 lg:mt-36 mt-28 mb-16 '>
                <ps-label class="text-center font-semibold text-2xl sm:text-3xl"> {{ $t("privacy_policy__privacy_policy") }} </ps-label>
                <ps-label class="mt-4" v-if="aboutUsStore.aboutus.data != null">
                   <span v-html="aboutUsStore.aboutus.data.privacypolicy" v-once></span>
                </ps-label>
                <div v-if="aboutUsStore.loading.value == true ">
                <privacy-skeletor />
                </div>
            </div>
        </template>
    </ps-content-container>
</template>

<script lang="ts">
import { Head } from '@inertiajs/vue3';
// import { useRoute } from 'vue-router';
// import router from '@template1/router';
import PsContentContainer from '@template1/vendor/components/layouts/container/PsContentContainer.vue';
import PsLabelTitle from '@template1/vendor/components/core/label/PsLabelTitle.vue';
import PsLabel from '@template1/vendor/components/core/label/PsLabel.vue';
import PrivacySkeletor from '@template1/vendor/components/modules/privacy/PrivacySkeletor.vue';

import { useAboutUsStoreState } from "@templateCore/store/modules/aboutus/AboutUsStore";
import PsFrontendLayout from '@template1/vendor/components/layouts/container/PsFrontendLayout.vue';
import PsConst from '@templateCore/object/constant/ps_constants';
import { PsValueStore } from '@templateCore/store/modules/core/PsValueStore';

export default {
    name : "PrivacyView",
    components : {
        PsContentContainer,
        PsLabelTitle,
       PsLabel,
        PrivacySkeletor,
        Head
    },
    layout: PsFrontendLayout,
    setup(props) {
        const aboutUsStore = useAboutUsStoreState();
        let psValueStore = PsValueStore();
        const loginUserId = psValueStore.getLoginUserId();
        aboutUsStore.loadAboutUs(loginUserId);

        return {
            aboutUsStore,

        };
    }
}
</script>
