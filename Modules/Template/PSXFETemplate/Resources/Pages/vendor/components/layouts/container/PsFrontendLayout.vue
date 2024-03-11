
<style>
/* @import "css/app.css"; */

.fade-enter,
.fade-leave-to
 {
  opacity: 0;
}

.fade-enter-to,
.fade-leave {
    opacity: 1;
}

.chat:hover .del{
    display: block;
}

.del{
    display: none;
}

</style>

<template>
<Head>
  <link rel="icon" type="image/svg+xml" :href="$page.props.thumb1xUrl + '/'+ appInfoStore.appInfo.data?.frontendConfigSetting?.frontendIcon?.imgPath" />
</Head>

    <div v-if="dataReady" :dir="getDir()" class='bg-feAchromatic-50 h-full'>
    <div  class="  w-full h-full flex flex-col min-h-screen ">

        <!-- Body -->
        <main class="flex-grow bg-feAchromatic-50 dark:bg-feAchromatic-900">
            <slot />
        </main>
        <!-- End Body -->

        <div class="top-0 fixed">
        <ps-nav-tab-bar  :topOfPage="atTopOfPage" />
        <ps-nav-bar :topOfPage="atTopOfPage" />
        </div>

        <!-- Footer -->
        <footer>
            <footer-view />
        </footer>
        <!-- End Footer -->

        </div>
    </div>
    <ps-notification-box></ps-notification-box>
</template>

<script lang="ts">

// libs
import { defineComponent, onMounted , onUnmounted, ref,reactive, computed,watch } from 'vue';
import { trans } from 'laravel-vue-i18n';
import { Head, Link } from '@inertiajs/vue3';
import { useStore } from 'vuex'


// Components
import FooterView from '@template1/vendor/views/general/FooterView.vue';
import PsNavTabBar from '../navbar/PsNavTabBar.vue';
import PsNavBar from '../navbar/PsNavBar.vue';
import PsIcon from '../../core/icons/PsIcon.vue';
import PsNotificationBox from '@template1/vendor/components/core/notificationbox/PsNotificationBox.vue';
import AppInfoParameterHolder from '@templateCore/object/holder/AppInfoParameterHolder';
import { usePsAppInfoStoreState } from '@templateCore/store/modules/appinfo/AppInfoStore'
import { PsValueStore } from '@templateCore/store/modules/core/PsValueStore';
import firebase from "firebase/app";
import 'firebase/messaging'
import PsConst from '@templateCore/object/constant/ps_constants';
import { useNotiStoreState } from "@templateCore/store/modules/noti/NotificationStore";
import NotiRegisterHolder from '@templateCore/object/holder/NotiRegisterHolder';
import NotiUnRegisterHolder from '@templateCore/object/holder/NotiUnRegisterHolder';
import { router } from '@inertiajs/vue3'
import firebaseApp from 'firebase/app';
import 'firebase/database';
import "https://unpkg.com/delayed-scroll-restoration-polyfill@0.1.1/index.js";

