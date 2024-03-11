
<template>
    <Head :title="$t('chat_list__messages')"/>
    <div class="sm:mt-32 lg:mt-36 mt-28 px-5 xl:px-52 mx-auto">
        <div class="grid lg:grid-col-12 sm:grid-col-8 grid-col-4  gap-4 ">
            <!-- <div class="col-span-4 sm:col-span-8 lg:col-span-12">

            </div> -->

            <div class="col-start-0 col-span-4 sm:col-span-2 lg:col-span-3" >
                <ps-label class="text-xl sm:text-3xl font-medium mb-5"> {{ $t("chat_list__messages") }}</ps-label>
                <div class="">
                    <div class=" flex flex-row " id="sellerbtn" v-if="!isSellerFocus" :disabled="true">
                        <ps-button class="w-34 bg-transparent mx-1" padding="py-2 px-3" justify="flex justify-start" @click="buyerClicked" color="bg-fePrimary-500 " hover="hover:none" rounded="none" border="border-b-2 border-b-fePrimary-500" >
                            <span class="me-2 md:me-4 text-fePrimary-500 font-semibold ">{{ $t("chat_list__from_buyer") }}</span>
                        <span class="flex justify-center items-center font-normal text-sm rounded-full w-5 h-5  bg-feSecondary-100 hover:bg-fePrimary-500 hover:text-feSecondary-100 text-feAchromatic-900"
                        >{{ userunreadmsgStore.unreadCount.data?.sellerUnreadCount}}</span>
                    </ps-button>
                   
                        <ps-button class="w-34" padding="py-2 px-3 " justify="flex justify-start"  colors="bg-transparent " rounded="none" border="border-b-2 border-b-feSecondary-50 dark:border-b-feSecondary-800" hover="hover-none" shadow="none"  @click="sellerClicked">
                            <span class="me-2 md:me-4 font-medium dark:text-feAchromatic-200 text-feArchromatic-900">{{ $t("chat_list__from_seller") }}</span>
                        <span class="flex justify-center items-center font-normal text-sm rounded-full w-5 h-5   bg-feSecondary-100 hover:bg-fePrimary-500 hover:text-feSecondary-100 text-feAchromatic-900">{{ userunreadmsgStore.unreadCount.data?.buyerUnreadCount}}</span>
                        </ps-button>
                    </div>

                    <div class="flex flex-row " id="buyerbtn"  v-else :disabled="true">
                        <ps-button class="w-34 mx-1" padding="py-2 px-3" justify="flex justify-start"  hover="none" colors="bg-transparent" border="border-b-2 border-b-feSecondary-50 dark:border-b-feSecondary-800" rounded="none" @click="buyerClicked" >
                            <span class="me-2 md:me-4 font-medium dark:text-feAchromatic-200">{{ $t("chat_list__from_buyer") }}</span>
                        <span class="flex justify-center items-center font-normal text-sm rounded-full w-5 h-5   bg-feSecondary-100 hover:bg-fePrimary-500 hover:text-feSecondary-100 text-feAchromatic-900">{{ userunreadmsgStore.unreadCount.data?.sellerUnreadCount}}</span>
                        </ps-button>
                        <ps-button class="w-34" padding="py-2 px-3 " justify="flex justify-start" hover="none" colors="bg-transparent" border="border-b-2 border-b-fePrimary-500" rounded="none"  @click="sellerClicked">
                            <span class="me-2 md:me-4 text-fePrimary-500 font-semibold">{{ $t("chat_list__from_seller") }}</span>
                            <span class="flex justify-center items-center font-normal text-sm rounded-full w-5 h-5 hover:bg-fePrimary-500 hover:text-feSecondary-100  bg-feSecondary-100 text-feAchromatic-900">{{ userunreadmsgStore.unreadCount.data?.buyerUnreadCount }}</span>
                        </ps-button>
                    </div>
                  
                </div>
                 <!-- google adsense desktop view -->
                 <!-- <ps-ad-sense></ps-ad-sense> -->

            </div>
            <div class="col-span-4 sm:col-span-6 lg:col-span-9">

                <!-- <div class="col-span-4 sm:col-span-6 lg:col-span-9 mb-2" v-if="isSellerFocus">
                    <ps-label class="text-sm sm:text-xl font-medium" > {{ $t("chat_list__messages_from_seller") }}</ps-label>
                </div>
                <div class="col-span-4 sm:col-span-6 lg:col-span-9 mb-2" v-else>
                    <ps-label class="text-sm sm:text-xl font-medium">{{ $t("chat_list__messages_from_buyer") }}</ps-label>
                </div> -->
                <div v-if="ps_loading == true">
                    <div id="seller" class="w-full flex flex-col lg:p-4 p-2 sm:p-3" >
                        <div class='w-full ' >
                            <div class=" flex flex-col">

                                <div class="w-full mt-2" v-for="index in 3" :key="index">
                                    <chat-skeletor-item />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Seller Chat horizontal -->
                <div id="seller" class="flex flex-col mb-16" v-if="isSellerFocus && ps_loading == false">
                    <div class="flex flex-row">
                        <div v-if="chatsellerhistorylistStore.chatHistoryList.data == null "
                        class="w-full flex flex-col lg:p-4 p-2 sm:p-3 rounded">
                        <img alt="Placeholder"  height="auto"  class="w-screen  object-cover  rtl:space-x-reverse space-x-48"
                                v-lazy=" { src: $page.props.sysImageUrl + '/undraw_quick_chat_rebit.png', loading: $page.props.sysImageUrl+'/loading_gif.gif', error: $page.props.sysImageUrl+'/default_photo.png' }"
                            >
                        <ps-label class="text-base text-center sm:text-2xl mb-3 mt-3">{{ $t('chat_list_no_messages') }}</ps-label>
                        <ps-label-caption-3 class="text-center mb-2" >{{ $t("chat_list_no_messages__paragraph") }}
                         </ps-label-caption-3>
                         <ps-button @click="startSellingClicked" class="w-full sm:mx-auto sm:w-[456px]" > {{ $t("chat_list__start_selling") }} </ps-button>

                        </div>

                        <div class="flex flex-col w-full" v-else>
                            <div class=" w-full" v-for="chathistory in chatsellerhistorylistStore.chatHistoryList?.data" :key="chathistory.id">

                                    <chat-horizontal-item :dateFormat="$page.props.dateFormat" :chathistory="chathistory" />
                                <!-- <ps-route-link :to="{name: 'fe_item_list'} ">
                                    <chat-horizontal-item  :chathistory="chathistory" />
                                </ps-route-link> -->
                            </div>
                        </div>
                    </div>
                    <div class="flex items-end justify-end">
                        <ps-button v-if="chatsellerhistorylistStore.loading.value == false" class="w-[108px] mx-auto mt-4 " @click="loadMore" :class="chatsellerhistorylistStore.isNoMoreRecord ? 'hidden' : ''"> {{ $t("list__load_more") }} </ps-button>
                        <ps-button v-else class="w-[108px] mx-auto mt-4" :disabled="true"> {{ $t("list__loading") }} </ps-button>
                    </div>
                </div>
                <!-- End Seller Chat horizontal -->

                <!-- Buyer Chat horizontal -->
                <div id="buyer" class="flex flex-col mb-16 " v-else-if="ps_loading == false">
                    <div class="flex flex-row">
                        <div v-if="chatbuyerhistorylistStore.chatHistoryList.data == null "
                        class="w-full flex flex-col lg:p-4 p-2 sm:p-3 rounded">
                        <img alt="Placeholder"  height="auto"  class="w-screen  object-cover  rtl:space-x-reverse space-x-48"
                                v-lazy=" { src: $page.props.sysImageUrl + '/undraw_quick_chat_rebit.png', loading: $page.props.sysImageUrl+'/loading_gif.gif', error: $page.props.sysImageUrl+'/default_photo.png' }"
                            >
                        <ps-label class="text-base text-center sm:text-2xl mb-3 mt-3">{{ $t('chat_list_no_messages') }}</ps-label>
                        <ps-label-caption-3 class="text-center mb-2" >{{ $t("chat_list_no_messages__paragraph") }}
                         </ps-label-caption-3>
                         <ps-button @click="startSellingClicked" class="w-full sm:mx-auto sm:w-[456px]" > {{ $t("chat_list__start_selling") }} </ps-button>

                        </div>
                        <div v-else class='w-full ' >
                            <div class="flex flex-wrap w-full">
                                <div class="w-full " v-for="chathistory in chatbuyerhistorylistStore.chatHistoryList.data" :key="chathistory.id">
                                        <chat-buyer-horizontal-item :dateFormat="$page.props.dateFormat"  :chathistory="chathistory" />
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-end justify-end">
                        <ps-button v-if="chatbuyerhistorylistStore.loading.value == false" class="w-[108px] mt-4 mx-auto" @click="loadMoreBuyer" :class="chatbuyerhistorylistStore.isNoMoreRecord.value ? 'hidden' : ''"> {{ $t("list__load_more") }} </ps-button>
                        <ps-button v-else class="w-[108px] mx-auto mt-4" :disabled="true"> {{ $t("list__loading") }} </ps-button>
                    </div>
                </div>
                <!-- End Buyer Chat horizontal -->

            </div>
        </div>
    </div>
