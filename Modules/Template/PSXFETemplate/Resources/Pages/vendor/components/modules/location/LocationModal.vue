<template>
    <ps-modal ref="psmodal"  :visibleLine="false" :isClickOut='false' :maxWidth="'280px'" :bodyHeight="'216px'" theme="p-6 rounded-md">
        <template #title>
            <div class="flex">
               <div class="grow">
                    <ps-label class="text-center">{{ $t('location_modal__title') }}</ps-label>
               </div>
                <ps-icon name="close" class="text-feAchromatic-500 hover:cursor-pointer" @click.prevent="psmodal.toggle(false)"></ps-icon>
            </div>
        </template>

        <template #body>
            <div>
                <!-- city dropdown start -->
             
                <ps-dropdown h="h-64"  align="left" :isFixed="true" boxPositioning="rtl:right-1/2 rtl:translate-x-1/2 ltr:left-1/2 ltr:-translate-x-1/2 top-1/2  -translate-y-[9%]" class="mt-3 w-56 " >
                    <template #select>
                        <ps-label>
                            <button
                            @onClick="loadLocationList"
                            type="button"
                            class="border inline-flex items-center justify-between content-start  w-full h-10 lg:rounded rounded-x
                            px-4  bg-feSecondary-000 text-sm leading-5 font-medium text-feSecondary-500 dark:text-feSecondary-200
                                        focus:shadow-outline-blue active:bg-feAchromatic-100 active:text-feSecondary-600 transition
                            ease-in-out duration-150 btn-focus
                             dark:bg-feSecondary-800 dark:border-feSecondary-200 ml-1 "
                                        aria-haspopup="true"
                            aria-expanded="true">
                            <div class="inline-flex">
                             <ps-icon textColor="text-feSecondary-800 dark:text-feSecondary-200"  name="locationBig" />
                             <div class="pl-2">
                            <ps-label v-if="locationCity=='Select Your Location'"   textColor="font-normal text-feSecondary-800 dark:text-feSecondary-200">{{ $t('location_modal__select_city') }}</ps-label>
                            <ps-label v-else textColor="font-normal text-feSecondary-800 dark:text-feSecondary-200">{{ locationCity }}</ps-label>
                             </div>
                            </div>

                            <ps-icon class="text-lg" textColor="text-feSecondary-800 dark:text-feSecondary-200" name="downArrow"   />
                            </button>
                        </ps-label>
                    </template>
                    <template #filter >
                        <div>
                            <ps-input-with-left-icon class="bg-feSecondary-100 dark:bg-transparent shadow-none" rounded="rounded-none" theme="border-none  focus:ring-0 focus:ring-transparent text-feSecondary-500 dark:text-feAchromatic-50" placeholderColor="placeholder-feSecondary-300" height="h-10" @keyup.capture="filterKeywordUpate(itemLocationStore.filterKeyword)"  v-model:value="itemLocationStore.filterKeyword" v-bind:placeholder= "$t('search_city')" style="width: 14rem; " >
                                <template #icon>
                                    <ps-icon textColor="text-fePrimary-500 dark:text-feAchromatic-500" name="search" class='cursor-pointer ml-1' @click="filterKeywordUpate(itemLocationStore.filterKeyword)" />
                                </template>

                                <template #removeIcon>
                                    <div v-if="itemLocationStore.filterKeyword !=''">
                                        <ps-icon textColor="text-feAchromatic-500 dark:text-feAchromatic-500" name="close" class='cursor-pointer' @click="itemLocationStore.filterKeyword = '',filterKeywordUpate(itemLocationStore.filterKeyword)"/>

                                    </div>
                                </template>
                            </ps-input-with-left-icon>
                      <hr class="bg-feSecondary-400  h-0.5 ">

                        </div>
                    </template>
                    <template #list >
                       
                        <div class="pt-1 z-50  ">
                            <div  v-if="itemLocationStore.loading.value == true" class='w-52 flex py-4 px-2 items-center '>
                                <ps-label-caption class="ms-2" > {{ $t("search_for_large_screem__loading") }} </ps-label-caption>
                            </div>
                            
                            <div v-if="itemLocationStore.itemLocationList.data == null" class='w-52 flex py-4 px-2 items-center hover:w-full'>
                                <ps-label class="ms-2" :class="locationId =='' ? ' font-bold text-fePrimary-700' : ''">{{ $t('no_result_found') }}</ps-label>
                                    
                            </div>

                            <div v-else class="w-52 flex py-4 px-2 hover:bg-fePrimary-50 dark:hover:bg-feAchromatic-800 cursor-pointer items-center hover:w-full "  @click="locationFilterClicked('',$t('item_list__all'),'','')" >
                                <ps-label class="ms-2" :class="locationId =='' ? ' font-bold text-fePrimary-700' : ''"  >{{ $t('item_list__all')}} </ps-label>
                            </div>
                            
                                <div  v-for="selectData in itemLocationStore.itemLocationList.data" :key="selectData.id" class="w-52 flex py-4 px-2 hover:bg-fePrimary-50 dark:hover:bg-feAchromatic-800 cursor-pointer items-center hover:w-full"  @click="locationFilterClicked(selectData.id,selectData.name,selectData.lat,selectData.lng)" >
                                <ps-label class="ms-2" :class="selectData.id==locationId ? ' font-bold text-fePrimary-700' : ''"  > {{selectData.name}} </ps-label>
                          
                            </div>
                          
                            <div class="border-t border-feAchromatic-100 "></div>
                        </div>
                      
                    </template>
                    <template #loadmore>

                    <div class=" w-52 ">
                        <div  v-if="itemLocationStore.itemLocationList.data != null
                        && itemLocationStore.loading.value == true" class='py-4 px-2 flex'>
                        <ps-label-caption >{{ $t("search_for_large_screem__loading") }} </ps-label-caption>
                        </div>
                        <ps-label v-if="itemLocationStore.loading.value == false" :class="itemLocationStore.isNoMoreRecord.value ? 'hidden' : ''" class="flex py-4 px-2 underline font-bold cursor-pointer ms-2"   @click.stop="loadLocationList" > {{ $t("search_for_large_screem__load_more") }} </ps-label>
                    </div>
                    </template>
                </ps-dropdown>
               
                <!-- city dropdown end -->

                <!-- township dropdown start -->
                <ps-dropdown h="h-64" v-if="appInfoStore.appInfo.data?.psAppSetting?.isSubLocation == '1'" align="center" :class=" `mt-3  w-56 ${locationCity == '' ? 'pointer-events-none' : ''}`" :isFixed="true" boxPositioning="rtl:right-1/2 rtl:translate-x-1/2 ltr:left-1/2 ltr:-translate-x-1/2 top-1/2  translate-y-[9%]" @click="loadLocationTownshipList">
                    <template #select>
                            <button
                            :disabled="locationId =='' "
                            type="button"
                            class="border inline-flex items-center content-start justify-between w-full h-10 lg:rounded rounded-x
                            px-4  bg-feSecondary-000 text-sm leading-5 font-medium text-feSecondary-500 dark:text-feSecondary-200
                                        focus:shadow-outline-blue active:bg-feAchromatic-100 active:text-feSecondary-600 transition
                            ease-in-out duration-150 btn-focus
                             dark:bg-feSecondary-800 dark:border-feSecondary-200 cursor-pointer disabled:opacity-70 ml-1"
                              aria-haspopup="true"
                            aria-expanded="true"
                            
                            >
                            <div class="inline-flex">
                            <ps-icon textColor="text-feSecondary-800 dark:text-feSecondary-200 disabled:opacity-30" name="locationBig" />
                          <div class="ml-2">
                            <ps-label v-if="locationTownship && locationId!=''" textColor="font-normal text-feSecondary-800 dark:text-feSecondary-200 disable ">{{ locationTownship }}</ps-label>
                            <ps-label v-else textColor="font-normal text-feSecondary-800 dark:text-feSecondary-200 disabled:opacity-30">{{ $t('location_modal__select_township') }}</ps-label>
                          </div>
                            </div>
                            
                            <ps-icon class="text-lg" textColor="text-feSecondary-800 dark:text-feSecondary-200 disabled:opacity-30" name="downArrow"   />
                            </button>
                    </template>
                    <template #filter>
                        <div>
                            <ps-input-with-left-icon  :placeholder="$t('search_location')" class="bg-feSecondary-100 dark:bg-transparent shadow-none " rounded="rounded-none" theme="border-none  focus:ring-0 focus:ring-transparent text-feSecondary-500 dark:text-feAchromatic-50" placeholderColor="placeholder-feSecondary-500" height="h-10" style="width: 14rem;" @keyup.capture="filtersubLocationUpdate(itemLocationTownshipStore.filterKeyword)" v-model:value="itemLocationTownshipStore.filterKeyword">
                                <template #icon>
                                    <ps-icon textColor="text-fePrimary-500 dark:text-feAchromatic-500" name="search" class='cursor-pointer ml-1' @click="filtersubLocationUpdate(itemLocationTownshipStore.filterKeyword)"/>
                                </template>
                                <template #removeIcon>
                                    <div v-if="itemLocationTownshipStore.filterKeyword !=''">
                                        <ps-icon textColor="text-feAchromatic-500 dark:text-feAchromatic-500" name="close" class='cursor-pointer' @click="itemLocationTownshipStore.filterKeyword='',filtersubLocationUpdate(itemLocationTownshipStore.filterKeyword)"/>

                                    </div>
                                </template>
                            </ps-input-with-left-icon>
                      <hr class="bg-feSecondary-400  h-0.5 ">

                        </div>

                    </template>
                    <template #list>
                        
                        <ps-label v-if="locationId == ''" class='py-4 px-2' textColor="text-feError-500"> {{
                                                            $t("item_entry__select_location_first") }}</ps-label>
                        <div v-else>
                            <div  v-if="itemLocationTownshipStore.loading.value == true" class='w-52 flex py-4 px-2 items-center'>
                                <ps-label-caption class="ms-2" > {{ $t("search_for_large_screem__loading") }} </ps-label-caption>
                            </div>
                            <div v-if="itemLocationTownshipStore.locationTownshipList.data ===null" class="w-52 flex py-4 px-2 hover:bg-fePrimary-50 dark:hover:bg-feAchromatic-800 cursor-pointer items-center hover:w-full"  >
                                <ps-label class="ms-2" :class="townshipId =='' ? ' font-bold text-fePrimary-700' : ''"  >{{ $t('no_result_found') }}</ps-label>
                            </div>
                            <div v-else class="w-52 flex py-4 px-2 hover:bg-fePrimary-50 dark:hover:bg-feAchromatic-800 cursor-pointer items-center hover:w-full"  @click="townshipFilterClicked('',$t('item_list__all'),'','')" >
                                <ps-label class="ms-2" :class="townshipId =='' ? ' font-bold text-fePrimary-700' : ''"  > {{$t('item_list__all')}} </ps-label>
                            </div>
                                <div v-for="township in itemLocationTownshipStore.locationTownshipList.data" :key="township.id" class="w-52 flex py-4 px-2 hover:bg-fePrimary-50 dark:hover:bg-feAchromatic-800 cursor-pointer items-center hover:w-full" @click.prevent="townshipFilterClicked(township.id,township.townshipName)">
                                <ps-label class="ms-2" :class="township.id==townshipId ? ' font-bold text-fePrimary-700' : ''"  > {{township.townshipName}} </ps-label>
                            </div>
                            
                            
                        </div>
                        <div class="border-t border-feAchromatic-100 "></div>
                    </template>
                    <template #loadmore>
                        <div class="mb-2 w-52">
                            <div  v-if="itemLocationTownshipStore.locationTownshipList.data != null
                            && itemLocationTownshipStore.loading.value == true" class='mt-4 ms-4 flex'>
                            <ps-label-caption >{{ $t("search_for_large_screem__loading") }} </ps-label-caption>
                            </div>
                            <ps-label  v-if="itemLocationTownshipStore.loading.value == false " :class="itemLocationTownshipStore.isNoMoreRecord.value ? 'hidden' : ''" class="flex mt-4 ms-4 mb-2 underline font-bold cursor-pointer"   @click.stop="loadLocationTownshipList" > {{ $t("search_for_large_screem__load_more") }} </ps-label>
                        </div>
                    </template>
                </ps-dropdown>

                <!-- township dropdown end -->
            </div>
        </template>
        <template #footer>
            <div class="flex justify-center">
                <ps-button textSize="text-xxs lg:text-sm" class="w-full" @click.prevent="confirmClicked" > {{ $t("chat__confirm") }}</ps-button>
            </div>
        </template>
    </ps-modal>
