<template>
    <ps-card class="lg:rounded-lg bg-feSecondary-50 dark:bg-feSecondary-800 p-4">
        <div class="w-32 h-32 rounded-full mx-auto relative">
            <img alt="Placeholder" class="rounded-full w-full h-full object-cover"
                v-lazy="{ src: $page.props.thumb1xUrl + '/' + userStore.user.data?.userCoverPhoto, loading: $page.props.sysImageUrl + '/loading_gif.gif', error: $page.props.sysImageUrl + '/default_profile.png' }" />
            <div v-if="userStore.user.data?.isVerifybluemark == '1'"
                class="w-8 h-8 bg-feInfo-500 rounded-full p-1 absolute bottom-0 right-0">
                <ps-icon name="bluemark" textColor="text-feSecondary-50" w="24" h="24" />
            </div>
        </div>
        <ps-label textColor="dark:text-feSecondary-200" class="text-center text-xl font-semibold mb-2 mt-2"> {{
            userStore.user.data ? userStore.user.data.userName : '' }} </ps-label>

        <ps-route-link :to="{ name: 'fe_review_list', query: { user_id: userStore.user.data?.userId } }">
            <rating class="flex items-center content-center justify-center px-4 mb-3 cursor-pointer"
                :rating="userStore.user.data ? Number(userStore.user?.data?.overallRating) : 0" :maxStars="5" size="md"
                iconColor="text-feWarning-500" />
        </ps-route-link>

        <ps-label textColor="text-feSecondary-600 dark:text-feSecondary-400" class="text-sm mb-3 text-center"> {{
            userStore.user.data ? userStore.user.data.ratingCount : '' }} {{ $t("profile__reviews") }} </ps-label>
        <ps-label textColor="dark:text-feSecondary-200" v-if="userStore.user.data && userStore.user.data.isShowEmail == '1'"
            class="text-sm mb-3 text-center"> {{
                userStore.user.data ? userStore.user.data.userEmail : '' }} </ps-label>
        <ps-label textColor="dark:text-feSecondary-200" v-if="userStore.user.data && userStore.user.data.isShowPhone == '1'"
            class="text-sm mb-3 text-center"> {{
                userStore.user.data ? userStore.user.data.userPhone : '' }} </ps-label>
        <ps-label textColor="dark:text-feSecondary-200" class="text-sm mb-3 text-center"> {{ userStore.user.data ?
            userStore.user.data.userAboutMe : '' }} </ps-label>
        <ps-label textColor="text-feSecondary-500 dark:text-feSecondary-400" class="text-xs mb-3 text-center">{{
            $t("profile__joined_on") }} {{ userStore.user.data ? getDateFormat(userStore.user.data.addedDate) : ''
    }}</ps-label>

        <ps-route-link class="cursor-pointer mb-2 flex justify-between"
            :to="{ name: 'fe_follower_list', query: { userId: userStore.user.data?.userId } }">
            <ps-label class="text-sm">{{ $t("other_profile__followers") }} </ps-label>
            <ps-label class="font-semibold text-sm">{{ userStore.user.data ?
                userStore.user.data.followerCount : '0' }}</ps-label>
        </ps-route-link>
        <ps-route-link class="cursor-pointer flex justify-between"
            :to="{ name: 'fe_following_list', query: { userId: userStore.user.data?.userId } }">
            <ps-label class="text-sm">{{ $t("other_profile__followings") }} </ps-label>
            <ps-label class="font-semibold text-sm">{{ userStore.user.data ?
                userStore.user.data.followingCount : '0' }}</ps-label>
        </ps-route-link>
    </ps-card>
</template>

<script>
//libs
import { usePage } from '@inertiajs/vue3';
import moment from 'moment';
//components
import PsCard from '@template1/vendor/components/core/card/PsCard.vue';
import PsLabel from '@template1/vendor/components/core/label/PsLabel.vue';
import Rating from '@template1/vendor/components/core/rating/RatingShow.vue';
import PsRouteLink from '@template1/vendor/components/core/link/PsRouteLink.vue';
import PsIcon from '@template1/vendor/components/core/icons/PsIcon.vue';
//store
import { useUserStore } from "@templateCore/store/modules/user/UserStore";

export default {
    name: "OtherUserProfileInfoCard",
    components: {
        PsCard,
        PsLabel,
        Rating,
        PsRouteLink,
        PsIcon
    },
    setup() {
        const userStore = useUserStore('other');

        function getDateFormat(addedDate) {
            return moment(addedDate).format(usePage().props.dateFormat);
        }

        return {
            userStore,
            getDateFormat
        }
    },
}
</script>
