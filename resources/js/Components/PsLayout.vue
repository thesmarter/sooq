<template>

    <Head>
        <link v-if="$page.props.favIcon" rel="icon" type="image/svg+xml"
              :href="$page.props.uploadUrl + '/' + $page.props.favIcon.img_path"/>
    </Head>

    <div class="flex flex-row" :dir="getDir()">

        <!-- right -->
        <div class="flex-grow w-full flex flex-col
      dark:bg-secondaryDark-black dark:text-textLight">

            <!-- content -->
            <div @click="clickOutsideSidebar" :style="[!project.ps_license_code ? 'margin-top: 24px !important;' : '']"
                 :class="{ 'xl:ms-76 ms-0': sidebarFull, 'ms-0 xl:ms-20': !sidebarFull }"
                 class="  h-screen  pt-18">
                <div class="h-full px-4  transition-all duration-600 overflow-x-hidden overflow-y-auto scroll-smooth">
                    <!-- <Link :href="route('versionUpdate.index')" v-if="$page.props.checkingVersionUpdate && isHideAlert">
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                            <strong class="font-bold">Holy smokes!</strong>
                            <span class="block sm:inline">Something seriously bad happened.</span>
                            <span class="absolute top-0 bottom-0 right-0 px-4 py-3" @click="isHide()">
                                <svg class="fill-current h-6 w-6 text-red-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><title>Close</title><path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/></svg>
                            </span>
                        </div>
                    </Link> -->
                    <div class="pb-18">
                        <div>
                            <div v-if="isVersionUpdate"
                                 class="flex p-6 gap-4 bg-yellow-50 rounded shadow border-l-4 border-yellow-500 mb-4">
                                <div class="">
                                    <ps-icon class="text-primary-500" name="hond" w="32" h="32" viewBox="0 0 32 32"/>
                                </div>
                                <div class="w-full">
                                    <div class="flex justify-between w-full">
                                        <ps-label textColor="leading-6 text-base font-medium text-primary-500">
                                            {{ $t("core_be__version_update_noti_title") }}
                                        </ps-label>
                                        <ps-label
                                            textColor="leading-5 text-sm text-secondary-400 flex items-center gap-2 cursor-pointer"
                                            @click="isVersionUpdate = false">
                                            <ps-icon name="close-fill" w="16" h="16" viewBox="0 0 16 16"/>
                                            {{ $t("core_be__dismiss") }}
                                        </ps-label>
                                    </div>
                                    <ps-label textColor="text-sm text-secondary-500 mt-3">
                                        {{ $t("core_be__version_update_noti_desc") }}
                                    </ps-label>
                                    <ps-button class="mt-6" rounded="rounded">
                                        <Link :href="route('NextLaravelUpdater::welcome')">
                                            {{ $t("btn_update") }}
                                        </Link>
                                    </ps-button>
                                </div>
                            </div>

                            <div v-if="isProjectChanged"
                                 class="flex p-6 gap-4 bg-yellow-50 rounded shadow border-l-4 border-yellow-500 mb-4">
                                <div class="">
                                    <ps-icon class="text-primary-500" name="project" w="32" h="32" viewBox="0 0 32 32"/>
                                </div>
                                <div class="w-full">
                                    <div class="flex justify-between w-full">
                                        <ps-label textColor="leading-6 text-base font-medium text-primary-500">
                                            {{ $t("core_be__project_change_noti_title") }}
                                        </ps-label>
                                        <ps-label
                                            textColor="leading-5 text-sm text-secondary-400 flex items-center gap-2 cursor-pointer"
                                            @click="isProjectChanged = false">
                                            <ps-icon name="close-fill" w="16" h="16" viewBox="0 0 16 16"/>
                                            {{ $t("core_be__dismiss") }}
                                        </ps-label>
                                    </div>
                                    <ps-label textColor="text-sm text-secondary-500 mt-3">
                                        {{ $t("core_be__project_change_noti_desc") }}
                                    </ps-label>
                                    <ps-button class="mt-6" rounded="rounded" @click="replaceTable()">
                                        {{ $t("setup") }}
                                    </ps-button>
                                </div>
                            </div>

                            <div v-if="isConnected"
                                 class="flex p-6 gap-4 bg-yellow-50 rounded shadow border-l-4 border-yellow-500 mb-4">
                                <div class="">
                                    <ps-icon class="text-primary-500" name="connection" w="32" h="32"
                                             viewBox="0 0 32 32"/>
                                </div>
                                <div class="w-full">
                                    <div class="flex justify-between w-full">
                                        <ps-label textColor="leading-6 text-base font-medium text-primary-500">
                                            {{ $t("core_be__losing_connection_noti_title") }}
                                        </ps-label>
                                        <ps-label
                                            textColor="leading-5 text-sm text-secondary-400 flex items-center gap-2 cursor-pointer"
                                            @click="isConnected = false">
                                            <ps-icon name="close-fill" w="16" h="16" viewBox="0 0 16 16"/>
                                            {{ $t("core_be__dismiss") }}
                                        </ps-label>
                                    </div>
                                    <ps-label textColor="text-sm text-secondary-500 mt-3">
                                        {{ $t("core_be__losing_connection_noti_desc") }}
                                    </ps-label>
                                    <ps-button class="mt-6" rounded="rounded">
                                        <a href="https://www.docs.smart.sd/psx-mpc/manual/web-manual/api-token-deleted-or-updated"
                                           target="_blank">{{ $t("core_be__open_token_key_documentation") }}</a>
                                    </ps-button>
                                </div>
                            </div>

                        </div>
                        <slot/>
                    </div>
                </div>
            </div>

        </div>

        <div class="fixed" :style="[!project.ps_license_code ? 'top: 24px !important;' : '']">
            <title-bar/>
        </div>

        <!-- left -->
        <div class="min-h-screen antialiased flex fixed ltr:left-0 rtl:right-0"
             :style="[!project.ps_license_code ? 'top: 24px !important;' : '']">
            <sidebar-menu/>
        </div>

    </div>
    <ps-success-dialog ref="ps_success_dialog"/>
