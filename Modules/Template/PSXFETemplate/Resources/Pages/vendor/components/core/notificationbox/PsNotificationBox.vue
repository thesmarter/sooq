<template>
    <div v-if="notify">
        <div id="notification-box" class="overflow-hidden flex items-end w-mobile sm:w-median lg:w-64 justify-between leading-none   bg-fePrimary-000 dark:bg-feAchromatic-900 rounded-t-xl lg:rounded-t-2xl top-0 fixed">
            <a href="#" id="notification-close" @click="closed()" title="close">x</a>
            <div class="bg-fePrimary-000 lg:px-2 py-1 px-1 h-18 cursor-pointer flex-grow flex flex-row items-center no-underline text-feAchromatic-900"  @click="clicked(flag)">
                <div class="me-2 w-8 sm:w-10 my-auto">
                    <img alt="Placeholder" class="rounded-md w-auto sm:h-10 h-8 cursor-pointer object-cover" width="50px" height="50px"
                    :src="$page.props.thumb1xUrl + '/' + $page.props.backendLogo.img_path"
                    >
                </div>

                <p class="ms-2 mt-1 text-sm flex-grow">
                    <span class="flex">
                        <ps-label class="truncate lg:text-sm text-xs font-medium" > {{title}} </ps-label>
                    </span>
                    <ps-label class=" font-medium text-xxs lg:text-xs " textColor="text-feSecondary-400 dark:text-feAchromatic-500"> {{subject}}  </ps-label>
                </p>
            </div>
        </div>
    </div>

</template>

<script>
import firebase from "firebase/app";
import "firebase/messaging";
// import router from '@/router';
import PsLabel from '@template1/vendor/components/core/label/PsLabel.vue';
import { useProductStore } from "@templateCore/store/modules/item/ProductStore";
import { PsValueStore } from '@templateCore/store/modules/core/PsValueStore';
import PsConst from '@templateCore/object/constant/ps_constants';
import { router } from '@inertiajs/vue3';
import { useUserUnReadMessageStore } from "@templateCore/store/modules/chat/UserUnreadMessageStore";
import UserUnReadMessageParameterHolder from '@templateCore/object/holder/UserUnReadMessageParameterHolder';


export default {
    name: "PsNotificationBox",

    components: {
        PsLabel
    },
    setup(){
        const userunreadmsgStore = useUserUnReadMessageStore();
        const userUnreadHolder = new UserUnReadMessageParameterHolder();
        userUnreadHolder.userId = localStorage.loginUserId;

        return{
            userunreadmsgStore,
            userUnreadHolder
        }
    },

    data() {

        return {

            title: "Test",
            subject: "Test",
            flag: "Test",
            messaging: firebase.messaging.isSupported() ? firebase.messaging() : null,
            currentMessage: "Test",
            requireInteraction: true,
            notify: false,
        };
    },

    methods: {
        closed(){
            this.notify = false;
        },
        async clicked(value){
            if(value == 'approval' || value == 'verify_blue_mark'){
                router.get(route('fe_profile'));
            }
            if(value == 'follow'){
                router.get(route('fe_follower_list'), { userId: localStorage.loginUserId  });
            }
            if(value == 'fe_broadcast') {
                 router.get(route('fe_notification_list'));
            }
            if(value == 'review') {
                router.get(route('fe_review_list'),  { user_id: localStorage.loginUserId } );
            }
            if(value == 'chat' || value == 'bought') {
                const itemProvider = useProductStore();
                const psValueStore = PsValueStore();
                const loginUserId = psValueStore.getLoginUserId();
                const itemId = this.currentMessage.data.item_id;
                const item = await itemProvider.loadItem(itemId, loginUserId);
                let chatFlag = PsConst.CHAT_FROM_BUYER.toString();
                if(loginUserId == this.currentMessage.data.buyer_id){
                    chatFlag = PsConst.CHAT_FROM_SELLER.toString();
                }
                router.get(route('fe_chat'),{
                    'buyer_user_id' : this.currentMessage.data.buyer_id,
                    'seller_user_id' : this.currentMessage.data.seller_id,
                    'item_id' : this.currentMessage.data.item_id,
                    'chat_flag' : chatFlag
                    });

            }
            if(value == "subscribe") {
                 router.get(route('fe_item_detail'), { item_id: this.currentMessage.data.item_id });

            }

        },
        receiveMessage() {
            try {
                if(!this.messaging) return;
                this.messaging.onMessage(async (payload) => {
                    // debugger
                    this.currentMessage = payload;
                    console.log("Message received. ", this.currentMessag);


                    await this.userunreadmsgStore.postUserUnreadMessageCount(this.userUnreadHolder);

                    this.setNotificationBoxForm(
                        payload.notification.title,
                        payload.notification.body,
                        payload.data.flag,
                    );

                    this.notify = true;
                    setTimeout(() => {
                        this.notify = false;
                    }, 10000);
                });

            } catch (e) {
                console.log(e);
            }
        },

        setNotificationBoxForm(title, subject, flag) {
            this.title = title;
            this.subject = subject;
            this.flag = flag;
        },
    },

    created() {
        this.receiveMessage();
    },
};
</script>

<style>
#notification-box {
    display: flex;
    position: fixed;
    right: 5px;
    height: 100px;
    background-color: #fff;
    z-index: 1;
    border: 1px dotted black;
    border-radius: 12px;
}
#notification-close {
    position: fixed;
    color: #777;
    font: 14px;
    right: 10px;
    text-decoration: none;
    text-shadow: 0 1px 0 #fff;
    top: 6px;
    z-index: 10;
    cursor: pointer;
}

img{
    width: 50px;
}
</style>