export default defineComponent({
    name : "PsFrontendLayout",
    components: {
        PsNavBar,
        FooterView,
        PsIcon,
        PsNavTabBar,
        Head, Link,
        PsNotificationBox,
    },
  props:['authUser','backendSetting','firebaseConfig','webPushKey', 'appUrl'],
    setup(props) {

        // For Scroll Position Restore When click Back button
        // Handling manual for polyfill
        if ('scrollRestoration' in window.history) {
           window.history.scrollRestoration = 'manual';
        }

        // Monitoring the back action
        // it will set true when back action.
        // But current is only supported for item list
        // That means if back action is target to item list
        // it will mark as true in popStateDetected
        window.popStateDetected = false; // declared in resources/js/Types/index.d.ts
        window.addEventListener('popstate', (event) => {
            // console.log(String(event.state.url));
            if(event != null 
                && event.state != null
                && event.state.url != null 
                && ( String(event.state.url).includes("item-list", 1)
                    || String(event.state.url).includes("active-items", 1)
                    || String(event.state.url).includes("other-profile", 1)
                )) {
                    window.popStateDetected = true;
                    // console.log("Event Trggered");                
            }else {
                // console.log("Else");
                window.popStateDetected = false;
            }
            
        })
        

        const atTopOfPage = ref(true);
        const dataReady = ref(true);

        const appInfoStore = usePsAppInfoStoreState();
        const appInfoParameterHolder = new AppInfoParameterHolder();
        let psValueStore = PsValueStore();
        const loginUserId = ref(props.authUser ? props.authUser.id : PsConst.NO_LOGIN_USER);
        psValueStore.replaceLoginUserId(loginUserId.value);

        appInfoParameterHolder.userId = loginUserId.value;
        appInfoStore.loadAppInfo(appInfoParameterHolder);

        const store = useStore();
        const notiStore = useNotiStoreState();
        const regiterHolder = new NotiRegisterHolder();
        const unRegiterHolder = new NotiUnRegisterHolder();
        const isDarkMode = computed(() => store.getters.isDarkMode);
        const mode = reactive({
            dark: isDarkMode
        });
        const dir = computed(() => store.getters.dir);

        router.on('finish', (event) => {
            if(localStorage.loginUserId && localStorage.loginUserId != '' && localStorage.loginUserId != null && localStorage.loginUserId != undefined && localStorage.loginUserId != PsConst.NO_LOGIN_USER){
                if(firebase.apps.length >= 1){
                    const userRef = firebaseApp.database().ref('User_Presence');
                    if(route().current() == 'fe_chat'){
                        const chat_user_presence = {
                            userId : localStorage.loginUserId,
                            userName : 'Tester'
                        };
                        userRef.child(localStorage.loginUserId).set(chat_user_presence);
                        // console.log('online');
                    }else{
                        userRef.child(localStorage.loginUserId).remove();
                        // console.log('offline');
                    }
                }

            }
        })

        watch(() => mode.dark, (newValue, oldValue) => {
            if(newValue){
                document.documentElement.classList.add('dark');
            }else{
                document.documentElement.classList.remove('dark');
            }
        })

        watch(() => props.authUser, (currentValue, oldValue) => {
            if (currentValue) {
                if(loginUserId.value != currentValue.id){
                    loginUserId.value = currentValue.id;
                    psValueStore.replaceLoginUserId(currentValue.id);
                    resetNotiSetting();
                }
            }else{
                firebase.auth().signOut();
                loginUserId.value = PsConst.NO_LOGIN_USER;
                psValueStore.replaceLoginUserId(PsConst.NO_LOGIN_USER);
            }

        })

        const firebaseConfiguration = JSON.parse(props.firebaseConfig);

        if (firebase.apps.length < 1) {
                firebase.initializeApp(firebaseConfiguration);
            }
            const messaging = firebase.messaging.isSupported() ? firebase.messaging() : null;
            if(messaging){
                Notification.requestPermission().then((permission) => {
                    if (permission === 'granted') {
                        console.log('Notification permission granted.');
                    } else {
                        console.log('Unable to get permission to notify.');
                    }
                });
            }


            function subscribeTokenToTopic(token, topic) {
                if(messaging){
                    fetch('https://iid.googleapis.com/iid/v1/' + token + '/rel/topics/' + topic, {
                        method: 'POST',
                        headers: new Headers({
                            'Authorization': 'key='+props.backendSetting.fcm_api_key
                        })
                    }).then(response => {
                        if (response.status < 200 || response.status >= 400) {
                            throw 'Error subscribing to topic: ' + response.status + ' - ' + response.text();
                        }
                        console.log('Subscribed to "' + topic + '"');
                    }).catch(error => {
                        console.error(error);
                    })
                }
            }

            ///end firebase noti


        ///end firebase noti
        function handleScroll(){
            // when the user scrolls, check the pageYOffset
            if(window.pageYOffset>30){
                // user is scrolled
                if(atTopOfPage.value) atTopOfPage.value = false;
            }else{
                // user is at top of page
                if(!atTopOfPage.value) atTopOfPage.value = true;
            }
        }


        onMounted( async () =>{

            
            
            window.addEventListener('scroll', handleScroll);

            var loading = document. getElementById("home_loading__container");
            loading.style.display = "none";

            if ("serviceWorker" in navigator) {

                if(messaging){
                    let appUrl = props.appUrl;
                    let url=appUrl+"/firebase-messaging-sw.js";
                    // console.log(appUrl);
                    // console.log(appUrl.endsWith("/"));

                    if(appUrl != null 
                        && String(appUrl).endsWith("/")){
                        url = appUrl+"firebase-messaging-sw.js";
                    }
                    navigator.serviceWorker .register(url)
                    .then(function(registration) {
                        messaging.getToken({vapidKey: props.webPushKey, serviceWorkerRegistration : registration })
                        .then(async (currentToken) =>
                        {
                            if (currentToken) {
                                console.log('current token for client: ', currentToken);
                                localStorage.deviceToken = currentToken;
                                psValueStore.replacedeviceToken(localStorage.deviceToken);
                                subscribeTokenToTopic(currentToken,'fe_broadcast');

                                await appInfoStore.loadAppInfo();
                                psValueStore.loadData();

                                if(localStorage.getItem("showProfile") == null || localStorage.showProfile == ''){
                                    if(appInfoStore.appInfo.data.mobileSetting.is_show_owner_info == '1'){
                                        localStorage.showProfile = 'show';

                                    }else{
                                        localStorage.showProfile = 'hide';
                                    }

                                }
                                if(localStorage.getItem("notiSetting") == null || localStorage.notiSetting == ''){
                                    resetNotiSetting();
                                }else if(localStorage.getItem("notiSetting") == "true"){
                                    regiterHolder.platformName = PsConst.PLATFORM;
                                    regiterHolder.deviceId = psValueStore.deviceToken;
                                    regiterHolder.loginUserId = loginUserId.value;
                                    notiStore.registerNotiToken(regiterHolder);
                                }else{
                                    unRegiterHolder.platformName = PsConst.PLATFORM;
                                    unRegiterHolder.deviceId = psValueStore.deviceToken;
                                    unRegiterHolder.userId = loginUserId.value;
                                    notiStore.unRegisterNotiToken(unRegiterHolder);
                                }
                                psValueStore.replaceshowProfile(localStorage.showProfile);
                                psValueStore.replaceNotiSetting(localStorage.notiSetting);

                            }
                        }).catch((err) => {
                            console.log('An error occurred while retrieving token. ', err);
                            // catch error while creating client token
                        });
                    }) .catch(function(err) {
                        console.log("Service worker registration failed, error:" , err );
                    });
                }
            }else{
                console.log('no serviceWorker in navigator');
            }



        }
        )

        onUnmounted(() => {
            window.removeEventListener('scroll', handleScroll);
        })

        function resetNotiSetting(){
            if(appInfoStore.appInfo.data.frontendConfigSetting.enableNotification == '1'){
                    localStorage.notiSetting = 'true';
                    regiterHolder.platformName = PsConst.PLATFORM;
                    regiterHolder.deviceId = psValueStore.deviceToken;
                    regiterHolder.loginUserId = loginUserId.value;
                    notiStore.registerNotiToken(regiterHolder);
                }else{
                    localStorage.notiSetting = 'hide';
                    unRegiterHolder.platformName = PsConst.PLATFORM;
                    unRegiterHolder.deviceId = psValueStore.deviceToken;
                    unRegiterHolder.userId = loginUserId.value;
                    notiStore.unRegisterNotiToken(unRegiterHolder);
                }
        }

        function getDir(){
            if(localStorage.activeLanguage != null && localStorage.activeLanguage != undefined && localStorage.activeLanguage == 'ar'){
                return 'rtl';
            }else{
                return 'ltr';
            }
        }



        return {
            atTopOfPage,
            isDarkMode,
            dataReady,
            dir,
            appInfoStore,
            getDir
        }
    }

})

</script>