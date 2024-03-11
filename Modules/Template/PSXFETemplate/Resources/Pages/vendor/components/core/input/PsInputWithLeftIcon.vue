<template>
    <div class="relative">
  
        <div
        class="absolute inset-y-0 flex items-center ltr:me-2 ltr:ms-3.5 ltr:left-0 rtl:ms-2 rtl:me-3.5 rtl:right-0"
        :class="theme"
      >
        <slot name="icon"></slot>
      </div>
        <input
        class="bg-transparent w-full text-sm ps-11"
      
        :class="disabled ? [rounded, disabledTheme, padding,] : [theme, rounded, defaultBorder, placeholderColor, padding,]"
        :disabled="disabled"
        :type="type"
        :value="value"
        :maxlength="maxlength"
        @input="handleInput($event.target.value)"
        :placeholder="placeholder" 
        
      />
      
       <div
        class="absolute inset-y-0 flex items-center ltr:me-4 ltr:ms-2 ltr:right-0 rtl:ms-4 rtl:me-2.5 rtl:left-0" @click.stop="clearInput()"
        :class="theme"
    >
        <slot name="removeIcon"></slot>
    </div>
 
    </div>
  </template>
  
  <script>
  export default {
    name: "PsInputWithLeftIcon",
    props: {
      value: { type: String, default: "" },
      type: { type: String, default: "text" },
      theme: { type: String, default: "text-feSecondary-500 dark:text-feSecondary-400" },
      rounded: { type: String, default: "rounded-lg" },
      maxlength: { type: Number, default: 99999999 },
      placeholder: { type: String, default: "" },
      placeholderColor: { type: String, default: "placeholder-feSecondary-500 dark:placeholder-feSecondary-400" },
      onInput: Function,
      disabled: { type: Boolean, default: false },
      padding: { type: String, default: "py-2.5 px-4" },
      disabledTheme: {
        type: String,
        default:
          " text-feSecondary-300 border-feSecondary-300 dark:border-feSecondary-800 dark:text-feSecondary-700 shadow-none placeholder-feSecondary-300 dark:placeholder-feSecondary-600",
      },
      defaultBorder: {
        type: String,
        default:
          "border  border-feSecondary-200 hover:border-feSecondary-400 dark:border-feSecondary-400 hover:dark:border-feSecondary-50 focus:outline-none focus:border-none focus:ring-2 focus:ring-fePrimary-300  placeholder-feSecondary-500 dark:placeholder-feSecondary-400",
      },
    },
    setup(props, { emit }) {
      function handleInput(value) {
        emit("update:value", value);
      }
  
      function clearInput() {
        emit("update:value", ""); 
      }
  
      return {
        handleInput,
        clearInput,
      };
    },
  };
  </script>
  