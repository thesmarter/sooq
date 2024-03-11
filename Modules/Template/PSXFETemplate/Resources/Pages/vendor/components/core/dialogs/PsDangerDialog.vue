<template>
    <ps-modal ref="psmodal" maxWidth="480px" line="hidden" :isClickOut='false' theme=" px-6 py-6 rounded-lg shadow-xl me-3" class=' z-20'>
        <template #title>
            <div class="flex flex-row items-center ">
                <ps-icon name="info"  class="text-feError-500 me-2.5"  />
                <ps-label class="text-lg font-semibold"> {{title}} </ps-label>
            </div>
        </template>
        <template #body>
            <div class="w-full text-start mt-2">
                <ps-label class="font-light text-sm lg:text-base"> {{message}} </ps-label>
            </div>
        </template>
        <template #footer>
            <div class=" flex flex-row justify-end">
                <ps-button rounded="rounded" @click="actionClicked('no')" textSize="text-xs lg:text-sm" class=" me-4" border="border border-feAchromatic-200 dark:border-feSecondary-700" colors="bg-none dark:text-feSecondary-100" hover="hover:outline-none hover:ring hover:ring-feSecondary-100 dark:hover:ring-feSecondary-600" > {{cancelButton}} </ps-button>
                <ps-button rounded="rounded" @click="actionClicked('yes')" textSize="text-xs lg:text-sm" class="" colors="bg-feError-500 text-feAchromatic-50 dark:text-feAchromatic-900"  hover="hover:outline-none hover:ring hover:ring-feError-100" focus="focus:outline-none focus:ring focus:ring-feError-300" > {{okButton}} </ps-button>
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

export default defineComponent({
    name: "PsDangerDialog",
    components : {
        PsModal,
        PsLabel,
        PsButton,
        PsIcon
    },
    setup() {
        const psmodal = ref();
        const title = ref(trans('ps_danger_dialog__danger'));
        const message = ref(trans('ps_danger_dialog__danger_message'));

        const okButton = ref(trans('ps_confirm_dialog__yes'));
        const cancelButton = ref(trans('ps_confirm_dialog__no'));
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

        function openModal(titleStr,
                messageStr,
                okString,
                cancelString,
                okClickedAction: Function,
                cancelClickedAction: Function) {
             if(titleStr != null && titleStr.trim() != '') {
                title.value = titleStr;
            }

            if(messageStr != null && messageStr.trim() != '') {
                message.value = messageStr;
            }
            if(cancelString != null && cancelString.trim() != '') {
                cancelButton.value = cancelString;
            }

            if(okString != null && okString.trim() != '') {
                okButton.value = okString;
            }
            psmodal.value.toggle(true);
            okClicked = okClickedAction;
            cancelClicked= cancelClickedAction;

        }

        function closeModal() {
            psmodal.value.toggle(false);
        }
        return {
            psmodal,
            openModal,
            closeModal,
            title,
            actionClicked,
            okButton,
            cancelButton,
            message
        }
    },
})
</script>