</template>

<script>

import { computed, ref,onMounted } from 'vue';
import { trans } from 'laravel-vue-i18n';

// Component
import PsModal from '@template1/vendor/components/core/modals/PsModal.vue';
import PsLabel from '@template1/vendor/components/core/label/PsLabel.vue';
import PsIcon from '@template1/vendor/components/core/icons/PsIcon.vue';
import PsButton from '@template1/vendor/components/core/buttons/PsButton.vue';
import PsDropdown from '@template1/vendor/components/core/dropdown/PsDropdown.vue';
import PsInputWithLeftIcon from '@template1/vendor/components/core/input/PsInputWithLeftIcon.vue';
import PsLabelCaption from '@template1/vendor/components/core/label/PsLabelCaption.vue';

// Providers
import { PsValueStore } from "@templateCore/store/modules/core/PsValueStore";
import { useItemLocationStoreState } from '@templateCore/store/modules/itemlocation/ItemLocationStore';
import { usePsAppInfoStoreState } from '@templateCore/store/modules/appinfo/AppInfoStore';
import { useItemLocationTownshipStoreState } from '@templateCore/store/modules/itemLocationTownship/ItemLocationTownshipStore';

// Holders
import ProductParameterHolder from '@templateCore/object/holder/ProductParameterHolder';
import LocationParameterHolder from '@templateCore/object/holder/LocationParameterHolder';
import LocationTownshipParameterHolder from '@templateCore/object/holder/LocationTownshipParameterHolder';

