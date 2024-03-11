<template>
    <div class="flex items-center">
        <!-- <button
            class="rounded-xl w-11 h-6 flex flex-row items-center" type="submit"
            :class="toggleValue ? ['justify-end ' , toggleOnTheme] : ['justify-start ' , toggleOffTheme]"
            @click="toggle">

            <span  class="w-5 h-5 rounded-full items-center justify-center flex flex-col" :class="toggleValue ? [toggleOffTheme] : [toggleOnTheme]"  >
                <ps-icon w="10" h="10" class=" flex-grow-0" :name="toggleValue ? toggleOnIcon : toggleOffIcon" />
            </span>

        </button> -->

        <button v-if="toggleValue" class="text-white" @click="toggle">
            <ps-icon name="sun" textColor="text-white" class="" w='20' h='20' viewBox="0 0 20 20"/>
        </button>
        <button v-else class="text-dark" @click="toggle">
            <ps-icon name="moon" textColor="text-white" class="" w='20' h='20' viewBox="0 0 20 20"/>
        </button>
    </div>
</template>

<script>
import { ref } from 'vue'
import PsIcon from "@/Components/Core/Icons/PsIcon.vue";

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
            default: 'bg-secondary-100 text-secondary-800 border border-secondary-100'
        },
        toggleOffTheme : {
            type : String,
            default: 'bg-secondary-800 text-secondary-100 border border-secondary-800'
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
            // console.log("dark " + toggleValue.value);
        }
        return{
            toggle,
            toggleValue
        }
    },
}
</script>
