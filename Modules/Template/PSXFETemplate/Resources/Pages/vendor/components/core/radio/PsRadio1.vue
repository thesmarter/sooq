<template>
    <div class="flex flex-row justify-between">
        <ps-label class="select-none  inline-flex flex-row items-center">  
            <input type="radio" class="form-radio me-2 checked:bg-fePrimary-500 dark:bg-feAccent-500" :id="id" :checked="getValue()" @change="handleInput()" />
                <slot name="title" />
        </ps-label>
        <slot name='price' />
    </div>
    
</template>

<script lang='ts'>
import PsLabel from "../label/PsLabel.vue";

export default {
    name: "PsRadio1",
    components: { PsLabel },
    props: {
        "value" : {
            type: Object       
        },
        "selectedValue" : {
            type: Object       
        },
        "id" : {
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