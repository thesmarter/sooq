<template>
    <ps-label class="sm:text-2xl text-xl font-semibold" :textColor="textColor"  >
        <slot></slot>
    </ps-label>
</template>

<script lang="ts">
import { defineComponent } from 'vue';
import PsLabel from './PsLabel.vue';

export default defineComponent({
    name : 'PsLabelHeader4',
    components: { PsLabel },
    props: {
        textColor : {
            type: String,
            default: 'text-feSecondary-800 dark:text-feSecondary-300',
        }
    }
});
</script>
