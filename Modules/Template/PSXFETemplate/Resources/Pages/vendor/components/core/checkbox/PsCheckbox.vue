<template>
    <ps-label class="select-none inline-flex items-center">  
        <input type="checkbox" 
        class="form-checkbox me-2 checked:bg-fePrimary-500 dark:bg-feAccent-500" 
        :id="title" 
        :checked="getValue()" @input="handleInput($event.target.checked)" />
        {{title}}
    </ps-label>
</template>

<script lang='ts'>
import { PropType } from 'vue-demi';
import PsLabel from "../label/PsLabel.vue";

class PsCheckboxDataHolder  {
    id: string = '';
    name: string = '';
}
export default {
    name: "PsCheckbox",
    components: { PsLabel },
    props: {
        "value" : {
            type: PsCheckboxDataHolder,
            default : new PsCheckboxDataHolder()
        },
        selectedValue : {
            type: Array as PropType<Array<PsCheckboxDataHolder>>,
            default: () => [new PsCheckboxDataHolder()]
        },
        "title" : {
            type : String,
            default : "N.A"
        }
    },
    setup(props, { emit }) {
        function getValue() {
            let isSelected = false;
            if(props.selectedValue != null) {
                for(let i = 0; i < props.selectedValue.length; i++) {
                    if(props.selectedValue[i].id == props.value.id) {
                        isSelected = true; 
                        break;
                    }
                }
            }
            return isSelected;   
        }
        function handleInput(v) {   
            const tmpSelectedValue = props.selectedValue;
            let isFoundKey = false;
            for(let i = 0; i<tmpSelectedValue.length ; i++) {
                if(tmpSelectedValue[i].name == props.value.name) {
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
            
            emit('update:selectedValue', tmpSelectedValue);
        }

        return {
            getValue,
            handleInput
        }
    }
}
</script>























