<template>
    <ps-modal ref="psmodal" maxWidth="350px" :isClickOut='false' theme="rounded-lg p-6" class=" z-50" >
        <template #title>
            <ps-label-title> {{title}} </ps-label-title>
        </template>
        <template #body>
            <ps-label> {{message}} </ps-label>
        </template>
        <template #footer>
            <div class="flex justify-end">
                <ps-button @click="psmodal.toggle(false)"> Ok </ps-button>
            </div>
        </template>
    </ps-modal>
</template>

<script lang='ts'>
import { defineComponent,ref } from 'vue';
import PsModal from '@template1/vendor/components/core/modals/PsModal.vue';
import PsLabelTitle from '@template1/vendor/components/core/label/PsLabelTitle.vue';
import PsLabel from '@template1/vendor/components/core/label/PsLabel.vue';
import PsButton from '@template1/vendor/components/core/buttons/PsButton.vue';
import { trans } from 'laravel-vue-i18n';export default defineComponent({
    name: "PsSuccessDialog",
    components : {
        PsModal,
        PsLabel,
        PsLabelTitle,
        PsButton
    },
    setup() {
        const psmodal = ref();
        const title = ref(trans('ps_success_dialog__success'));
        const message = ref(trans('ps_success_dialog__success_message'));

        function openModal(titleStr, messageStr) {
            if(titleStr != null && titleStr.trim() != '') {
                title.value = titleStr;
            }

            if(messageStr != null && messageStr.trim() != '') {
                message.value = messageStr;
            }
            psmodal.value.toggle(true);
        }

        function closeModal() {
            psmodal.value.toggle(false);
        }
        return {
            psmodal,
            openModal,
            closeModal,
            title,
            message
        }
    },
})
</script>
