<template>
    <Head :title="$t('blockd_user_list__blockd_user_list')"/>
    <ps-content-container>
        <template #content>
            <div class="mt-32 mb-10">

                <div class="mb-6">
                    <ps-breadcrumb-2 :items="breadcrumb" class="" />
                </div>

                <div class="mt-4">
                    <div class='w-full' v-if="blockUserStore.blockUserList.data != null">
                        <div class="grid lg:grid-cols-3 sm:grid-cols-2 grid-cols-1 gap-6">
                            <div class="w-full" v-for="block in blockUserStore.blockUserList.data" :key="block.userId">
                                <block-user-list-horizontal  :block="block" :onClick="unBlockClicked"  />
                            </div>
                        </div>
                        <div class="flex flex-col items-center">
                            <ps-button v-if="blockUserStore.loading.value == false" class="font-medium mx-auto mt-6" @click="loadMore" :class="blockUserStore.isNoMoreRecord.value ? 'hidden' : ''"> {{ $t("follower_list__load_more") }} </ps-button>
                            <ps-button v-else class="font-medium mx-auto mt-6" @click="loadMore" :disabled="true"> {{ $t("follower_list__loading") }} </ps-button>
                        </div>

                    </div>
                    <ps-no-result v-if="blockUserStore.loading.value == false && blockUserStore.blockUserList?.data == null" @onClick="loadMore" />
                </div>

            </div>
        </template>
    </ps-content-container>
    <ps-confirm-dialog ref='ps_confirm_dialog' />
    <ps-loading-dialog ref="psloading"  :isClickOut='false'/>
</template>

<script lang="ts">
import { Head } from '@inertiajs/vue3';
// Vue
import { ref } from 'vue';

import PsContentContainer from '@template1/vendor/components/layouts/container/PsContentContainer.vue';
import PsLabelHeader4 from '@template1/vendor/components/core/label/PsLabelHeader4.vue';
import PsLabel from '@template1/vendor/components/core/label/PsLabel.vue';
import PsButton from '@template1/vendor/components/core/buttons/PsButton.vue';
import PsIcon from '@template1/vendor/components/core/icons/PsIcon.vue';
import PsLoadingDialog from '@template1/vendor/components/core/dialog/PsLoadingDialog.vue';
import PsConfirmDialog from '@template1/vendor/components/core/dialog/PsConfirmDialog.vue';
import PsRouteLink from '@template1/vendor/components/core/link/PsRouteLink.vue';
import PsBreadcrumb2 from '@template1/vendor/components/core/breadcrumbs/PsBreadcrumb2.vue';
import PsNoResult from "@template1/vendor/components/modules/list/PsNoResult.vue";

//Modules
import { blockUserStoreState } from "@templateCore/store/modules/user/BlockUserStore";
import { useUserStore } from "@templateCore/store/modules/user/UserStore";
import BlockUserListHorizontal from "@template1/vendor/components/modules/user/BlockUserListHorizontal.vue";
import { PsValueStore } from "@templateCore/store/modules/core/PsValueStore";
import { useOtherUserStoreState } from "@templateCore/store/modules/user/OtherUserStore";

import UserBlockParameterHolder from '@templateCore/object/holder/UserBlockParameterHolder';
import PsStatus from '@templateCore/api/common/PsStatus';
import { trans } from 'laravel-vue-i18n';import PsUtils from '@templateCore/utils/PsUtils';
import PsFrontendLayout from '@template1/vendor/components/layouts/container/PsFrontendLayout.vue';

export default {
    name : "BlockedUserListView",
    components : {
        PsContentContainer,
        PsLabelHeader4,
        BlockUserListHorizontal,
        PsLabel,
        PsButton,
        PsIcon,
        PsLoadingDialog,
        PsConfirmDialog,
        PsRouteLink,
        PsBreadcrumb2,
        PsNoResult,
        Head
    },
    props: ['mobileSetting'],
    layout: PsFrontendLayout,
    setup(props) {

        const ps_confirm_dialog = ref();
        const psloading = ref();
        const psValueStore = PsValueStore();
        const blockUserStore = blockUserStoreState();
        const breadcrumbuserProvider = useUserStore();
        const userProvider = useOtherUserStoreState();

        blockUserStore.limit = props.mobileSetting?.default_loading_limit ?? 12;

        const loginUserId = psValueStore.getLoginUserId();
        const userblock = new UserBlockParameterHolder();
        // Load User By ID List
        blockUserStore.loadBlockedUserList(loginUserId);

        // Load User By ID List
        breadcrumbuserProvider.loadUser(loginUserId);

        function loadMore() {
            blockUserStore.loadBlockedUserList(loginUserId);
        }

       // UnBlock this user

        function unBlockClicked(block) {
            ps_confirm_dialog.value.openModal(
                trans('blocked_user_list__confirm'),
                trans('blocked_user_list__confirm_to_unblock_user'),
                trans('blocked_user_list__unblock'),
                trans('blocked_user_list__cancel'),
                () => {
                    doUnBlock(block.userId);
                },
                () => {
                    PsUtils.log("Cancel");
                }
            );
        }
        //unblock user

        //Block User
        async function doUnBlock(userId) {

            userblock.loginUserId = loginUserId;
            userblock.addedUserId = userId;
            psloading.value.openModal();
            const returnData = await userProvider.postUnBlockUser(loginUserId, userblock);

            if(returnData.status == PsStatus.ERROR) {
                psloading.value.closeModal();
                return;
            }else if(returnData.status == PsStatus.SUCCESS) {

                blockUserStore.refreshBlockedUserList(loginUserId);
                psloading.value.closeModal();

            }
        }
        return {
            blockUserStore,
            loadMore,
            breadcrumbuserProvider,
            unBlockClicked,
            doUnBlock,
            ps_confirm_dialog,
            psloading
        }
    },
    computed: {
        breadcrumb() {
            return [
                {
                    label: trans('ps_nav_bar__profile'),
                    url: route('fe_profile')
                },
                {
                    label: trans('blockd_user_list__blockd_user_list'),
                    color: "text-fePrimary-500"
                }
            ]
        }
    },
}
</script>
