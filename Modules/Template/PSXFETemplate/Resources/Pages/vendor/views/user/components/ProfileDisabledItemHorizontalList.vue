<template>
    <div v-if="itemdisableProvider.itemList?.data != null" class="mb-6">
        <div class="flex justify-between items-center sm:mt-8 mt-6">
            <ps-label-header-4> {{ $t("profile__disabled_listing") }} </ps-label-header-4>
            <ps-route-link :to="{name: 'fe_disable_items' }">
                <ps-button class="flex flex-row" padding="p-2 sm:px-4 sm:py-2" shadow="shadow-sm" rounded="rounded" hover=""
                    focus="" border="border border-feSecondary-200 dark:border-feSecondary-800"
                    colors="bg-feAchromatic-50 text-feSecondary-800 dark:bg-feSecondary-800 dark:text-feSecondary-200">
                    <ps-label class="hidden sm:inline">{{ $t("list_fe__view_all_label") }}</ps-label>
                    <ps-icon class="sm:ms-2 block rtl:hidden" textColor="dark:text-feSecondary-200" name="rightChervon"
                        h="24" w="24" />
                    <ps-icon class="sm:ms-2 hidden rtl:block" textColor="dark:text-feSecondary-200" name="leftChervon"
                        h="24" w="24" />
                </ps-button>
            </ps-route-link>
        </div>

        <!-- Paid ON -->
        <div v-if="appInfoStore.isPaidAppOn()" class="w-full sm:mt-6 mt-4 flex flex-col" :class="itemGrid">
            <div class="hidden lg:block" v-for="item in itemdisableProvider.itemList.data.slice(0, 4)" :key="item.id">
                <profile-item-horizontal-item  :item="item" storeName="disable"/>
            </div>

            <div class="hidden md:block lg:hidden" v-for="item in itemdisableProvider.itemList.data.slice(0, 3)" :key="item.id">
                <profile-item-horizontal-item  :item="item" storeName="disable"/>
            </div>

            <div class="hidden sm:block md:hidden" v-for="item in itemdisableProvider.itemList.data.slice(0, 2)" :key="item.id">
                <profile-item-horizontal-item  :item="item" storeName="disable"/>
            </div>

            <div class="sm:hidden" v-for="item in itemdisableProvider.itemList.data.slice(0, 1)" :key="item.id">
                <profile-item-horizontal-item  :item="item" storeName="disable"/>
            </div>
        </div>

        <div class="w-full sm:mt-6 mt-4 flex flex-col" :class="itemGrid">
            <div class="hidden xl:block" v-for="item in itemdisableProvider.itemList.data.slice(0, 3)" :key="item.id">
                <profile-item-horizontal-item  :item="item" storeName="disable"/>
            </div>

            <div class="hidden lg:block xl:hidden" v-for="item in itemdisableProvider.itemList.data.slice(0, 2)" :key="item.id">
                <profile-item-horizontal-item  :item="item" storeName="disable"/>
            </div>

            <div class="hidden md:block lg:hidden" v-for="item in itemdisableProvider.itemList.data.slice(0, 2)" :key="item.id">
                <profile-item-horizontal-item  :item="item" storeName="disable"/>
            </div>

            <div class="hidden sm:block md:hidden" v-for="item in itemdisableProvider.itemList.data.slice(0, 2)" :key="item.id">
                <profile-item-horizontal-item  :item="item" storeName="disable"/>
            </div>

            <div class="sm:hidden" v-for="item in itemdisableProvider.itemList.data.slice(0, 1)" :key="item.id">
                <profile-item-horizontal-item  :item="item" storeName="disable"/>
            </div>
        </div>
    </div>
</template>

<script setup>

import { ref } from 'vue';

import PsButton from '@template1/vendor/components/core/buttons/PsButton.vue';
import PsRouteLink from '@template1/vendor/components/core/link/PsRouteLink.vue';
import PsLabelHeader4 from '@template1/vendor/components/core/label/PsLabelHeader4.vue';
import PsIcon from '@template1/vendor/components/core/icons/PsIcon.vue';
import ProfileItemHorizontalItem from "@template1/vendor/components/modules/item/ProfileItemHorizontalItem.vue";

import ProductParameterHolder from "@templateCore/object/holder/ProductParameterHolder";
import { useProductStore } from "@templateCore/store/modules/item/ProductStore";
import { usePsAppInfoStoreState } from '@templateCore/store/modules/appinfo/AppInfoStore';

const appInfoStore = usePsAppInfoStoreState();

const itemGrid = ref() ;

itemGrid.value = "grid grid-cols-1 sm:grid-cols-2 md:grid-cols-2 lg:grid-cols-2 xl:grid-cols-3 md:gap-6 gap-4";

const itemdisableProvider = useProductStore('disable');
const loginUserId = localStorage.loginUserId;

itemdisableProvider.paramHolder = new ProductParameterHolder().getDisabledProductParameterHolder();
itemdisableProvider.paramHolder.addedUserId = loginUserId;

itemdisableProvider.resetProductList(loginUserId, itemdisableProvider.paramHolder);

</script>