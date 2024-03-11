<template>
    <Head :title="$t('chat_list__messages')"/>
    <div class="sm:mt-32 lg:mt-36 mt-28 mb-16 px-5 xl:px-52 mx-auto">
        <div class="grid lg:grid-cols-12 sm:grid-cols-8 grid-cols-4  gap-4 sm:gap-3.5 lg:gap-10 ">
            <!-- <div class="col-span-4 sm:col-span-8 lg:col-span-12">

            </div> -->
            <div class="col-start-0 col-span-4 sm:col-span-2 lg:col-span-3" >
                <ps-label class="text-xl sm:text-3xl font-medium mb-5"> {{ $t("core__fe_offers") }}</ps-label>
                <div class=" ">
                    <div class=" flex flex-row rtl:space-x-reverse space-x-2 sm:space-x-0 space-y-0 sm:space-y-2 sm:flex-col" id="sellerbtn" v-if="!isSellerFocus" :disabled="true">
                        <ps-button class="w-full" @click="buyerClicked" >
                            <span class="me-2 md:me-4">{{ $t("offer_list__offer_sent") }}</span>
                            <!-- <span class="rounded-full w-8 float-right bg-fePrimary-500 text-feAchromatic-50">{{ userunreadmsgStore.unreadCount.data?.sellerUnreadCount}}</span> -->
                        </ps-button>

                        <ps-button class="w-full" colors="bg-feAchromatic-50 dark_bg-feAchromatic-800 dark_text-feAchromatic-200 hover_text-feAchromatic-50" border="border border-feAchromatic-300 dark_border-feAchromatic-500" rounded="rounded" @click="sellerClicked">
                            <span class="me-2 md:me-4">{{ $t("offer_list__offer_received") }}</span>
                            <!-- <span class="rounded-full w-8 float-right bg-fePrimary-500 text-feAchromatic-50">{{ userunreadmsgStore.unreadCount.data?.buyerUnreadCount}}</span> -->
                        </ps-button>
                    </div>

                    <div class="flex flex-row rtl:space-x-reverse space-x-2 sm:space-x-0 space-y-0 sm:space-y-2 sm:flex-col" id="buyerbtn"  v-else :disabled="true">
                        <ps-button class="w-full" colors="bg-feAchromatic-50 dark_bg-feAchromatic-800 dark_text-feAchromatic-200 hover_text-feAchromatic-50" border="border border-feAchromatic-300 dark_border-feAchromatic-500" rounded="rounded" @click="buyerClicked" >
                            <span class="me-2 md:me-4">{{ $t("offer_list__offer_sent") }}</span>
                            <!-- <span class="rounded-full w-8 float-right bg-fePrimary-500 text-feAchromatic-50">{{ userunreadmsgStore.unreadCount.data?.sellerUnreadCount}}</span> -->
                        </ps-button>

                        <ps-button class="w-full"  @click="sellerClicked">
                            <span class="me-2 md:me-4">{{ $t("offer_list__offer_received") }}</span>
                            <!-- <span class="rounded-full w-8 float-right bg-fePrimary-500 text-feAchromatic-50">{{ userunreadmsgStore.unreadCount.data?.buyerUnreadCount }}</span> -->
                        </ps-button>
                    </div>
                </div>
            </div>
            <div class="col-span-4 sm:col-span-6 lg:col-span-9">

                <div class="col-span-4 sm:col-span-6 lg:col-span-9 mb-2" v-if="isSellerFocus">
                    <!-- <ps-label class="text-sm sm:text-xl font-medium" > {{ $t("offer_list__offer_sent") }}</ps-label> -->
                    <ps-label class="text-sm sm:text-xl font-medium">{{ $t("offer_list__offer_received") }}</ps-label>
                </div>
                <div class="col-span-4 sm:col-span-6 lg:col-span-9 mb-2" v-else>
                    <!-- <ps-label class="text-sm sm:text-xl font-medium">{{ $t("offer_list__offer_received") }}</ps-label> -->
                    <ps-label class="text-sm sm:text-xl font-medium" > {{ $t("offer_list__offer_sent") }}</ps-label>
                </div>
                <div v-if="ps_loading">
                    <div id="seller" class="w-full flex flex-col bg-fePrimary-50 dark:bg-feAchromatic-800 lg:p-4 p-2 sm:p-3 rounded-2xl" >
                        <div class='w-full ' >
                            <div class=" flex flex-col">
                                <!-- Column -->
                                <div class="w-full mt-2" v-for="index in 3" :key="index">
                                <chat-skeletor-item />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Offer sent horizontal -->
                <div id="seller" class="flex flex-row mb-16" v-if="isSellerFocus && ps_loading == false">
                    <div v-if="offerListProvider.offerList.data == null "
                    class="w-full flex flex-col bg-fePrimary-50 dark_bg-feAchromatic-800 lg:p-4 p-2 sm:p-3 rounded-2xl">
                       <ps-icon name="mailOpen" textColor="text-feSecondary-400 dark_text-feAchromatic-500"  class='mt-2 w-full mx-auto' w="80" h="80" />
                       <ps-label-caption-3 class="text-center mb-2" > {{ $t("offer_list__nothing_in_offer_received") }}  </ps-label-caption-3>

                    </div>
                    <div v-else class='w-full flex flex-col bg-fePrimary-50 dark:bg-feAchromatic-800 lg:p-4 p-2 sm:p-3 rounded-2xl' >
                        <div class="flex flex-wrap ">
                            <!-- Column -->
                            <div class=" mt-2 w-full" v-for="chathistory in offerListProvider.offerList.data" :key="chathistory.id">
                                <ps-route-link :to="{ 
                                    name : 'chat',
                                    query: {
                                        buyer_user_id : chathistory.buyerUserId,
                                        seller_user_id : chathistory.sellerUserId,
                                        item_name : chathistory.item.title ,
                                        item_id : chathistory.itemId,
                                        item_image_name : chathistory.item.defaultPhoto.imgPath,
                                        item_price : chathistory.item.price,
                                        currency : chathistory.item.itemCurrency.currencySymbol,
                                        chat_flag : PsConst.CHAT_FROM_SELLER.toString(),
                                        is_sold_out : chathistory.item.isSoldOut,

                                    }
                                }">
                                <!-- <chat-horizontal-item  :chathistory="chathistory" /> -->
                                <offer-horizontal-item :chathistory="chathistory" />
                                </ps-route-link>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End offer sent horizontal -->

                <!-- Buyer Chat horizontal -->
                <div id="buyer" class="flex flex-row mb-16 " v-else-if="ps_loading == false">
                    <div v-if="offerReceivedListProvider.offerList.data == null  "
                     class="w-full flex flex-col bg-fePrimary-50 dark_bg-feAchromatic-800 lg:p-4 p-2 sm:p-3 rounded-2xl">
                       <ps-icon name="mailOpen" textColor="text-feSecondary-400 dark_text-feAchromatic-500"  class='mt-2 w-full mx-auto' w="80" h="80" />
                       <ps-label-caption-3 class="text-center mb-2" >{{ $t("offer_list__nothing_in_offer_sent") }}</ps-label-caption-3>

                    </div>
                    <div v-else class='w-full'>
                        <div class="w-full flex flex-col bg-fePrimary-50 dark_bg-feAchromatic-800 lg:p-4 p-2 sm:p-3 rounded-2xl ">
                            <!-- Column -->
                            <div class="w-full mt-2" v-for="chathistory in offerReceivedListProvider.offerList.data" :key="chathistory.id">
                                <ps-route-link :to="{
                                    name : 'chat',
                                    query: {
                                        buyer_user_id : chathistory.buyerUserId,
                                        seller_user_id : chathistory.sellerUserId,
                                        item_name : chathistory.item.title ,
                                        item_id : chathistory.itemId,
                                        item_image_name : chathistory.item.defaultPhoto.imgPath,
                                        item_price : chathistory.item.price,
                                        currency : chathistory.item.itemCurrency.currencySymbol,
                                        chat_flag : PsConst.CHAT_FROM_BUYER.toString(),
                                        is_sold_out : chathistory.item.isSoldOut,

                                    }
                                }">
                               <!-- <chat-buyer-horizontal-item  :chathistory="chathistory" /> -->
                               <offer-horizontal-item :chathistory="chathistory" />
                                </ps-route-link>
                            </div>
                        </div>
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

