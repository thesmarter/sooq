<template>

    <nav class="fixed z-50 w-full h-10 flex items-center bg-feSecondary-900"
        :class="topOfPage ? ' ' : 'hidden'">

        <div class="xl:w-feLarge lg:w-large w-full flex justify-between mx-4 sm:mx-6 lg:mx-auto">
            <div class="flex items-center cursor-pointer" @click.prevent="openLocationModal">
                <ps-icon name="locationBig" class="text-feSecondary-100 me-2"></ps-icon>
                    
      <ps-label v-if="psValueStore.locationName !='Select Your Location'" textColor="text-feSecondary-200 dark:text-feAchromatic-50" class="text-sm hidden md:block">
        {{ psValueStore.locationName }}{{ appInfoStore.appInfo.data?.psAppSetting?.isSubLocation == '1' ? `,${psValueStore.subLocationName}` : '' }}
      </ps-label>
      <ps-label v-else textColor="text-feSecondary-200 dark:text-feAchromatic-50" class="text-sm hidden md:block">
        {{ $t("select_your_location") }} 
      </ps-label>
     
    
            </div>
            <!-- <div v-if="appInfoStore.appInfo.data?.psAppSetting?.isSubLocation">
                {{ checkTownship() }}
            </div> -->
            <div v-if="dataReady" class="flex gap-4" >

                <div class="">
                    <ps-dropdown h="h-64" align="left">
                        <template #select>
                            <ps-label class="">
                                <button
                                    type="button"
                                    class="inline-flex items-center content-start justify-between w-full
                                                leading-5 font-medium text-sm
                                                focus:outline-none transition
                                                ease-in-out duration-150 btn-focus text-feAchromatic-50 "

                                    :id="($page.props.languages).filter(language => language.symbol==activeLanguage)[0].name"
                                    aria-haspopup="true"
                                    aria-expanded="true" >
                                    {{($page.props.languages).filter(language => language.symbol==activeLanguage)[0].name}}

                                    <ps-icon name="downArrow" textColor="text-feAchromatic-50" class=" ms-2" w='24' h='24' />

                                </button>
                            </ps-label>
                        </template>
                        <template #filter>
                        <div>
                            <ps-input-with-left-icon class="bg-feSecondary-100 dark:bg-transparent shadow-none" rounded="rounded-none" theme="border-none  focus:ring-0 focus:ring-transparent text-feSecondary-500 dark:text-feAchromatic-50" height="h-10" placeholderColor="placeholder-feSecondary-300" @keyup="languageSearch(languageKeys)"  v-model:value="languageKeys" v-bind:placeholder= "$t('search_language')" style="width: 14.5rem;">
                                <template #icon>
                                    <ps-icon textColor="text-fePrimary-500 dark:text-feAchromatic-500" name="search" class='cursor-pointer'  />
                                </template>
                               
                                    <template #removeIcon>
                                       <div v-if="languageKeys !=''">
                                        <ps-icon textColor="text-feAchromatic-500 dark:text-feAchromatic-500" name="close" class='cursor-pointer' @click="[languageKeys='',filteredLanguage = $page.props.languages]"/>

                                       </div> 
                                </template>
                               
                                
                            </ps-input-with-left-icon>
                            

                        </div>
                      <hr class="bg-feSecondary-400  h-0.5 ">
                       
                    </template>
                      
                        <template #list>

                            
                                <div class="pt-1 z-30 ">
                                    <div v-if="filteredLanguage.length===0">
                                        <ps-label class="ms-2"
                                            >{{ $t('no_result_found') }}</ps-label>
                                            
                                    </div>
                                    <div v-else>
                                        <div  v-for="language in filteredLanguage" :key="language.id"
                                        class="w-56 flex py-4 px-2 hover:bg-fePrimary-50 dark:hover:bg-fePrimary-900 cursor-pointer items-center"
                                        @click="handleLanguage(language)">

                                        <ps-label class="ms-2"
                                            :class="language.symbol==activeLanguage ? ' font-bold' : ''">
                                            {{ language.name }} </ps-label>
                                            
                                    </div>
                                    </div>
                                    </div>
                           
                        </template>
                    </ps-dropdown>
                </div>
                <div class="flex items-center gap-3">
                    <ps-label textColor="text-feAchromatic-50" >Dark</ps-label>
                    <ps-icon-toggle toggleOffIcon="day" :selectedValue="isDarkMode" @onChange="toggleDarkMode" />
                </div>
            </div>
        </div>
    </nav>

    <location-modal @changeLocation="changeLocation" ref="ps_location_modal"/>
</template>

<script lang="ts">
import { defineComponent, ref,onMounted, reactive,computed } from "vue";
import { usePage } from '@inertiajs/vue3';

import PsIcon from '../../core/icons/PsIcon.vue';
import PsDropdown from '../../core/dropdown/PsDropdown.vue';
import PsDropdownSelect from "../../core/dropdown/PsDropdownSelect.vue";
import PsIconToggle from '../../core/toggle/PsIconToggle.vue';
import PsInputWithLeftIcon from '@template1/vendor/components/core/input/PsInputWithLeftIcon.vue';
import { mapActions, mapGetters } from "vuex";
import PsLabel from "../../core/label/PsLabel.vue";
import LocationModal from "@template1/vendor/components/modules/location/LocationModal.vue";
import { trans, loadLanguageAsync } from 'laravel-vue-i18n';
import { useStore } from 'vuex';
import { PsValueStore } from "@templateCore/store/modules/core/PsValueStore";
import { usePsAppInfoStoreState } from '@templateCore/store/modules/appinfo/AppInfoStore';
import { useItemLocationTownshipStoreState } from '@templateCore/store/modules/itemLocationTownship/ItemLocationTownshipStore';
import { useItemLocationStoreState } from '@templateCore/store/modules/itemlocation/ItemLocationStore';

