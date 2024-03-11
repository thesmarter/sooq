<template>
  <div
    :class="{ 'ps-0 xl:ps-76' : sidebarFull, 'ps-0 xl:ps-20' : !sidebarFull}"
    class=" fixed transition-all duration-600
      bg-white z-50
      dark:bg-secondary-800 dark:text-white
      shadow
      py-2
      pe-6
      items-center
      flex
      justify-between
        w-full
    "
  >
    <div class="text-secondary-800 dark:text-secondary-100 font-extrabold flex flex-row ps-6">
      <!-- Sidebar Toggle -->
      <button
        @click="handleShowMenu((!sidebarFull) ? false : showMenu);handleSidebarFull(!sidebarFull);"
        class="hidden xl:block"
      >
        <!-- Menu Icons -->
        <ps-icon name="hamburger"/>
      </button>

      <button
        @click="
        handleSidebarNavOpen(!sidebarNavOpen);
          handleShowMenu(!sidebarFull ? false : showMenu);
        "
        class="xl:hidden block"
      >
        <!-- Menu Icons -->
        <ps-icon name="hamburger"/>
      </button>
    </div>

    <div class="text-secondary-800 flex items-center ">
        <div class="ms-3 sm:ms-4 lg:ms-6 xxl:ms-8">
            <ps-dropdown align="left">
                <template #select>
                    <ps-dropdown-select padding="px-4 py-0.5"
                        :selectedValue="($page.props.languages).filter(language => language.symbol==activeLanguage)[0].name"
                        />
                </template>
                <template #list>
                    <div class="rounded-md shadow-xs w-32">
                        <div class="pt-2 z-30 ">
                            <div v-for="language in ($page.props.languages)" :key="language.id"
                                class="w-56 flex py-4 px-2 hover:bg-primary-000 dark:hover:bg-primary-900 cursor-pointer items-center"
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

        <!-- dark/light mode -->
        <ps-icon-toggle class="ms-3 sm:ms-4 lg:ms-6 xxl:ms-8" :selectedValue="isDarkMode" @onChange="toggleDarkMode" />

        <!-- for notification dropdown -->
        <!-- <ps-dropdown horizontalAlign="right" class='w-full' h="h-auto">
            <template #select>
                <div class="relative cursor-pointer">
                <div class="absolute w-3 h-3 ms-3 -top-1.5 bg-red-500 rounded-full text-xxs px-0.5 text-white"><span>1</span></div>
                <ps-icon name="bell" w="22" h="22" theme="#1F2937" class="ms-2"/>
            </div>
            </template>
            <template #list>
                <div class="rounded-md shadow-xs w-112">
                    <div class="z-30 ">
                        <div class="w-full items-center p-4 py-2">

                            <div class="flex justify-between">
                                <ps-label-header-6 textColor="text-secondary-800 dark:text-secondary-100">Notifications</ps-label-header-6>
                                <ps-label textColor="text-primary-500">Mark as all read</ps-label>
                            </div>
                            <ps-label textColor="text-primary-500 mt-2">Today</ps-label>

                        </div>

                        <div v-for="index in 3" :key="index">
                            <ps-activity theme="bg-indigo-100">
                                <template #content>
                                    <img class="mx-2 inline-block h-8 w-8 rounded-full ring-2 ring-white" src="https://images.unsplash.com/photo-1491528323818-fdd1faba62cc?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80" alt=""/>
                                    <span class="w-full">
                                        <span class="flex justify-between text-sm ">
                                            <ps-label>Alisa</ps-label>
                                            <ps-label-caption-3>by:admin@gmial.com</ps-label-caption-3>
                                        </span>
                                        <ps-label-caption-3>Hello</ps-label-caption-3>
                                    </span>
                                </template>
                            </ps-activity>
                        </div>

                        <ps-text-button class="w-full justify-center m-3" textColor="text-primary-500" @click="toContact()">View All Notification</ps-text-button>
                    </div>
                </div>
            </template>
        </ps-dropdown> -->
        <!-- end notification -->

        <!-- for message dropdown -->
        <ps-dropdown horizontalAlign="right" class='ms-3 sm:ms-4 lg:ms-6 xxl:ms-8 w-full' h="h-auto">
            <template #select>
                <ps-icon @click="clickMessageButton" name="email" class='text-secondary-800 dark:text-secondary-100 cursor-pointer text-'/>
            </template>
            <template #list>
                <div class="rounded-md shadow-xs w-72 sm:w-96 lg:w-140">
                    <div class="z-30 py-2">
                        <div class="w-full items-center px-4 py-2">

                            <div class="flex justify-between">
                                <ps-label class="text-md lg:text-lg" textColor="text-secondary-800 dark:text-secondary-100">{{$t('core__be_messages')}}</ps-label>
                                <ps-label v-if="$page.props.can.updateContact" @click="makeAllRead" class="text-xs sm:text-sm cursor-pointer" textColor="text-primary-500">{{$t('core__be_mark_all_as_read')}}</ps-label>
                            </div>
                            <ps-label textColor="text-sm sm:text-md text-primary-500 mt-4 lg:mt-6 mb-2">{{$t('core__be_new_messages')}}<span class="text-white text-center ms-2 px-1.5 text-sm py-0.5 rounded-full bg-primary-500" v-if="count > 0"> {{count }} </span></ps-label>
                            {{contacts.data.newMessageCount}}
                        </div>
                        <div v-if="loading">
                            <ps-label class="text-center py-6"> {{$t('core__be_loading')}} </ps-label>
                        </div>

                        <div v-else-if="contacts.data?.length > 0" class=" h-80 overflow-y-auto">
                            <div v-for="contact in contacts.data" :key="contact.id" class="cursor-pointer">
                                <div :class="contact.is_read == 0 ? 'bg-primary-50 dark:bg-primary-900': 'bg-secondary-50 dark:bg-secondary-900 '" class="rounded py-3 px-4 mb-2 text-base inline-flex items-center w-full shadow-sm">
                                    <div class="flex flex-row w-full items-center" @click="goToDetail(contact.id)">
                                        <!-- <template #content> -->
                                        <!-- <img class="mx-2 inline-block h-8 w-8 rounded-full ring-2 ring-white" src="https://images.unsplash.com/photo-1491528323818-fdd1faba62cc?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80" alt=""/> -->
                                        <div class="h-8 w-8 sm:w-12 sm:h-12">
                                            <img
                                            v-lazy=" { src: $page.props.uploadUrl + '/' + contact.owner?.user_cover_photo, loading: $page.props.sysImageUrl+'/loading_gif.gif', error: $page.props.sysImageUrl+'/default_profile.png' }"
                                             width="20" height="20"
                                                    class="mx-2  h-8 w-8 sm:w-12 sm:h-12 rounded-full ring-2 object-cover" alt="Profile photo"/>
                                            <!-- <img v-else :src="defaultProfileImg" width="20" height="20"
                                                    class="mx-2 h-8 w-8  sm:w-12 sm:h-12 rounded-full ring-2 object-cover" alt="Profile photo" @error="defaultProfileImg" /> -->
                                        </div>
                                        <span class="w-full ms-4">
                                            <span class="flex flex-col sm:flex-row sm:justify-between lg:justify-start lg:grid grid-cols-2 text-sm ">
                                                <ps-label class="font-semibold text-xs sm:text-sm">{{contact.contact_name}}</ps-label>
                                                <ps-label-caption-3>{{$t('core__be_by')}} : {{contact.contact_email}}</ps-label-caption-3>
                                            </span>
                                            <div class="truncate w-44 sm:w-68 lg:w-104 overflow-hidden">
                                                <ps-label-caption-3 class="truncate w-44 sm:w-68 lg:w-104">{{contact.contact_message}}</ps-label-caption-3>
                                            </div>
                                        </span>
                                        <!-- </template> -->
                                    </div>
                                    <ps-icon v-if="contact.authorization.delete" name="close" @click="clickedDeleteContact(contact.id)" class="text-secondary-400 text-sm ms-auto justify-end my-auto focus:shadow-none hover:text-purple-500"  />
                                </div>
                            </div>
                        </div>

                        <div v-else>
                            <ps-label  class="text-center py-6"> {{$t('core__be_no_contact_message')}} </ps-label>
                        </div>

                        <ps-text-button class="w-full justify-center m-3" textColor="text-primary-500" @click="toContact()">{{$t('core__be_view_all_message')}}</ps-text-button>
                    </div>
                </div>
            </template>
        </ps-dropdown>
        <!-- end message -->

        <!-- for profile dropdown -->
        <ps-dropdown horizontalAlign="right" class='ms-3 sm:ms-4 lg:ms-6 xxl:ms-8 w-full' h="h-auto">
            <template #select>
                <div class="h-8 w-8 rounded-full">
                <img v-if="$page.props.authUser?.user_cover_photo" class="object-cover h-8 w-8 rounded-full cursor-pointer"
                v-lazy=" { src: $page.props.uploadUrl + '/' + $page.props.authUser?.user_cover_photo, loading: $page.props.sysImageUrl+'/loading_gif.gif', error: $page.props.sysImageUrl+'/default_profile.png' }"
                 :alt="$t('core__be_profile')">
                <img v-else class="object-cover h-8 w-8 rounded-full cursor-pointer" :src="$page.props.uploadUrl + '/default_profile.png' "
                :alt="$t('core__be_profile')">
                </div>
            </template>
            <template #list>
                <div class="rounded-md shadow-xs w-56 ">
                    <div class="z-30 ">
                        <Link :href="route('user.profile.edit', $page.props.user)"
                            class="w-56 flex p-4 hover:bg-primary-000 dark:hover:bg-primary-900 cursor-pointer items-center">
                            <ps-icon name="user-line"  />
                            <ps-label class="ms-2">{{$t('core__be_profile')}}</ps-label>
                        </Link>
                        <Link :href="route('vendor.setSession')" v-if="$page.props.canAccessVendor"
                            class="w-56 flex p-4 hover:bg-primary-000 dark:hover:bg-primary-900 cursor-pointer items-center">
                            <ps-icon name="refresh"  />
                            <ps-label class="ms-2">{{$t('core__be_switch_to_vendor')}}</ps-label>
                        </Link>
                        <Link :href="route('dashboard')"
                            class="w-56 flex p-4 hover:bg-primary-000 dark:hover:bg-primary-900 cursor-pointer items-center">
                            <ps-icon name="refresh"  />
                            <ps-label class="ms-2">{{$t('core__be_switch_to_fe')}}</ps-label>
                        </Link>
                        <form @submit.prevent="logout">
                            <button type="submit" class="w-56 flex p-4 hover:bg-primary-000 dark:hover:bg-primary-900 cursor-pointer items-center">
                                <ps-icon name="signOut" />
                                <ps-label class="ms-2">{{$t('core__be_logout')}}</ps-label>
                            </button>
                        </form>
                    </div>
                </div>
            </template>
        </ps-dropdown>
        <!-- end profile -->

    </div>
    <ps-danger-dialog ref="ps_danger_dialog" />
    <ps-warning-dialog ref="ps_warning_dialog" />
  </div>
