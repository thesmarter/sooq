<template>
    <div class="grid sm:grid-cols-8 xl:grid-cols-9 grid-cols-4 gap-4 sm:gap-3.5 lg:gap-6">

        <div class=" sm:col-span-8 zl:col-span-9 col-span-4 w-full h-screen"
            v-if="itemProvider.loading.value == false && itemProvider.itemList.data == null && initial == false">
            <div class="flex flex-col items-center justify-center">
                <div class="h-52 ">
                    <img v-lazy="{ src: $page.props.sysImageUrl + '/no_result.png' }" alt=""
                        class="w-full h-52 object-contain">
                </div>
                <ps-label textColor="text-feSecondary-800 dark:text-feSecondary-300" class="text-lg font-semibold mt-4"> {{
                    $t("list__no_result") }} </ps-label>
                <ps-label textColor="text-feSecondary-500" class="mt-2 text-sm"> {{
                    $t("list__no_list_found") }} </ps-label>
                <ps-label textColor="text-feSecondary-500" class="text-sm"> {{
                    $t("list__search_again") }} </ps-label>
                <ps-button class="mt-6"> {{ $t("list__search") }} </ps-button>
            </div>
        </div>

        <!-- Column -->
        <div class="col-span-4 sm:col-span-4 xl:col-span-3 w-full" v-for="item in itemProvider.itemList.data"
            :key="item.id">
            <item-horizontal-item :item="item" class="infobox-item-properties" storeName="list" />

        </div>
        <!-- END Column -->

    </div>

    <div v-if="itemProvider.itemList?.code != null && itemProvider.itemList?.code.toString() != notDataCode">

        <div class="flex flex-wrap justify-center mb-6">
            <ps-button v-if="itemProvider.loading.value == false && initial == false" class="mx-auto mt-8"
                @click="loadItemList" :class="itemProvider.isNoMoreRecord.value ? 'hidden' : ''"> {{
                    $t("list__load_more") }} </ps-button>
            <ps-button v-else-if="initial == false" class="mx-auto mt-8" @click="loadItemList" :disabled="true">{{
                $t("list__loading") }}</ps-button>
        </div>
    </div>
</template>

<script>
// Libs
import { defineAsyncComponent } from 'vue';
// Components
import PsLabel from '@template1/vendor/components/core/label/PsLabel.vue';
import PsButton from '@template1/vendor/components/core/buttons/PsButton.vue';
const ItemHorizontalItem = defineAsyncComponent(() => import('@template1/vendor/components/modules/item/ItemHorizontalItem.vue'));
// Providers
import { PsValueStore } from "@templateCore/store/modules/core/PsValueStore";
import { useProductStore } from "@templateCore/store/modules/item/ProductStore";
// Const
import PsConst from '@templateCore/object/constant/ps_constants';

export default {
    name: 'ItemFilterItemVerticalList',
    components: {
        PsLabel,
        PsButton,
        ItemHorizontalItem,
    },
    props: {
        initial : {
            type: Boolean,
            default: false
        }
    },
    setup() {
        const psValueStore = PsValueStore();
        const itemProvider = useProductStore('list');
        const notDataCode = PsConst.ERROR_CODE_10001;

        function loadItemList() {
            itemProvider.loadItemList(psValueStore.getLoginUserId(), itemProvider.paramHolder);
        }

        return {
            notDataCode,
            itemProvider,
            loadItemList
        }
    }

}

</script>
