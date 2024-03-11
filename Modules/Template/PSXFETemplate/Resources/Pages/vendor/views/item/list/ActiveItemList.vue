<template>
    <Head :title="$t('list__active_items_list')"/>
    <ps-content-container>
        <template #content>
            <div class="mt-32">

                <div class="">
                    <ps-breadcrumb-2 :items="breadcrumb" class="" />
                </div>

                <div class="lg:flex md:flex lg:justify-between md:justify-between mt-6">
                    <ps-label-header-4 class="font-medium"> {{ $t("active_item_list__active_item_list") }}
                    </ps-label-header-4>
                </div>


                <div class="flex flex-col mt-6">



                    <div class='flex flex-row mb-8 '>
                        <div class="w-full flex flex-col">
                            <!-- paid & promote ads -->

                            <div v-if="itemactiveProvider.itemList?.data != null">
                                <div class="grid grid-cols-4 sm:grid-cols-4 md:grid-cols-9 lg:grid-cols-12 md:gap-6 gap-4 ">

                                    <div class="w-full col-span-4 sm:col-span-2 md:col-span-3 lg:col-span-4 xl:col-span-3"
                                        v-for="item in itemactiveProvider.itemList.data" :key="item.id">


                                            <item-horizontal-item :item="item" storeName="active"/>


                                    </div>

                                </div>
                            </div>
                            <div v-else-if="itemactiveProvider.loading.value == true">
                                <div
                                    class="w-full grid lg:grid-cols-12 sm:grid-cols-8 grid-cols-4  gap-4 sm:gap-3.5 lg:gap-4">

                                    <div class="w-full col-span-2 lg:col-span-3" v-for="i in 3" :key="i">

                                        <item-horizontal-skeletor-item />

                                    </div>

                                </div>
                            </div>
                            <div v-else class="flex justify-center flex flex-col">

                                <ps-no-data @handleRefresh="handleRefresh"
                                    :name="'list__active_items_no_result'"></ps-no-data>

                            </div>
                            <!-- end paid ads -->

                        </div>


                    </div>
                    <div class="mb-10">
                    <ps-button v-if="itemactiveProvider.loading.value == false" class="w-60 mx-auto mt-8" @click="loadMore"
                        :class="itemactiveProvider.isNoMoreRecord.value == true || itemactiveProvider.itemList?.data == null ? 'hidden' : ''">
                        {{ $t("list__load_more") }} </ps-button>
                    <ps-button v-else class="w-60 mx-auto mt-8" @click="loadMore" :disabled="true"> {{ $t("list__loading")
                    }} </ps-button>
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
import PsLabel from '@template1/vendor/components/core/label/PsLabel.vue';
import PsButton from '@template1/vendor/components/core/buttons/PsButton.vue';
import PsNoData from '@template1/vendor/components/core/noData/PsNoDataList.vue';
import PsIcon from '@template1/vendor/components/core/icons/PsIcon.vue';
import PsRouteLink from '@template1/vendor/components/core/link/PsRouteLink.vue';

import ItemHorizontalItem from "@template1/vendor/components/modules/item/ItemHorizontalItem.vue";
import ItemHorizontalSkeletorItem from "@template1/vendor/components/modules/item/ItemHorizontalSkeletorItem.vue";
import { PsValueStore } from "@templateCore/store/modules/core/PsValueStore";
//Models
import { useProductStore } from "@templateCore/store/modules/item/ProductStore";

import ProductParameterHolder from "@templateCore/object/holder/ProductParameterHolder";
import PsFrontendLayout from '@template1/vendor/components/layouts/container/PsFrontendLayout.vue';

import "vue-skeletor/dist/vue-skeletor.css";
import PsBreadcrumb2 from "@template1/vendor/components/core/breadcrumbs/PsBreadcrumb2.vue";
import { trans } from 'laravel-vue-i18n';

export default {
    name: "ActiveItemListView",
    components: {
        PsContentContainer,
        PsLabelHeader4,
        PsLabel,
        ItemHorizontalItem,
        PsButton,
        PsIcon,
        ItemHorizontalSkeletorItem,
        PsRouteLink,
        PsBreadcrumb2,
        PsNoData,
        Head
    },
    props: ['mobileSetting'],
    layout: PsFrontendLayout,
    setup(props) {

        const psValueStore = PsValueStore();
        const loginUserId = psValueStore.getLoginUserId();
        // Inject Provider
        const itemactiveProvider = useProductStore('active');

        itemactiveProvider.limit = props.mobileSetting?.default_loading_limit ?? 12;

        // Load Item List
        const holder = new ProductParameterHolder().resetParameterHolder();
        holder.addedUserId = loginUserId;
        itemactiveProvider.resetProductList(loginUserId, holder);

        // Load User By ID List
        // breadcrumbuserProvider.getUser(loginUserId);

        function loadMore() {
            itemactiveProvider.loadItemList(loginUserId, holder);
        }

        function handleRefresh() {
            itemactiveProvider.resetProductList(loginUserId, holder);
        }

        return {
            itemactiveProvider,
            loadMore,
            handleRefresh,
            // breadcrumbuserProvider,
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
                    label: trans('list__active_items_list'),
                    color: "text-fePrimary-500"
                }
            ]
        }
    },
}
</script>
