<template>
    <div v-if="itemStore.itemList?.data != null" class="w-full col-span-4 sm:col-span-6 lg:col-span-9">
        <div class="w-full sm:mt-2">
            <ps-label-header-4 class="mt-8 mb-6 sm:mt-0 font-medium"> {{ userStore.user.data ? userStore.user.data.userName+ "'s" :'' }} {{ $t("other_profile__active_post") }} </ps-label-header-4>
            <div class="w-full h-full grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3  gap-6">
                <!-- Active Listing -->
                <div class=" w-full h-full"
                    v-for="item in itemStore.itemList.data"
                    :key="item.id">
                    <ps-route-link :to="{ name: 'item', params: { itemName: item.title.split(' ').join('-').toLowerCase(),imgName: item.defaultPhoto.imgPath  }, query: { item_id: item.id, item_name: item.title.split(' ').join('-').toLowerCase()   }}">
                        <item-horizontal-item  :item="item" />
                    </ps-route-link>
                </div>
                <!-- END Active Listing -->
            </div>

        </div>

        <div v-if="itemStore.isNoMoreRecord.value == false" class="flex justify-center mt-6 mb-4">
            <ps-button v-if="itemStore.loading.value == false" @click.prevent="loadMoreActivePost" class="flex flex-row " theme="bg-fePrimary-500 dark:bg-feAccent-500 text-feAchromatic-50 dark:text-feAchromatic-900 capitalize px-4 py-1">
                    {{ $t("blog_list__load_more") }}
            </ps-button>
            <ps-button v-else class="mx-auto mt-8" :disabled="true">{{ $t("list__loading") }}</ps-button>
        </div>

        <!-- google adsense desktop view -->
        <div class="mx-auto">
            <ps-ad-sense adFormat="horizontal"></ps-ad-sense>
        </div>

    </div>
    <div v-else-if="itemStore.itemList != null && itemStore.loading.value " class="w-full col-span-4 sm:col-span-6 lg:col-span-9 mt-4  mb-6">
        <div class="w-full">
            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3  gap-6 ">
                <div class="w-full" v-for="i in 6" :key="i">
                    <item-horizontal-skeletor-item />
                </div>
            </div>
        </div>
    </div>
    <div v-else class="col-span-4 sm:col-span-6 lg:col-span-9 mt-2  mb-6 w-full flex justify-center">
        <ps-label textColor="text-feSecondary-500 dark:text-feAchromatic-50 lg:text-xl sm:text-lg text-base font-medium" class="mt-10 flex-grow-0 mx-auto"> {{ $t("list__no_result") }} </ps-label>
    </div>
</template>

<script>

//libs
import { onMounted} from 'vue';
//components
import PsLabelHeader4 from '@template1/vendor/components/core/label/PsLabelHeader4.vue';
import PsLabel from '@template1/vendor/components/core/label/PsLabel.vue';
import PsButton from '@template1/vendor/components/core/buttons/PsButton.vue';
import PsRouteLink from '@template1/vendor/components/core/link/PsRouteLink.vue';
import PsAdSense from '@template1/vendor/components/core/adsense/PsAdSense.vue';
import ItemHorizontalItem from "@template1/vendor/components/modules/item/ItemHorizontalItem.vue";
import ItemHorizontalSkeletorItem from "@template1/vendor/components/modules/item/ItemHorizontalSkeletorItem.vue";
//store
import { useProductStore } from "@templateCore/store/modules/item/ProductStore";
import { useUserStore } from "@templateCore/store/modules/user/UserStore";
//holder
import ProductParameterHolder from "@templateCore/object/holder/ProductParameterHolder";

export default {
    name : "ProfileLatestItemGrid",
    components : {
        PsLabelHeader4,
        PsLabel,
        PsButton,
        PsRouteLink,
        PsAdSense,
        ItemHorizontalItem,
        ItemHorizontalSkeletorItem
    },
    props : {
        userId : {
            type: String
        }
    },
    setup(props) {
        
        const itemStore = useProductStore();
        let userStore = useUserStore('other');
        itemStore.paramHolder = new ProductParameterHolder().getLatestParameterHolder();
        itemStore.paramHolder.addedUserId = props.userId;

        function loadMoreActivePost(){
            itemStore.loadItemList(props.userId, itemStore.paramHolder);
        }

        onMounted(async () => {
            setTimeout(async ()=>{
            // console.log(window.popStateDetected);
                if(!window.popStateDetected) {
                    itemStore.resetProductList(props.userId, itemStore.paramHolder);
                } else {
                    window.popStateDetected = false;
                }
            },1000);        
        });

        return {
            itemStore,
            userStore,
            loadMoreActivePost
        }
        
    },
}
</script>
