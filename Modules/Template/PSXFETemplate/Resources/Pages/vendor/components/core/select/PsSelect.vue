<template>
    <select class="appearance-none form-select mt-1 w-60 " :value="getValue(value)" @change="handleInput($event.target.value)"  >         
        <option v-for=" selectData  in dataList" :key="selectData.id" class="focus:bg-fePrimary-500 dark:bg-feAccent-500"> {{selectData.name}} </option>        
    </select>    
</template>

<script lang='ts'>
import { PropType } from 'vue-demi';
class PsSelectDataHolder  {
    id: string = '';
    name: string = '';
}

export default {    
    name: "PsSelect",
    props: {
        dataList : {
            type: Array as PropType<Array<PsSelectDataHolder>>,
            default: () => [new PsSelectDataHolder()]            
        },
        value : {
            type: Array as PropType<Array<PsSelectDataHolder>>,
            default: () => [new PsSelectDataHolder()]            
        }
    },
    setup(props, {emit}) {
        function getValue(id) {
            let name = "";
            for(let i = 0; i<props.dataList.length; i++) {
                if(props.dataList[i].id == id) {
                    name = props.dataList[i].name;
                    break;
                }
            }
            return name;   
        }
        function handleInput(v) {      
            let id = "";
            for(let i = 0; i<props.dataList.length; i++) {
                if(props.dataList[i].name == v) {
                    id = props.dataList[i].id;
                    break;
                }
            }   
            emit('update:value', id);
        }

        return {
            getValue,
            handleInput
        }
    }
}
</script>























