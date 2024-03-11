<template>
  <div ref="sidebarMenuC"
    class="h-screen bg-primary-50 dark:border-gray-600 dark:bg-secondary-900 text-secondary-800 dark:text-secondary-100 transition-all duration-30 p-4 fixed xl:relative overflow-y-scroll overflow-x-hidden scroll-smooth"
    :class="{ 'w-76': sidebarFull,'w-76 xl:w-20': !sidebarFull, 'top-0 start-0': sidebarNavOpen,'top-0 -left-76 xl:left-0': !sidebarNavOpen,}">
    <!-- sidebar title -->


    <ps-dropdown h="h-auto" align="left" class='lg:mt-2 mt-1  w-full'>
        <template #select>
            
            <div  class="cursor-pointer flex flex-row  ms-1 mt-4 justify-center xl:ms-0 " :class="!sidebarFull ? '' : 'ps-2 pe-2'">
                <div v-if="$page.props.currentVendor?.logo != null" class="rounded-lg pe-1" :class="!sidebarFull ? 'w-8 h-8' : 'h-12 w-12'">
                    <img  v-lazy=" { src: $page.props.uploadUrl + '/' + $page.props.currentVendor?.logo.img_path, loading: $page.props.sysImageUrl+'/default_vendor_shop.png', error: $page.props.sysImageUrl+'/default_vendor_shop.png' }"

                    class="rounded-lg object-contain" :class="!sidebarFull ? 'w-8 h-8' : 'h-12 w-12'"
                    />
                </div>
                <div v-else class="rounded-lg pe-1" :class="!sidebarFull ? 'w-8 h-8' : 'h-12 w-12'">
                    <img  :src="$page.props.sysImageUrl+'/default_vendor_shop.png'"

                    class="rounded-lg object-contain" :class="!sidebarFull ? 'w-8 h-8' : 'h-12 w-12'"
                    />
                </div>

            </div>
            <div  class="font-semibold flex flex-row mt-4 cursor-pointer" :class="!sidebarFull ? 'xl:hidden' : ''">
                <div  class="flex-grow flex flex-row justify-center items-center" >
                    <ps-label class="text-base w-52 truncate font-semibold" > {{ $page.props.currentVendor?.name }} </ps-label>
                </div>
                <ps-icon name="downChervon" />
            </div>
        </template>
        <template #list>
            <div class="rounded-md shadow-xs  " :class="!sidebarFull ? 'xl:w-10 w-64' : 'w-64'">
                <div class="pt-2 z-30 ">
                    <div v-for="vendor in $page.props.vendorList" :key="vendor.id"
                        class="w-full flex-row flex py-4 px-2 hover:bg-primary-000 dark:hover:bg-secondary-700 cursor-pointer items-center"
                        @click="clickVendor(vendor.id)"
                        >
                        <img  v-lazy=" { src: $page.props.uploadUrl + '/' + vendor?.logo?.img_path, loading: $page.props.sysImageUrl+'/default_vendor_shop.png', error: $page.props.sysImageUrl+'/default_vendor_shop.png' }"
                            :class="!sidebarFull ? 'xl:mx-0.5 mx-2' : 'mx-2'"
                            class=" rounded-lg object-contain w-8 h-8" 
                            />
                        <div :class="!sidebarFull ? 'xl:hidden' : ''">
                            <ps-label class="w-48 truncate" 
                                :class="vendor.id == $page.props.currentVendor?.id ? ' font-semibold' : ''">
                                {{ vendor.name }} </ps-label>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </ps-dropdown>

    <!-- search -->
    <div class="mt-5 mb-5" :class="sidebarFull ? 'flex' : 'flex xl:hidden'">
        <ps-input-with-right-icon v-model:value="searchterm"
        theme="bg-white dark:bg-secondaryDark-black border-none rounded-lg placeholder-secondary-800 dark:placeholder-secondary-200" class="w-full rounded-full flex" :placeholder="$t('core__be_search')" >
            <template #icon >
                <ps-icon name="search" class='cursor-pointer' theme="text-secondary-800 dark:text-secondary-200" />
            </template>
        </ps-input-with-right-icon>
    </div>
    <div class="rounded-lg p-1 mt-6 mb-8 w-full flex items-center text-secondary-800 bg-primary-50 hover:bg-primary-200 dark:hover:bg-secondary-700 dark:text-secondary-300 dark:bg-secondary-900"
    :class="sidebarFull ? 'hidden' : 'hidden xl:flex'"
     @click="handleSidebarFull">
        <ps-icon name="search" class='cursor-pointer' w="20" h="20" />
    </div>
    <!-- sidebar navigation -->
    <div class="grid grid-cols-1 divide-y-4">
        <div class="">
            <!-- Dashboard -->
            <!-- <div class="relative" >
                <div >
                    <side-bar-tab
                    :name='$t("core__be_dashboard_label")'
                    :showIcon="true"
                    icon="chart-line"
                    url="admin.index"
                    :showGroupIcon="false"/>
                </div>
            </div> -->

            <div v-for="menugroup in $page.props.vendorMenuGroups" :key="menugroup.id">
                <ps-label :class="{ 'flex': sidebarFull, 'hidden' : !sidebarFull }" class="mt-4 mb-2" v-if="menugroup.sub_menu_group.length != 0 && searchterm==null && menugroup.is_invisible_group_name != 1"> {{ $t(menugroup.group_lang_key) }} </ps-label>
                <ps-label :class="{ 'flex': sidebarFull, 'hidden' : !sidebarFull }" class="mt-4 mb-2" v-else-if="menugroup.sub_menu_group.length != 0 && menugroup.is_invisible_group_name != 1">
                    <span v-if="$t(menugroup.group_lang_key).toLowerCase().trim().includes(searchterm.toLowerCase().trim())">
                        {{ $t(menugroup.group_lang_key) }}
                    </span>
                    <span v-else-if="menugroup.sub_menu_group.filter(
                        (sub)=>$t(sub.sub_menu_lang_key).toLowerCase().trim().includes(searchterm.toLowerCase().trim()))
                        .length>0">
                        {{ $t(menugroup.group_lang_key) }}
                    </span>
                    <span v-else-if="menugroup.sub_menu_group.filter(
                        (sub)=> sub.is_dropdown==1 && sub.module.filter((module)=>
                            $t(module.module_lang_key).toLowerCase().trim().includes(searchterm.toLowerCase().trim())
                        ).length>0).length>0">
                        {{ $t(menugroup.group_lang_key) }}
                    </span>
                    <span v-else></span>
                </ps-label>
                <div v-for="group in menugroup.sub_menu_group" :key="group.id" class="mt-2">
                    <sidebar-menu-item v-model:dropDown="dropDownOpen" :group="group" :searchterm="searchterm" ></sidebar-menu-item>
                    <!-- <sidebar-menu-item v-if="group.module.length > 0 && group.is_dropdown == 1" :group="group" :searchterm="searchterm" ></sidebar-menu-item> -->
                    <!-- <sidebar-menu-item v-if="group.is_dropdown == 0" :group="group" :searchterm="searchterm" ></sidebar-menu-item>   -->
                </div>


            </div>
        </div>
        <div class=" flex items-center justify-center my-2 py-2">
                    <ps-label
                        class="text-xs font-regular"
                        textColor="text-gray-500"
                        :class="!sidebarFull ? 'xl:hidden' : 'ms-1 mt-1'">
                        {{ $t('core__be_version') }} {{ $page.props.backendSetting.backend_version_no }}
                    </ps-label>
        </div>
    </div>
  </div>
