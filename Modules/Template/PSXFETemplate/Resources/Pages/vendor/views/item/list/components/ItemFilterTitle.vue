<template>
    <div class="sm:ms-16 mt-4 sm:mt-0" v-if="itemProvider.paramHolder.catId == ''">
        <p v-if="query.ad_post_type == 'only_paid_item' && query.order_by == 'added_date'"
            class="text-lg text-feSeondary-800 font-semibold dark:text-feSecondary-200">{{
                $t("home__fe_featured_items") }}</p>
        <p v-else-if="itemProvider.paramHolder.searchTerm"
            class="text-lg text-feSeondary-800 font-semibold dark:text-feSecondary-200">{{
                $t("search_result_for") }} "{{ itemProvider.paramHolder.searchTerm }}"</p>
        <p v-else-if="query.order_by == 'item_touch_count'"
            class="text-lg text-feSeondary-800 font-semibold dark:text-feSecondary-200">{{
                $t("dashboard__popular") }}</p>
        <p v-else-if="(query.is_discount == '1' && query.order_by == 'added_date')"
            class="text-lg text-feSeondary-800 font-semibold dark:text-feSecondary-200">{{
                $t("dashboard__discount") }}</p>
        <p v-else-if="query.order_by == 'added_date'"
            class="text-lg text-feSeondary-800 font-semibold dark:text-feSecondary-200">{{
                $t("home__fe_recently_added") }}</p>
    </div>
</template>

<script>

// Providers
import { useProductStore } from "@templateCore/store/modules/item/ProductStore";

export default {
    name: 'ItemFilterTitle',
    props: {
        query : {
            type : Map,
            default: {}
        }
    },
    setup() {
        const itemProvider = useProductStore('list');
        return {
            itemProvider,
        }
    }

}

</script>