</template>
<script>
import { reactive, ref,computed } from 'vue';
import { mapActions, mapGetters } from "vuex";
import { Link } from '@inertiajs/vue3';
import PsIcon from "@/Components/Core/Icons/PsIcon.vue";
import PsDropdown from "@/Components/Core/Dropdown/PsDropdown.vue";
import PsDropdownSelect from "@/Components/Core/Dropdown/PsDropdownSelect.vue";
import PsLabel from "@/Components/Core/Label/PsLabel.vue";
import PsLabelCaption3 from "@/Components/Core/Label/PsLabelCaption3.vue";
import PsLabelHeader6 from "@/Components/Core/Label/PsLabelHeader6.vue";
import PsToggle from '@/Components/Core/Toggle/PsToggle.vue';
import PsButton from "@/Components/Core/Buttons/PsButton.vue";
import PsIconToggle from '@/Components/Core/Toggle/PsIconToggle.vue';
import PsTextButton from '@/Components/Core/Buttons/PsTextButton.vue';
import { trans, loadLanguageAsync } from 'laravel-vue-i18n';
import PsDangerDialog from "@/Components/Core/Dialog/PsDangerDialog.vue";
import PsWarningDialog from "@/Components/Core/Dialog/PsWarningDialog.vue";
import { router } from '@inertiajs/vue3'
import { useStore } from 'vuex'
import firebaseApp from 'firebase/app';
import "firebase/auth";

