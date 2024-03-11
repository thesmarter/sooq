<template>

    <ps-loading-dialog ref="psloading" /> 

    <ps-modal ref="psmodal" maxWidth="350px" :isClickOut='false' class=' z-20'>
        <template #title>
            <ps-label-title> {{ $t("ps_model_view__title") }}</ps-label-title>
        </template>
        <template #body>
            <ps-label> {{ $t("ps_model_view__message") }} </ps-label>
            <ps-label> {{ $t("ps_model_view__message") }} </ps-label>
            <ps-label> {{ $t("ps_model_view__message") }} </ps-label>
            <ps-label> {{ $t("ps_model_view__message") }} </ps-label>
            <ps-label> {{ $t("ps_model_view__message") }} </ps-label>
            <ps-label> {{ $t("ps_model_view__message") }} </ps-label>
            <ps-label> {{ $t("ps_model_view__message") }} </ps-label>
            <ps-label> {{ $t("ps_model_view__message") }} </ps-label>
            <ps-label> {{ $t("ps_model_view__message") }} </ps-label>
            <ps-label> {{ $t("ps_model_view__message") }} </ps-label>
        </template>
        <template #footer>
            <div class="flex flex-row justify-end">
                <ps-button @click="psloading.openModal()"> {{ $t("ps_model_view__save") }} </ps-button>
                <ps-button @click="psmodal.toggle(false)"> {{ $t("ps_model_view__end") }} </ps-button>
            </div>
        </template>
    </ps-modal>
</template>

<script lang='ts'>
import { defineComponent,ref } from 'vue'
import PsModal from '@template1/vendor/components/core/modals/PsModal.vue';
import PsLabel from '@template1/vendor/components/core/label/PsLabel.vue';
import PsLabelTitle from '@template1/vendor/components/core/label/PsLabelTitle.vue';
import PsButton from '@template1/vendor/components/core/buttons/PsButton.vue';
import PsLoadingDialog from '../../components/core/dialog/PsLoadingDialog.vue';

export default defineComponent({
    name : "PsModelView",
    components : {
        PsModal,
        PsLabel,
        PsLabelTitle,
        PsLoadingDialog,
        PsButton
    },
    setup() {
        const psmodal = ref();
        const psloading = ref();

        function openModal() {
            psmodal.value.toggle(true);
        }
        return {
            psmodal,
            openModal,
            psloading
        }
    },
})
</script>
