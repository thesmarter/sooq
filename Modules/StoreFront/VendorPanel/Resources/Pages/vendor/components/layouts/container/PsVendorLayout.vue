<template>

    <Head>
        <link v-if="$page.props.favIcon" rel="icon" type="image/svg+xml"
            :href="$page.props.uploadUrl + '/' + $page.props.favIcon.img_path" />
    </Head>

    <div class="flex flex-row" :dir="getDir()">

        <div class="flex-grow w-full flex flex-col
      dark:bg-secondaryDark-black dark:text-textLight">

            <!-- content -->
            <div @click="clickOutsideSidebar"
                :class="{ 'xl:ms-76 ms-0': sidebarFull, 'ms-0 xl:ms-20': !sidebarFull }"
                class="  h-screen  pt-18">
                <div class="h-full px-4  transition-all duration-600 overflow-x-hidden overflow-y-auto scroll-smooth">

                    <div class="pb-18">
                        <slot />
                    </div>
                </div>
            </div>

        </div>

        <div class="fixed" >
            <title-bar />
        </div>

        <!-- left -->
        <div class="min-h-screen antialiased flex fixed ltr:left-0 rtl:right-0"
            >
            <sidebar-menu />
        </div>

    </div>
</template>

<script>
import { defineComponent, computed, onMounted, reactive,watch,  ref } from "vue";
import TitleBar from "@vendorPanel/vendor/components/layouts/titlebar/TitleBar.vue";
import SidebarMenu from "@vendorPanel/vendor/components/layouts/sidebar/SidebarMenu.vue";
import { Head, Link } from '@inertiajs/vue3';
import { useStore } from 'vuex'
import { usePage } from '@inertiajs/vue3'
import { trans } from 'laravel-vue-i18n';
import { router } from '@inertiajs/vue3'
import firebaseApp from 'firebase/app';
import "firebase/auth";
import PsLabel from "@/Components/Core/Label/PsLabel.vue";
import PsIcon from "@/Components/Core/Icons/PsIcon.vue";
import PsButton from "@/Components/Core/Buttons/PsButton.vue";

export default defineComponent({
    components: { TitleBar, SidebarMenu, Head, Link, PsLabel, PsIcon, PsButton},
    props: [
        "title",
        'firebaseConfig',
        'status',
        'errors',
        ],
    setup(props) {

        const firebaseConfiguration = JSON.parse(props.firebaseConfig);

        if (firebaseApp.apps.length < 1) {
            firebaseApp.initializeApp(firebaseConfiguration);
        }
        const store = useStore();
        const isDarkMode = computed(() => store.getters.isDarkMode);
        const sidebarFull = computed(() => store.getters.sidebarFull);
        const dir = computed(() => store.getters.dir);

        const mode = reactive({
            dark: isDarkMode
        });
        watch(() => mode.dark, (newValue, oldValue) => {
            if(newValue){
                document.documentElement.classList.add('dark');
            }else{
                document.documentElement.classList.remove('dark');
            }
        })



        onMounted(() => {

            var loading = document. getElementById("home_loading__container");
            loading.style.display = "none";

            const currentRouteArr = usePage().props.currentRoute;
            const currentRoute = currentRouteArr.split(".")[0];
            const menugroup = usePage().props.vendorMenuGroups;

            if (currentRoute == 'vendor_info') {
                const dashboardName = 'core__vendor_my_store_module';
                localStorage.sidebarScroll = 0;
                setTimeout(() => {
                    store.dispatch('handleSidebarActive', trans(dashboardName));
                }, 100);
            } else {
                for (let i = 0; i < menugroup.length; i++) {
                    for (let j = 0; j < menugroup[i].sub_menu_group.length; j++) {
                        if (menugroup[i].sub_menu_group[j].module.length > 0) {
                            for (let k = 0; k < menugroup[i].sub_menu_group[j].module.length; k++) {
                                if (menugroup[i].sub_menu_group[j].module[k].module_name == currentRoute) {
                                    const moduleLangName = menugroup[i].sub_menu_group[j].module[k].module_lang_key;
                                    setTimeout(() => {
                                        store.dispatch('handleSidebarActive', trans(moduleLangName));
                                    }, 100);
                                }
                            }
                        } else {
                            if (menugroup[i].sub_menu_group[j].sub_menu_name == currentRoute) {
                                const moduleLangName = menugroup[i].sub_menu_group[j].sub_menu_lang_key;
                                setTimeout(() => {
                                    store.dispatch('handleSidebarActive', trans(moduleLangName));
                                }, 100);
                            }
                        }

                    }
                }
            }

            // for dark or light mode local storage
            store.dispatch('loadIsDarkMode');

            // for language switch local storage
            store.dispatch('loadActiveLanguage');

            // for rtl or ltr directory switch local storage
            store.dispatch('loadDirectory');

        });


        router.on('start', (event) => {
            // store.dispatch('handleSidebarFull',false);
            //  store.dispatch('handleShowMenu',false);
            store.dispatch('handleSidebarNavOpen', false);
        })

        function getDir(){
            if(localStorage.activeLanguage != null && localStorage.activeLanguage != undefined && localStorage.activeLanguage == 'ar'){
                return 'rtl';
            }else{
                return 'ltr';
            }
        }

        function clickOutsideSidebar() {
            store.dispatch('handleSidebarNavOpen', false);
        }


        return {
            getDir,
            isDarkMode,
            dir,
            sidebarFull,
            clickOutsideSidebar,
        }

    }
});
</script>


