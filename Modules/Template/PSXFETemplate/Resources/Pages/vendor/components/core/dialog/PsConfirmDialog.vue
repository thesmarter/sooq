<template>
    <ps-modal ref="psmodal" maxWidth="370px" :isClickOut='false' line="hidden" theme="p-5 rounded-lg" class=' z-20'>
        <template #title>
            <div class="w-full">
                <ps-label class="font-semibold text-lg text-feSecondary-800"> {{title}} </ps-label>
            </div>
        </template>
        <template #body>
            <div class="w-full mt-3">
                <ps-label class="text-sm text-feSecondary-700"> {{message}} </ps-label>
            </div>
        </template>
        <template #footer>
            <div class="flex justify-end mt-5">
                <div class="flex gap-4">
                    <ps-button @click="actionClicked('yes')" > {{okButton}} </ps-button>
                    <ps-button @click="actionClicked('no')" colors="bg-feAchromatic-50" border="border" hover="" focus=""> {{cancelButton}} </ps-button>
                </div>
            </div>
        </template>
    </ps-modal>
</template>

<script lang='ts'>
import { defineComponent,ref } from 'vue';
import PsModal from '@template1/vendor/components/core/modals/PsModal.vue';
import PsLabel from '@template1/vendor/components/core/label/PsLabel.vue';
import PsButton from '@template1/vendor/components/core/buttons/PsButton.vue';
import { trans } from 'laravel-vue-i18n';
export default defineComponent({
    name : "PsConfirmDialog",
    components : {
        PsModal,
        PsLabel,
        PsButton
    },
    setup() {
        const psmodal = ref();
        const title = ref(trans('ps_confirm_dialog__confirm'));
        const message = ref(trans('ps_confirm_dialog__are_you_confirm'));
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

        function openModal(
                titleString,
                messageString,
                okString,
                cancelString,
                okClickedAction: Function,
                cancelClickedAction: Function) {
            title.value = titleString;
            message.value = messageString.toString();
            okButton.value = okString;
            cancelButton.value = cancelString;
            psmodal.value.toggle(true);
            okClicked = okClickedAction;
            cancelClicked= cancelClickedAction;
        }

        return {
            psmodal,
            title,
            message,
            openModal,
            actionClicked,
            okButton,
            cancelButton
        }
    },
})
</script>
