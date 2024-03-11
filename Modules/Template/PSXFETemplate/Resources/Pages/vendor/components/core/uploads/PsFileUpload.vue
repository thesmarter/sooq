<template>
    <div class="border-dashed border-2 border-secondary-200 dark:border-secondary-800 rounded p-2 flex gap-2 items-center">
        <ps-button :disabled="disabled" colors="bg-transparent" border="border border-secondary-300 dark:border-secondary-800" shadow="shadow" rounded="rounded">
            <ps-icon name="fileUpload2" w="24" h="24"/>
            <ps-label class="ms-2">
                {{ $t('core_fe__upload_file') }}</ps-label>
        </ps-button>
        <ps-label :textColor="disabled ? disabledTheme : defaultTheme">{{ title == '' ? $t(placeholder) : title }}</ps-label>
    </div>
</template>

<script>
import { ref, defineComponent } from "vue";
import PsIcon from '@template1/vendor/components/core/icons/PsIcon.vue';
import PsLabel from '@template1/vendor/components/core/label/PsLabel.vue';
import PsButton from '@template1/vendor/components/core/buttons/PsButton.vue';

    export default defineComponent({
        name: "PsFileUpload",
        components: {
            PsButton,
            PsIcon,
            PsLabel
        },
        props: {
            title: {
                type: String,
                default: ""
            },
            placeholder: {
                type: String,
                default: "core_fe__or_drop_files"
            },
            disabled: {
                type: Boolean,
                default: false
            },
            disabledTheme: {
                type: String,
                default: "text-secondary-300 dark:text-secondary-700 placeholder-secondary-300 dark:placeholder-secondary-700"
            },
            defaultTheme: {
                type: String,
                default: "text-secondary-800 dark:text-secondary-100 placeholder-secondary-800 dark:placeholder-secondary-100"
            },
        },
    })
</script>
