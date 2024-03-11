
<style scoped>
.vue-neat-modal {
    max-height: 95%;
   
}
</style> 


<template>
    <Modal
        v-model="isOpen"
        :fullscreen="isFullscreen"
        :modal-transition="modalTransition"
        :click-out="isClickOut"
        :disable-motion="isMotionDisabled"
        :max-width="maxWidth"
        :remove-backdrop="isBackdropRemoved"
        class="mx-auto"      
    >            
        <div :class="isDarkMode ? 'dark' : ''" class=" w-screem" :dir="getDir()" >
            <slot name="body"  />
        </div>                
    </Modal>
</template>

<script lang='ts'>
import { Modal } from "vue-neat-modal";
import 'vue-neat-modal/dist/vue-neat-modal.css'
import { ref, computed } from 'vue';
import { useStore } from 'vuex'

export default {
    name : 'PsImageModal',
    components : {
        Modal,
    },
    props : {       
        maxWidth : {
            type : String,
            default : "100%"
        },
        isBackdropRemoved : {
            type : Boolean,
            default : false
        },
        isMotionDisabled : {
            type : Boolean,
            default : false
        }, 
        isClickOut : {
            type : Boolean,
            default : true
        }, 
        modalTransition : {
            type : String,
            default : "scale"
        }, 
        isFullscreen : {
            type : Boolean,
            default : false
        }
    },
    setup() {
        const isOpen = ref(false);

        // Modal
        const store = useStore();

        // for dark or light mode local storage
        store.dispatch('loadIsDarkMode');

        const isDarkMode = computed(() => store.getters.isDarkMode);
        
        function toggle(status) {
            isOpen.value = status;
        }

        function getDir(){
            if(localStorage.activeLanguage != null && localStorage.activeLanguage != undefined && localStorage.activeLanguage == 'ar'){
                return 'rtl';
            }else{
                return 'ltr';
            }
        }
       
        return {
            isDarkMode,
            isOpen,
            toggle,
            getDir
        }
    }
}
</script>