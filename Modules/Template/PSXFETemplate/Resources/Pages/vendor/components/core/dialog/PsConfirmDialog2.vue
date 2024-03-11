<template>
    <ps-modal ref="psmodal" maxWidth="472px" :isClickOut='false' theme="px-5 py-6 rounded-md" class=' z-20' :visibleLine="false">
        <template #title>
            <div class="w-full flex justify-between">
                <ps-label class="font-semibold text-lg"> {{title}} </ps-label>
                <ps-icon name="close" class="text-feAchromatic-400 hover:cursor-pointer" @click.prevent="actionClicked('no')"></ps-icon>
            </div>
        </template>
        <template #body>
            <div class="w-full">
                <ps-label class="font-light text-xs lg:text-sm"> {{message}} </ps-label>
            </div>
        </template>
        <template #footer>
            <div class=" flex flex-row justify-end  rtl:space-x-reverse space-x-5">
                <!-- <ps-button @click="actionClicked('no')" textSize="text-xxs lg:text-sm" class="py-3 " theme="bg-feSecondary-50 dark:bg-feAchromatic-500 text-fePrimary-500 dark:text-feAchromatic-50"> {{cancelButton}} </ps-button>
                <ps-button @click="actionClicked('yes')" textSize="text-xxs lg:text-sm" class="py-3"  > {{okButton}} </ps-button> -->
                <ps-button @click="actionClicked('yes')" textSize="font-normal text-xxs lg:text-sm" colors="bg-fePrimary-500 cursor-pointer text-feAchromatic-50" rounded="rounded"> {{ okButton }}</ps-button>
                <ps-button @click="actionClicked('no')" textSize="font-normal text-xxs lg:text-sm" colors="dark:bg-feAchromatic-800 dark:text-feAchromatic-200 hover:text-feAchromatic-50" focus="focus:text-feAchromatic-50 focus:bg-fePrimary-500" border="border border-feAchromatic-300 dark:border-feAchromatic-500" rounded="rounded"> {{ cancelButton }} </ps-button>
            </div>
        </template>
    </ps-modal>
</template>

<script lang='ts'>
import { defineComponent,ref } from 'vue';
import PsModal from '@template1/vendor/components/core/modals/PsModal.vue';
import PsLabel from '@template1/vendor/components/core/label/PsLabel.vue';
import PsButton from '@template1/vendor/components/core/buttons/PsButton.vue';
import PsIcon from '@template1/vendor/components/core/icons/PsIcon.vue';
import { trans } from 'laravel-vue-i18n';
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
