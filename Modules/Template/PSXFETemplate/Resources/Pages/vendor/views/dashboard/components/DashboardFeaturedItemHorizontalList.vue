<template>
     <div class="xl:w-feLarge lg:w-large md:w-full md:px-6 lg:px-0 mx-auto">
            <!-- Start feature items -->
            <div
                class="bg-feSecondary-50 dark:bg-feAchromatic-900 flex flex-col sm:flex-row justify-between gap-8 sm:gap-5 mt-10 px-4 py-6 sm:p-10">
                <div class="md:w-64 flex flex-col gap-4">
                    <div class="md:w-64 flex justify-between items-center">
                        <ps-label-header-5 class="font-semibold" textColor="">{{ $t("home__fe_featured_items")
                        }}</ps-label-header-5>
                        <ps-route-link :to="{ name: 'fe_item_list', query: paidParams['query'] }" class="block sm:hidden">
                            <ps-button class="flex flex-row" padding="p-2 sm:px-4 sm:py-2" shadow="shadow-sm"
                                rounded="rounded" hover="" focus=""
                                border="border border-feSecondary-200 dark:border-fePrimary-500"
                                colors="bg-fePrimary-500 text-feSecondary-50">
                                <ps-icon class=" block rtl:hidden" name="rightChervon" h="24" w="24" />
                                <ps-icon class=" hidden rtl:block" name="leftChervon" h="24" w="24" />
                            </ps-button>
                        </ps-route-link>
                    </div>
                    <div class="mt-4 sm:mt-0">
                        <ps-label class="text-base sm:text-lg font-normal" textColor="dark:text-feSecondary-300">{{
                            $t("home__fe_featured_items__desc") }}</ps-label>
                    </div>
                    <ps-route-link :to="{ name: 'fe_item_list', query: paidParams['query'] }" class="hidden sm:block">
                        <ps-button class="flex flex-row" padding="p-2 sm:px-4 sm:py-2" shadow="shadow-sm" rounded="rounded"
                            hover="" focus="" border="border border-feSecondary-200 dark:border-fePrimary-500">
                            <ps-label class="hidden sm:inline" textColor="text-feSecondary-50  dark:text-feSecondary-300">{{
                                $t("list_fe__view_all_label") }}</ps-label>
                            <ps-icon class="sm:ms-2 block rtl:hidden" name="rightChervon" h="24" w="24" />
                            <ps-icon class="sm:ms-2 hidden rtl:block" name="leftChervon" h="24" w="24" />
                        </ps-button>
                    </ps-route-link>
                </div>
                <div class="w-full sm:max-w-sm md:max-w-md lg:max-w-xl xl:max-w-3xl">
                    <feature-item-horizontal-swiper 
                        :itemList="itempaidProvider.itemList?.data" notShowTitle
                        storeName="dashboard_paid" />
                </div>
            </div>
            <!-- End feature items -->
        </div>
</template>

<script>

// Libs
import { onMounted } from 'vue';
// Components
import FeatureItemHorizontalSwiper from "@template1/vendor/components/modules/item/FeatureItemHorizontalSwiper.vue";
import PsLabel from '@template1/vendor/components/core/label/PsLabel.vue';
import PsLabelHeader5 from '@template1/vendor/components/core/label/PsLabelHeader5.vue';
import PsButton from '@template1/vendor/components/core/buttons/PsButton.vue';
import PsRouteLink from '@template1/vendor/components/core/link/PsRouteLink.vue';
import PsIcon from '@template1/vendor/components/core/icons/PsIcon.vue';
// Providers
import { useProductStore } from "@templateCore/store/modules/item/ProductStore";
import { PsValueStore } from "@templateCore/store/modules/core/PsValueStore";
// Holder
import ProductParameterHolder from '@templateCore/object/holder/ProductParameterHolder';

export default {
    name: 'DashboardFeaturedItemHorizontalList',
    components: {
        FeatureItemHorizontalSwiper,
        PsLabel,
        PsLabelHeader5,
        PsButton,
        PsRouteLink,
        PsIcon
    },
    props: {
        limit: {
            type: Number,
            default: 12
        },
    },
    setup(props) {
        const psValueStore = PsValueStore();
        const loginUserId = psValueStore.getLoginUserId();
        const itempaidProvider = useProductStore('dashboard_paid');
        itempaidProvider.limit = props.limit;
        itempaidProvider.paramHolder = new ProductParameterHolder().getPaidItemParameterHolder();
        let paidParams = itempaidProvider.paramHolder.getUrlParamsAndQuery();

        onMounted(() => {
            itempaidProvider.resetProductList(loginUserId, itempaidProvider.paramHolder);
        });

        return {
            itempaidProvider,
            paidParams,
        }
    }

}
</script>
