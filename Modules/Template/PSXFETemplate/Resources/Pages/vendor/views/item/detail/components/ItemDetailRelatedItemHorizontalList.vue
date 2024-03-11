<template>
    <div class="">
        <div v-if="relatedItemStore.loading.value || relatedItemStore.itemList?.data != null">
            <div class="flex justify-between items-center mt-10">
                <ps-label textColor="text-2xl font-semibold text-feSecondary-800 dark:text-feSecondary-50">{{
                    $t("core__fe_related_items") }}</ps-label>
                <ps-route-link :to="{
                    name: 'fe_item_list', params: {
                        cat_id: productStore.item?.data?.category?.catId,
                        cat_name: productStore.item?.data?.category?.catName,
                        status: 1
                    }
                }">
                    <ps-button class="flex flex-row" padding="p-2 sm:px-4 sm:py-2" shadow="shadow-sm"
                        rounded="rounded" hover="" focus=""
                        border="border border-feSecondary-200 dark:border-feSecondary-800"
                        colors="bg-feAchromatic-50 text-feSecondary-800 dark:bg-feSecondary-800 dark:text-feSecondary-200">
                        <ps-label class="hidden sm:inline">{{ $t("list_fe__view_all_label") }}</ps-label>
                        <ps-icon class="sm:ms-2" textColor="dark:text-feSecondary-200" name="arrowNarrowRight"
                            h="23" w="23" viewBox="0 -3 9 20" />
                    </ps-button>
                </ps-route-link>
            </div>
            <div class="mt-6 mb-26 sm:mb-0">
                <div v-if="relatedItemStore.itemList?.data != null" class="">

                    <item-horizontal-swiper
                        :itemList="relatedItemStore.itemList?.data.filter((item) => item.id !== itemId)"
                        storeName="related_item"></item-horizontal-swiper>
                </div>
                <div v-else-if="relatedItemStore.loading.value">
                    <div class="grid lg:grid-cols-12 sm:grid-cols-8 grid-cols-4  gap-4 sm:gap-3.5 lg:gap-4">
                        <div class="w-full col-span-2 lg:col-span-3" v-for="i in 4" :key="i">
                            <item-horizontal-skeletor-item />
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- End related items -->
    </div>
</template>

<script>
    import PsLabel from '@template1/vendor/components/core/label/PsLabel.vue';
    import PsIcon from '@template1/vendor/components/core/icons/PsIcon.vue';
    import PsRouteLink from '@template1/vendor/components/core/link/PsRouteLink.vue';
    import PsButton from '@template1/vendor/components/core/buttons/PsButton.vue';
    import ItemHorizontalSwiper from "@template1/vendor/components/modules/item/ItemHorizontalSwiper.vue";
    import ItemHorizontalSkeletorItem from "@template1/vendor/components/modules/item/ItemHorizontalSkeletorItem.vue"

    import { useProductStore } from '@templateCore/store/modules/item/ProductStore';

    export default {
        name : "RelatedItem",
        components : {
            PsLabel,
            PsIcon,
            PsButton,
            PsRouteLink,
            ItemHorizontalSwiper,
            ItemHorizontalSkeletorItem
        },
        setup(){
            const relatedItemStore = useProductStore('related_item');
            const productStore = useProductStore('detail');

            return {
                relatedItemStore,
                productStore
            }
        }
    }

</script>