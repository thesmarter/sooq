<template>
    <div class="bg-fePrimary-50 dark:bg-feAchromatic-800 p-3 lg:rounded-2xl rounded-xl h-auto ms-4">
        <div class="flex">
            <ps-dropdown align="left" class=' w-full' >
                <template #select>
                   <button
                        type="button"
                        class="shadow-sm inline-flex items-center content-start
                        text-sm leading-5 font-medium text-feSecondary-500 dark:text-feAchromatic-50
                                 focus:outline-none focus:border-fePrimary-300 
                                focus:shadow-outline-blue active:bg-feAchromatic-100 active:text-feSecondary-600 transition 
                                ease-in-out duration-150 btn-focus"
                        id="options-menu"
                        aria-haspopup="true"
                        aria-expanded="true">
                        <ps-label class="text-center text-xs lg:text-sm font-medium"> {{ hour }}</ps-label> 
                    </button>
                </template>
                <template #list >
                    <div class="rounded-md shadow-xs w-14" >
                        <div class="pt-1 z-30">
                            <div v-for="i in 13" :value="i-1" :key="i-1" class="cursor-pointer text-center text-feSecondary-500 dark:text-feAchromatic-50" @click="handleHourInput(('00'+(i-1)).slice(-2))" > 
                                <ps-label> {{('00'+(i-1)).slice(-2)}} </ps-label>
                            </div>      
                        </div>
                                
                    </div>
                </template>
            </ps-dropdown>

            <ps-label class="ms-1">h</ps-label>
            <ps-label  class=" ms-2 me-2">:</ps-label >
            <ps-dropdown align="left" class=' w-full' >
                <template #select>
                   <button
                        type="button"
                        class="shadow-sm inline-flex items-center content-start
                        text-sm leading-5 font-medium text-feSecondary-500 dark:text-feAchromatic-50
                                 focus:outline-none focus:border-fePrimary-300 
                                focus:shadow-outline-blue active:bg-feAchromatic-100 active:text-feSecondary-600 transition 
                                ease-in-out duration-150 btn-focus"
                        id="options-menu"
                        aria-haspopup="true"
                        aria-expanded="true">
                        <ps-label class="text-center text-xs lg:text-sm font-medium"> {{ min }}</ps-label> 
                    </button>
                </template>
                <template #list >
                    <div class="rounded-md shadow-xs w-14" >
                        <div class="pt-1 z-30">
                            <div v-for="i in 60" :value="i-1" :key="i-1" class="cursor-pointer text-center text-feSecondary-500 dark:text-feAchromatic-50" @click="handleMinuteInput(('00'+(i-1)).slice(-2))" > 
                                <ps-label> {{('00'+(i-1)).slice(-2)}} </ps-label>
                            </div>      
                        </div>
                                
                    </div>
                </template>
            </ps-dropdown>
            <ps-label class=" ms-2 me-2">m</ps-label>    
            <ps-label  class=" ms-2 me-2">:</ps-label >
            <ps-dropdown align="left" h='16' class=' w-full' >
                <template #select>
                   <button
                        type="button"
                        class="shadow-sm inline-flex items-center content-start
                        text-sm leading-5 font-medium text-feSecondary-500 dark:text-feAchromatic-50
                                 focus:outline-none focus:border-fePrimary-300 
                                focus:shadow-outline-blue active:bg-feAchromatic-100 active:text-feSecondary-600transition 
                                ease-in-out duration-150 btn-focus"
                        id="options-menu"
                        aria-haspopup="true"
                        aria-expanded="true">
                        <ps-label class="text-center text-xs lg:text-sm font-medium"> {{ ampm == '1'? 'AM' : 'PM' }}</ps-label> 
                    </button>
                </template>
                <template #list >
                    <div class="rounded-md shadow-xs w-14" >
                        <div class="pt-1 z-30">
                            <div class="cursor-pointer text-center text-feSecondary-500 dark:text-feAchromatic-50" @click="handleAmPmInput('1')" > 
                                <ps-label> AM </ps-label>
                            </div>
                            <div class="cursor-pointer text-center text-feSecondary-500 dark:text-feAchromatic-50" @click="handleAmPmInput('2')" > 
                                <ps-label> PM </ps-label>
                            </div>      
                        </div>
                                
                    </div>
                </template>
            </ps-dropdown>

                                     
        </div>
    </div>
</template>

<script lang='ts'>
import PsLabel from '@template1/vendor/components/core/label/PsLabel.vue';
import PsDropdown from '@template1/vendor/components/core/dropdown/PsDropdown.vue';

export default {
    name:"PsTimePicker",
    props : {
        hour :{
            type: String,
            default: "0"
        },
        min :{
            type: String,
            default: "0"
        },
        ampm :{
            type: String,
            default: "1"
        },
        isDisabled : {
            type: Boolean,
            default: false
        }
    },
    components : {
        PsLabel,
        PsDropdown
    },
    setup(_, {emit}) {
        function getHour(hour) {  
         
            if(hour == null || hour == '') {
                hour = '0';
            }
            return hour;   
        }
        function handleHourInput(v) {      
            emit('update:hour', v);
        }
        function getMinute(min) {  
           
            if(min == null || min == '') {
                min = '0';
            }
            return min;   
        }
        function handleMinuteInput(v) {      
            emit('update:min', v);
        }
        function getAmPm(ampm) {  
        
            if(ampm == null || ampm == '') {
                ampm = '1';
            }
            return ampm;   
        }
        function handleAmPmInput(v) {      
            emit('update:ampm', v);
        }

        return {
            getHour,
            handleHourInput,
            getMinute,
            handleMinuteInput,
            getAmPm,
            handleAmPmInput
        }
    },
    
}
</script>