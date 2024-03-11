<template>

    <Head :title="$t('privacy_policy_module')" />
  
    <ps-label class="p-8" v-if="privacy_policy && privacy_policy.content">
        <span v-html="privacy_policy.content" />
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
    props: ['privacy_policy' ],
    setup(props) {

        return {

        }
    },
    
})
</script>
