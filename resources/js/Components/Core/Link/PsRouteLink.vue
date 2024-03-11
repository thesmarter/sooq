<template>   
    <router-link :class="[textColor, textSize]">
        <slot></slot>
    </router-link>      
</template>

<script>

export default {
    name : 'PsRouteLink',
    props: {
        textColor : {
            type: String,               
            default: 'text-secondary-500 dark:text-secondaryDark-white'                
        },
        textSize : {
            type : String,
            default : 'text-base'
        }        
    }
}
</script>
