<template>
    <div class="flex items-center">
        <button
            class="rounded-xl w-12 h-6 flex flex-row items-center" type="submit"
            :class="toggleValue ? ['justify-end ' , toggleOnTheme] : ['justify-start ' , toggleOffTheme]"
            @click="toggle">

            <span  class="w-4 h-4 rounded-full items-center justify-center flex flex-col bg-feAchromatic-400 mx-1" >
                <ps-icon w="10" h="10" class="text-feAchromatic-50 flex-grow-0" :name="toggleValue ? toggleOnIcon : toggleOffIcon" />
            </span>

        </button>

        <!-- <button :class="toggleValue ? 'text-dark':'text-feAchromatic-50'" @click="toggle">
            <ps-icon name="moon" textColor="text-feAchromatic-50" class="" w='20' h='20' viewBox="0 0 20 20"/>
        </button> -->
    </div>
</template>

<script>
import { ref } from 'vue'
import PsIcon from "../icons/PsIcon.vue";

export default {
    name: "PsIconToggle",
    components: {
        PsIcon
    },
    props: {
        selectedValue : {
            type : Boolean,
            default: false
        },
        toggleOnTheme : {
            type : String,
            default: 'bg-fePrimary-500'
        },
        toggleOffTheme : {
            type : String,
            default: 'bg-feSecondary-600'
        },
        toggleOnIcon: {
            type : String,
            default: 'night'
        },
        toggleOffIcon: {
            type : String,
            default: 'day'
        },
    },
    emit : ['onChange'],
    setup(props, context) {
        const toggleValue = ref(props.selectedValue)
        function toggle(){
            toggleValue.value = !toggleValue.value;
            context.emit('onChange')
        }
        return{
            toggle,
            toggleValue
        }
    },
}
</script>
