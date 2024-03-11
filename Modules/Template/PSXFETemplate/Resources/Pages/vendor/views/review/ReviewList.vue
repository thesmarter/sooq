<template>
    <Head :title="$t('review_list__review_list')" />
    <ps-content-container>
        <template #content>
            <div class="mt-32 mb-10">

                <!-- breadcrumb starts-->
                <div class=" mb-4 flex items-center mt-8">
                    <ps-route-link :to="{ name: 'fe_profile' }">
                        <ps-label class=" cursor-pointer text-xs lg:text-base">
                            {{ $t('profile_label') }}
                        </ps-label>
                    </ps-route-link>

                    <span class="ms-2 me-2" v-if="breadcrumbuserProvider.user?.data?.userId != loginUserId">/</span>

                    <ps-route-link :to="{
                        name: 'fe_other_profile',
                        query: { userId: breadcrumbuserProvider.user?.data?.userId }
                    }" v-if="breadcrumbuserProvider.user?.data?.userId != loginUserId">
                        <ps-label class=" cursor-pointer text-xs lg:text-base">
                            {{ breadcrumbuserProvider.user.data ? breadcrumbuserProvider.user.data.userName : '' }}
                        </ps-label>
                    </ps-route-link>

                    <span class="ms-2 me-2">/</span>

                    <ps-label class="text-xs lg:text-base" textColor="text-fePrimary-500">
                        {{ $t("review_list__review_list") }}
                    </ps-label>
                </div>
                <!-- breadcrumb end-->

                <ps-label class="font-semibold mb-4 text-xl">{{ $t('review_list__review_list') }}</ps-label>

                <!-- customer review -->
                <div class="flex flex-col sm:flex-row bg-feAchromatic-50 dark:bg-feAchromatic-900 gap-6">
                    <div class="lg:w-100 xl:w-2/5 flex flex-col rounded-lg border dark:border-feAchromatic-700 p-8">

                        <h1 class="text-center font-semibold text-2xl dark:text-feAchromatic-50">{{
                            $t("review_list__customer_reviews") }}</h1>

                        <div class="w-auto mx-auto mt-6">
                            <div class="rounded-full p-1 flex-row lg:flex items-center justify-between w-full mx-auto">
                                <rating-show class=""
                                    :rating="reviewuserProvider.user.data ? reviewuserProvider.user.data.overallRating : 0"
                                    :maxStars="5" :iconColor="'text-feWarning-500 dark:text-feWarning-accent'" />
                                <ps-label-title3 class="mx-4 lg:mx-0 lg:me-3" textColor="dark:text-feAchromatic-50"> {{
                                    reviewuserProvider.user.data?.overallRating }} out of 5</ps-label-title3>
                            </div>

                            <ps-label-title-3 class="mt-3 text-center"> {{ reviewuserProvider.user.data ?
                                reviewuserProvider.user.data.ratingCount : '' }} {{ $t("review_list__customer_ratings") }}
                            </ps-label-title-3>
                        </div>


                        <div class="flex flex-col mt-8">
                            <div class="flex justify-between mb-2">
                                <ps-label-title-3 class="w-20 md:w-16 "> {{ $t("review_list__5_stars") }}
                                </ps-label-title-3>
                                <ps-label-title-3> {{ reviewuserProvider.user.data ?
                                    reviewuserProvider.user.data.ratingDetail?.fiveStarPercent : 0 }} %</ps-label-title-3>
                            </div>
                            <div class="">
                                <base-progress :color="'bg-fePrimary-500'"
                                    :percentage="reviewuserProvider.user.data ? reviewuserProvider.user?.data?.ratingDetail?.fiveStarPercent : 0"
                                    class="mx-2 mb-2 h-20" />
                            </div>

                        </div>

                        <div class="flex flex-col mt-5">
                            <div class="flex justify-between mb-2">
                                <ps-label-title-3 class="w-20 md:w-16 ">{{ $t("review_list__4_stars") }} </ps-label-title-3>
                                <ps-label-title-3> {{ reviewuserProvider.user.data ?
                                    reviewuserProvider.user.data.ratingDetail?.fourStarPercent : 0 }} %</ps-label-title-3>
                            </div>
                            <div class="">
                                <base-progress :color="'bg-fePrimary-500'"
                                    :percentage="reviewuserProvider.user.data ? reviewuserProvider.user.data.ratingDetail?.fourStarPercent : 0"
                                    class="mx-2 mb-2 h-20" />
                            </div>
                        </div>

                        <div class="flex flex-col mt-5">
                            <div class="flex justify-between mb-2">
                                <ps-label-title-3 class="w-20 md:w-16 "> {{ $t("review_list__3_stars") }}
                                </ps-label-title-3>
                                <ps-label-title-3> {{ reviewuserProvider.user.data ?
                                    reviewuserProvider.user.data.ratingDetail?.threeStarPercent : 0 }} %</ps-label-title-3>
                            </div>
                            <div class="">
                                <base-progress :color="'bg-fePrimary-500'"
                                    :percentage="reviewuserProvider.user.data ? reviewuserProvider.user.data.ratingDetail?.threeStarPercent : 0"
                                    class="mx-2 mb-2 h-20" />
                            </div>
                        </div>

                        <div class="flex flex-col mt-5">
                            <div class="flex justify-between mb-2">
                                <ps-label-title-3 class="w-20 md:w-16 "> {{ $t("review_list__2_stars") }}
                                </ps-label-title-3>
                                <ps-label-title-3> {{ reviewuserProvider.user.data ?
                                    reviewuserProvider.user.data.ratingDetail?.twoStarPercent : 0 }} %</ps-label-title-3>
                            </div>
                            <div class="">
                                <base-progress :color="'bg-fePrimary-500'"
                                    :percentage="reviewuserProvider.user.data ? reviewuserProvider.user.data.ratingDetail?.twoStarPercent : 0"
                                    class="mx-2 mb-2 h-20" />
                            </div>
                        </div>

                        <div class="flex flex-col mt-5">
                            <div class="flex justify-between mb-3">
                                <ps-label-title-3 class="w-20 md:w-16 "> {{ $t("review_list__1_star") }} </ps-label-title-3>
                                <ps-label-title-3> {{ reviewuserProvider.user.data ?
                                    reviewuserProvider.user.data.ratingDetail?.oneStarPercent : 0 }} %</ps-label-title-3>
                            </div>
                            <div class="">
                                <base-progress :color="'bg-fePrimary-500'"
                                    :percentage="reviewuserProvider.user.data ? reviewuserProvider.user.data.ratingDetail?.oneStarPercent : 0"
                                    class="mx-2 mb-2 h-20" />
                            </div>
                        </div>
                    </div>
                    <div class="w-full">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6" v-if="ratinglistProvider.ratingList?.data != null">
                            <!-- Rating User Listing -->
                            <div v-for="rate in ratinglistProvider.ratingList.data" :key="rate.id">
                                <rating-horizontal :rate="rate" :onClick="rateClicked" />
                            </div>
                            <!-- End Rating User -->
                        </div>

                        <div class="grid lg:grid-cols-12 sm:grid-cols-8 grid-cols-4  gap-4 sm:gap-3.5 lg:gap-4"
                            v-else-if="ratinglistProvider.loading.value == true">
                            <div class="w-full col-span-4 sm:col-span-2 md:col-span-3 lg:col-span-4 xl:col-span-3"
                                v-for="i in 3" :key="i">
                                <item-horizontal-skeletor-item />
                            </div>
                        </div>
                        <ps-button v-if="ratinglistProvider.loading.value == false" class="mx-auto mt-6" @click="loadMore"
                            :class="ratinglistProvider.isNoMoreRecord.value || ratinglistProvider.ratingList?.data == null ? 'hidden' : ''">{{
                                $t("list__load_more") }} </ps-button>
                        <ps-button v-else class="mx-auto mt-6" @click="loadMore" :disabled="true"> {{ $t("list__loading")
                        }}</ps-button>
                    </div>

                </div>
                <!-- end customer review -->
            </div>
        </template>
    </ps-content-container>
