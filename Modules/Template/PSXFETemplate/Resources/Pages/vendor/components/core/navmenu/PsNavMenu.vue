<template>
<div>
</div>
    <!-- <ps-route-link
            :to="{name: name, params : params2 }" 
            :textColor="route.name === name && textColor == '' ? 'text-fePrimary-500 dark:text-fePrimary-500': textColor "
            :class="route.name === name ? ' font-bold flex': 'flex' "> 
            {{title}}
            
    </ps-route-link> -->
</template>

<script lang="ts">
import PsRouteLink from '@template1/vendor/components/core/link/PsRouteLink.vue';

export default {
    name: 'PsNavMenu',
    components: {
        PsRouteLink
    },
    props: {
        name : {
            type: String,
            default : 'Dashboard'
        },
        title : {
            type: String,
            default : ''
        },
        params2 : {
            type : Object,            
        },
        textColor : {
            type : String,
            default : ''
        }
    },
    setup() {
        return {
            
        }
    }
}
</script>PsLink