<template>
    <div class="bg-feAchromatic-100 dark:bg-feAchromatic-700 px-5 py-3">
        <ps-label class="font-bold text-2xl"
            textColor="text-feSecondary-800 dark:text-feSecondary-300"> {{ title }} </ps-label>
    </div>
</template>

<script>

import PsLabel from '@template1/vendor/components/core/label/PsLabel.vue';

 export default{
    
    name : "VendorCheckoutGroupTitle",
    components : {
        PsLabel
    },
    props : {
        title : String
    }
 }
</script>