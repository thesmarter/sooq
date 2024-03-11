<template>
    <div class="flex mb-4">
      <!-- Dynamically generate radio buttons based on the options array -->
      <div v-for="(option, index) in options" :key="index" class="mr-8">
        <input 
          type="radio"
          :id="option.toLowerCase()"
          v-model="selectedOption"
          :value="option.toLowerCase()"
          class="mr-2 checked:bg-fePrimary-500 checked:dark:bg-fePrimary-500 dark:bg-feSecondary-800"
          @change="handleChange"
        />
        <label :for="option.toLowerCase()" class="text-sm">
          {{ option }}
        </label>
      </div>
    </div>
  </template>
  
  <script>
  export default {
    props: {
      options: {
        type: Array,
        required: true,
      },
      selectedOption: {
        type: String,
        required: true,
      },
    },
    data() {
        return {
        selectedOption: this.options[0].toLowerCase(), // Set the default selected option
        };
    },
    methods: {
      handleChange() {
        // Emit the change event with the selected option
        this.$emit('change', this.selectedOption);
      },
    },
  };
  </script>