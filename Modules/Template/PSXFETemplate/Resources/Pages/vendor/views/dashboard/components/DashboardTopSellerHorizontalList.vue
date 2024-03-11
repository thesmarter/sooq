<template>
    <div v-if="topRatedSellerStore.hasData()">
        <div class="flex justify-between items-center mt-10">
            <ps-label-header-5 class="font-semibold">{{ $t("core_fe__top_rated_seller") }}</ps-label-header-5>
            <ps-route-link :to="{ name: 'fe_top_rated_seller_list' }">
                <ps-button class="flex flex-row" padding="p-2 sm:px-4 sm:py-2" shadow="shadow-sm" rounded="rounded" hover=""
                    focus="" border="border border-feSecondary-200 dark:border-feSecondary-800"
                    colors="bg-feAchromatic-50 text-feSecondary-800 dark:bg-feSecondary-800 dark:text-feSecondary-200">
                    <ps-label class="hidden sm:inline">{{ $t("list_fe__view_all_label") }}</ps-label>
                    <ps-icon class="sm:ms-2 block rtl:hidden" textColor="dark:text-feSecondary-200" name="rightChervon"
                        h="24" w="24" />
                    <ps-icon class="sm:ms-2 hidden rtl:block" textColor="dark:text-feSecondary-200" name="leftChervon"
                        h="24" w="24" />
                </ps-button>
            </ps-route-link>
        </div>
        <div class="mt-6 mb-26 sm:gap-6 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-6">
            <div v-for="user in topRatedSellerStore.userList?.data.slice(0, 4)" :key="user.id" class="hidden md:block">
                <user-list-horizontal
                    :user="user"
                    storeName="top_rated_seller" />
            </div>
            <div v-for="user in topRatedSellerStore.userList?.data.slice(0, 2)" :key="user.id" class="block md:hidden">
                <user-list-horizontal
                    :user="user"
                    storeName="top_rated_seller" />
            </div>
        </div>
    </div>
</template>

<script>
// Libs
import { ref, onMounted } from 'vue';
// Components
import PsLabel from '@template1/vendor/components/core/label/PsLabel.vue';
import PsLabelHeader5 from '@template1/vendor/components/core/label/PsLabelHeader5.vue';
import PsButton from '@template1/vendor/components/core/buttons/PsButton.vue';
import PsRouteLink from '@template1/vendor/components/core/link/PsRouteLink.vue';
import PsIcon from '@template1/vendor/components/core/icons/PsIcon.vue';
import UserListHorizontal from '@template1/vendor/components/modules/user/UserListHorizontal.vue';
// Providers
import { PsValueStore } from "@templateCore/store/modules/core/PsValueStore";
import { useUserListStoreState } from "@templateCore/store/modules/user/UserListStore";

export default {
    name: 'DashboardTopSellerHorizontalList',
    components: {
        PsLabel,
        PsLabelHeader5,
        PsButton,
        PsRouteLink,
        PsIcon,
        UserListHorizontal
    },
    setup() {
        const psValueStore = PsValueStore();
        const loginUserId = psValueStore.getLoginUserId();
        const topRatedSellerStore = useUserListStoreState('top_rated_seller');

        onMounted(() => {
            topRatedSellerStore.resetTopRatedSellerList(loginUserId);
        });

        return {
            topRatedSellerStore
        }
    }

}
</script>