</template>

<script lang="ts">
import { Head } from '@inertiajs/vue3';
// import router from "@template1/router";

import PsContentContainer from '@template1/vendor/components/layouts/container/PsContentContainer.vue';
import PsLabel from '@template1/vendor/components/core/label/PsLabel.vue';
import PsLabelHeader4 from '@template1/vendor/components/core/label/PsLabelHeader4.vue';
import PsLabelTitle3 from '@template1/vendor/components/core/label/PsLabelTitle3.vue';
import PsIcon from '@template1/vendor/components/core/icons/PsIcon.vue';
import RatingShow from '@template1/vendor/components/core/rating/RatingShow.vue';
import BaseProgress from '@template1/vendor/components/core/progressbar/BaseProgress.vue';
import PsLabelCaption from '@template1/vendor/components/core/label/PsLabelCaption.vue';
import PsRouteLink from '@template1/vendor/components/core/link/PsRouteLink.vue';
import PsBreadcrumb2 from '@template1/vendor/components/core/breadcrumbs/PsBreadcrumb2.vue';
import ItemHorizontalSkeletorItem from "@template1/vendor/components/modules/item/ItemHorizontalSkeletorItem.vue";

//Modules
import { PsValueStore } from "@templateCore/store/modules/core/PsValueStore";
import { useUserStore } from "@templateCore/store/modules/user/UserStore";
import { useRatingListStoreState } from "@templateCore/store/modules/rating/RatingListStore";
import RatingHorizontal from "@template1/vendor/components/modules/rating/RatingHorizontal.vue";
import { trans } from 'laravel-vue-i18n';
// import { useRoute } from 'vue-router';
// Params Holders
import RatingListHolder from '@templateCore/object/holder/RatingListHolder';
import PsFrontendLayout from '@template1/vendor/components/layouts/container/PsFrontendLayout.vue';
import PsButton from '@template1/vendor/components/core/buttons/PsButton.vue';

