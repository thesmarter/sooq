<template>
    <!-- <ps-secondary-button  
        @click="mapFilterClicked()" class="mt-4 w-full ">
        
       
        <div  class="flex flex-row items-start">
            <ps-icon class=""  textColor="dark:text-feSecondary-200" name="location"
                         />
            <ps-label v-if="itemProvider.paramHolder.mile !==''">
            item area within
            {{ itemProvider.paramHolder.mile }}with
        </ps-label>
            <ps-label v-else >
          {{ $t("item_list__pick_location") }}
        </ps-label>
        </div>
        
    
     
    </ps-secondary-button> -->
    <ps-label class="mt-4 ">
                            <button
                            @Click="mapFilterClicked()"
                            type="button"
                            class="border inline-flex items-center justify-between content-start  w-full  rounded-md hover:shadow-sm
                            px-2 py-2 bg-feSecondary-000 text-sm  font-medium text-feSecondary-500 dark:text-feSecondary-200
                                         active:bg-feAchromatic-100 active:text-feSecondary-600 transition hover:border-feSecondary-400
                            ease-in-out duration-150 btn-focus 
                             dark:bg-feSecondary-800 dark:border-feSecondary-200  "
                                       >
                            <div class="flex flex-row  items-center ">
                                <div class="ltr:mr-3 rtl:ml-3">
                                    <ps-icon class="" textColor="text-feSecondary-800 dark:text-feSecondary-200" name="locationBig" />

                                </div>
                             <div class="ltr:text-left rtl:text-right">
                                <ps-label v-if="itemProvider.paramHolder.mile !==''">Area within {{ formatkm() }}km with {{ latlangFormat(itemProvider.paramHolder.lat) }}, {{ latlangFormat(itemProvider.paramHolder.lng) }}
        </ps-label>
            <ps-label v-else  >
          {{ $t("item_list__pick_location") }}
        </ps-label>
                             </div>
                            </div>

                            </button>
                        </ps-label>

    <ps-loading-dialog ref="ps_loading_dialog" class='z-40' />
    <map-with-makers-modal ref="map_with_makers_modal" />
</template>

<script lang="ts">
// Libs
import { ref } from 'vue';
import Axios from 'axios';
// Components
import PsSecondaryButton from '@template1/vendor/components/core/buttons/PsSecondaryButton.vue';
import PsLoadingDialog from '@template1/vendor/components/core/dialog/PsLoadingDialog.vue';
import MapWithMakersModal from '@template1/vendor/components/layouts/map/MapWithMakersModal.vue';
import PsLabel from '@template1/vendor/components/core/label/PsLabel.vue';
import PsIcon from '@template1/vendor/components/core/icons/PsIcon.vue';


// Providers
import { PsValueStore } from "@templateCore/store/modules/core/PsValueStore";
import { usePsAppInfoStoreState } from '@templateCore/store/modules/appinfo/AppInfoStore';
import { useProductStore } from "@templateCore/store/modules/item/ProductStore";
//Utils
import PsUtils from '../../../../../../../../../TemplateCore/utils/PsUtils';

