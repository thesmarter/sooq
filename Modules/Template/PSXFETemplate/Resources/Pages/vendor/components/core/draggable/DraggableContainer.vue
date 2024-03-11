<template>
  <!-- <draggable class="flex flex-wrap" :list="list" @change="log"> -->
  <slot></slot>
  <!--<div
    class="list-styles"
    v-for="element in list"
    :key="element.name"
  >
    {{ element.name }}
  </div>-->
<!-- </draggable> -->
</template>

<script lang="ts">
import { defineComponent } from 'vue'
// import { VueDraggableNext } from 'vue-draggable-next'

export default defineComponent({
  components: {
    // draggable: VueDraggableNext,
  },
  props : {
    list : [] as any ,
},
  data() {
   return {
      enabled: true,
      dragging: false,
    }
  },
  methods: {
    log(event) {
      console.log(event)
    },
  },
})
</script>

<style scoped></style>