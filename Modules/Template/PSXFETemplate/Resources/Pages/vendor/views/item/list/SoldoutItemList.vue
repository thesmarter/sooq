<template>
    <Head :title="$t('soldout_item_list__soldout_item_list')"/>
    <ps-content-container>
        <template #content>
            <div class="mt-32 mb-10">
                <!-- breadcrumb starts-->
                <ps-breadcrumb-2 :items="breadcrumb" class="" />
                <!-- breadcrumb end-->
                <ps-label-header-4 class="font-semibold mt-6">{{ $t('profile__sold_out_item_list')}}</ps-label-header-4>

                <div class="w-full mt-6">
                    <div v-if="itemSoldoutProvider.itemList?.data != null">
                        <div class="grid grid-cols-4 sm:grid-cols-4 md:grid-cols-9 lg:grid-cols-12 xl:gap-6 gap-4 sm:gap-6 ">
                            <div class="w-full col-span-4 sm:col-span-2 md:col-span-3 lg:col-span-4 xl:col-span-3" v-for="item in itemSoldoutProvider.itemList.data" :key="item.id">
                                <!-- <ps-route-link :to="{ name: 'fe_item_detail', query: { item_id: paiditem.item.id   }}"> -->
                                    <item-horizontal-item  :item="item" storeName="sold_out" />
                                <!-- </ps-route-link> -->
                            </div>
                        </div>
                    </div>
                    <div v-else-if=" itemSoldoutProvider.loading.value == true ">
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

                <ps-button v-if="itemSoldoutProvider.loading.value == false" class="mx-auto mt-6" @click="loadMore" :class="itemSoldoutProvider.isNoMoreRecord.value || itemSoldoutProvider.itemList?.data == null ? 'hidden' : ''">{{ itemSoldoutProvider.itemList?.data != null ? $t("list__load_more") : $t("refresh") }} </ps-button>
                <ps-button v-else class="mx-auto mt-6" @click="loadMore" :disabled="true"> {{ $t("list__loading") }}</ps-button>

                <!-- <div class="flex flex-col items-start mt-8">
                    <div class='w-full flex flex-col items-start ' >
                        <div class="w-full">
                            <div v-if="itemSoldoutProvider.itemList?.data != null">
                                <div class="grid lg:grid-cols-12 sm:grid-cols-8 grid-cols-4  gap-4 sm:gap-3.5 lg:gap-4">
                                    <div class="w-full col-span-2 lg:col-span-3" v-for="item in itemSoldoutProvider.itemList.data" :key="item.id">
                                        <ps-route-link :to="{ name: 'fe_item_detail', query: { item_id: item.id   }}">
                                            <item-horizontal-item  :item="item" storeName="sold_out" />
                                        </ps-route-link>
                                    </div>
                                </div>
                            </div>
                            <div v-else-if=" itemSoldoutProvider.loading.value ==true">
                                <div class="grid lg:grid-cols-12 sm:grid-cols-8 grid-cols-4  gap-4 sm:gap-3.5 lg:gap-4">
                                    <div class="w-full col-span-2 lg:col-span-3" v-for="i in 3" :key="i">
                                        <item-horizontal-skeletor-item />
                                    </div>
                                </div>
                            </div>
                            <div v-else class="w-full flex justify-center">
                                <ps-label textColor="text-feSecondary-500 dark:text-feAchromatic-50 lg:text-xl sm:text-lg text-base font-medium" class="mt-10 flex-grow-0 mx-auto"> {{ $t("list__no_result") }} </ps-label>
                            </div>
                        </div>

                        <ps-button v-if="itemSoldoutProvider.loading.value == false" class="w-60 mx-auto mt-8 text-center" @click="loadMore" :class="itemSoldoutProvider.isNoMoreRecord.value ? 'hidden' : ''"> {{ $t("list__load_more") }} </ps-button>
                        <ps-button v-else class="w-60 mx-auto mt-8 text-center" @click="loadMore" :disabled="true"> {{ $t("list__loading") }} </ps-button>

                    </div>
                </div> -->
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

import ItemHorizontalItem from "@template1/vendor/components/modules/item/ItemHorizontalItem.vue";
import ItemHorizontalSkeletorItem from "@template1/vendor/components/modules/item/ItemHorizontalSkeletorItem.vue";
import { PsValueStore } from "@templateCore/store/modules/core/PsValueStore";
//Models
import { useProductStore } from "@templateCore/store/modules/item/ProductStore";

import ProductParameterHolder from "@templateCore/object/holder/ProductParameterHolder";
import PsFrontendLayout from '@template1/vendor/components/layouts/container/PsFrontendLayout.vue';
import { trans } from 'laravel-vue-i18n';

import "vue-skeletor/dist/vue-skeletor.css";

export default {
    name : "RejectItemListView",
    components : {
        PsContentContainer,
        PsLabelHeader4,
        PsBreadcrumb2,
        PsLabel,
        ItemHorizontalItem,
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
        const itemSoldoutProvider = useProductStore('sold_out');
        // const breadcrumbuserProvider = useUserStore();
        itemSoldoutProvider.limit = props.mobileSetting?.default_loading_limit ?? 12;

        // Load Item List
        const holder = new ProductParameterHolder().getSoldoutProductParameterHolder();
        itemSoldoutProvider.paramHolder = new ProductParameterHolder().getSoldoutProductParameterHolder();
        holder.addedUserId = loginUserId;
        itemSoldoutProvider.resetProductList(loginUserId, holder);


        function loadMore() {
            itemSoldoutProvider.loadItemList(loginUserId, holder);
        }


        return {
            itemSoldoutProvider,
            loadMore,
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
                    label: trans('profile__sold_out_item_list'),
                    color: "text-fePrimary-500"
                }
            ]
        }
    },
}
</script>