export default {
    components: {
        PsIcon,
        PsDropdown,
        PsDropdownSelect,
        PsLabel,
        Link,
        PsToggle,
        PsButton,
        PsIconToggle,
        PsLabelHeader6,
        // PsActivity,
        PsTextButton,
        PsLabelCaption3,
        PsDangerDialog,
        PsWarningDialog
    },
    props: ['can','defaultProfileImg'],
  data() {
    return {
      show: false,
      selectedLanguage: ''
    };
  },
  setup (){
    // console.log(JSON.stringify(firebaseApp.auth()))
    const contacts = reactive({data : {}});

    const loading = ref(false);
    const ps_danger_dialog = ref();
    const count = ref();
    const ps_warning_dialog = ref();
    const store = useStore();
    const activeLanguage = ref(localStorage.activeLanguage);

    async function clickMessageButton(){
        if(contacts.data != null && contacts.data.length > 0){
            await loadContact();
        }else{
            loading.value = true;
            await loadContact();
            loading.value = false;
        }
    }

    async function loadContact(){
        await axios.get(route('contact.getContactFormTitle'))
            .then(res => {
                contacts.data = res.data.contacts;
                count.value = res.data.unseenCount;
            })
            .catch(error => {
                    // psmodal.value.toggle(true);
                });
    }

    function clickedDeleteContact(id){
        ps_danger_dialog.value.openModal(
                trans('core__delete'),
                trans('core__comfirm_to_delete_contact'),
                trans('core__be_btn_confirm'),
                trans('core__be_btn_cancel'),
                () => {
                    router.delete(route("contact.destroy", id),{
                        onSuccess: () => {
                            loadContact();
                        },
                        onError: () => {

                        },
                        });
                },
                () => { }
            );
    }

    function makeAllRead(){
        ps_warning_dialog.value.openModal(
                trans('core__warning'),
                trans('core__comfirm_to_mark_as_read'),
                trans('core__be_btn_confirm'),
                trans('core__be_btn_cancel'),
                () => {
                    router.put(route("contact.allasread"),{
                        onSuccess: () => {
                            loadContact();
                        },
                        onError: () => {

                        },
                        });
                },
                () => { }
            );
    }

    function goToDetail(id){
         router.get(route("contact.edit",id));
    }

    function handleLanguage(row){
        // this.$inertia.put(route('language.changeLanguage',row.symbol));
        router.put(route('language.changeLanguage',row.symbol), '', {
            onSuccess: () => {
                loadLanguageAsync(row.symbol);
                store.dispatch('handleActiveLanguage',row.symbol);
                setTimeout(()=>{
                        window.location.reload();
                    },1000)
            }
        });
    }

    return {
        contacts,
        clickMessageButton,
        clickedDeleteContact,
        loading,
        ps_danger_dialog,
        makeAllRead,
        goToDetail,
        count,
        ps_warning_dialog,
        handleLanguage,
        activeLanguage
    }
  },
  computed: {
    ...mapGetters([
      "isDarkMode",
      "dir",
      "sidebarNavOpen",
      "sidebarFull",
      "showMenu",
      "supportedLanguages",
    ]),
  },
  methods: {
    ...mapActions([
      "handleSidebarFull",
      "handleSidebarNavOpen",
      "handleShowMenu",
      "toggleDir",
      "toggleDarkMode",
    ]),
    logout() {
        firebaseApp.auth().signOut();
        this.$inertia.post(route('logout'));
    },
    toContact() {
        this.$inertia.get(route('contact.index'));
    },

  },
};
</script>