export default{
    name: 'LocationModal',
    components: {
        PsModal,
        PsLabel,
        PsIcon,
        PsButton,
        PsDropdown,
        PsLabelCaption,
        PsInputWithLeftIcon,
    },
    emits:['changeLocation'],
    setup(props,context){
        const psValueStore = PsValueStore();
        const psmodal = ref();
        const paramHolder = new LocationParameterHolder().getDefaultParameterHolder();
        const subLocationParamHolder = new LocationTownshipParameterHolder();
        const locationCity = ref('');
        const locationTownship = ref('');
        const locationId = ref();

        const locationLat = ref(0);
        const locationLng = ref(0);
        const townshipId = ref();
        const isSubLocationOn = ref('');
        let timer =0;
     
        
        // get login user id
        const loginUserId = psValueStore.getLoginUserId();

        // get city list
        const itemLocationStore = useItemLocationStoreState('navtab-location');

        // get township list
        const itemLocationTownshipStore = useItemLocationTownshipStoreState('navtab-township');

        const appInfoStore = usePsAppInfoStoreState();
      

        // const initial = ref(true);
        // const initial2 = ref(true);

        function openModal(){
            psmodal.value.toggle(true);

            locationId.value = psValueStore.locationId;
            
            locationCity.value = psValueStore.locationName =psValueStore.locationName;
            locationLat.value = psValueStore.locationLat;
            locationLng.value = psValueStore.locationLng;

            // itemLocationTownshipStore.cityId = psValueStore.locationId;

            townshipId.value = psValueStore.subLocationId;
            locationTownship.value = psValueStore.subLocationName =psValueStore.subLocationName;

            // initial.value = true;
            // initial2.value = true;

        }


    function loadLocationList(){
        
      itemLocationStore.loadItemLocationList(loginUserId, paramHolder);
            // initial.value = false;
 
          
      

        }

        function loadLocationTownshipList(){
          itemLocationTownshipStore.loadItemLocationTownshipList(locationId.value);
            // initial2.value = false;
        }

        async function locationFilterClicked(id,name,lat,lng) {

            locationId.value = id;
            locationCity.value = name;
            locationLat.value = lat;
            locationLng.value = lng;

            // itemLocationTownshipStore.cityId = id;
            locationTownship.value = '';
            // psValueStore.replaceSubLocation('', trans('item_list__all'));

            // sublocationId.value = '';
            // sublocationName.value = trans('item_list__all');
            // itemProvider.paramHolder.itemLocationTownship = '';
            // itemProvider.paramHolder.itemLocationTownshipName = trans('item_list__all');
            // await itemProvider.updateLocation(loginUserId, locationId.value, locationName.value);
            // categoryStore.cityId = id;
            // categoryStore.resetItemLocationTownshipList(id);
            // context.emit('clicklocation', {id: id,name: name,lat: lat,lng: lng});
        }

        async function townshipFilterClicked(id,name){
            locationTownship.value = name;
            townshipId.value = id;
        }

        async function confirmClicked(){
            if(locationCity.value != ''){
                psValueStore.replaceLocation(locationId.value, locationCity.value, locationLat.value, locationLng.value);
                if(locationId.value != '') {
                    psValueStore.replaceSubLocation(townshipId.value !== undefined ? townshipId.value : '',locationTownship.value != '' ? locationTownship.value : trans('item_list__all'));
                } else { //when city is ALL, township values should be ALL.
                    psValueStore.replaceSubLocation('', '');
                }
                locationId.value = '';
                locationCity.value = '';
                locationLat.value = '';
                locationLng.value = '';

                locationTownship.value = '';
                townshipId.value = '';


                context.emit('changeLocation');
                psmodal.value.toggle(false);
            }
            else if(localStorage.subLocationName === '' || localStorage.subLocationName === undefined){
                psValueStore.replaceLocation(locationId.value, locationCity.value, locationLat.value, locationLng.value);
                psValueStore.replaceSubLocation('','');
                locationId.value = '';
                locationCity.value = '';
                locationLat.value = '';
                locationLng.value = '';

                locationTownship.value = '';
                townshipId.value = '';

                context.emit('changeLocation');
                psmodal.value.toggle(false);
            }
        }

        function filterKeywordUpate(value){
            clearTimeout(timer);

timer = window.setTimeout(async () => {
await itemLocationStore.filterKeywordUpate(value,loginUserId,paramHolder);

}, 400); 
           
        }

       function filtersubLocationUpdate(value){
            subLocationParamHolder.locationId = locationId.value;
            subLocationParamHolder.keyword = value;
            clearTimeout(timer);

timer =window.setTimeout(async () => {
    
await itemLocationTownshipStore.filtersubLocationUpdate(loginUserId, subLocationParamHolder);
}, 400);

        }


        return {
            psmodal,
            openModal,
            psValueStore,
            itemLocationStore,
            itemLocationTownshipStore,
            loadLocationList,
            loadLocationTownshipList,
            locationCity,
            locationId,
            locationTownship,
            townshipId,
            isSubLocationOn,
            locationFilterClicked,
            townshipFilterClicked,
            appInfoStore,
            confirmClicked,
            filterKeywordUpate,
            filtersubLocationUpdate,
            timer
       
            // initial,
            // initial2
        }
    }
}
</script>
