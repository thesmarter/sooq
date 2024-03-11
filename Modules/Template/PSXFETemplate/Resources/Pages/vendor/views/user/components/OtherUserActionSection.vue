<template>
    <div class=" text-center mt-6" v-if="userStore.user.data?.userId != LoginUserId" @click="followClick">
        <div v-if="userStore.user.data?.isFollowed == '0'">
            <ps-button rounded="rounded" textSize="text-sm" class=" w-full  mx-auto"> {{ $t("other_profile__follow") }}
            </ps-button>
        </div>
        <div v-else-if="userStore.user.data?.isFollowed == '1'">
            <ps-button rounded="rounded" textSize="text-sm" class=" w-full  mx-auto"> {{ $t("other_profile__unfollow") }}
            </ps-button>
        </div>
    </div>

    <div v-if="appInfoStore?.appInfo?.data?.psAppSetting?.isBlockUser == PsConst.ONE"
        class="mt-5 py-2 px-4 text-center w-full border bg-feAchromatic-50 border-1 border-feSecondary-300 dark:border-feSecondary-500 dark:bg-feSecondary-800 rounded lg:w-full mx-auto">
        <ps-label class="text-sm cursor-pointer" @click="blockClicked(userStore.user?.data?.userId ?? '')">
            {{ $t("other_profile__block_user") }}
        </ps-label>
    </div>

    <ps-loading-dialog ref="psloading" :isClickOut='false' />

    <ps-confirm-dialog ref='ps_confirm_dialog' />
</template>

<script>

// Libs
import { router } from '@inertiajs/vue3';
import { ref } from 'vue';
import { trans } from 'laravel-vue-i18n';
//Components
import PsLabel from '@template1/vendor/components/core/label/PsLabel.vue';
import PsButton from '@template1/vendor/components/core/buttons/PsButton.vue';
import PsLoadingDialog from '@template1/vendor/components/core/dialog/PsLoadingDialog.vue';
import PsConfirmDialog from '@template1/vendor/components/core/dialog/PsConfirmDialog.vue';
//Store
import { useUserStore } from "@templateCore/store/modules/user/UserStore";
import { PsValueStore } from "@templateCore/store/modules/core/PsValueStore";
import { usePsAppInfoStoreState } from '@templateCore/store/modules/appinfo/AppInfoStore';
//Holder
import UserParameterHolder from '@templateCore/object/holder/UserParameterHolder';
import UserFollowHolder from '@templateCore/object/holder/UserFollowHolder';
import UserBlockParameterHolder from '@templateCore/object/holder/UserBlockParameterHolder';

import PsConst from "@templateCore/object/constant/ps_constants";
import PsUtils from '@templateCore/utils/PsUtils';
import PsStatus from '@templateCore/api/common/PsStatus';



export default {
    name: "OtherUserProfileInfoCard",
    components: {
        PsLabel,
        PsButton,
        PsLoadingDialog,
        PsConfirmDialog
    },
    props: {
        userId: String
    },
    setup(props) {

        const psloading = ref();
        const ps_confirm_dialog = ref();

        const psValueStore = PsValueStore();
        const LoginUserId = psValueStore.getLoginUserId();

        const userStore = useUserStore('other');
        const appInfoStore = usePsAppInfoStoreState();
        const paramHolder = new UserParameterHolder().getOtherUserData();

        const followHolder = new UserFollowHolder();
        const userblock = new UserBlockParameterHolder();

        paramHolder.loginUserId = LoginUserId;
        paramHolder.id = props.userId;
        userStore.loadOtherUser(LoginUserId, paramHolder);

        async function followClick() {
            if (LoginUserId && LoginUserId != PsConst.NO_LOGIN_USER) {
                psloading.value.openModal();
                if (userStore.user.data?.isFollowed == '1') {
                    psloading.value.message = trans('other_profile__removing_user_from_follower_list');
                } else {
                    psloading.value.message = trans('other_profile__adding_user_to_follower_list');
                }
                followHolder.userId = LoginUserId;
                followHolder.followedUserId = userStore.user?.data?.userId ?? '';
                await userStore.postUserFollow(followHolder);
                await userStore.loadOtherUser(LoginUserId, paramHolder);

                psloading.value.closeModal();
            }
            else {
                router.get(route('login'));
            }
        }

        // Block this user
        function blockClicked(userId) {
            if (LoginUserId && LoginUserId != PsConst.NO_LOGIN_USER) {
                ps_confirm_dialog.value.openModal(
                    trans('other_profile__confirm'),
                    trans('other_profile__confirm_to_block_user'),
                    trans('other_profile__block'),
                    trans('other_profile__cancel'),
                    () => {
                        doBlock(userId);
                    },
                    () => {
                        PsUtils.log("Cancel");
                    }
                );
            }
            else {
                router.get(route('login'));
            }

        }
        //unblock user

        //Block User
        async function doBlock(userId) {

            userblock.loginUserId = LoginUserId;
            userblock.addedUserId = userId;

            psloading.value.openModal();
            psloading.value.message = trans('other_profile__adding_user_to_block_list');
            const returnData = await userStore.blockUser(userblock, LoginUserId);
            psloading.value.closeModal();

            if (returnData.status == PsStatus.ERROR) {
                return;
            } else if (returnData.status == PsStatus.SUCCESS) {
                router.get(route('dashboard'));
            }
        }


        return {
            userStore,
            appInfoStore,
            LoginUserId,
            PsConst,
            psloading,
            ps_confirm_dialog,
            followClick,
            blockClicked
        }
    },
}
</script>
