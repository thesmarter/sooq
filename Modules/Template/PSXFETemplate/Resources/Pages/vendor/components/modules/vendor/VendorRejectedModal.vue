<template>
    <ps-modal ref="psmodal" line="hidden" maxWidth="472px" :isClickOut='false' class='h-full' bodyHeight="max-h-80" theme="pt-4 px-6 pb-10 border rounded" >
        <template #title>
            <div class="w-full relative h-6">
                <ps-icon class="cursor-pointer dark:text-feSecondary-500 absolute right-0" name="close" w="24" h="24" @click="psmodal.toggle(false)"/>
            </div>
        </template>
        <template #body>
            <div class="flex flex-col items-center">
                <img alt="Placeholder" class="w-52 h-32 my-6"
                    v-lazy="{ src: $page.props.sysImageUrl + '/rejected.png' }" />
                <ps-label textColor="text-feSecondary-800 text-xl font-semibold">{{ $t('core_fe__vendor_application_rejected_title') }}</ps-label>

                <ps-label textColor="text-center text-feSecondary-600 text-base mt-3">{{ $t('core_fe__vendor_application_rejected_description') }}</ps-label>
            </div>
        </template>
        <template #footer>
            <div class="w-full flex justify-center ">
                <ps-button @click="actionClicked()">{{ $t('core_fe__resubmit') }}</ps-button>
            </div>
        </template>
    </ps-modal>
</template>

<script lang="ts">
import { defineComponent, ref } from 'vue';

import PsModal from '@template1/vendor/components/core/modals/PsModal.vue';
import PsIcon from '@template1/vendor/components/core/icons/PsIcon.vue';
import PsLabel from '@template1/vendor/components/core/label/PsLabel.vue';
import PsButton from '@template1/vendor/components/core/buttons/PsButton.vue';

    export default defineComponent({
        name: 'VendorRejectedModal',
        components: {
            PsModal,
            PsIcon,
            PsLabel,
            PsButton
        },
        setup() {
            const psmodal = ref();
            let okClicked: Function;

            function actionClicked() {
                okClicked();
                psmodal.value.toggle(false);
            }

            function openModal(okClickedAction: Function) {
                psmodal.value.toggle(true);
                okClicked = okClickedAction;
            }

            function closeModal() {
                psmodal.value.toggle(false);
            }
            return {
                psmodal,
                openModal,
                closeModal,
                actionClicked,
            }
        },
    })
</script>
