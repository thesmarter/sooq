<template>
    <ps-label class="select-none inline-flex items-center">
        <input type="checkbox"
        class="form-checkbox me-2 text-fePrimary-500 dark:text-fePrimary-500 focus:ring-2 focus:ring-fePrimary-300 hover:bg-fePrimary-500 dark:hover:bg-fePrimary-500 checked:bg-fePrimary-500 dark:checked:bg-fePrimary-500"
        :id="title" :checked="value"
        @input="handleInput($event.target.checked)" />
        {{title}}
    </ps-label>
</template>

<script lang='ts'>
import PsLabel from "../label/PsLabel.vue";

export default {
    name: "PsCheckboxValue",
    components: { PsLabel },
    props: {
        "value" : Boolean,
        "title" : {
            type : String,
            default : "N.A"
        }
    },
    setup(_, { emit }) {

        function handleInput(v) {
            emit('update:value', v);
        }

        return {
            handleInput
        }
    }
}
</script>
