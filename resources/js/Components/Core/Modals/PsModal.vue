<style scoped>
    .vue-neat-modal {
        max-height: 80%;
    }
</style>

<template>


    <Modal
        v-model="isOpen"
        :fullscreen="isFullscreen"
        :modal-transition="modalTransition"
        :click-out="isClickOut"
        :disable-motion="isMotionDisabled"
        teleportTarget="#mainbody"
        :max-width="!isFullscreen ? maxWidth : undefined"
        :remove-backdrop="isBackdropRemoved"
        >
        <div :class="isDarkMode ? 'dark' : ''" :dir="getDir()">
            <div :class="theme" class="max-h-auto bg-background dark:bg-backgroundDark "  >
                <slot name="title"   />
                <!-- <ps-line class="mb-4" :class='line' /> -->
                <div class=" mb-4" :class="bodyHeight" >
                    <slot name="body"  />
                </div>
                <slot name="footer" />

            </div>
        </div>
    </Modal>

</template>
<script>
import { Modal } from "vue-neat-modal";
import 'vue-neat-modal/dist/vue-neat-modal.css';
import { ref, computed } from 'vue';
import PsLine from '../Line/PsLine.vue';
import { useStore } from 'vuex'

export default {
    name:"PsModal",
    components : {
        Modal,
        PsLine
    },
    props : {
        maxWidth : {
            type : String,
            default : "500px"
        },
        bodyHeight : {
            type : String,
            default : "max-h-80"
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
        },
        theme : {
            type : String,
            default : " p-6 border lg:rounded-2xl rounded-xl  "
        },
        line : {
            type : String,
            default : "mt-4 "
        }
    },
    setup() {
        // Modal
        const store = useStore();

        // for dark or light mode local storage
        store.dispatch('loadIsDarkMode');

        const isDarkMode = computed(() => store.getters.isDarkMode);

        const isOpen = ref(
            
        );

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
            isOpen,
            toggle,
            store,
            isDarkMode,
            getDir
        }
    }
}
</script>
