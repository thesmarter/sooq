<template>
    <div class="bg-feAchromatic-800 py-9 mt-10">
            <div class="w-full lg:w-large xl:w-feLarge mx-auto">
                <ps-label-header-3 class="text-center mt-7" textColor="text-feAchromatic-50">{{
                    $t("home__how_it_works") }}</ps-label-header-3>
                <div
                    class="flex justify-between flex-col sm:flex-row sm:flex-wrap md:flex-nowrap gap-y-4 sm:gap-x-0 sm:gap-y-8 md:gap-0 lg:gap-4 mt-4 sm:mt-10 mx-2 sm:mx-4 lg:mx-0">
                    <div class="w-full sm:w-84 md:w-64 lg:w-full rounded-t-3xl rounded-b-xl bg-feAchromatic-700 px-4 py-5">
                        <div class="h-56 w-60 md:w-44 md:h-48 mx-auto">
                            <img v-lazy="{ src: $page.props.sysImageUrl + '/hand_phone.png' }" alt=""
                                class="w-full h-full object-cover bottom-0">
                        </div>
                        <div class="flex flex-col items-center">
                            <ps-label-header-6 textColor="text-feSecondary-50" class="my-2">{{ $t("home__search_or_list")
                            }}</ps-label-header-6>
                            <ps-label class="text-center" textColor="text-feSecondary-300">{{
                                $t("home__search_or_list__desc") }}</ps-label>
                        </div>
                    </div>
                    <div class="w-full sm:w-84 md:w-64 lg:w-full rounded-t-3xl rounded-b-xl bg-feAchromatic-700 px-4 py-5">
                        <div class="h-56 w-60 md:w-44 md:h-48 mx-auto">
                            <img v-lazy="{ src: $page.props.sysImageUrl + '/connect.png' }" alt=""
                                class="w-full h-full object-contain bottom-0">
                        </div>
                        <div class="flex flex-col items-center">
                            <ps-label-header-6 textColor="text-feSecondary-50" class="my-2">{{ $t("home__connect")
                            }}</ps-label-header-6>
                            <ps-label class="text-center" textColor="text-feSecondary-300">{{ $t("home__connect__desc")
                            }}</ps-label>
                        </div>
                    </div>
                    <div class="w-full sm:w-full md:w-64 lg:w-full rounded-t-3xl rounded-b-xl bg-feAchromatic-700 px-4 py-5 ">
                        <div class="h-56 w-60 md:w-44 md:h-48 mx-auto">
                            <img v-lazy="{ src: $page.props.sysImageUrl + '/transation.png' }" alt=""
                                class="w-full h-full object-cover bottom-0">
                        </div>
                        <div class="flex flex-col items-center">
                            <ps-label-header-6 textColor="text-feSecondary-50" class="my-2">{{
                                $t("home__fe_complete_transaction") }}</ps-label-header-6>
                            <ps-label class="text-center" textColor="text-feSecondary-300">{{
                                $t("home__fe_complete_transaction__desc") }}</ps-label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
</template>

<script>
// Components
import PsLabel from '@template1/vendor/components/core/label/PsLabel.vue';
import PsLabelHeader3 from '@template1/vendor/components/core/label/PsLabelHeader3.vue';
import PsLabelHeader6 from '@template1/vendor/components/core/label/PsLabelHeader6.vue';

export default {
    name: 'DashboardHowItWorksCard',
    components: {
        PsLabel,
        PsLabelHeader3,
        PsLabelHeader6
    },
}

</script>
