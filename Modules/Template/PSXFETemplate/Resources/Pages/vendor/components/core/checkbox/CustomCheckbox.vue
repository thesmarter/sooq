<template>
  <label class="checkbox-label">
    <input type="checkbox" class="w-6 h-6 text-fePrimary-500 bg-feAchromatic-50 border-feSecondary-300 rounded focus:ring-fePrimary-500 focus:ring-2 dark:bg-feSecondary-800 dark:checked:bg-fePrimary-500 dark:border-feSecondary-300" :checked="getValue()" @input="handleInput($event.target.checked)" />
    <!-- <span class="circle-checkbox"></span> -->
  </label>
</template>
<script>
export default {
  name: 'CustomCheckbox',
  props: {
    "value" : {
        type : String,
        default : "N.A"
    },
    isScribe : {
        type : String,
        default : "N.A"
    },
    selectedValue : {
        type: Array,
        default: () => []
    }
  },
  emit: ['updateSelectedValue'],
  setup(props, context) {

    function getValue() {
        let isSelected = false;

        if(props.isScribe == '1') {
          isSelected = true;
        }

        return isSelected;
    }

    function handleInput(v) {
        const tmpSelectedValue = props.selectedValue;
        let isFoundKey = false;
        for(let i = 0; i<tmpSelectedValue.length ; i++) {
            if(tmpSelectedValue[i] == props.value) {
                isFoundKey = true;
                if(!v) {
                    tmpSelectedValue.splice(i, 1);
                }
                break;
            }
        }

        if(!isFoundKey) {
            tmpSelectedValue.push(props.value);
        }

        context.emit('updateSelectedValue', tmpSelectedValue);
    }
    return {
        handleInput,
        getValue
    }
  }
};
</script>
<style lang="scss">
:root {
  .checkbox-label {
    position: relative;
  }
  .circle-checkbox {
    width: 16px;
    height: 16px;
    border: 1px solid #ccc;
    display: inline-block;
    border-radius: 50%;
    transition: all linear 0.3s;
    &:after {
      content: "";
      position: absolute;
      top: -1px;
      left: 6px;
      border-bottom: 2px solid #fff;
      border-right: 2px solid #fff;
      height: 9px;
      width: 4px;
      transform: rotate(45deg);
      visibility: hidden;
    }
  }
  .checkbox-input {
    display: none;
    &:checked ~ span {
      background: #A92428;
      &:after {
        visibility: visible;
      }
    }
  }
}
</style>
