<template>
    <a :href="url" class="w-full flex uppercase justify-center text-sm font-medium p-4" target="_parent" :class="textColor === '' ? 'text-feSecondary-500 dark:text-feAchromatic-50' : textColor">
        <slot></slot>
    </a>    
</template>

<script lang='ts'>
export default {
    name : 'PsLink1',
    props: {
        textColor : {
            type: String,               
            default: 'text-feSecondary-500 dark:text-feAchromatic-50',                
        },
        url : {
            type : String
        }        
    }
}
</script>