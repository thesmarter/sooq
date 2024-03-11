<template>

    <Head :title="$t('privacy_policy_module')"  >
        <meta name="description" content="This is a dynamic meta description from inertia head tag">
        <meta name="keywords" content="dynamic, meta, tags from inertia head tag">
    </Head>

    <ps-label class="p-8" v-if="privacy_policy && privacy_policy.content">
        <span v-html="privacy_policy.content" />
    </ps-label>

</template>

<script>
import PsLabel from "@template1/vendor/components/core/label/PsLabel.vue";
import { defineComponent,onMounted,ref } from "vue";
// import { router } from '@inertiajs/vue3'
import { Head } from "@inertiajs/vue3";


export default defineComponent({
    name: "Index",
    components: {
        PsLabel,
        Head
    },
    props: ['privacy_policy' ],
    setup() {


        onMounted(() => {
            var loading = document. getElementById("home_loading__container");
            loading.style.display = "none";
        })

        return {
        }
    },

})
</script>
