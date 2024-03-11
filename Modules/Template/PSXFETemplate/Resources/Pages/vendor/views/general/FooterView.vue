<template>
    <div class='flex flex-col w-full' >
       
        <!-- Footer Posts -->
        <!-- <div class='' :class="route.name === 'chat' ? 'hidden' : ''" > -->
            <footer-link-section />
        <!-- </div> -->
        <!-- End Footer Posts -->
    </div>
</template>

<script lang='ts'>
import FooterLinkSection from '@template1/vendor/components/layouts/footer/FooterLinkSection.vue';
// import { useRoute } from 'vue-router';

export default {
    name : "FooterView",
    components: { 
        FooterLinkSection 
    },    
    setup() {
        // const route = useRoute()
        return {
            // route
        }
    }

}
</script>