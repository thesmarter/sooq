<template>
    <div class="relative">
        <input
        class="bg-feSecondary-200 dark:bg-feSecondary-700 focus:bg-feSecondary-300 focus:dark:bg-feSecondary-900 w-full text-sm shadow-sm ps-10"
        :class="disabled ? [ rounded,disabledTheme, padding] : [theme, rounded,defaultBorder,placeholderColor, padding]"
        :disabled="disabled"
         :type="type" :value="value" :maxlength="maxlength" @input="handleInput($event.target.value)" :placeholder="placeholder"/>
        <div class="absolute inset-y-0 text-fePrimary-500 dark:text-feSecondary-300 flex items-center me-2 ms-3.5 left-0" :class="theme">
            <ps-icon name="search" class='cursor-pointer'/>
        </div>
    </div>
</template>

<script>
import PsIcon from '../icons/PsIcon.vue';

export default {
    name: "PsInputWithLeftIcon",
    components : {
        PsIcon,
        // PsLabel
    },
    props: {
        "value" : { type: String, default: "" },
        "type" : { type: String, default: "text" },
        "theme" : { type: String, default : "text-feSecondary-500 dark:text-feSecondary-300" },
        "rounded": { type: String, default: "" },
        "maxlength" : { type : Number, default : 99999999 },
        "placeholder": { type: String, default: "" },
        "placeholderColor": {type: String, default: "placeholder-feSecondary-500 dark:placeholder-feSecondary-400"},
        "onInput" : Function,
        "disabled": { type: Boolean, default: false },
        "padding": { type:String, default: "py-2.5 px-4"},
        "disabledTheme": { type: String, default: "bg-none text-feSecondary-300 border-none dark:text-feSecondary-700 shadow-none placeholder-feSecondary-300 dark:placeholder-feSecondary-600" },
        "defaultBorder": { type: String, default: "border-none focus:outline-none focus:ring-feAchromatic-50 dark:focus:ring-feAchromatic-800 placeholder-feSecondary-500 dark:placeholder-feSecondary-400" },
    },
    setup(_, {emit}) {
        // const psValueHolder = PsValueProvider.psValueHolder;
        function handleInput(v) {
            emit('update:value', v)
        }

        return {
            handleInput,
            // psValueHolder
        }
    }
}
</script>