import PsIcon from '@template1/vendor/components/core/icons/PsIcon.vue';
import PsLabelCaption3 from '@template1/vendor/components/core/label/PsLabelCaption3.vue';
import PsButton from '@template1/vendor/components/core/buttons/PsButton.vue';
import PsRouteLink from '@template1/vendor/components/core/link/PsRouteLink.vue';
import PsLabel from '@template1/vendor/components/core/label/PsLabel.vue';

// Providers
import { useOfferStoreState } from "@templateCore/store/modules/offer/OfferStore";
import { PsValueStore } from "@templateCore/store/modules/core/PsValueStore";

// Holders
import OfferParameterHolder from '@templateCore/object/holder/OfferParameterHolder';

import ChatHorizontalItem from "@template1/vendor/components/modules/chat/ChatHorizontalItem.vue";
import OfferHorizontalItem from "@template1/vendor/components/modules/chat/OfferHorizontalItem.vue";
import ChatBuyerHorizontalItem from "@template1/vendor/components/modules/chat/ChatBuyerHorizontalItem.vue";
import ChatSkeletorItem from "@template1/vendor/components/modules/chat/ChatSkeletorItem.vue";
import PsConst from '@templateCore/object/constant/ps_constants';

import PsFrontendLayout from '@template1/vendor/components/layouts/container/PsFrontendLayout.vue';
// import { Inertia } from "@inertiajs/inertia";
import { router } from '@inertiajs/vue3';
// import OfferHorizontalItem from '../../../components/modules/chat/OfferHorizontalItem.vue';

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
        OfferHorizontalItem,
        Head
    },
    props: ['mobileSetting'],
    layout: PsFrontendLayout,
    setup (props) {

        // Inject Chat Provider
        const psValueStore = PsValueStore();
        const offerListProvider = useOfferStoreState();
        const offerReceivedListProvider = useOfferStoreState();
        const loginUserId = psValueStore.getLoginUserId();


        if(loginUserId == null || loginUserId == '' || loginUserId == PsConst.NO_LOGIN_USER) {
            router.get(route('login'));
        }
        const holder = new OfferParameterHolder().getOfferSentList();
        const ps_loading = ref(true);
        const isSellerFocus = ref(false);

        holder.userId = loginUserId;
        holder.returnType = PsConst.CHAT_TYPE_SELLER;

        loadSellerList();

        async function loadSellerList(){
            await offerListProvider.resetOfferList(holder,loginUserId);
            // console.log(sellers);
            console.log(offerListProvider.offerList.data);
            ps_loading.value = false;
        }

        

        async function sellerClicked() {

            isSellerFocus.value = true;
            ps_loading.value = true ;
            holder.userId = loginUserId;
            holder.returnType = PsConst.CHAT_TYPE_BUYER;
            await offerListProvider.resetOfferList(holder,loginUserId);
            console.log(offerListProvider.offerList.data);
            ps_loading.value = false;

        }

        async function buyerClicked() {
            isSellerFocus.value = false;
            ps_loading.value = true ;
            holder.userId = loginUserId;
            holder.returnType = PsConst.CHAT_TYPE_SELLER;
            await offerReceivedListProvider.resetOfferList(holder,loginUserId);
            console.log(offerReceivedListProvider.offerList.data);
            ps_loading.value = false ;

        }

        return {
            offerListProvider,
            offerReceivedListProvider,
            sellerClicked,
            buyerClicked,
            ps_loading,
            PsConst,
            isSellerFocus
        }
    }
}
</script>
