<template>
    <Head :title="$t('user_list__users')"/>
    <ps-content-container>
        <template #content>
            <div class="mt-32 mb-10">
                <div class="flex flex-col sm:flex-row sm:justify-between items-start sm:items-center">
                    <div class="w-full mb-6 sm:mb-0">
                        <ps-breadcrumb-2 :items="breadcrumb" class="" />
                    </div>

                    <!-- start keyword search -->
                    <!-- <div class="flex justify-end content-center items-center w-full">
                        <ps-input-with-right-icon v-on:keyup.enter="usernameFilterClicked" v-model:value="userStore.userparamHolder.keyword" class="sm:w-80 w-full" padding="py-2 px-4 h-10" v-bind:placeholder="$t('item_list__user_search')" >
                            <template #icon>
                                <ps-icon name="search" class='cursor-pointer'/>
                            </template>
                        </ps-input-with-right-icon>

                        <ps-dropdown align="right" class="ms-4" >
                            <template #select>
                                <ps-dropdown-select class="h-10 w-10 sm:w-auto sm:ps-4 ps-2.5" leftIcon="filter" leftIconTheme="text-feAchromatic-50 sm:me-2 me-0" bgColor="bg-fePrimary-500" text="text-sm font-medium text-feAchromatic-50 hidden sm:inline" iconTheme="text-feAchromatic-50 ms-2 hidden sm:inline" :selectedValue="activeSortingName" placeholder="Sort" />
                            </template>
                            <template #list>
                                <div
                                    class="rounded-md bg-feAchromatic-50 dark:bg-feSecondary-800 shadow-xs w-28"
                                    role="menu"
                                    aria-orientation="vertical"
                                    aria-labelledby="options-menu">
                                    <div class="pt-2">
                                        <div v-for="sort in usersorting" :key="sort.id" class="flex py-4 px-2 hover:bg-fePrimary-50 dark:hover:bg-feSecondary-500 cursor-pointer items-center"  @click="usersortingFilterClicked(sort)" >
                                            <span class="ms-2 text-feSecondary-800 dark:text-feSecondary-200" :class="sort.id==activeSortingId ? 'font-semibold' : ''"  > {{sort.name}} </span>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </ps-dropdown>
                    </div> -->
                    <!-- end keyword search -->
                </div>
                <div class="mt-8">
                    <div class="w-full" v-if="topRatedSellerStore.userList.data != null">
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
                            <div class="w-full" v-for="user in topRatedSellerStore.userList?.data" :key="user.userId">
                                <!-- <ps-route-link :to="{ name: 'fe_other_profile', params: { userId: user.userId }}"> -->
                                    <user-list-horizontal :user="user" storeName="top_rated_seller"/>
                                <!-- </ps-route-link> -->
                            </div>
                        </div>
                        <div class="flex flex-col items-center">
                            <ps-button v-if="topRatedSellerStore.loading.value == false && initial == false" class="font-medium mx-auto mt-6" @click="loadMore" :class="topRatedSellerStore.isNoMoreRecord.value ? 'hidden' : ''"> {{ $t("follower_list__load_more") }} </ps-button>
                            <ps-button v-else-if="initial == false" class="font-medium mx-auto mt-6" @click="loadMore" :disabled="true"> {{ $t("follower_list__loading") }} </ps-button>
                        </div>
                    </div>
                    <ps-no-result v-if="topRatedSellerStore.loading.value == false && topRatedSellerStore.userList?.data == null && initial == false" @onClick="loadMore" />
                </div>
            </div>
        </template>
    </ps-content-container>
    <ps-loading-dialog ref="ps_loading_dialog" />
</template>

<script lang="ts">
import { Head } from '@inertiajs/vue3';
// Vue
import { ref } from 'vue'
// import router from '@template1/router';
import PsContentContainer from '@template1/vendor/components/layouts/container/PsContentContainer.vue';
import PsButton from '@template1/vendor/components/core/buttons/PsButton.vue';
import PsLabel from '@template1/vendor/components/core/label/PsLabel.vue';
import PsInput from '@template1/vendor/components/core/input/PsInput.vue';
import PsConst from '@templateCore/object/constant/ps_constants';
import PsLoadingDialog from '@template1/vendor/components/core/dialog/PsLoadingDialog.vue';
import PsBreadcrumb2 from '@template1/vendor/components/core/breadcrumbs/PsBreadcrumb2.vue';
import PsInputWithRightIcon from '@template1/vendor/components/core/input/PsInputWithRightIcon.vue';
import PsDropdown from '@template1/vendor/components/core/dropdown/PsDropdown.vue';
import PsDropdownSelect from '@template1/vendor/components/core/dropdown/PsDropdownSelect.vue';
import PsNoResult from "@template1/vendor/components/modules/list/PsNoResult.vue";

