<template>
  <div class="flex items-center">
      <div v-for="star in maxStars" :class="{ 'active': star <= stars }" :key="star" @click="rate(star)" class="star me-0.5">
          <ps-icon name="starFill" :class="star <= grade ? 'text-feWarning-500 ' : 'hidden ' "/>
          <ps-icon name="starOutline" :class="star <= grade ? 'hidden ' : 'text-feWarning-500 ' "/>
      </div>
  </div>
</template>

<script lang='ts'>
import PsIcon from '@template1/vendor/components/core/icons/PsIcon.vue';

export default {
    name: 'RatingView',
    components : {
        PsIcon
    },
    props: {
        'grade': {
            type : Number,
            default : 0
        },
        'maxStars': {
            type : Number,
            default : 0
        },
        'hasCounter': {
            type : Boolean,
            default : true
        },
    },
    setup(props) {
        let stars;

        function rate(star) {
            if (star <= props.maxStars && star >= 0) {
                stars = props.grade === star ? star - 1 : star;
            }
        }

        return {
            rate,
            stars
        }
    },
    // data() {
    //     return {

    //     }
    // },

}
</script>
