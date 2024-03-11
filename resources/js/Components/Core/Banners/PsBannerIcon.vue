<template>
    <div :class="[theme]" v-show="this.visible && openBannerBox">
        <div class="max-w-7xl mx-auto py-3 px-3 sm:px-6 lg:px-8 flex items-center justify-center">
            <ps-icon-1  :name="iconName" w="20" h="20" class="me-2" :theme="iconColor"  />
            <div class=" sm:text-xs text-md">
                <slot />
            </div>
        </div>
    </div>
</template>
<script>
import { ref } from "vue";
import PsIcon1 from "@/Components/Core/Icons/PsIcon1.vue";

export default {
    name: "PsBannerIcon",
    components: {
        PsIcon1
    },
    props: {
        theme: {
            type: String,
            default: "bg-indigo-500"
        },
        iconName: {
            type: String,
            default: "badge"
        },
        iconColor: {
            type: String,
            default: "#4B5563"
        },
        duration: {
            type: Number,
            default: 3000
        },
        visible: {
            type: Boolean,
        },
        uicomponent: {
            type: Boolean,
        }
    },
    data() {
        const openBannerBox = ref(this.visible);
        if(!this.uicomponent){
            if(this.visible){
                setTimeout(() => {
                    openBannerBox.value = false;
                }, this.duration);
                this.$emit('update:visible', false)
            }
        } else {
            openBannerBox.value = true;
        }
        
        return {
            openBannerBox
        };
    }
}
</script>
