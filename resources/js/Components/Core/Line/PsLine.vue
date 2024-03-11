<template>

    <div class="h-0.5"  :class="theme"></div>

</template>

<script>
export default {
    name : 'PsLine',
    props: {
        theme : {
            type: String,
            default: 'bg-secondary-700 dark:bg-white',
        }
    }
}
</script>
