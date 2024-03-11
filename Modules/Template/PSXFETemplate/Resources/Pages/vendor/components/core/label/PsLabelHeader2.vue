<template>
    <ps-label class="lg:text-5xl sm:text-4xl text-3xl" :textColor="textColor"  >
        <slot></slot>
    </ps-label> 
</template>

<script lang="ts">
import { defineComponent } from 'vue';
import PsLabel from './PsLabel.vue';

export default defineComponent({
    name : 'PsLabelHeader2',
    components: { PsLabel },
    props: {
        textColor : {
            type: String,               
            default: 'text-feSecondary-800 dark:text-feSecondary-300',            
        }        
    }    
});
</script>