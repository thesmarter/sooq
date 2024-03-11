<template>
    <div
        class="bg-feAchromatic-50 dark:bg-feAchromatic-900 w-full lg:w-large xl:w-feLarge mx-auto flex flex-col sm:flex-row justify-center items-center gap-1 sm:gap-2 sm:px-4 md:px-6 lg:gap-16">
        <div class="sm:w-[450px] mt-7 px-4 sm:px-0">
            <ps-label-header-5 class="font-semibold">{{ $t("home__fe_footer__header") }}</ps-label-header-5>
            <ps-label class="font-normal text-base mt-2" textColor="">{{ $t("home__fe_footer__desc") }}</ps-label>
        </div>
        <div class="h-56 w-80 relative">
            <div class="absolute bottom-0"
                v-if="appBrandingImagePath != ''">
                <img v-lazy="{ src: $page.props.uploadUrl + '/' + appBrandingImagePath }"
                    alt="" class="w-80 h-48 object-contain object-bottom">
            </div>
        </div>

    </div>
</template>

<script>

// Components
import PsLabel from '@template1/vendor/components/core/label/PsLabel.vue';
import PsLabelHeader5 from '@template1/vendor/components/core/label/PsLabelHeader5.vue';

export default {
    name: 'DashboardMobileShowcaseCard',
    components: {
        PsLabel,
        PsLabelHeader5,
    },
    props: {
        appBrandingImagePath : {
            type : String,
            default : ''
        },
    },
}

</script>