export default {
    name: 'ItemFilterPickLocationButton',
    components: {
        PsSecondaryButton,
        PsLoadingDialog,
        MapWithMakersModal,
        PsLabel,
        PsIcon
    },
    setup() {
        const psValueStore = PsValueStore();
        const itemProvider = useProductStore('list');
        const appInfoStore = usePsAppInfoStoreState();
        const ps_loading_dialog = ref();
        const map_with_makers_modal = ref();
        

        function mapFilterClicked() {
            if (map_with_makers_modal.value.isFirstTime) {
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(position => {
                        Axios.get('https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=' + position.coords.latitude + '&lon=' + position.coords.longitude,
                        ).then(async (response) => {
                            map_with_makers_modal.value.closeModal();

                            itemProvider.paramHolder.lat = await response.data.lat.toString();
                            itemProvider.paramHolder.lng = await response.data.lon.toString();

                            map_with_makers_modal.value.openModal(itemProvider.paramHolder.lat, itemProvider.paramHolder.lng, itemProvider.paramHolder.mile,
                                (lat, lng, mile) => {
                               

                                    if (lat == null || lng == null) {
                                        return;
                                    }
                                    
                                    itemProvider.paramHolder.lat = lat;
                                    itemProvider.paramHolder.lng = lng;
                                    itemProvider.paramHolder.mile = mile;
                                    loadDataList();
                                });

                        }).catch(error => {
                         
                            console.log("sese1");
                            console.log(error);
                         
                            setDefaultLatLng();
                            map_with_makers_modal.value.openModal(itemProvider.paramHolder.lat, itemProvider.paramHolder.lng, itemProvider.paramHolder.mile,
                                (lat, lng, mile) => {

                                    if (lat == null || lng == null) {
                                        return;
                                    }
                                    
                                    itemProvider.paramHolder.lat = lat;
                                    itemProvider.paramHolder.lng = lng;
                                    itemProvider.paramHolder.mile = mile;
                                    loadDataList();
                                });

                        });
                    },
                        error => {
                         
                            console.log("sese");
                            console.log(error);
                            setDefaultLatLng();
                            map_with_makers_modal.value.openModal(itemProvider.paramHolder.lat, itemProvider.paramHolder.lng, itemProvider.paramHolder.mile,
                                (lat, lng, mile) => {

                                    if (lat == null || lng == null) {
                                        return;
                                    }

                                    itemProvider.paramHolder.lat = lat;
                                    itemProvider.paramHolder.lng = lng;
                                    itemProvider.paramHolder.mile = mile;
                                    loadDataList();
                                });

                        },
                        {
                            enableHighAccuracy: true,
                            timeout: 7000,
                            maximumAge: 0
                        });
                } else {
                    alert("Your browser doesn't support geolocation API");
                    setDefaultLatLng();
                    map_with_makers_modal.value.openModal(itemProvider.paramHolder.lat, itemProvider.paramHolder.lng, itemProvider.paramHolder.mile,
                        (lat, lng, mile) => {

                            if (lat == null || lng == null) {
                                return;
                            }

                            itemProvider.paramHolder.lat = lat;
                            itemProvider.paramHolder.lng = lng;
                            itemProvider.paramHolder.mile = mile;
                            loadDataList();
                        });

                }
            } else {

                if (itemProvider.paramHolder.lat == null || itemProvider.paramHolder.lat == '' || itemProvider.paramHolder.lng == null || itemProvider.paramHolder.lng == '') {
                    setDefaultLatLng();
                }
                map_with_makers_modal.value.openModal(itemProvider.paramHolder.lat, itemProvider.paramHolder.lng, itemProvider.paramHolder.mile,
                    (lat, lng, mile) => {

                        if (lat == null || lng == null) {
                            return;
                        }

                        itemProvider.paramHolder.lat = lat;
                        itemProvider.paramHolder.lng = lng;
                        itemProvider.paramHolder.mile = mile;
                        loadDataList();
                    }
                );

            }

        }
      function  formatkm() {
            if (itemProvider.paramHolder.mile !== '') {
                const miles = parseFloat(itemProvider.paramHolder.mile);
                const kilometers = miles * 1.60934;
                return kilometers.toFixed(2);
            }
            return '';
        }
        function latlangFormat(value){
            if (value !== '') {
                const decivalue = parseFloat(value);
              
                return decivalue.toFixed(2);
            }
            return '';
        }
        function setDefaultLatLng(){
            if (itemProvider.paramHolder.lat == null || itemProvider.paramHolder.lat == 0 || itemProvider.paramHolder.lat == undefined || 
                itemProvider.paramHolder.lng == null || itemProvider.paramHolder.lng == 0 || itemProvider.paramHolder.lng == undefined) {
                    itemProvider.paramHolder.lat = appInfoStore.appInfo.data?.psAppSetting?.latitude;
                    itemProvider.paramHolder.lng = appInfoStore.appInfo.data?.psAppSetting?.longitude;
                }
        }

        async function loadDataList() {
            ps_loading_dialog.value.openModal();
            PsUtils.addParamToCurrentUrl(itemProvider.getURLforListByParam(itemProvider.paramHolder));
            await itemProvider.resetProductList(psValueStore.getLoginUserId(), itemProvider.paramHolder);
            ps_loading_dialog.value.closeModal();
            //hide popup filter
            itemProvider.showPopUpFilter = false;
        }

        return {
            map_with_makers_modal,
            ps_loading_dialog,
            itemProvider,
            mapFilterClicked,
            formatkm,
            latlangFormat
        }
    }

}

</script>
