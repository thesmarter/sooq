<template>
    <Head :title="$t('profile__favourite_item_list')"/>
  <ps-content-container>
    <template #content>
      <div class="mt-32 mb-10">

            <!-- breadcrumb start -->
            <ps-breadcrumb-2 :items="breadcrumb"></ps-breadcrumb-2>
            <!-- breadcrumb end -->

            <!-- header label start -->
            <ps-label-header-4 class="font-semibold mt-6">{{ $t('profile__favourite_item_list') }}</ps-label-header-4>
            <!-- header label end -->

            <div class="w-full mt-6">
                    <div v-if="favouriteitemProvider.favouriteItemList?.data != null">
                        <div class="grid grid-cols-4 sm:grid-cols-4 md:grid-cols-9 lg:grid-cols-12 xl:gap-6 gap-4 sm:gap-6 ">
                            <div class="w-full col-span-4 sm:col-span-2 md:col-span-3 lg:col-span-4 xl:col-span-3" v-for="favouriteItem in favouriteitemProvider.favouriteItemList.data" :key="favouriteItem.id">
                                <!-- <ps-route-link
                                    :to="{
                                        name: 'fe_item_detail',
                                        query: { item_id: favouriteItem.id },
                                    }"
                                >
                                </ps-route-link> -->
                                    <item-horizontal-item :item="favouriteItem" storeName="favourite"/>
                            </div>
                        </div>
                    </div>
                    <div v-else-if="favouriteitemProvider.loading.value == true ">
                        <div class="grid lg:grid-cols-12 sm:grid-cols-8 grid-cols-4  gap-4 sm:gap-3.5 lg:gap-4">
                            <div class="w-full col-span-4 sm:col-span-2 md:col-span-3 lg:col-span-4 xl:col-span-3" v-for="i in 3" :key="i">
                                <item-horizontal-skeletor-item />
                            </div>
                        </div>
                    </div>

                    <div v-else class="flex text-center content-center flex-col">
                        <ps-no-data @handleRefresh="handleRefresh"
                        :name="'list__favourite_items_no_result'"></ps-no-data>
                    </div>

                    <ps-button v-if="favouriteitemProvider.loading.value == false" class="mx-auto mt-6" @click="loadMore" :class="favouriteitemProvider.isNoMoreRecord.value || favouriteitemProvider.favouriteItemList?.data == null ? 'hidden' : ''">{{ favouriteitemProvider.favouriteItemList?.data != null ? $t("list__load_more") : $t("refresh") }} </ps-button>
                    <ps-button v-else class="mx-auto mt-6" @click="loadMore" :disabled="true"> {{ $t("list__loading") }}</ps-button>
            </div>

      </div>
    </template>
  </ps-content-container>
</template>

<script lang='ts'>
import { Head } from "@inertiajs/vue3";
import PsContentContainer from "@template1/vendor/components/layouts/container/PsContentContainer.vue";
import PsLabelHeader4 from "@template1/vendor/components/core/label/PsLabelHeader4.vue";
import PsButton from "@template1/vendor/components/core/buttons/PsButton.vue";
import PsLabel from "@template1/vendor/components/core/label/PsLabel.vue";
import PsIcon from "@template1/vendor/components/core/icons/PsIcon.vue";
import PsRouteLink from "@template1/vendor/components/core/link/PsRouteLink.vue";
import PsBreadcrumb2 from "@template1/vendor/components/core/breadcrumbs/PsBreadcrumb2.vue";
import PsLabelHeader5 from '@template1/vendor/components/core/label/PsLabelHeader5.vue';
import PsNoData from '@template1/vendor/components/core/noData/PsNoDataList.vue';
import { trans } from 'laravel-vue-i18n';

import { PsValueStore } from "@templateCore/store/modules/core/PsValueStore";

import { useFavouriteItemStoreState } from "@templateCore/store/modules/item/FavouriteItemStore";
import ItemHorizontalSkeletorItem from "@template1/vendor/components/modules/item/ItemHorizontalSkeletorItem.vue";
import ItemHorizontalItem from "@template1/vendor/components/modules/item/ItemHorizontalItem.vue";
import PsFrontendLayout from "@template1/vendor/components/layouts/container/PsFrontendLayout.vue";

import "vue-skeletor/dist/vue-skeletor.css";
export default {
  name: "FavouriteListView",
  components: {
    PsContentContainer,
    PsLabelHeader4,
    PsButton,
    ItemHorizontalSkeletorItem,
    ItemHorizontalItem,
    PsLabel,
    PsIcon,
    PsRouteLink,
    PsBreadcrumb2,
    PsLabelHeader5,
    PsNoData,
  },
  props: ['mobileSetting'],
  layout: PsFrontendLayout,
  setup(props) {
    const psValueStore = PsValueStore();
    // Inject Item Provider
    const favouriteitemProvider = useFavouriteItemStoreState('favourite');

    favouriteitemProvider.limit = props.mobileSetting?.default_loading_limit ?? 12;
    const loginUserId = psValueStore.getLoginUserId();
    // Load Data
    favouriteitemProvider.resetFavouriteItemList(loginUserId);

    // Load User By ID List
    // breadcrumbuserProvider.getUser(loginUserId);

    function loadMore() {
      favouriteitemProvider.loadFavouriteItemList(loginUserId);
    }

    function handleRefresh(){
        favouriteitemProvider.resetFavouriteItemList(loginUserId);
    }

    return {
      favouriteitemProvider,
      loadMore,
      handleRefresh,
      // breadcrumbuserProvider
    };
  },
  computed: {
    breadcrumb() {
      return [
        {
          label: trans("profile_label"),
          url: route("fe_profile"),
        },
        {
          label: trans("profile__favourite_item_list"),
          color: "text-fePrimary-500",
        },
      ];
    },
  },
};
</script>
