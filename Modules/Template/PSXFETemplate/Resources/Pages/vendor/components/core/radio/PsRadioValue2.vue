<template>
  <div class="me-4">
      
    <input
      type="radio"
      class="w-4 h-4 focus:ring-1 bg-feAchromatic-100 dark:bg-feAchromatic-700 dark:border-feAchromatic-600
      "
      :disabled="disabled"
      :class="[disabled ? 'text-fePrimary-400 border-feAchromatic-400 focus:ring-fePrimary-400' : color]"
     
      :id="title"
      :checked="title === value"
      @change="handleInput(title)"
    />
    <ps-label :for="title" class="ms-2 select-none inline-flex items-center"   :class="[disabled ? 'text-opacity-50': '']">
      {{ title_label == "" ? title : title_label }}
    </ps-label>
  </div>
</template>

<script>
import PsLabel from "../label/PsLabel.vue";

export default {
  name: "PsRadioValue2",
  components: { PsLabel },
  props: {
    value: String,
    title: {
      type: String,
      default: "",
    },
    title_label:{
        type:String,
        default:"",
    },
    color: {
      type: String,
      default: "text-fePrimary-500 border-feAchromatic-300 focus:ring-fePrimary-500",
    },
    disabled:{
        type:Boolean,
        default:false
    }
  },
  setup(_, { emit }) {
    function handleInput(v) {
      emit("update:value", v);
    }

    return {
      handleInput,
    };
  },
};
</script>
