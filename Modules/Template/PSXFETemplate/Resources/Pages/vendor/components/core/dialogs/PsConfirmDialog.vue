<template>
    <ps-modal ref="psmodal" maxWidth="480px" line="hidden" :isClickOut='false' theme=" px-6 py-6 rounded-lg shadow-xl" class=' z-50 h-56 bg-feAchromatic-50'>
        <template #title>
            <div class="w-full text-start">
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
                <ps-button rounded="rounded" @click="actionClicked('no')" textSize="text-xs lg:text-sm" class=" me-4" border="border border-feAchromatic-200" colors="bg-none" hover="hover:outline-none hover:ring hover:ring-feAchromatic-100" > {{cancelButton}} </ps-button>
                <ps-button rounded="rounded" @click="actionClicked('yes')" textSize="text-xs lg:text-sm" class=""  > {{okButton}} </ps-button>
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
