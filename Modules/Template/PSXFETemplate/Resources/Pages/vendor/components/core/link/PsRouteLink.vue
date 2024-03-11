<template>
    <div :class="[textColor, textSize]" @click="clickRoute">
        <slot></slot>
    </div>
</template>

<script>
import { router } from '@inertiajs/vue3';

export default {
    name : 'PsRouteLink',
    props: {
        textColor : {
            type: String,
            default: 'text-feSecondary-500 dark:text-feAchromatic-50'
        },
        textSize : {
            type : String,
            default : 'text-base'
        },
        // name : {
        //     type : String,
        //     default : 'dashboard'
        // },
        to : {
            type : Object,
            default : {name : 'dashboard'}
        }
    },
    setup(props) {
        function clickRoute(){
            if(props.to.params){
                router.get(route(props.to.name),props.to.params);
            }else if(props.to.query){
                router.get(route(props.to.name),props.to.query);
            }else{
                router.get(route(props.to.name));
            }

        }
        return{
            clickRoute
        }
    }
}
</script>
