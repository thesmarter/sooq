<template>
    <ps-label class="text-lg sm:text-xl lg:text-2xl " :textColor="textColor"  >
        <slot></slot>
    </ps-label> 
</template>

<script lang="ts">
import { defineComponent } from 'vue';
import PsLabel from './PsLabel.vue';

export default defineComponent({
    name : 'PsLabelHeader5',
    components: { PsLabel },
    props: {
        textColor : {
            type: String,               
            default: 'text-feSecondary-800 dark:text-feSecondary-300',            
        }        
    }
    
});
</script>