</template>

<script>

import { Link } from '@inertiajs/vue3';
import { computed, onMounted, onUnmounted, ref } from 'vue';
import { useStore } from 'vuex';
import SidebarMenuItem from '@/Components/Layouts/Sidebar/SidebarMenuItem.vue';
import SideBarTab from '@/Components/Layouts/Sidebar/SideBarTab.vue'
import PsLabel from "@/Components/Core/Label/PsLabel.vue";
import PsIcon from "@/Components/Core/Icons/PsIcon.vue";
import PsInputWithRightIcon from '@/Components/Core/Input/PsInputWithRightIcon.vue';
import sidebarMenuJson from './tempJson.json'
import PsDropdown from "@/Components/Core/Dropdown/PsDropdown.vue";
import { router } from '@inertiajs/vue3';

export default {
  components:{ Link, SidebarMenuItem,SideBarTab,PsLabel,PsIcon,PsInputWithRightIcon,PsDropdown},
  setup(){
    const searchterm = ref('')
    const store = useStore();
    const sidebarNavOpen = computed(() => store.getters.sidebarNavOpen);
    const sidebarFull = computed(() => store.getters.sidebarFull);
    const sidebarActive = ref(computed(() => store.getters.sidebarActive));
    const dropDownOpen = ref('0');

    function handleSidebarNavOpen(v){
        store.dispatch('handleSidebarNavOpen',v);
    }
    function handleSidebarFull(v){
        store.dispatch('handleSidebarFull',v);
    }

    const sidebarMenuC = ref(null);

    function handleScroll(){
        localStorage.sidebarScroll = sidebarMenuC.value.scrollTop;
    }

    onUnmounted(() => {
        // sidebarMenuC.value.removeEventListener('scroll', handleScroll);
    })

    onMounted(() => {
        sidebarMenuC.value.addEventListener("scroll", handleScroll);

        if(localStorage.sidebarScroll != null){
            setTimeout(() => {
                sidebarMenuC.value.scrollTo(0,parseInt(localStorage.sidebarScroll));
            }, 1000);
        }
    })

    function clickVendor(id){
        router.put(route("vendor.changeVendor", id));
    }

    return{
        searchterm,
        sidebarNavOpen,
        handleSidebarFull,
        sidebarFull,
        sidebarActive,
        dropDownOpen,
        handleSidebarNavOpen,
        sidebarMenuC,
        sidebarMenuJson,
        clickVendor

    }
  },
};
</script>


