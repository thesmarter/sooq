<template>
    <ps-modal ref="psmodal" maxWidth="472px" line="hidden" :isClickOut='false' theme="p-6 rounded-lg bg:white" class=' z-20'>
        <template #title>
            <ps-label class="font-semibold text-xl text-feSecondary-800"> {{title}} </ps-label>
        </template>
        <template #body>
            <div class="w-full mt-4">
                <ps-label class="font-medium text-base text-feSecondary-700"> {{message}} </ps-label>
            </div>
        </template>
        <template #footer>
            <div class="flex justify-end mt-8">
                <ps-button @click="actionClicked('yes')"> {{okButton}} </ps-button>
            </div>
        </template>
    </ps-modal>
</template>

<script lang='ts'>
import { defineComponent,ref } from 'vue';
import { trans } from 'laravel-vue-i18n';
import PsModal from '@template1/vendor/components/core/modals/PsModal.vue';
import PsLabel from '@template1/vendor/components/core/label/PsLabel.vue';
import PsButton from '@template1/vendor/components/core/buttons/PsButton.vue';

export default defineComponent({
    name : "PsConfirmDialog3",
    components : {
        PsModal,
        PsLabel,
        PsButton
    },
    setup() {
        const psmodal = ref();
        const title = ref(trans('ps_warning_dialog__warning'));
        const message = ref(trans('ps_warning_dialog__warning_message'));
        const okButton = ref(trans('ps_confirm_dialog__yes'));
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

        function openModal(
                titleString,
                messageString,
                okString,
                okClickedAction: Function,) {
            title.value = titleString;
            message.value = messageString.toString();
            okButton.value = okString;
            psmodal.value.toggle(true);
            okClicked = okClickedAction;
        }

        return {
            psmodal,
            title,
            message,
            openModal,
            actionClicked,
            okButton,
        }
    },
})
</script>
