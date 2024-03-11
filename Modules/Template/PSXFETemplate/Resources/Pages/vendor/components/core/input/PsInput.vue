<template>
    <input
        class="block bg-transparent w-full px-3 py-2 text-sm shadow-sm dark:placeholder-feAchromatic-600 placeholder-feAchromatic-200"
        :type="type"
        :value="value"
        :disabled="disabled"
        :readonly="readonly"
        :class="disabled ? [ opacity,rounded,disabledTheme, ] : isError ? [theme, rounded, , errorBorder, opacity] : [theme, rounded, defaultBorder, opacity]"
        :maxlength="maxlength"
        :placeholder="placeholder"
        @input="handleInput($event.target.value)" />
</template>

<script>
import { ref } from 'vue';

export default {
    name : "PsInput",
    props: {
        "value" : { type: String, default: "" },
        "type" : { type: String, default: "text" },
        "theme" : { type: String, default : "text-feSecondary-600 dark:text-feSecondary-400" },
        "rounded": { type: String, default: "rounded" },
        "maxlength" : { type : Number, default : 99999999 },
        "placeholder": { type: String, default: "" },
        "disabled": { type: Boolean, default: false },
        "readonly": { type: Boolean, default: false },
        "disabledTheme": { type: String, default: " text-feSecondary-300 border-feSecondary-300 dark:border-feSecondary-800 dark:text-feSecondary-700 shadow-none placeholder-feSecondary-300 dark:placeholder-feSecondary-600" },
        "defaultBorder": { type: String, default: "border border-feSecondary-200 hover:border-feSecondary-400 dark:border-feSecondary-400 hover:dark:border-feSecondary-50 focus:outline-none focus:border-none focus:ring-2 focus:ring-fePrimary-300 ring-fePrimary-300 placeholder-feSecondary-500 dark:placeholder-feSecondary-400" },
        "errorBorder": { type: String, default: "border border-fePrimary-500 focus:border-fePrimary-500 focus:outline-none focus:border-none focus:ring-1 focus:ring-fePrimary-500-500 ring-fePrimary-500-500 placeholder-feSecondary-500 dark:placeholder-feSecondary-400" },
        "onInput" : Function,
        opacity: {
                type: String,
                default: 'opacity-100'
            },

    },

    setup( props , { emit}) {

        const isError = ref(false);
        function handleInput(v) {
            emit('update:value', v);

            if(props.onInput != null) {
                props.onInput(v);
            }
        }

        return {
            handleInput,
            isError
        }
    }
}
</script>
