<template>
    <div class="relative inline-block text-left text-feSecondary-500 " ref="dropdown" >
        <div @click="clicked"  >
            <slot name="select"   />
        </div>
            <transition
                enter-active-class="transition ease-out duration-100"
                enter-class="transform opacity-0 scale-95"
                enter-to-class="transform opacity-100 scale-100"
                leave-active-class="transition ease-in duration-75"
                leave-class="transform opacity-100 scale-100"
                leave-to-class="transform opacity-0 scale-95">
                <div v-if="isMenuOpen"

                    class=" text-sm overflow-hidden z-20 bg-feAchromatic-50 dark:bg-feAchromatic-900"
                    :class="[rounded,shadow, horizontalAlign == 'right' ? 'ltr:origin-top-right ltr:right-0 rtl:origin-top-left rtl:left-0' : horizontalAlign == 'left' ? 'ltr:origin-top-left ltr:left-0 rtl:origin-top-right rtl:right-0' : 'ltr:origin-top-right ltr:right-0 rtl:origin-top-right rtl:right-0',
                    isPopup ? 'bottom-12' : 'mt-2',isFixed ? `fixed ${boxPositioning}` :`absolute ${boxPositioning}`]">
                        <div class="flex flex-col">
                            <div class="overflow-hidden">
                                <slot name="filter" />
                            </div>
                            <div :class="['overflow-y-auto overflow-x-hidden', h]">
                                <div class="relative ">
                                    <div @click="isMenuOpen = !isMenuOpen" >
                                        <slot name="list"  />
                                        
                                    </div>
                                    <slot name="loadmore" />
                                </div>
                            </div>
                           

                        </div>
                </div>
                <!--  -->
            </transition>

    </div>
</template>

<script lang="ts">
import { ref, onMounted, onUnmounted } from 'vue';
import { PsValueStore } from '@templateCore/store/modules/core/PsValueStore';

export default {
    name:"PsDropdown",
    props: {
        horizontalAlign : {
            type : String,
            default : 'left'
        },
        h: {
            type : String,
            default : ' h-36'
        },
        rounded: {
            type : String,
            default: 'rounded'
        },
        isPopup: {
            type: Boolean,
            default: false
        },
        shadow:{
            type: String,
            default:"shadow-lg"
        },
        isFixed:{
            type:Boolean,
            default: false
        },
        boxPositioning:{
            type:String,
            default: ''
        }
    },
    emits : [
        'on-click'
    ],
    setup(_, { emit }) {

        const psValueStore = PsValueStore();

        const isMenuOpen = ref(false);
        function hide() {
            if(isMenuOpen.value) {
                isMenuOpen.value = !isMenuOpen.value;
            }
        }

        const dropdown = ref();
        function close(e) {
            if(!dropdown.value.contains(e.target)) {
                hide();
            }
        }

        onMounted(() => {
         document.addEventListener('click', close)
        });

        onUnmounted(() => {
            document.removeEventListener('click', close)
        });

        function clicked() {

            isMenuOpen.value = !isMenuOpen.value;
            emit('on-click');

        }

        return {
            isMenuOpen,
            dropdown,
            hide,
            clicked,
            psValueStore
        }
    }
}
</script>
