<template>
    <ps-content-container v-if="categoryStore.hasData() || categoryStore.loading.value" class="mt-10">
            <template #content>
                <div class="flex justify-between items-center mb-6">
                    <ps-label-header-5 class="font-semibold">{{ $t("home__categories_label") }}</ps-label-header-5>
                    <ps-route-link :to="{ name: 'fe_category.index' }">
                        <ps-button class="flex flex-row" padding="p-2 sm:px-4 sm:py-2" shadow="shadow-sm" rounded="rounded"
                            hover="" focus="" border="border border-feSecondary-200 dark:border-feSecondary-800"
                            colors="bg-feAchromatic-50 text-feSecondary-800 dark:bg-feSecondary-800 dark:text-feSecondary-200">
                            <ps-label class="hidden sm:inline">{{ $t("list_fe__view_all_label") }}</ps-label>
                            <ps-icon class="sm:ms-2 block rtl:hidden" textColor="dark:text-feSecondary-200"
                                name="rightChervon" h="24" w="24" />
                            <ps-icon class="sm:ms-2 hidden rtl:block" textColor="dark:text-feSecondary-200"
                                name="leftChervon" h="24" w="24" />
                        </ps-button>
                    </ps-route-link>
                </div>
                <div class="grid grid-cols-4 sm:grid-cols-4 md:grid-cols-9 lg:grid-cols-12 xl:gap-6 gap-4 sm:gap-6 "
                    v-if="categoryStore.hasData()">
                    <div v-for="category in categoryStore.itemList.data.slice(0, 8)" :key="category.catId"
                        class="w-full col-span-2 md:col-span-3 lg:col-span-4 xl:col-span-3 sm:block hidden">
                        <ps-route-link :to="{
                            name: showSubCat ? 'fe_sub_category' : 'fe_item_list',
                            query: {
                                cat_id: category.catId,
                                cat_name: category.catName,
                                status: 1
                            }
                        }" @click="updateCategoryTouchCount(category.catId)">
                            <category-horizontal-item :category="category" />
                        </ps-route-link>
                    </div>
                    <div v-for="category in categoryStore.itemList.data.slice(0, 4)" :key="category.catId"
                        class="w-full col-span-2 md:col-span-3 lg:col-span-4 xl:col-span-3 block sm:hidden">
                        <ps-route-link :to="{
                            name: showSubCat ? 'fe_sub_category' : 'fe_item_list',
                            query: {
                                cat_id: category.catId,
                                cat_name: category.catName,
                                status: 1
                            }
                        }" @click="updateCategoryTouchCount(category.catId)">
                            <category-horizontal-item :category="category" />
                        </ps-route-link>
                    </div>
                </div>
                <div v-else class="grid grid-cols-4 sm:grid-cols-4 md:grid-cols-9 lg:grid-cols-12 xl:gap-6 gap-4 sm:gap-6 ">
                    <div v-for="category in 10" :key="category"
                        class="w-full col-span-2 md:col-span-3 lg:col-span-4 xl:col-span-3 sm:block hidden">
                        <category-horizontal-skeletor-item />
                    </div>
                </div>
            </template>
        </ps-content-container>
</template>

<script>
// Libs
import { onMounted } from 'vue';
// Components
import PsLabel from '@template1/vendor/components/core/label/PsLabel.vue';
import PsLabelHeader5 from '@template1/vendor/components/core/label/PsLabelHeader5.vue';
import PsButton from '@template1/vendor/components/core/buttons/PsButton.vue';
import PsRouteLink from '@template1/vendor/components/core/link/PsRouteLink.vue';
import PsIcon from '@template1/vendor/components/core/icons/PsIcon.vue';
import CategoryHorizontalItem from "@template1/vendor/components/modules/category/CategoryHorizontalItem.vue";
import CategoryHorizontalSkeletorItem from "@template1/vendor/components/modules/category/CategoryHorizontalSkeletorItem.vue";
import PsContentContainer from '@template1/vendor/components/layouts/container/PsContentContainer.vue';
// Providers
import { PsValueStore } from "@templateCore/store/modules/core/PsValueStore";
import { useCategoryStoreState } from "@templateCore/store/modules/category/CategoryStore";
import { useTouchCountStoreState } from '@templateCore/store/modules/category/TouchCountStore';
//Holder
import TouchCountParameterHolder from '@templateCore/object/holder/TouchCountParameterHolder';

export default {
    name: 'DashboardCategoryHorizontalList',
    components: {
        PsContentContainer,
        PsLabel,
        PsLabelHeader5,
        PsButton,
        PsRouteLink,
        PsIcon,
        CategoryHorizontalItem,
        CategoryHorizontalSkeletorItem
    },
    props: {
        limit: {
            type: Number,
            default: 12
        },
        showSubCat: {
            type: Boolean,
            default: true
        }
    },
    setup(props) {
        const psValueStore = PsValueStore();
        const loginUserId = psValueStore.getLoginUserId();

        //touch count
        const touchCountStore = useTouchCountStoreState();
        const touchCountHolder = new TouchCountParameterHolder();
        touchCountHolder.typeName = 'category';
        touchCountHolder.userId = loginUserId;

        //category
        const categoryStore = useCategoryStoreState('dashboard');
        categoryStore.limit = props.limit;
        categoryStore.paramHolder.keyword = '';
        categoryStore.paramHolder.orderType = 'desc';
        categoryStore.paramHolder.orderBy = 'name';

        function updateCategoryTouchCount(catId) {
            touchCountHolder.typeId = catId;
            touchCountStore.postTouchCount(loginUserId, touchCountHolder);
        }

        onMounted(() => {
            categoryStore.resetCategoryList(loginUserId, categoryStore.paramHolder);
        });

        return {
            categoryStore,
            updateCategoryTouchCount
        }
    }

}
</script>
