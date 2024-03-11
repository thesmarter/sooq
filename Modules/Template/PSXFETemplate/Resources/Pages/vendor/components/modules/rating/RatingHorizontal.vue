<template>
    <div class=" w-full md:w-auto cursor-pointer">
        <!-- Pscard -->
        <div class="flex flex-col h-[196px] rounded-md border dark:border-feAchromatic-700" :enabledHover="true">
            <div class="px-4 mt-4">

                <div class="flex justify-between">
                    <rating-view :iconColor="'text-feWarning-500 dark:text-feWarning-accent'" :size="'md'" :rating="rate ? rate.rating:0" :maxStars="5" :hasCounter="true" />
                    <ps-label-title-3 class="lg:text-xs sm:text-xxs text-3xs"> {{rate ? moment(rate.addedDate).format($page.props.dateFormat):''}} </ps-label-title-3>
                </div>
                <ps-label-header-5 class="mt-2 mb-2 dark:text-feAchromatic-200"> {{rate ? rate.title:''}} </ps-label-header-5>
                <!-- <ps-label class="mt-2 mb-4"> {{rate ? rate.description:''}} </ps-label> -->
                <div class="h-[94px] overflow-y-auto mb-2">
                    <ps-label class="mt-2 mb-4"> {{rate ? rate.description:''}}  </ps-label>
                </div>
                <!-- <ps-icon name="editPencil" class="text-feAchromatic-900 me-2"></ps-icon> -->
            </div>
        </div>
        <!-- end Pscard -->
    </div>
</template>

<script lang="ts">

import PsLabel from '@template1/vendor/components/core/label/PsLabel.vue';
import PsLabelTitle3 from '@template1/vendor/components/core/label/PsLabelTitle3.vue';
import PsLabelHeader5 from '@template1/vendor/components/core/label/PsLabelHeader5.vue';
import PsIcon from '../../core/icons/PsIcon.vue';
import PsCard from '@template1/vendor/components/core/card/PsCard.vue';
import RatingView from '@template1/vendor/components/core/rating/RatingShow.vue';

import Rating from '@templateCore/object/Rating';
import moment from "moment";

export default {
    name : "RatingHorizontal",
    components : {
        PsLabel,
        PsCard,
        PsLabelHeader5,
        PsIcon,
        PsLabelTitle3,
        RatingView
    },
    props : {
        rate : { type : Rating } ,
        onClick : Function
    },
    setup() {
        // Inject Provider
        return {
            moment
        }
    }
}
</script>
