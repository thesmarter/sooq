<template>
    <div class="w-full block ">
        <textarea :disabled="disabled" class=" w-full px-4 py-2 text-sm shadow-sm bg-transparent  placeholder-feSecondary-500 "
                    :rows="rows"
                    :value="value"
                    :class="disabled ? [ rounded,disabledTheme, ] : isError ? [theme, rounded, errorBorder] : [theme, rounded, defaultBorder]"
                    :placeholder="placeholder"
                    @input="handleInput($event.target.value)"
        ></textarea>
    </div>
</template>

<script>
import { ref } from 'vue';

export default {
    name : 'PsTextArea',
    props: {
        "rows" : {
            type: Number,
            default: 3
        },
        "placeholder" : {
            type: String,
            default: ""
        },
        "value" : { type: String, default: "" },
        "theme" : {
            type: String,
            default : "text-feSecondary-500 dark:text-feSecondary-400"
        },
        "rounded" : {
            type: String,
            default : "rounded"
        },
        "disabled": {
            type: Boolean,
            default: false
        },
        "disabledTheme": { type: String, default: " text-feSecondary-300 border-feSecondary-300 dark:border-feSecondary-800 dark:text-feSecondary-700 shadow-none placeholder-feSecondary-300 dark:placeholder-feSecondary-600" },
        "defaultBorder": { type: String, default: "border border-feSecondary-200 hover:border-feSecondary-400 dark:border-feSecondary-400 hover:dark:border-feSecondary-50 focus:outline-none focus:border-none focus:ring-2 focus:ring-fePrimary-300 ring-fePrimary-300 placeholder-feSecondary-500 dark:placeholder-feSecondary-400" },
        "errorBorder": { type: String, default: "border border-fePrimary-500 focus:border-fePrimary-500 focus:outline-none focus:border-none focus:ring-1 focus:ring-fePrimary-500-500 ring-fePrimary-500-500 placeholder-feSecondary-500 dark:placeholder-feSecondary-400" },
    },
    setup(_, {emit}) {
        const isError = ref(false);
        function handleInput(v) {
            emit('update:value', v);
        }

        return {
            handleInput,
            isError
        }
    }
};
</script>
