<template>
    <Head :title="$t('notification_list__notification_list')"/>
<ps-content-container>
        <template #content>
    <!-- <div class="mx-auto">
        <ps-ad-sense adFormat="horizontal"></ps-ad-sense>
    </div> -->

    <div class="sm:mt-32 lg:mt-36 mt-28 mb-16 ">

        <!-- Notification list -->
        <div class=" mb-4">
            <div class="">
                <ps-label class="text-xl sm:text-2xl lg:text-3xl font-medium">{{ $t("notification_list__notification_list") }} </ps-label>
            </div>


            <div class="flex flex-row mb-16 ">
                <div v-if="notiStore.notiList?.data == null && initial == false"
                class="w-3/4 mx-auto flex flex-col lg:p-4 p-2 sm:p-3 rounded">
                    <img alt="Placeholder"  height="auto"  class="w-screen  object-cover  rtl:space-x-reverse space-x-48"
                            v-lazy=" { src: $page.props.sysImageUrl + '/undraw_new_notifications_re_xpcv_1.png', loading: $page.props.sysImageUrl+'/loading_gif.gif', error: $page.props.sysImageUrl+'/default_photo.png' }"
                        >
                    <ps-label class="text-base text-center sm:text-2xl mb-3 mt-3">{{ $t('noti_list__no_items') }}</ps-label>
                    <!-- <ps-label-caption-3 class="text-center mb-2" >{{ $t("chat_list_no_messages__paragraph") }}</ps-label-caption-3> -->

                    <ps-button v-if="notiStore.loading.value == false && initial == false" class="mx-auto mt-6" @click="handleRefresh">{{ $t("refresh") }} </ps-button>
                    <ps-button v-else class="mx-auto mt-6" @click="handleRefresh" :disabled="true"> {{ $t("list__loading") }}</ps-button>
                </div>
                <div v-else-if="notiStore.notiList?.data != null" class='w-full flex flex-col' >
                    <div class="flex flex-col ">
                        <div class="w-full" v-for="noti in notiStore.notiList.data" :key="noti.id">
                            <noti-horizontal-item  :noti="noti" :onClick="notiClicked"/>
                        </div>
                    </div>

                    <ps-button v-if="notiStore.loading.value == false && initial == false" class="w-60 mx-auto mt-8" @click="loadMore" :class="notiStore.isNoMoreRecord ? 'hidden' : ''">{{ $t("notification_list__load_more") }} </ps-button>
                    <ps-button v-else-if="initial == false" class="w-60 mx-auto mt-8" @click="loadMore" :disabled="true"> {{ $t("notification_list__loading") }}  </ps-button>

                </div>


            </div>
        </div>
        <!-- End List -->
    </div>
            </template>
    </ps-content-container>
    <ps-loading-dialog ref="ps_loading_dialog"/>
</template>

<script lang="ts">
import { Head } from '@inertiajs/vue3';
import { ref,onMounted } from 'vue';
// Vue
// import router from '@template1/router';
import PsButton from '@template1/vendor/components/core/buttons/PsButton.vue';
import PsLabel from '@template1/vendor/components/core/label/PsLabel.vue';
import PsAdSense from '@template1/vendor/components/core/adsense/PsAdSense.vue';
import { PsValueStore } from "@templateCore/store/modules/core/PsValueStore";

// Models
import { useNotiStoreState } from "@templateCore/store/modules/noti/NotificationStore";

// Modules
import NotiHorizontalItem from "@template1/vendor/components/modules/noti/NotiHorizontalItem.vue";

// Params Holders
import NotiParameterHolder from '@templateCore/object/holder/NotiParameterHolder';
import PsFrontendLayout from '@template1/vendor/components/layouts/container/PsFrontendLayout.vue';
import NotiRegisterHolder from '@templateCore/object/holder/NotiRegisterHolder';
import NotiUnRegisterHolder from '@templateCore/object/holder/NotiUnRegisterHolder';
import PsConst from '@templateCore/object/constant/ps_constants';
import PsContentContainer from "@template1/vendor/components/layouts/container/PsContentContainer.vue";
import PsLoadingDialog from '@template1/vendor/components/core/dialog/PsLoadingDialog.vue';

export default {
    name : "NotificationListView",
    components : {
        NotiHorizontalItem,
        PsButton,
        PsLabel,
        PsAdSense,
        PsContentContainer,
        PsLoadingDialog,
        Head
    },
    props: ['mobileSetting'],
    layout: PsFrontendLayout,
    setup(props) {

        const psValueStore = PsValueStore();
        const loginUserId = psValueStore.getLoginUserId();
        const notiStore = useNotiStoreState();
        const holder = new NotiParameterHolder();
        holder.userId = loginUserId;
        holder.deviceToken = localStorage.deviceToken;
        // notiStore.limit = props.mobileSetting?.default_loading_limit ?? 12;
        notiStore.limit = 6;
        const regiterHolder = new NotiRegisterHolder();
        const unRegiterHolder = new NotiUnRegisterHolder();
        const initial = ref(true);
        const ps_loading_dialog = ref();

        setTimeout(async () => {
           await loadDataList();
        },1000);

        async function loadDataList(){
            await notiStore.resetNotiList(holder);

            ps_loading_dialog.value.closeModal();
            initial.value == false;
        }

        function loadMore() {
            notiStore.loadNotiList(holder);
        }

        function handleRefresh(){
            notiStore.resetNotiList(holder);
        }

        function notiClicked(noti: any) {

        }

        function shownotiSetting(value){
            if(value == 'hide') {

                unRegiterHolder.platformName = PsConst.PLATFORM;
                unRegiterHolder.deviceId = localStorage.deviceToken;
                unRegiterHolder.userId = loginUserId;
                notiStore.unRegisterNotiToken(unRegiterHolder);

            }else {

                regiterHolder.platformName = PsConst.PLATFORM;
                regiterHolder.deviceId = localStorage.deviceToken;
                regiterHolder.loginUserId = loginUserId;
                notiStore.registerNotiToken(regiterHolder);

            }
            psValueStore.replaceNotiSetting(value);
        }

        onMounted(() => {
            if(initial.value == true && notiStore.notiList?.data == null){
                ps_loading_dialog.value.openModal();
            }
        })

        return {
            notiStore,
            notiClicked,
            loadMore,
            shownotiSetting,
            psValueStore,
            initial,
            ps_loading_dialog,
            handleRefresh,
            loadDataList,
        };
    }
}
</script>
