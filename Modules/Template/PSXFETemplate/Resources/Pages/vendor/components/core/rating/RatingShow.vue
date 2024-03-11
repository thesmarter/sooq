<template>
    <div class="flex">
        <div v-for="star in maxStars" :key="star" class="relative star me-1">
            <ps-icon name="starFill" v-if="star <= rating && star - rating != 0.5" :class="iconColor"/>
            <ps-icon name="halfStar" v-else-if="star - rating == 0.5 || Math.round(rating % (star - 1)) == 1 &&  Math.floor(rating) == star - 1" :class="iconColor"/>
            <ps-icon name="starOutline" v-else :class="iconColor" />
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
        'rating': {
            type : Number,
            default : 0
        },
        'maxStars': {
            type : Number,
            default : 0
        },
        'iconColor': {
            type: String,
            default: 'text-fePrimary-500 dark:text-feAccent-500'
        },
        'ratingIcon': {
            type: String,
            default: 'star'
        }
    },
}
</script>
