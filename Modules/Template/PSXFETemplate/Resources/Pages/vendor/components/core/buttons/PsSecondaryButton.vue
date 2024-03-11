<template>
    <button :type="type" :disabled="disabled" :class="disabled ? [rounded, padding, border, shadow, textSize , justify, 'bg-feSecondary-100 text-feSecondary-300 dark:text-feSecondary-600 dark:bg-feSecondary-700 dark:border-feSecondary-500 cursor-not-allowed opacity-90'] : [rounded, colors, padding, border, shadow, textSize, hover, focus, cursor, opacity , justify]"
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
                default: 'bg-feAchromatic-50 dark:bg-feAchromatic-900 text-feSecondary-800 dark:text-feAchromatic-50'
            },
            padding : {
                type: String,
                default: 'py-2 px-4'
            },
            border : {
                type: String,
                default: 'border border-feSecondary-200 '
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
                default: 'hover:outline-none dark:hover:text-feSecondary-800 dark:active:text-feSecondary-800  hover:bg-feSecondary-50 active:bg-feSecondary-500 dark:hover:bg-feSecondary-500 dark:active:bg-feSecondary-400'
            },
            focus : {
                type: String,
                default: 'focus:outline-none dark:focus:text-feAchromatic-50 focus:border-none focus:bg-feAchromatic-50 dark:focus:bg-feAchromatic-900 focus:ring focus:ring-fePrimary-200'
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