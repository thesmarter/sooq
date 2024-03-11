<template>

    <Head :title="$t('edit_data_deletion_policy')" />
  
    <ps-label class="p-8" v-if="data_deletion_policy && data_deletion_policy.content">
        <span v-html="data_deletion_policy.content" />
    </ps-label>

</template>

<script>
import PsLabel from "@/Components/Core/Label/PsLabel.vue";
  import { defineComponent } from "vue";


export default defineComponent({
    name: "Index",
    components: {
        PsLabel
    },
    props: ['data_deletion_policy' ],
    setup(props) {

        return {

        }
    },
    
})
</script>
