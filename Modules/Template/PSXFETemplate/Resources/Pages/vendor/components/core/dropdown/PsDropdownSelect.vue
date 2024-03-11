<style scoped>
    .child2{
        display: none;
    }
    .parent:focus > .child > .child1{
        display: none;
    }
    .parent:focus  > .child > .child2{
        display: block;
    }


</style>

<template>
    <button
        type="button" :disabled="disabled"
        class="parent inline-flex items-center leading-5 transition ease-in-out duration-150 btn-focus"
        :id="id"
        :class="[bgColor, w,h,padding,shadow,rounded,border,hover,focus, cursor,opacity]"
        aria-haspopup="true"
        aria-expanded="true">

        <div class="w-full flex flex-row child items-center" :class="justify" >
            <ps-icon v-if="leftIcon!=''" :name="leftIcon" :class="leftIconTheme" />
            <p :class="disabled ? 'text-xs lg:text-sm font-medium text-feSecondary-300 dark:text-feSecondary-100' : text" v-if="placeholder==''">
                {{selectedValue ? selectedValue : $t(placeholderLang)}}
            </p>
            <p :class="disabled ? 'text-xs lg:text-sm font-medium text-feSecondary-300 dark:text-feSecondary-100' : text" v-else>{{ selectedValue ? selectedValue : placeholder }}</p>
            
            <ps-icon name="downChervon" class="child1" :class="disabled ? 'text-feSecondary-300 dark:text-feSecondary-100' : iconTheme"  />
            <ps-icon name="upChervon" class="child2" :class="disabled ? 'text-feSecondary-300 dark:text-feSecondary-100' : iconTheme"  />
        </div>

    </button>

</template>

<script>
import PsIcon from '../icons/PsIcon.vue';
// import PsLabel from '../label/PsLabel.vue';
export default {
    name : "PsDropdownSelect",
    components : {
        PsIcon,
        // PsLabel
    },
    props : {
        placeholderLang: {
            type : String,
            default : "ps_dropdown_select__chooseone"
        },
        selectedValue : {
            type : String,
            default : ''
        },
        bgColor : {
            type : String,
            default : ' '
        },
        w: {
            type : String,
            default : 'w-full'
        },
        h: {
            type : String,
            default : '10'
        },
        id : {
            type : String
        },
        rounded : {
            type: String,
            default: ' rounded'
        },
        text : {
            type: String,
            default: 'text-xs lg:text-sm font-medium text-feSecondary-600 dark:text-feSecondary-100'
        },
        iconTheme : {
            type: String,
            default: 'text-feSecondary-500 dark:text-feSecondary-200 '
        },
        padding : {
            type: String,
            default: ' px-4 py-2 '
        },
        border : {
            type: String,
            default: 'border border-1 border-feSecondary-200 dark:border-feSecondary-800'
        },
        shadow : {
            type: String,
            default: 'shadow-sm'
        },
        justify : {
            type: String,
            default: 'flex justify-between '
        },
        hover : {
            type: String,
            default: ' hover:border-feSecondary-400'
        },
        focus : {
            type: String,
            default: 'focus:border-none focus:ring-2 focus:ring-fePrimary-300 ring-fePrimary-300 focus:outline-none focus:shadow-outline-blue text-feSecondary-800 dark:text-feSecondary-400 '
        },
        cursor: {
            type: String,
            default: 'cursor-pointer'
        },
        opacity: {
            type: String,
            default: 'opacity-100'
        },
        leftIcon: {
            type: String,
            default: '',
        },
        leftIconTheme: {
            type: String,
            default: 'me-1',
        },
        placeholder: {
            type : String,
            default : ''
        },
        disabled:{
            type : Boolean,
            default : false
        }
    },
    setup() {
    }

}
</script>
