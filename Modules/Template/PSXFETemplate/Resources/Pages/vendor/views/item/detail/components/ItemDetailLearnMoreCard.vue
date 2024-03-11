<template>
    <div class="flex items-center  hover:text-fePrimary-500 pl-4 pt-2 pb-3  cursor-pointer" @click="clickLeranMore">
       <slot name="icon" ></slot>
        <ps-label 
        
        textColor="text-md pl-3 hover:text-fePrimary-500 text-feSecondary-800 dark:text-feSecondary-50"> {{ text }} </ps-label>
     </div>
</template>

<script>

import PsLabel from '@template1/vendor/components/core/label/PsLabel.vue';

    export default{
        name : 'ItemDetailLearnMoreCard',
        components : {
            PsLabel,
            
        },
 
        props: {
            clickLeranMore: {
                type: Function
            },
            text : String,
            iconName:String
    }
    }
</script>

