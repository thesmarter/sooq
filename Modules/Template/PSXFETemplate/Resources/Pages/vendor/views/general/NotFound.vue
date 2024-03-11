<template>
    <Head :title="$t('not_found__page_does_not_exit')"/>
    <div class=' mt-36 flex flex-col items-center'>
        <!-- <img src="@template1/assets/images/not_found.png"  width="30px" height="20px" alt='not found' class=" w-2/3 h-auto object-cover "/> -->
        <ps-label class="">
            {{ $t('not_found__page_does_not_exit') }}
        </ps-label>
        <ps-button @click="goToDashboard" class='mt-12 mb-20'> {{ $t('not_found__go_to_dashboard') }} </ps-button>
    </div>
</template>

<script lang='ts'>
import { Head } from "@inertiajs/vue3";
import PsLabel from "@template1/vendor/components/core/label/PsLabel.vue";
import PsButton from "@template1/vendor/components/core/buttons/PsButton.vue";
// import router from '@template1/router'
import { defineComponent } from "vue";
import { router } from '@inertiajs/vue3';

export default defineComponent({
    name: "NotFoundView",
    components: {
        PsLabel,
        PsButton,
        Head
    },
    setup() {
        function goToDashboard() {
            router.get(route('dashboard'));
        }

        return {
            goToDashboard
        }

    }


});
</script>
