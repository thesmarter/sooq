<template>
    <div class="flex relative">
        <div class="flex cursor-pointer "  ref="ratingContainer" @mousemove="hoverMove" @click="rate" @mouseleave="hoverLeave">
            <div v-for="star in maxStars" :key="star" class="relative star me-1">
                {{ conditions(star) }}
                <div :class="`absolute overflow-hidden `" :style="`width: ${showRatingWithPrecision ? `${(activeState % 1) * 100}%` : '0%'}`">
                    <ps-icon name="starFill" :class="iconColor" w="28" h="28"/>
                </div>
                <ps-icon name="starOutline" v-if="showEmptyIcon" :class="iconColor"  w="28" h="28"/>
                <ps-icon name="starFill" v-else :class="iconColor" w="28" h="28"/>
            </div>
        </div>
    </div>
</template>

<script>
import { ref } from 'vue';
import PsIcon from '@template1/vendor/components/core/icons/PsIcon.vue';

export default {
    name: 'RatingSelected',
    components : {
        PsIcon
    },
    props: {
        'increment': {
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
        },
        'rateAble' : {
            type: Boolean,
            default: true
        }
    },
    data() {
        return {
            stars: 0
        }
    },
    setup(){
        const isHover = ref(false);
        const activeIcon = ref(-1);
        const hoverActiveIcon = ref(-1);
        const ratingContainer = ref();

        const activeState = ref();
        const isActiveRating = ref();
        const isRatingWithPrecision = ref();
        const isRatingEqualToIndex = ref();
        const showRatingWithPrecision = ref(false);
        const showEmptyIcon = ref(true);

        return {
            ratingContainer,
            isHover,
            activeIcon,
            hoverActiveIcon,
            activeState,
            isActiveRating,
            isRatingEqualToIndex,
            isRatingWithPrecision,
            showRatingWithPrecision,
            showEmptyIcon
        }
    },
    methods: {

        calculateRate(e) {
            //    if(typeof star == 'number' && star <= this.maxStars && star >= 0){
            //         this.stars = this.stars === star ? star - 1 : star;
            //    }

            // get width and left value of a star container
            const { width, left } = this.ratingContainer.getBoundingClientRect();

            // get percent for each star box
            let percent = (e.clientX - left) / width;
            const numberInStars = percent * this.maxStars;

            // get nearest number of number star
            const nearestNumber = Math.round((numberInStars + this.increment / 2) / this.increment) * this.increment;

            // return a double with 1 decimal place
            return Number(nearestNumber.toFixed(this.increment.toString().split('.')[1]?.length || 0));
        },
        getRating() {
            return this.stars;
        },
        hoverMove(e){
            this.isHover = true;
            this.hoverActiveIcon = this.calculateRate(e);
        },
        hoverLeave(e){
            this.isHover = false;
            this.hoverActiveIcon = -1;
        },
        rate(e){
            this.isHover = false;
            this.activeIcon = this.calculateRate(e);
            this.stars = this.activeIcon;
        },
        conditions(index){
            this.activeState = this.isHover ? this.hoverActiveIcon : this.activeIcon;

            this.showEmptyIcon = this.activeState === -1 || this.activeState < index;
            this.isActiveRating = this.activeState !== 1;
            this.isRatingWithPrecision = this.activeState % 1 !== 0;
            this.isRatingEqualToIndex = Math.ceil(this.activeState) === index;
            this.showRatingWithPrecision =
            this.isActiveRating && this.isRatingWithPrecision && this.isRatingEqualToIndex;
        },
    },
}
</script>
