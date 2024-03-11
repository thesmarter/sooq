<template>
    <div>
        <div class="flex flex-col p-4 gap-2 border border-feAchromatic-100 dark:border-feAchromatic-700 rounded-lg mb-2">
            
            <!-- Profile Image -->
            <div class="w-32 h-32 rounded-full mx-auto relative">
                <img alt="Placeholder" class="rounded-full w-full h-full object-cover"
                    v-lazy=" { src: $page.props.thumb1xUrl + '/' + userStore.user.data?.userCoverPhoto, loading: $page.props.sysImageUrl+'/loading_gif.gif', error: $page.props.sysImageUrl+'/default_profile.png' }" />
                <div v-if="userStore.verifybluemark('1')" class="w-8 h-8 bg-feInfo-500 rounded-full p-1 absolute bottom-0 right-0">
                    <ps-icon name="bluemark" textColor="text-feSecondary-50" w="24" h="24" />
                </div>
            </div>

            <!-- Profile Name -->
            <ps-label textColor="text-center text-xl font-semibold text-feSecondary-800">{{  userStore.user.data ? userStore.user.data.userName:'' }}</ps-label>
            
            <!-- Blue Mark Section -->
            <div v-if="userStore.verifybluemark('0') || userStore.verifybluemark('1')" class="bg-feInfo-50 border border-feInfo-500 px-4 py-2 flex gap-2 justify-center rounded cursor-pointer" @click="openApplyUserBluemark(loginUserId)">
                <ps-icon class="text-feInfo-500 dark:text-feInfo-500" name="checkCircle" w="24" h="24" />
                <ps-label textColor="text-feInfo-500">{{ $t("bluemark__verify") }}</ps-label>
            </div>
            <div v-else-if="userStore.verifybluemark('2')" class="bg-feWarning-50 border border-feWarning-500 px-4 py-2 flex gap-2 justify-center rounded cursor-pointer">
                <ps-icon class="text-feInfo-500 dark:text-feInfo-500" name="checkCircle" w="24" h="24" />
                <ps-label textColor="text-feWarning-500">{{ $t("bluemark__pending") }}</ps-label>
            </div>
            <div v-else-if="userStore.verifybluemark('3')" class="bg-feError-50 border border-feError-500 px-4 py-2 flex gap-2 justify-center rounded cursor-pointer" @click="openApplyUserBluemark(loginUserId)">
                <ps-icon class="text-feInfo-500 dark:text-feInfo-500" name="checkCircle" w="24" h="24" />
                <ps-label textColor="text-feError-500">{{ $t("bluemark__rejected") }}</ps-label>
            </div>

            <!-- Review -->
            <ps-route-link :to="{ name: 'fe_review_list',query: { user_id: loginUserId } }" class="flex flex-col items-center gap-2 cursor-pointer">
                <div class="flex items-center justify-center w-full mx-auto">
                    <rating class="h-6" :rating="userStore.user.data ? userStore.user?.data?.overallRating : 0" :maxStars="5" iconColor="text-feWarning-500"/>
                    <ps-label textColor="text-sm font-medium text-feSecondary-500 dark:text-feSecondary-50 pb-1">{{ "(" }}{{userStore.user?.data?.ratingCount}}{{ ")" }}</ps-label>
                </div>
                <!-- <ps-label textColor="text-sm text-feSecondary-600 dark:text-feSecondary-200">{{  userStore.user.data ? userStore.user.data.ratingCount:'' }} {{ $t('profile__reviews') }}</ps-label> -->
            </ps-route-link>

            <!-- Phone -->
            <router-link class="cursor-pointer text-center" v-if="userStore.user?.data?.isShowPhone == '1'">
                <a :href="'tel:' + userStore.user?.data?.userPhone ">
                    <ps-label textColor="text-sm text-feSecondary-800 dark:text-feSecondary-200"> {{  userStore.user?.data ? userStore.user.data.userPhone :'' }} </ps-label>
                </a>
            </router-link>

            <!-- Email -->
            <router-link class="cursor-pointer text-center" v-if="userStore.user?.data?.isShowEmail == '1'">
                <a :href="'tel:' + userStore.user?.data?.userEmail">
                    <ps-label textColor="text-sm text-feSecondary-800 dark:text-feSecondary-200"> {{  userStore.user?.data ? userStore.user.data.userEmail :'' }} </ps-label>
                </a>
            </router-link>

            <!-- About Me -->
            <ps-label class="text-center" textColor="text-sm text-feSecondary-800 dark:text-feSecondary-200"> {{  userStore.user.data ? userStore.user.data.userAboutMe:'' }} </ps-label>
            
            <!-- Joined Date-->
            <ps-label class="text-xs text-feSecondary-500 text-center">{{ $t("profile__joined_on") }} - {{ JoinedDate }} </ps-label>

            <!-- Profile  unlimited -->
            <ps-label class="flex justify-between items-center" textColor="text-sm text-feSecondary-800 dark:text-feSecondary-200 pt-2">
                {{ $t("profile__available") }}
                <span v-if="userStore.user?.data?.postCount == PsConst.LIMITED" class="font-semibold">{{userStore.user.data ? userStore.user.data.remainingPost:'0'}}</span>
                <span v-else class="font-semibold">{{ $t("profile__unlimited") }}</span>
            </ps-label>

            <!-- Profile  posts -->
            <ps-label class="flex justify-between items-center" textColor="text-sm text-feSecondary-800 dark:text-feSecondary-200">
                {{ $t("profile__posts") }}
                <span class="font-semibold postion-end">{{ userStore.user.data ? userStore.user.data.activeItemCount : '0' }}</span>
            </ps-label>

            <!-- Profile  followers -->
            <ps-label class="flex justify-between items-center" textColor="text-sm text-feSecondary-800 dark:text-feSecondary-200">
                {{ $t("profile__followers") }}
                <span class="font-semibold">{{userStore.user.data ? userStore.user.data.followerCount:'0'}}</span>
            </ps-label>

            <!-- Profile  followings -->
            <ps-label class="flex justify-between items-center" textColor="text-sm text-feSecondary-800 dark:text-feSecondary-200">
                {{ $t("profile__followings") }}
                <span class="font-semibold">{{userStore.user.data ? userStore.user.data.followingCount:'0'}}</span>
            </ps-label>
        </div>

        <!-- Profile Edit -->
        <ps-route-link :to="{name: 'fe_profile_edit' }">
            <ps-button class="w-full">
                <ps-icon class="me-2" name="edit" />
                {{ $t("profile__edit_profile") }}
            </ps-button>
        </ps-route-link>
        
        <!-- User Setting -->
        <ps-button class="w-full mt-2" hover="" focus="" colors="bg-feSecondary-700 text-feSecondary-50" @click='openMoreUserSetting'>
            <ps-icon class="me-2" name="setting" />
            {{ $t("profile__user_setting") }}
        </ps-button>
    </div>

    <user-blue-mark-modal ref="user_blue_mark_modal" />

    <user-setting-modal ref="user_setting_modal" />

