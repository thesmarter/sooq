<template>
  <div>
    <Toggle v-model="toggleValue" @change="changeFunction"  :on-label="onLabel" :off-label="offLabel" :disabled="disabled"
    :classes="{
        container: 'inline-block rounded-full outline-none ',
        toggle: disabled ? 'flex w-12 h-5 rounded-full relative cursor-not-allowed transition items-center box-content border-2 text-xs leading-none ' : 'flex w-12 h-5 rounded-full relative cursor-pointer transition items-center box-content border-2 text-xs leading-none ',
        toggleOn: [toggleOnTheme],
        toggleOff: [toggleOffTheme],
        toggleOnDisabled: 'bg-feSecondary-300 border border-feSecondary-300 justify-start text-feAchromatic-50',
        toggleOffDisabled: 'bg-feSecondary-300 border border-feSecondary-300 justify-end text-feAchromatic-50',
        handle: 'inline-block bg-feAchromatic-50 w-5 h-5 top-0 rounded-full absolute transition-all',
        handleOn: 'left-full transform -translate-x-full',
        handleOff: 'left-0',
        handleOnDisabled: 'bg-text-7 left-full transform -translate-x-full',
        handleOffDisabled: 'bg-text-7 left-0',
        label: 'text-center w-8 border-box whitespace-nowrap select-none',
}"
    labelledby="toggle-label" />
  </div>
</template>

<script>
import Toggle from '@vueform/toggle'
import { ref,watch } from 'vue'
import { router } from '@inertiajs/vue3'

  export default {
    props: {
      "toggleOnTheme" : {
        type: String,
        default: "bg-fePrimary-500 border border-fePrimary-500 justify-start text-feAchromatic-50"
      },
      "toggleOffTheme" : {
        type: String,
        default: "bg-feAchromatic-200 border-feAchromatic-200 justify-end text-feAchromatic-700"
      },
      "selectedValue" : {
        type: Boolean,
      },
      "disabled": {
        type: Boolean,
        default: false
      },
      "onLabel": {
        type: String,
        default: ''
      },
      "offLabel": {
        type: String,
        default: ''
      }
    },
    components: {
      Toggle,
    },
    setup(props, { emit}) {
      let toggleValue = ref(false);
      toggleValue.value = props.selectedValue;

      function changeFunction(){
        if(!props.disabled){
            emit('update:selectedValue', toggleValue.value);
        }
        
      }


      watch(
        () => props.selectedValue,
        () => {
            /* ... */
            toggleValue.value = props.selectedValue;
        }
    )

    router.on('finish', (event) => {
        toggleValue.value = props.selectedValue;
    })

      return {
        toggleValue,
        changeFunction
      }
    }
  }
</script>

<style src="@vueform/toggle/themes/default.css"></style>
