<template>
    <Head :title="$t('follower_item_list__follower_item_list')"/>
    <ps-content-container>
        <template #content>
            <div class="sm:mt-32 lg:mt-36 mt-28">

                <div class="flex items-center mt-24">
                    <ps-breadcrumb-2 :items="breadcrumb" class="mb-4 sm:mb-6" />
                </div>

                <div class="pt-1 lg:flex md:flex lg:justify-between md:justify-between mb-2">
                    <ps-label-header-4 class=" mb-4 font-medium">
                        {{ $t("follower_item_list__follower_item_list") }}
                    </ps-label-header-4>
                </div>

                <div class="flex flex-col">


                    <div class='flex flex-col mb-8' >
                        <div class="w-full flex flex-col">
                            <!-- paid & promote ads -->
                            <div v-if="itemFollowerProvider.itemList?.data != null">
                                <div class="grid grid-cols-4 sm:grid-cols-4 md:grid-cols-9 lg:grid-cols-12 md:gap-6 gap-4 ">

                                    <div class="w-full col-span-4 sm:col-span-2 md:col-span-3 lg:col-span-4 xl:col-span-3"
                                        v-for="item in itemFollowerProvider.itemList.data" :key="item.id">

                                       <!-- <ps-route-link :to="{ name: 'fe_item_detail', query: { item_id: item.id   }}"> -->
                                            <item-horizontal-item  :item="item" />
                                        <!-- </ps-route-link> -->

                                    </div>

                                </div>
                            </div>
                            <div v-else-if=" itemFollowerProvider.loading.value == true ">
                                <div class="grid grid-cols-4 sm:grid-cols-4 md:grid-cols-9 lg:grid-cols-12 md:gap-6 gap-4 ">

                                    <div class="w-full col-span-4 sm:col-span-2 md:col-span-3 lg:col-span-4 xl:col-span-3" v-for="i in 3" :key="i">

                                        <item-horizontal-skeletor-item />

                                    </div>

                                </div>
                            </div>
                            <div v-else class="flex text-center content-center flex-col">
                                <ps-no-result v-if="itemFollowerProvider.loading.value == false && itemFollowerProvider.itemList?.data == null" @onClick="loadMore" />
                            </div>
                            <!-- end paid ads -->

                        </div>

                        <ps-button v-if="itemFollowerProvider.loading.value == false" class="mx-auto mt-8" @click="loadMore" :class="itemFollowerProvider.isNoMoreRecord.value ? 'hidden' : ''"> {{ $t("list__load_more") }} </ps-button>
                        <ps-button v-else class="mx-auto mt-8" @click="loadMore" :disabled="true"> {{ $t("list__loading") }} </ps-button>

                    </div>
                </div>
            </div>
        </template>
    </ps-content-container>
</template>

<script lang="ts">
import { Head } from '@inertiajs/vue3';
import PsContentContainer from '@template1/vendor/components/layouts/container/PsContentContainer.vue';
import PsLabelHeader4 from '@template1/vendor/components/core/label/PsLabelHeader4.vue';
import PsLabel from '@template1/vendor/components/core/label/PsLabel.vue'
import PsButton from '@template1/vendor/components/core/buttons/PsButton.vue';
import PsIcon from '@template1/vendor/components/core/icons/PsIcon.vue';
import PsRouteLink from '@template1/vendor/components/core/link/PsRouteLink.vue';
import PsNoResult from "@template1/vendor/components/modules/list/PsNoResult.vue";

import ItemHorizontalItem from "@template1/vendor/components/modules/item/ItemHorizontalItem.vue";
import ItemHorizontalSkeletorItem from "@template1/vendor/components/modules/item/ItemHorizontalSkeletorItem.vue";
import FollowItemParameterHolder from '@templateCore/object/holder/FollowItemParameterHolder';
import { PsValueStore } from "@templateCore/store/modules/core/PsValueStore";
//Models
import { useFollowerItemStoreState } from "@templateCore/store/modules/item/FollowerItemStore";
// import { useUserStore } from "@templateCore/store/modules/user/UserStore";
import PsFrontendLayout from '@template1/vendor/components/layouts/container/PsFrontendLayout.vue';
import PsBreadcrumb2 from "@template1/vendor/components/core/breadcrumbs/PsBreadcrumb2.vue";
import "vue-skeletor/dist/vue-skeletor.css";
import { trans } from 'laravel-vue-i18n';


export default {
    name : "FollowerItemListView",
    components : {
        PsContentContainer,
        PsLabelHeader4,
        PsLabel,
        PsNoResult,
        ItemHorizontalItem,
        PsButton,
        PsIcon,
        PsBreadcrumb2,
        ItemHorizontalSkeletorItem,
        PsRouteLink,
        Head
    },
    props: ['mobileSetting'],
    layout: PsFrontendLayout,
    setup(props) {

        const psValueStore = PsValueStore();
        const loginUserId = psValueStore.getLoginUserId();
        const holder = new FollowItemParameterHolder();

        // Inject Provider
        const itemFollowerProvider = useFollowerItemStoreState();
        // const breadcrumbuserProvider = useUserStore();
        holder.itemLocationId = psValueStore.locationId;
        holder.itemLocationTownship = psValueStore.subLocationId;

        itemFollowerProvider.limit = props.mobileSetting?.default_loading_limit ?? 12;
        // Load Item List
        itemFollowerProvider.loadItemList(loginUserId, holder);

        // Load User By ID List
        // breadcrumbuserProvider.loadUser(loginUserId);


        function loadMore() {
            itemFollowerProvider.loadItemList(loginUserId, holder);
        }

        function handleRefresh() {
            itemFollowerProvider.resetProductList(loginUserId, holder);
        }

        return {
            itemFollowerProvider,
            loadMore,
            // breadcrumbuserProvider,
            handleRefresh,
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
                    label: trans('follower_item_list__follower_item_list'),
                    color: "text-fePrimary-500"
                }
            ]
        }
    },
}
</script>
