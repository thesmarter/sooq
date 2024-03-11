<template>
    <div class="flex items-center">
        <div @click="rate(star)" v-for="star in maxStars" :class="{ 'active': star <= stars }" :key="star" class="star me-1">
            <ps-icon name="starFill" v-if="star <= stars" :class="colors" w="28" h="28"/>
            <ps-icon name="starOutline" v-else :class="colors"  w="28" h="28"/>
        </div>
    </div>
</template>

<script>
import PsIcon from '@template1/vendor/components/core/icons/PsIcon.vue';

export default {
    name: 'RatingSelected',
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
        'colors': {
            type: String,
            default : 'text-fePrimary-500 dark:text-feAccent-500'
        }
    },
    data() {
        return {
            stars: this.grade
        }
    },
    methods: {
        rate(star) {
            if (typeof star === 'number' && star <= this.maxStars && star >= 0) {
                this.stars = this.stars === star ? star - 1 : star;
            }
        },
        getRating() {
            return this.stars;
        }
    },
}
</script>
