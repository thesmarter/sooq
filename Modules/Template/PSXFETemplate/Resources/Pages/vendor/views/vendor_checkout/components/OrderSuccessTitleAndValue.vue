<template>
    <div class="flex flex-row mb-4">
      <ps-label class="w-1/2" textColor="text-feAchromatic-600 dark:bg-feAchromatic-200"> {{ title }} </ps-label>
      <ps-label class="w-1/2" textColor="text-feAchromatic-800 dark:bg-feAchromatic-50"> {{ value }} </ps-label>
    </div>
</template>

<script>

import PsLabel from '@template1/vendor/components/core/label/PsLabel.vue';

export default ({
    name : "OrderSuccessTitleAndValue",
    components : {
        PsLabel
    },
    props : {
        title : String,
        value : String
    }
})
</script>
