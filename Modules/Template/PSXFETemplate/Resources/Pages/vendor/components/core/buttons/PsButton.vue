<template>
    <button :type="type" :disabled="disabled" :class="disabled ? [rounded, padding, border, shadow, textSize , justify, 'bg-feSecondary-100 text-feSecondary-300 dark:text-feSecondary-600 dark:bg-feSecondary-700 cursor-not-allowed opacity-90'] : [rounded, colors, padding, border, shadow, textSize, hover, focus, cursor, opacity , justify]"
    class="font-medium transition duration-150 ease-in-out" >
        <slot />
    </button>
</template>

<script>
    export default {
        name: "PsCustomButton",
        props: {
            type: {
                type: String,
                default: 'submit',
            },
            rounded : {
                type: String,
                default: 'rounded'
            },
            colors : {
                type: String,
                default: 'bg-fePrimary-500 text-feAchromatic-50 '
            },
            padding : {
                type: String,
                default: 'py-2 px-4'
            },
            border : {
                type: String,
                default: 'border-none'
            },
            shadow : {
                type: String,
                default: 'shadow-none'
            },
            textSize : {
                type: String,
                default: 'text-sm '
            },
            justify : {
                type: String,
                default: 'flex justify-center items-center '
            },
            hover : {
                type: String,
                default: 'hover:outline-none hover:bg-fePrimary-600 active:bg-fePrimary-700'
            },
            focus : {
                type: String,
                default: 'focus:outline-none focus:bg-fePrimary-500 focus:ring focus:ring-fePrimary-300'
            },
            cursor: {
                type: String,
                default: 'cursor-pointer'
            },
            opacity: {
                type: String,
                default: 'opacity-100'
            },
            disabled: {
                type: Boolean,
                default: false
            },
        }
    }
</script>