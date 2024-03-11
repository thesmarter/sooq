<template>
    <ps-label class="lg:text-sm sm:text-xs text-xxs font-light" :textColor="textColor"  >
        <slot></slot>
    </ps-label> 
</template>

<script lang="ts">
import { defineComponent } from 'vue';
import PsLabel from './PsLabel.vue';

export default defineComponent({
    name : 'PsLabelCaption2',
    components: { 
        PsLabel 
    },
    props: {
        textColor : {
            type: String,               
            default: 'text-feSecondary-300 dark:text-feSecondary-300',                
        }
    }    
});
</script>