</template>


<script lang="ts">
import { Head } from '@inertiajs/vue3';
import { ref } from 'vue';
// import router from '@template1/router';
import PsIcon from '@template1/vendor/components/core/icons/PsIcon.vue'
import PsLabelCaption3 from '@template1/vendor/components/core/label/PsLabelCaption3.vue'
import PsButton from '@template1/vendor/components/core/buttons/PsButton.vue';
import PsRouteLink from '@template1/vendor/components/core/link/PsRouteLink.vue';

// Providers
import { useChatHistoryListStoreState } from "@templateCore/store/modules/chat/ChatHistoryListStore";
import { useUserUnReadMessageStore } from "@templateCore/store/modules/chat/UserUnreadMessageStore";
import { PsValueStore } from "@templateCore/store/modules/core/PsValueStore";
import PsLabel from '@template1/vendor/components/core/label/PsLabel.vue';
import PsAdSense from '@template1/vendor/components/core/adsense/PsAdSense.vue';
import { usePsAppInfoStoreState } from '@templateCore/store/modules/appinfo/AppInfoStore';
// Holders
import AppInfoParameterHolder from '@templateCore/object/holder/AppInfoParameterHolder';
import ChatHistoryParameterHolder from '@templateCore/object/holder/ChatHistoryParameterHolder';
import UserUnReadMessageParameterHolder from '@templateCore/object/holder/UserUnReadMessageParameterHolder';

