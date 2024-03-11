<template>
    <ps-label class="select-none items-center">
        <input type="radio" class="border-1 form-radio me-1 checked:bg-fePrimary-500 checked:dark:bg-fePrimary-500 dark:bg-feSecondary-800" :id="title" :checked="getValue()" @change="handleInput()" />
        {{title}}
    </ps-label>
</template>

<script lang='ts'>
import PsLabel from "../label/PsLabel.vue";

export default {
    name: "PsRadio",
    components: { PsLabel },
    props: {
        "value" : {
            type: Object
        },
        "selectedValue" : {
            type: Object
        },
        "title" : {
            type : String,
            default : "N.A"
        },
        onChange : Function
    },
    setup(props, {emit}) {
        function getValue() {
            let isSelected = false;
            if(props.selectedValue != undefined &&  props.value != undefined && props.selectedValue.id == props.value.id) {
                isSelected = true;
            }
            return isSelected;
        }
        function handleInput() {
            const tmpSelectedValue = props.selectedValue;

            Object.assign(tmpSelectedValue, props.value);

            emit('update:selectedValue', tmpSelectedValue);

            if(props.onChange != undefined){
                props.onChange(tmpSelectedValue);
            }

        }
        return {
            getValue,
            handleInput
        }
    }
}
</script>