</template>

<script>
import {defineComponent, computed, onMounted, reactive, watch, ref} from "vue";
import TitleBar from "@/Components/Layouts/TitleBar/TitleBar.vue";
import SidebarMenu from "@/Components/Layouts/Sidebar/SidebarMenu.vue";
import {Head, Link} from '@inertiajs/vue3';
import {useStore} from 'vuex'
import {usePage} from '@inertiajs/vue3'
import {trans} from 'laravel-vue-i18n';
import {router} from '@inertiajs/vue3'
import firebaseApp from 'firebase/app';
import "firebase/auth";
import PsLabel from "@/Components/Core/Label/PsLabel.vue";
import PsIcon from "@/Components/Core/Icons/PsIcon.vue";
import PsButton from "@/Components/Core/Buttons/PsButton.vue";
import PsSuccessDialog from '@/Components/Core/Dialog/PsSuccessDialog.vue';

export default defineComponent({
    components: {TitleBar, SidebarMenu, Head, Link, PsLabel, PsIcon, PsButton, PsSuccessDialog},
    props: [
        "title",
        "project",
        'firebaseConfig',
        'builderAppInfo',
        'hasError',
        'logMessages',
        'status',
        'purchased_code',
        'project',
        'errors',
        'systemCode2',
        'checkVersionUpdate'
    ],
    setup(props) {

        const firebaseConfiguration = JSON.parse(props.firebaseConfig);

        if (firebaseApp.apps.length < 1) {
            firebaseApp.initializeApp(firebaseConfiguration);
        }
        const isHideAlert = ref(1);
        const store = useStore();
        const isDarkMode = computed(() => store.getters.isDarkMode);
        const sidebarFull = computed(() => store.getters.sidebarFull);
        const dir = computed(() => store.getters.dir);

        const mode = reactive({
            dark: isDarkMode
        });
        watch(() => mode.dark, (newValue, oldValue) => {
            if (newValue) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        })

        const isVersionUpdate = ref(false);
        const isProjectChanged = ref(false);
        const isConnected = ref(false);
        const ps_success_dialog = ref();
        const ps_license_activate_modal = ref();

        if (props.builderAppInfo != null && props.builderAppInfo != '') {

            const builderVersioncode = ref(props.builderAppInfo?.latestVersion?.version_code);
            const currentVersionUpdate = ref(props.checkVersionUpdate);

            if (currentVersionUpdate.value != null) {
                const sourceCode = ref(currentVersionUpdate.value.source_code_version_code == builderVersioncode.value ? true : false);
                const backendLanguage = ref(currentVersionUpdate.value.backend_language_version_code == builderVersioncode.value ? true : false);
                const frontendLanguage = ref(currentVersionUpdate.value.frontend_language_version_code == builderVersioncode.value ? true : false);
                const mobileLanguage = ref(currentVersionUpdate.value.mobile_language_version_code == builderVersioncode.value ? true : false);
                const syncAble = ref(props.builderAppInfo.syncAble == 1 ? true : false);
                if (sourceCode.value && backendLanguage.value && frontendLanguage.value && mobileLanguage.value && !syncAble.value) {
                    isVersionUpdate.value = false;
                } else {
                    isVersionUpdate.value = true;
                }
            } else {
                isVersionUpdate.value = true;
            }

            isProjectChanged.value = props.builderAppInfo.isProjectChanged == 1 ? true : false;
            isConnected.value = props.builderAppInfo.isConnected == 0 ? true : false;
        }
        console.log("builderAppInfo", props.builderAppInfo);
        console.log("checkVersionUpdate", props.checkVersionUpdate);

        function replaceTable() {
            router.get(route("table.replace"));
        }

        function clickOutsideSidebar() {
            store.dispatch('handleSidebarNavOpen', false);
        }

        // const route = ref(window.location.href);


        onMounted(() => {

            var loading = document.getElementById("home_loading__container");
            loading.style.display = "none";

            const currentRouteArr = usePage().props.currentRoute;
            const currentRoute = currentRouteArr.split(".")[0];
            const menugroup = usePage().props.menuGroups;

            if (props.builderAppInfo != null && props.builderAppInfo.isValid == 0) {
                ps_license_activate_modal.value.openModal(trans('pls_activate_your_app'), 'You have successfully imported the file.', 'Back',
                    () => {
                        console.log('open');
                    },
                    false);
            }

            if (currentRoute == 'admin') {
                const dashboardName = 'core__be_dashboard_label';
                localStorage.sidebarScroll = 0;
                setTimeout(() => {
                    store.dispatch('handleSidebarActive', trans(dashboardName));
                }, 100);
            } else {
                for (let i = 0; i < menugroup.length; i++) {
                    for (let j = 0; j < menugroup[i].sub_menu_group.length; j++) {
                        if (menugroup[i].sub_menu_group[j].module.length > 0) {
                            for (let k = 0; k < menugroup[i].sub_menu_group[j].module.length; k++) {
                                if (menugroup[i].sub_menu_group[j].module[k].module_name === currentRoute) {
                                    const moduleLangName = menugroup[i].sub_menu_group[j].module[k].module_lang_key;
                                    setTimeout(() => {
                                        store.dispatch('handleSidebarActive', trans(moduleLangName));
                                    }, 100);
                                }
                            }
                        } else {
                            if (menugroup[i].sub_menu_group[j].sub_menu_name === currentRoute) {
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

            isHideAlert.value = 1;

        });


        router.on('start', (event) => {
            // store.dispatch('handleSidebarFull',false);
            //  store.dispatch('handleShowMenu',false);
            store.dispatch('handleSidebarNavOpen', false);
        })

        function getDir() {
            if (localStorage.activeLanguage != null && localStorage.activeLanguage === 'ar') {
                return 'rtl';
            } else {
                return 'ltr';
            }
        }

        function isHide() {
            isHideAlert.value = !isHideAlert.value;
        }

        return {
            replaceTable,
            ps_success_dialog,
            ps_license_activate_modal,
            getDir,
            isDarkMode,
            dir,
            sidebarFull,
            clickOutsideSidebar,
            isHide,
            isHideAlert,
            isVersionUpdate,
            isProjectChanged,
            isConnected
        }

    }
});
</script>


