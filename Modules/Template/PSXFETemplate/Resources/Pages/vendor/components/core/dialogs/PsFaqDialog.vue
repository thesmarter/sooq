<template>
    <ps-modal ref="psmodal" maxWidth="868px" line="hidden" :isClickOut='false' theme="rounded-lg shadow-xl overflow-hidden dark:bg-feSecondary-800" class=' z-20'>
        <template #title>
            <div class="flex justify-between shadow p-6 dark:bg-feSecondary-800">
                <ps-label textColor="text-2xl font-semibold text-feSecondary-800 dark:text-feSecondary-50"> {{ $t('item_detail__faq') }} </ps-label>
                <ps-icon name="close" class="text-feAchromatic-400 hover:cursor-pointer" @click.prevent="psmodal.toggle(false)"></ps-icon>
            </div>
        </template>
        <template #body>
                <div class="h-52 overflow-y-auto my-6 ps-6 pe-10">
                    <ps-label class="text-base font-normal text-feSecondary-700 dark:text-feSecondary-400">
                        <div v-html="aboutUsProvider.aboutus?.data?.faqPage"></div>
                    </ps-label>
                </div>
        </template>
        <template #footer>
            <div class="flex justify-end p-4 bg-feSecondary-100 dark:bg-feSecondary-800">
                <ps-button @click="close()"> {{ $t('Close') }} </ps-button>
                <ps-button @click="close()" class='ms-4' colors="bg-feAchromatic-50 dark:bg-feAchromatic-800 dark:text-feAchromatic-200 hover:text-feAchromatic-50" border="border border-feAchromatic-300 dark:border-feAchromatic-500"> {{ $t('Cancel') }} </ps-button>
            </div>
        </template>
    </ps-modal>
</template>

<script lang="ts">
import { defineComponent,ref } from 'vue';
import PsModal from '../modals/PsModal.vue';
import PsLabel from '../label/PsLabel.vue';
import PsButton from '../buttons/PsButton.vue';
import { trans } from 'laravel-vue-i18n';
import PsIcon from "../icons/PsIcon.vue";

import { useAboutUsStoreState } from "@templateCore/store/modules/aboutus/AboutUsStore";

export default defineComponent({
    name : "PsFaqDialog",
    components : {
        PsModal,
        PsLabel,
        PsButton,
        PsIcon
    },
    setup() {
        const aboutUsProvider = useAboutUsStoreState();
        aboutUsProvider.loadAboutUs('1');
        const psmodal = ref();
        let okClicked: Function;
        let cancelClicked: Function;

        function actionClicked(status) {
            if(status == 'yes') {
                okClicked();
            }else {
                cancelClicked();
            }
            psmodal.value.toggle(false);
        }

        function openModal() {
            psmodal.value.toggle(true);
        }

        function close() {
            psmodal.value.toggle(false);
        }

        return {
            aboutUsProvider,
            psmodal,
            openModal,
            actionClicked,
            close
        }
    },
})
</script>