// Providers
// import { PsValueStore } from '@templateCore/store/modules/core/PsValueStore';
// import { usePsAppInfoStoreState } from '@templateCore/store/modules/appinfo/AppInfoStore';

// Holders
// import Language from "@templateCore/object/Language";
import LocationParameterHolder from '@templateCore/object/holder/LocationParameterHolder';
import AppInfoParameterHolder from '@templateCore/object/holder/AppInfoParameterHolder';

export default defineComponent ({
    components : {
        PsIcon,
        PsIconToggle,
        PsDropdown,
        PsLabel,
        PsDropdownSelect,
        LocationModal,
        PsInputWithLeftIcon
    },
    props : {
        topOfPage : {
            type : Boolean,
            default : false
        },
    },
    setup(props) {

        const store = useStore();
        const activeLanguage = ref(localStorage.activeLanguage);
        const psValueStore = PsValueStore();
        const appInfoStore = usePsAppInfoStoreState();
        const loginUserId = psValueStore.getLoginUserId();

        const paramHolder = new LocationParameterHolder().getDefaultParameterHolder();
        const appInfoParameterHolder = new AppInfoParameterHolder();
        appInfoParameterHolder.userId = loginUserId;
        appInfoStore.loadAppInfo(appInfoParameterHolder);
        const filteredLanguage = ref(usePage().props.languages) ;
       const languageKeys= ref('');
        // get city list
        const itemLocationStore = useItemLocationStoreState('navtab-location');

        // console.log('city',cityData.value);

        // get township list
        const itemLocationTownshipStore = useItemLocationTownshipStoreState('navtab-township');
        // appInfoStore.loadAppInfo(appInfoParameterHolder);

        // const isSubLocationOn = computed(() => appInfoStore.appInfo.data?.psAppSetting?.isSubLocation);


        const dataReady = ref(true);
        const ps_location_modal = ref();

function languageSearch(e) {

  this.filteredLanguage = usePage().props.languages.filter((language) =>
        
      language.name.toLowerCase().includes(e.toLowerCase())
    );


}

 

        function handleLanguage(row){
            // this.$inertia.put(route('language.statusChange',row.id), '', {
            //     onSuccess: () => {
                    loadLanguageAsync(row.symbol);
                    store.dispatch('handleActiveLanguage',row.symbol);
                    // setTimeout(()=>{
                    //         window.location.reload();
                    //     },1000)
                // }
            // });
        }

        function openLocationModal(){
            ps_location_modal.value.openModal();
        }


        async function loadLocationData(){
            await itemLocationStore.resetItemLocationList(loginUserId, paramHolder);

            // let cityData = itemLocationStore.itemLocationList.data;

            let isSubLocationOn = appInfoStore.appInfo.data?.psAppSetting?.isSubLocation;

            // console.log('sub',isSubLocationOn);

            // if(localStorage.locationName === '' || localStorage.locationName === undefined){

            //     localStorage.locationName = cityData[0].name;
            //     localStorage.locationId = cityData[0].id;
            //     localStorage.locationLat = cityData[0].lat;
            //     localStorage.locationLng = cityData[0].lng;
            // }
            if(localStorage.locationName === '' || localStorage.locationName === undefined){
                localStorage.locationName = 'Select Your Location';
                localStorage.locationId = '';
                localStorage.locationLat = '';
                localStorage.locationLng = '';
            }


            // itemLocationTownshipStore.cityId = localStorage.locationId;
            // await itemLocationTownshipStore.loadItemLocationTownshipList(localStorage.locationId);

            // let townShipData = itemLocationTownshipStore.locationTownshipList.data;



            if(isSubLocationOn == '1' && (localStorage.subLocationName === '' || localStorage.subLocationName === undefined)){
                localStorage.subLocationId = '';
                localStorage.subLocationName = 'All';
            }
            else if(isSubLocationOn != '1' && (localStorage.subLocationName !== '' || localStorage.subLocationName !== undefined)){
                localStorage.subLocationId = '';
                localStorage.subLocationName = '';
            }

            // if(localStorage.subLocationName === '' || localStorage.subLocationName === undefined){
            //     psValueStore.replaceSubLocation('','');
            // }
            psValueStore.replaceLocation(localStorage.locationId,localStorage.locationName,localStorage.locationLat,localStorage.locationLng);
            psValueStore.replaceSubLocation(localStorage.subLocationId,localStorage.subLocationName);
        }

        function changeLocation(){
            if(window.location.pathname.includes('item-list')){
                location.replace(window.location.origin+window.location.pathname+`?item_location_id=${localStorage.locationId}&item_location_township_id=${localStorage.subLocationId}&order_by=added_date&order_type=desc&status=1`)
            }
            else{
                window.location.reload();
            }
        }

        onMounted(() => {
         
            loadLocationData();
        });



        return {
            handleLanguage,
            activeLanguage,
            dataReady,
            ps_location_modal,
            itemLocationTownshipStore,
            itemLocationStore,
            openLocationModal,
            changeLocation,
            loadLocationData,
            languageSearch,
            // checkTownship,
            // city,
            // township,
            appInfoStore,
            psValueStore,
            PsInputWithLeftIcon,
            filteredLanguage,
            languageKeys
            
        }

    },
    methods: {
    ...mapActions([
      "toggleDarkMode",
    ]),

  },
  computed: {
    ...mapGetters([
      "isDarkMode"
    ]),

  },
});
</script>
