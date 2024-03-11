<template>
    <Head :title="$t('reported_item_list__reported_item_list')"/>
    <ps-content-container>
        <template #content>
            <div class="mt-32 mb-10">
                <!-- breadcrumb starts-->
                <ps-breadcrumb-2 :items="breadcrumb" class="" />
                <!-- breadcrumb end-->

                <ps-label-header-4 class="font-semibold mt-10">{{ $t('reported_item_list__reported_item_list')}}</ps-label-header-4>

                <!-- start report item -->
                <div class="w-full mt-6">
                    <div v-if="reporteditemProvider.reportedReportedItemList?.data != null">
                        <div class="grid grid-cols-4 sm:grid-cols-4 md:grid-cols-9 lg:grid-cols-12 xl:gap-6 gap-4 sm:gap-6 ">
                            <div class="w-full col-span-4 sm:col-span-2 md:col-span-3 lg:col-span-4 xl:col-span-3" v-for="reportItem in reporteditemProvider.reportedReportedItemList.data" :key="reportItem.id">
                                <!-- <ps-route-link :to="{ name: 'fe_item_detail', query: { item_id: paiditem.item.id   }}"> -->
                                    <item-horizontal-item  :item="reportItem"/>
                                <!-- </ps-route-link> -->
                            </div>
                        </div>
                    </div>
                    <div v-else-if="reporteditemProvider.loading.value == true">
                <div class="grid lg:grid-cols-12 sm:grid-cols-8 grid-cols-4 gap-4 sm:gap-3.5 lg:gap-4">
                 <div class="w-full col-span-4 sm:col-span-2 md:col-span-3 lg:col-span-4 xl:col-span-3" v-for="i in 3" :key="i">
        <item-horizontal-skeletor-item />
      </div>
    </div>
  </div>

                    <div v-else class="flex text-center content-center flex-col">
                        <ps-no-result @handleRefresh="handleRefresh"
                        :name="'list__report_items_no_result'"></ps-no-result>
                    </div>
                </div>

                <ps-button v-if="reporteditemProvider.loading.value == false" class="mx-auto mt-6" @click="loadMore" :class="reporteditemProvider.isNoMoreRecord.value || reporteditemProvider.reportedReportedItemList?.data == null ? 'hidden' : ''">{{ reporteditemProvider.reportedReportedItemList?.data != null ? $t("list__load_more") : $t("refresh") }} </ps-button>
                <ps-button v-else class="mx-auto mt-6" :disabled="true"> {{ $t("list__loading") }}</ps-button>
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

import ItemHorizontalItem from "@template1/vendor/components/modules/item/ItemHorizontalItem.vue";
import ItemHorizontalSkeletorItem from "@template1/vendor/components/modules/item/ItemHorizontalSkeletorItem.vue";
import { PsValueStore } from "@templateCore/store/modules/core/PsValueStore";
import { useReportedItemStoreState } from "@templateCore/store/modules/item/ReportedItemStore";

import PsFrontendLayout from '@template1/vendor/components/layouts/container/PsFrontendLayout.vue';
import PsBreadcrumb2 from '@template1/vendor/components/core/breadcrumbs/PsBreadcrumb2.vue';
// import PsNoData from '@template1/vendor/components/core/noData/PsNoDataList.vue';
import PsNoResult from "@template1/vendor/components/modules/list/PsNoResult.vue";
import { trans } from 'laravel-vue-i18n';

import "vue-skeletor/dist/vue-skeletor.css";

export default {
    name : "ReportedItemListView",
    components : {
        PsContentContainer,
        PsLabelHeader4,
        PsLabel,
        ItemHorizontalItem,
        PsButton,
        PsIcon,
        ItemHorizontalSkeletorItem,
        PsRouteLink,
        PsBreadcrumb2,
        // PsNoData,
        PsNoResult,
        Head
    },
    props: ['mobileSetting'],
    layout: PsFrontendLayout,
    setup(props) {

        const psValueStore = PsValueStore();
        const loginUserId = psValueStore.getLoginUserId();
        // Inject Provider
        const reporteditemProvider = useReportedItemStoreState();
        reporteditemProvider.limit = props.mobileSetting?.default_loading_limit ?? 12;

        // Load Item By login user ID List
        reporteditemProvider.resetReportedItemList(loginUserId);

        // Load User By ID List

        function loadMore() {
            reporteditemProvider.loadReportedItemList(loginUserId);
        }

        function handleRefresh(){
            reporteditemProvider.resetReportedItemList(loginUserId)
        }

        return {
            reporteditemProvider,
            loadMore,
            handleRefresh,
        }
    },
    computed:{
        breadcrumb() {
            return [
                {
                    label: trans('profile_label'),
                    url: route('fe_profile')
                },
                {
                    label: trans('reported_item_list__reported_item_list'),
                    color: "text-fePrimary-500"
                }
            ]
        }
    }
}
</script>
