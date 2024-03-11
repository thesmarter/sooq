<template>
    <div v-if="productStore.item?.data"
        class="mt-6 px-2 py-6 lg:p-4 bg-feSecondary-50 dark:bg-feSecondary-800 rounded-lg">
        <div class="flex flex-col gap-2 mb-6">
            <ps-label textColor="text-xl font-semibold text-feSecondary-800 dark:text-feSecondary-50">{{
                $t('item_detail__location') }}</ps-label>
            <ps-label v-if="appInfoProvider?.appInfo?.data.psItemUploadConfig?.address == PsConst.ONE"
                class="text-feSecondary-800 dark:text-feSecondary-200 font-normal text-sm"> {{
                    productStore.item?.data?.itemLocation.name }} </ps-label>
        </div>

        <map-with-pin-picker
            v-if="appInfoProvider?.appInfo?.data.frontendConfigSetting.googleMap == PsConst.ONE"
            :lat="parseFloat(productStore.item?.data?.lat + '0')"
            :lng="parseFloat(productStore.item?.data?.lng + '0')" :draggable="false" />

        <open-street-map :mapContainer="mapContainer" :dir="$page.props.dir"
            v-if="appInfoProvider?.appInfo?.data.frontendConfigSetting.openStreetMap == PsConst.ONE"
            mapSize="h-68" :lat="parseFloat(productStore.item?.data?.lat + '0')"
            :lng="parseFloat(productStore.item?.data?.lng + '0')" :dragValue="false" />
    </div>
</template>

<script>

import { defineAsyncComponent } from 'vue';

    import PsConst from '@templateCore/object/constant/ps_constants';

    ///Components
    import PsLabel from '@template1/vendor/components/core/label/PsLabel.vue';
    const MapWithPinPicker = defineAsyncComponent(() => import('@template1/vendor/components/layouts/map/MapWithPinPicker.vue'))
    const OpenStreetMap = defineAsyncComponent(() => import('@template1/vendor/components/layouts/map/OpenStreetMap.vue'))

    export default {
        name : 'ItemDetailLocationCard',
        components : {
            PsLabel,
            MapWithPinPicker,
            OpenStreetMap
        },
        props : {
            appInfoProvider : Object,
            productStore : Object,
            mapContainer : {
                type : String,
                default : 'mapContainer'
            }
        },
        setup(){
            return {
                PsConst
            }
        }
    }
    
</script>