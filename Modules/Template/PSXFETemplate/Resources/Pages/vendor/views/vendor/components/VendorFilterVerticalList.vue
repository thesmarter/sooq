<template>
    <div class="grid sm:grid-cols-8 xl:grid-cols-9 grid-cols-4 gap-4 sm:gap-3.5 lg:gap-6 mt-5">

        <div class=" sm:col-span-8 zl:col-span-9 col-span-4 w-full h-screen"
            v-if="vendorSearchStore.loading.value == false && vendorSearchStore.vendorSearchList.data == null">
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
        <div class="col-span-4 sm:col-span-4 xl:col-span-3 w-full" v-for="vendor in vendorSearchStore.vendorSearchList.data"
            :key="vendor.id">
            <vendor-card :vendor="vendor" class="infobox-item-properties"/>

        </div>
        <!-- END Column -->

        </div>

        <div v-if="vendorSearchStore.vendorSearchList?.code != null && vendorSearchStore.vendorSearchList?.code.toString() != PsConst.ERROR_CODE_10001">

        <div class="flex flex-wrap justify-center mb-6">
            <ps-button v-if="vendorSearchStore.loading.value == false && initial == false" class="mx-auto mt-8"
                @click="loadMore" :class="vendorSearchStore.isNoMoreRecord.value ? 'hidden' : ''"> {{
                    $t("list__load_more") }} </ps-button>
            <ps-button v-else-if="initial == false" class="mx-auto mt-8" @click="loadMore" :disabled="true">{{
                $t("list__loading") }}</ps-button>
        </div>
    </div>
</template>

<script>
import { onMounted } from 'vue';
import PsConst from '@templateCore/object/constant/ps_constants';

import PsLabel from '@template1/vendor/components/core/label/PsLabel.vue';
import VendorCard from '@template1/vendor/components/modules/item/VendorHorizontalVendor.vue';
import PsButton from '@template1/vendor/components/core/buttons/PsButton.vue';

import { useVendorSearchStoreState } from "@templateCore/store/modules/vendor/VendorSearchStore";

export default ({
    name : "VendorFilterViewBy",
    components : {
        PsLabel,
        VendorCard,
        PsButton
    },
    props : {
        loginUserId : String
    },
    setup(props) {

        const vendorSearchStore = useVendorSearchStoreState();

        function loadMore() {
        }

        onMounted(() => {

            vendorSearchStore.getVendorSearchList(props.loginUserId,vendorSearchStore.paramHolder);

        })

        return {
            PsConst,
            loadMore,
            vendorSearchStore
        }
    },
})
</script>
