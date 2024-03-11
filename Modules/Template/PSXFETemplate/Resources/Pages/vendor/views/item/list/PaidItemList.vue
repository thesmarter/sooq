<template>
    <Head :title="$t('profile__fe_paid_item_list')"/>
    <ps-content-container>
        <template #content>
            <div class="mt-32 mb-10">
                <!-- breadcrumb starts-->
                <ps-breadcrumb-2 :items="breadcrumb" class="" />
                <!-- breadcrumb end-->

                <ps-label-header-4 class="font-semibold mt-6">{{ $t('profile__fe_paid_item_list')}}</ps-label-header-4>

                <div class="w-full mt-6">
                    <div v-if="itemProvider.paidAdItemList?.data != null">
                        <div class="grid grid-cols-4 sm:grid-cols-4 md:grid-cols-9 lg:grid-cols-12 xl:gap-6 gap-4 sm:gap-6 ">
                            <div class="w-full col-span-4 sm:col-span-2 md:col-span-3 lg:col-span-4 xl:col-span-3" v-for="paiditem in itemProvider.paidAdItemList.data" :key="paiditem.id">
                                <!-- <ps-route-link :to="{ name: 'fe_item_detail', query: { item_id: paiditem.item.id   }}"> -->
                                    <paid-ad-item-horizontal-item  :paiditem="paiditem" />
                                <!-- </ps-route-link> -->
                            </div>
                        </div>
                    </div>
                    <div v-else-if=" itemProvider.loading.value == true ">
                        <div class="grid lg:grid-cols-12 sm:grid-cols-8 grid-cols-4  gap-4 sm:gap-3.5 lg:gap-4">
                            <div class="w-full col-span-4 sm:col-span-2 md:col-span-3 lg:col-span-4 xl:col-span-3" v-for="i in 3" :key="i">
                                <item-horizontal-skeletor-item />
                            </div>
                        </div>
                    </div>

                    <div v-else class="flex text-center content-center flex-col">
                        <ps-no-data @handleRefresh="handleRefresh"
                        :name="'list__pending_items_no_result'"></ps-no-data>
                    </div>
                </div>

                <ps-button v-if="itemProvider.loading.value == false" class="mx-auto mt-6" @click="loadMore" :class="itemProvider.isNoMoreRecord.value || itemProvider.paidAdItemList?.data == null ? 'hidden' : ''">{{ itemProvider.paidAdItemList?.data != null ? $t("list__load_more") : $t("refresh") }} </ps-button>
                <ps-button v-else class="mx-auto mt-6" @click="loadMore" :disabled="true"> {{ $t("list__loading") }}</ps-button>

            </div>
        </template>
    </ps-content-container>
</template>

<script lang="ts">
import { Head } from '@inertiajs/vue3';
import PsContentContainer from '@template1/vendor/components/layouts/container/PsContentContainer.vue';
import PsLabelHeader4 from '@template1/vendor/components/core/label/PsLabelHeader4.vue';
import PsLabelHeader5 from '@template1/vendor/components/core/label/PsLabelHeader5.vue';
import PsLabel from '@template1/vendor/components/core/label/PsLabel.vue';
import PsButton from '@template1/vendor/components/core/buttons/PsButton.vue';
import PsNoData from '@template1/vendor/components/core/noData/PsNoDataList.vue';
import PsIcon from '@template1/vendor/components/core/icons/PsIcon.vue';
import PsRouteLink from '@template1/vendor/components/core/link/PsRouteLink.vue';
import PsBreadcrumb2 from '@template1/vendor/components/core/breadcrumbs/PsBreadcrumb2.vue';
import PaidAdItemHorizontalItem from "@template1/vendor/components/modules/item/PaidAdItemHorizontalItem.vue";
import ItemHorizontalSkeletorItem from "@template1/vendor/components/modules/item/ItemHorizontalSkeletorItem.vue";
import { PsValueStore } from "@templateCore/store/modules/core/PsValueStore";
import { trans } from 'laravel-vue-i18n';
//Models
import { usePaidAdItemStoreState } from "@templateCore/store/modules/item/PaidAdItemStore";
import PsFrontendLayout from '@template1/vendor/components/layouts/container/PsFrontendLayout.vue';

import "vue-skeletor/dist/vue-skeletor.css";

export default {
    name : "PaidItemListView",
    components : {
        PsContentContainer,
        PsBreadcrumb2,
        PsLabelHeader4,
        PsLabelHeader5,
        PsLabel,
        PsNoData,
        PaidAdItemHorizontalItem,
        PsButton,
        PsIcon,
        ItemHorizontalSkeletorItem,
        PsRouteLink,
        Head
    },
    props: ['mobileSetting'],
    layout: PsFrontendLayout,
    setup(props) {

        const psValueStore = PsValueStore();
        const loginUserId = psValueStore.getLoginUserId();
        // Inject Provider
        const itemProvider = usePaidAdItemStoreState();
        itemProvider.limit = props.mobileSetting?.default_loading_limit ?? 12;

        itemProvider.resetPaidAdItemList(loginUserId);

        // Load User By ID List
        // breadcrumbuserProvider.getUser(loginUserId);

        function loadMore() {
            itemProvider.loadPaidAdItemList(loginUserId);
        }

        function handleRefresh() {
            itemProvider.resetPaidAdItemList(loginUserId);
        }


        return {
            itemProvider,
            loadMore,
            handleRefresh
            // breadcrumbuserProvider,
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
                    label: trans('profile__fe_paid_item_list'),
                    color: "text-fePrimary-500"
                }
            ]
        }
    },
}
</script>