import ChatHorizontalItem from "@template1/vendor/components/modules/chat/ChatHorizontalItem.vue";
import ChatBuyerHorizontalItem from "@template1/vendor/components/modules/chat/ChatBuyerHorizontalItem.vue";
import ChatSkeletorItem from "@template1/vendor/components/modules/chat/ChatSkeletorItem.vue";
import PsConst from '@templateCore/object/constant/ps_constants';

import PsFrontendLayout from '@template1/vendor/components/layouts/container/PsFrontendLayout.vue';
import { router } from '@inertiajs/vue3';

export default {
    name : "ChatListView",
    components : {
        PsButton,
        PsLabel,
        PsLabelCaption3,
        PsIcon,
        ChatHorizontalItem,
        ChatBuyerHorizontalItem,
        ChatSkeletorItem,
        PsRouteLink,
        PsAdSense,
        Head
    },
    props: ['mobileSetting'],
    layout: PsFrontendLayout,
    setup (props) {

        // Inject Chat Provider
        const psValueStore = PsValueStore();
        const chatbuyerhistorylistStore = useChatHistoryListStoreState('buyer');
        const chatsellerhistorylistStore = useChatHistoryListStoreState('seller');

        chatbuyerhistorylistStore.limit = props.mobileSetting?.default_loading_limit ?? 12;
        chatsellerhistorylistStore.limit = props.mobileSetting?.default_loading_limit ?? 12;

        const userunreadmsgStore = useUserUnReadMessageStore();
        const loginUserId = psValueStore.getLoginUserId();
        if(loginUserId == null || loginUserId == '' || loginUserId == PsConst.NO_LOGIN_USER) {
            router.get(route('login'));
        }
        const sellerholder = new ChatHistoryParameterHolder().resetParameterHolder();
        const buyerholder = new ChatHistoryParameterHolder().resetParameterHolder();
        const userUnreadHolder = new UserUnReadMessageParameterHolder();
        const ps_loading = ref(true);
        const isSellerFocus = ref(false);
        const appInfoStore = usePsAppInfoStoreState();
        const appInfoParameterHolder = new AppInfoParameterHolder();
        appInfoParameterHolder.userId = loginUserId;
        // appInfoStore.loadAppInfo(appInfoParameterHolder);

        //load Chat List
        sellerholder.userId = loginUserId;
        sellerholder.returnType = PsConst.CHAT_TYPE_SELLER;

        buyerholder.userId = loginUserId;
        buyerholder.returnType = PsConst.CHAT_TYPE_BUYER;

        userUnreadHolder.userId = loginUserId;
        userUnreadHolder.deviceToken = localStorage.deviceToken;
        userunreadmsgStore.postUserUnreadMessageCount(userUnreadHolder);

        loadBuyerList();

        async function loadBuyerList(){
            await chatbuyerhistorylistStore.resetChatHistoryList(loginUserId,buyerholder);

            ps_loading.value = false;
        }
        // loadSellerList();
        // async function loadSellerList(){
        //     await chatsellerhistorylistStore.resetChatHistoryList(loginUserId, sellerholder);

        //     ps_loading.value = false;
        // }

        async function sellerClicked() {

            isSellerFocus.value = true;
            ps_loading.value = true ;
            await chatsellerhistorylistStore.resetChatHistoryList(loginUserId, sellerholder);
            await userunreadmsgStore.postUserUnreadMessageCount(userUnreadHolder);

            ps_loading.value = false;

        }

        async function buyerClicked() {
            isSellerFocus.value = false;
            ps_loading.value = true ;
            await chatbuyerhistorylistStore.resetChatHistoryList(loginUserId, buyerholder);
            await userunreadmsgStore.postUserUnreadMessageCount(userUnreadHolder);

            ps_loading.value = false ;

        }
        async function loadMore() {
            // ps_loading.value = true ;
            await chatsellerhistorylistStore.loadChatHistoryList(loginUserId, sellerholder);
            // ps_loading.value = false;
        }
        async function loadMoreBuyer() {
            await chatbuyerhistorylistStore.loadChatHistoryList(loginUserId, buyerholder);
        }
        function getPrice(value){
            // if(value.price!='0' && appInfoStore.appInfo.data.psItemUploadConfig.discountRate== PsConst.ONE){
            //     return parseFloat(value.price)-(parseFloat(value.price)*(parseFloat(value.price)/100));
            // }else{
            //     return value.price;
            // }
            return "10";
        }
        function startSellingClicked(){
            router.get(route('fe_item_entry'));
        }

        return {
            getPrice,
            chatsellerhistorylistStore,
            chatbuyerhistorylistStore,
            userunreadmsgStore,
            sellerClicked,
            buyerClicked,
            ps_loading,
            isSellerFocus,
            PsConst,
            loadMore,
            loadMoreBuyer,
            appInfoStore,
            startSellingClicked
        }
    }
}
</script>