</template>

<script setup>
import { usePage } from '@inertiajs/vue3';
import { ref, defineAsyncComponent } from 'vue';

import PsLabel from '@template1/vendor/components/core/label/PsLabel.vue';
import PsIcon from '@template1/vendor/components/core/icons/PsIcon.vue';
import Rating from '@template1/vendor/components/core/rating/RatingShow.vue';
import PsButton from '@template1/vendor/components/core/buttons/PsButton.vue';
import PsRouteLink from '@template1/vendor/components/core/link/PsRouteLink.vue';

import PsConst from '@templateCore/object/constant/ps_constants';
import { useUserStore } from "@templateCore/store/modules/user/UserStore";
import moment from 'moment';

const UserBlueMarkModal = defineAsyncComponent(() => import('@template1/vendor/components/modules/user/UserBlueMarkModal.vue'));
const UserSettingModal = defineAsyncComponent(() => import('@template1/vendor/components/modules/user/UserSettingModal.vue'));

const userStore = useUserStore();
const loginUserId = localStorage.loginUserId;

const user_blue_mark_modal = ref();
const user_setting_modal = ref();
const JoinedDate = ref();

loadData();

async function loadData(){

    await userStore.loadUser(loginUserId);
    const temp = userStore.user.data ? new Date(userStore.user.data.addedDate)  :  '' ;

    const joinedDate = moment(temp).format(usePage().props.dateFormat);
    JoinedDate.value = joinedDate;
}

// Verify blue mark user
function openApplyUserBluemark(loginUserId) {
    user_blue_mark_modal.value.openModal(loginUserId);
}

function openMoreUserSetting() {
    user_setting_modal.value.openModal();
}

</script>