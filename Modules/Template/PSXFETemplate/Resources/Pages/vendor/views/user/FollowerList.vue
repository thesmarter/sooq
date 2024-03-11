<template>
    <Head :title="$t('follower_list__follower_list')"/>
    <ps-content-container>
        <template #content>
            <div class="mt-32 mb-10">

                <div class="mb-6">
                    <ps-breadcrumb-2 :items="breadcrumb" class="" />
                </div>

                <div class="mt-4">
                    <div class='w-full'  v-if="userStore.userList.data != null">
                        <div class="grid lg:grid-cols-3 sm:grid-cols-2 grid-cols-1 gap-6">
                            <div class="w-full" v-for="user in userStore.userList.data" :key="user.userId">
                                <!-- <ps-route-link :to="{ name: 'fe_other_profile', params: { userId: user.userId }}"> -->
                                    <user-list-horizontal  :user="user" storeName="profile-follower"/>
                                <!-- </ps-route-link> -->
                            </div>
                        </div>
                        <div class="flex flex-col items-center">
                            <ps-button v-if="userStore.loading.value == false" class="font-medium mx-auto mt-6" @click="loadMore" :class="userStore.isNoMoreRecord.value ? 'hidden' : ''"> {{ $t("follower_list__load_more") }} </ps-button>
                            <ps-button v-else class="font-medium mx-auto mt-6" @click="loadMore" :disabled="true"> {{ $t("follower_list__loading") }} </ps-button>
                        </div>
                    </div>
                    <ps-no-result v-if="userStore.loading.value == false && userStore.userList?.data == null" @onClick="loadMore" />
                </div>

            </div>
        </template>
    </ps-content-container>
</template>

<script lang="ts">
import { Head } from '@inertiajs/vue3';
import PsContentContainer from '@template1/vendor/components/layouts/container/PsContentContainer.vue';
import PsLabelHeader4 from '@template1/vendor/components/core/label/PsLabelHeader4.vue';
import PsLabel from '@template1/vendor/components/core/label/PsLabel.vue';
import PsButton from '@template1/vendor/components/core/buttons/PsButton.vue';
import PsIcon from '@template1/vendor/components/core/icons/PsIcon.vue';
import PsRouteLink from '@template1/vendor/components/core/link/PsRouteLink.vue';
import PsBreadcrumb2 from '@template1/vendor/components/core/breadcrumbs/PsBreadcrumb2.vue';
import PsNoResult from "@template1/vendor/components/modules/list/PsNoResult.vue";
import { trans } from 'laravel-vue-i18n';

//Modules
import { useUserListStoreState } from "@templateCore/store/modules/user/UserListStore";
import { useUserStore } from "@templateCore/store/modules/user/UserStore";
import UserListHorizontal from "@template1/vendor/components/modules/user/UserListHorizontal.vue";
import { PsValueStore } from "@templateCore/store/modules/core/PsValueStore";
import PsFrontendLayout from '@template1/vendor/components/layouts/container/PsFrontendLayout.vue';

import UserParameterHolder from '@templateCore/object/holder/UserParameterHolder';

export default {
    name : "FollowerListView",
    components : {
        PsContentContainer,
        PsLabelHeader4,
        UserListHorizontal,
        PsLabel,
        PsButton,
        PsIcon,
        PsRouteLink,
        PsBreadcrumb2,
        PsNoResult,
        Head
    },
    layout: PsFrontendLayout,
    props : ['query','mobileSetting'],
    setup(props) {

        const psValueStore = PsValueStore();
        const userStore = useUserListStoreState('profile-follower');
        const breadcrumbuserProvider = useUserStore();
        const loginUserId = psValueStore.getLoginUserId();
        const paramHolder = new UserParameterHolder().getFollowerUsers();

        userStore.limit = props.mobileSetting?.default_loading_limit ?? 12;

        paramHolder.loginUserId = props.query.userId;

        // Load User By ID List
        userStore.resetUserList(loginUserId, paramHolder);

        // Load User By ID List
        breadcrumbuserProvider.loadUser(props.query.userId);

        function loadMore() {
            userStore.loadUserList(loginUserId, paramHolder);
        }

        return {
            userStore,
            loadMore,
            breadcrumbuserProvider
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
                    label: trans('follower_list__follower_list'),
                    color: "text-fePrimary-500"
                }
            ]
        }
    },
}
</script>