import { usePsValueHolderState } from '@templateCore/object/core/PsValueHolder';
import { PsValueStore } from "@templateCore/store/modules/core/PsValueStore";
import { useUserStore } from "@templateCore/store/modules/user/UserStore";
import { useUserListStoreState } from "@templateCore/store/modules/user/UserListStore";

// Modules
import UserSearchListHorizontal from "@template1/vendor/components/modules/user/UserSearchListHorizontal.vue";
import UserListHorizontal from "@template1/vendor/components/modules/user/UserListHorizontal.vue";
import PsFrontendLayout from '@template1/vendor/components/layouts/container/PsFrontendLayout.vue';
import { trans } from 'laravel-vue-i18n';

export default {
    name : "TopRatedSellerList",
    components : {
        PsContentContainer,
        UserSearchListHorizontal,
        PsButton,
        PsLabel,
        PsInput,
        PsDropdown,
        PsLoadingDialog,
        UserListHorizontal,
        PsBreadcrumb2,
        PsInputWithRightIcon,
        PsDropdownSelect,
        PsNoResult,
        Head
    },
    props: ['mobileSetting'],
    layout: PsFrontendLayout,
    setup(props) {
        const topRatedSellerStore = useUserListStoreState("top_rated_seller");
        let activeSortingId = ref('');
        let activeSortingName = ref('');
        const ps_loading_dialog = ref();
         PsValueStore.psValueStore = usePsValueHolderState();
        const psValueStore = PsValueStore();
        const loginUserId = psValueStore.getLoginUserId();
        const usersorting = [
            {
                id:"0",
                orderBy:"name",
                orderType:PsConst.FILTERING__ASC,
                name:"A to Z"
            },
            {
                id:"1",
                orderBy:"name",
                orderType:PsConst.FILTERING__DESC,
                name:"Z to A"
            }
        ];
        const userStore = useUserStore('userSearch');
        const initial = ref(true);

        userStore.limit = props.mobileSetting?.default_loading_limit ?? 12;

        setTimeout(async () => {
            await loadUserDataList();
        },100);
        // userStore.resetUserSearchList(loginUserId,userStore.userparamHolder);


        async function usernameFilterClicked() {
            ps_loading_dialog.value.openModal();
            await userStore.resetUserSearchList( loginUserId,userStore.userparamHolder);
            ps_loading_dialog.value.closeModal();
        }

        async function loadUserDataList() {
            if(userStore.userList.data == null){
                ps_loading_dialog.value.openModal();
            }

            // await userStore.loadUserSearchList( loginUserId,userStore.userparamHolder);
            await topRatedSellerStore.resetTopRatedSellerList(loginUserId);
            ps_loading_dialog.value.closeModal();
            initial.value = false;
        }

        async function usersortingFilterClicked(value) {
            ps_loading_dialog.value.openModal();
            activeSortingId.value = value.id;
            activeSortingName.value = value.name;
            userStore.userparamHolder.orderBy = value.orderBy;
            userStore.userparamHolder.orderType = value.orderType;
            await userStore.resetUserSearchList( loginUserId,userStore.userparamHolder);
             ps_loading_dialog.value.closeModal();
        }

        function loadMore() {
            topRatedSellerStore.loadTopRatedSellerList(loginUserId);
        }


        return {
            topRatedSellerStore,
            // loginUserId,
            ps_loading_dialog,
            userStore,
            loadMore,
            usernameFilterClicked,
            usersortingFilterClicked,
            activeSortingId,
            activeSortingName,
            usersorting,
            loadUserDataList,
            initial
        };
    },
    computed: {
        breadcrumb() {
            return [
                {
                    label: trans('ps_nav_bar__home'),
                    url: route('dashboard')
                },
                {
                    label: trans('core_fe__top_rated_seller'),
                    color: "text-fePrimary-500"
                }
            ]
        }
    },
}
</script>
