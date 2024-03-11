<template>
    <ps-modal ref="psmodal" maxWidth="400px" :isClickOut='false' theme=" lg:px-8 px-6 py-6 lg:rounded-2xl rounded-xl" class=' z-20'>
        <template #title>
            <div class="w-full text-center flex flex-auto justify-center">
                <ps-icon name="warningTriangle" class="text-feWarning-300 mt-1 me-2" />
                <ps-label class="font-medium text-lg lg:text-xl"> {{title}} </ps-label>
            </div>
        </template>
        <template #body>
            <div class="w-full text-center ">
                <ps-label class="font-light text-xs lg:text-sm"> {{message}} </ps-label>
            </div>
        </template>
        <template #footer>
            <div class=" flex flex-row justify-between">
                <ps-button @click="actionClicked('no')" textSize="text-xxs lg:text-sm" class="py-3 " theme="bg-feSecondary-300 dark:bg-feAchromatic-500 text-feAchromatic-50 dark:text-feAchromatic-50"> {{cancelButton}} </ps-button>  
                <ps-button @click="actionClicked('yes')" textSize="text-xxs lg:text-sm" class="py-3"  > {{okButton}} </ps-button>                 
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
import PsIcon from '@template1/vendor/components/core/icons/PsIcon.vue';

export default defineComponent({
    name : "PsConfirmDialog",
    components : {
        PsModal,
        PsLabel,
        PsButton,
        PsIcon
    },
    setup() {
        const psmodal = ref();
        const title = ref(trans('ps_warning_dialog__warning'));
        const message = ref(trans('ps_warning_dialog__warning_message'));
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
