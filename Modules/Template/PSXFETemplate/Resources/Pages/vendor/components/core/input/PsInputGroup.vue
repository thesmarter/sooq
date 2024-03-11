<template>

    <div class="flex flex-row " :class="[background ]">

        <div class="flex-grow-0 flex flex-row">
            <slot name="leftButton"/>

            <ps-line :theme="line" />

            <ps-line v-if="istwoline" class="ms-1" :theme="line"/>
        </div>

        <input
        class="dark:bg-feAchromatic-800 flex-grow block w-full px-4 py-3 text-sm shadow-sm border border-none placeholder-feSecondary-500"
        :type="type"
        :value="value"
        :disabled="disabled"
        :class="disabled ? [ opacity,disabledTheme, ] : [theme,invalid, focus, opacity]"
        :maxlength="maxlength"
        :placeholder="placeholder"
        @input="handleInput($event.target.value)" />

         <div class="flex-grow-0 flex flex-row">
        
            <ps-line :theme="line"/>

            <ps-line v-if="istwoline" class="ms-1" :theme="line"/>

            <slot name="rightButton"/>
         </div>

    </div>
    
</template>

<script>
import PsLine from "../line/PsLine.vue";

export default {
    name : "PsInputGroup",
    components : {
            PsLine
        },
    props: {
        "value" : { type: String, default: "" },
        "type" : { type: String, default: "text" },
        "theme" : { type: String, default : "text-feSecondary-500 dark:text-feSecondary-400" },
        "maxlength" : { type : Number, default : 99999999 },
        "placeholder": { type: String, default: "" },
        "disabled": { type: Boolean, default: false },
        "disabledTheme": { type: String, default: " text-feSecondary-300 border-feSecondary-200 shadow-none placeholder-feSecondary-300" },
        "invalid": { type: String, default: "invalid:border-pink-500 invalid:text-pink-600 focus:invalid:border-pink-500 focus:invalid:ring-pink-500" },
        "focus": { type: String, default: " focus:outline-none focus:ring-0" },
        "onInput" : Function,
        background : {
            type: String,
            default: 'bg-feAchromatic-50 dark:bg-fePrimary-100 rounded-md border border-feAchromatic-200 shadow-sm'
        },
        opacity: {
            type: String,
            default: 'opacity-100'
        },
        line: {
            type: String,
            default: 'border-feAchromatic-200 dark:border-white'
        },
        istwoline: {
            type: Boolean,
            default: false
        },
    },

    setup( props , { emit}) {
        function handleInput(v) {
            emit('update:value', v);

            if(props.onInput != null) {
                props.onInput(v);
            }
        }

        return {
            handleInput
        }
    }
}
</script>