export default {
    name: "ReviewListView",
    components: {
        PsContentContainer,
        PsLabel,
        PsLabelHeader4,
        PsIcon,
        RatingShow,
        PsLabelTitle3,
        BaseProgress,
        PsLabelCaption,
        RatingHorizontal,
        PsRouteLink,
        PsBreadcrumb2,
        ItemHorizontalSkeletorItem,
        PsButton,
        Head
    },
    layout: PsFrontendLayout,
    props: ['query', 'mobileSetting'],
    setup(props) {

        const psValueStore = PsValueStore();
        const holder = new RatingListHolder();
        const breadcrumbuserProvider = useUserStore();
        const reviewuserProvider = useUserStore();
        const ratinglistProvider = useRatingListStoreState();
        const loginUserId = psValueStore.getLoginUserId();
        // const route = useRoute();
        const userId = props.query.user_id;

        ratinglistProvider.limit = props.mobileSetting?.default_loading_limit ?? 12;

        // Load User By ID List
        breadcrumbuserProvider.loadUser(userId);

        //load Rating list
        holder.userId = userId;
        ratinglistProvider.resetRatingList(loginUserId, holder);
        // Load User By ID List
        reviewuserProvider.loadUser(userId);
        console.log("review", reviewuserProvider);

        function loadMore() {
            ratinglistProvider.loadRatingList(loginUserId, holder);
        }

        function rateClicked() {

        }


        return {
            breadcrumbuserProvider,
            ratinglistProvider,
            reviewuserProvider,
            rateClicked,
            loginUserId,
            userId,
            loadMore
        }
    },
    computed: {
        breadcrumb() {
            return [
                {
                    label: trans('profile_label'),
                    url: route('fe_profile')
                },
                {
                    label: trans('review_list__review_list'),
                    color: "text-fePrimary-500"
                }
            ]
        }
    },
}
</script>
