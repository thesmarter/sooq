<template>
    <ps-modal ref="psmodal" line="hidden" maxWidth="472px" :isClickOut='false' theme="p-6 rounded-lg dark:bg-feSecondary-800" class=' z-20'>
        <template #title>
            <div class="w-full">
                <ps-label class="font-semibold text-xl text-feSecondary-800 dark:text-feSecondary-200"> {{title}} </ps-label>
            </div>
        </template>
        <template #body>
            <div class="w-full mt-4">
                <ps-label class="text-base font-normal text-feSecondary-700 dark:text-feSecondary-300"> {{message}} </ps-label>
            </div>
        </template>
        <template #footer>
            <div class=" flex justify-end mt-8">
                <div class="flex-grow-0">
                    <ps-button @click="actionClicked()" textSize="text-xxs lg:text-sm" class="py-3"  > {{okButton}} </ps-button>
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
import { trans } from 'laravel-vue-i18n';export default defineComponent({
    components : {
        PsModal,
        PsLabel,
        PsButton
    },
    setup() {
        const psmodal = ref();
        const title = ref(trans('ps_success_dialog__success'));
        const message = ref(trans('ps_success_dialog__success_message'));
        const okButton = ref(trans('ps_confirm_dialog__yes'));
        let okClicked: Function;

        function actionClicked() {
            okClicked();
            psmodal.value.toggle(false);
        }

        function openModal(
                titleString,
                messageString,
                okString,
                okClickedAction: Function, ) {
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
