<template>
    <ps-modal ref="psmodal" theme="p-0 rounded-2xl dark:bg-feSecondary-800" line="hidden" bodyHeight="max-h-108" maxWidth="800px" :isClickOut='false' class=" z-50 " >
        <template #title>
            <ps-label class="font-medium lg:text-base text-xs hover:underline cursor-pointer pt-2 px-2.5 flex justify-end" @click="psmodal.toggle(false)">
                <ps-icon class="m-3" name="close" />
            </ps-label>
        </template>
        <template #body>
            <div class="grid sm:grid-cols-2 grid-cols-1 xl:gap-[3.125rem] sm:gap-y-[2.75rem] gap-y-[2rem] items-start xl:px-10 xl:py-16 py-7 px-10 self-stretch">
               
                <div class="">

                    <ps-label textColor="text-feAchromatic-900 dark:text-feSecondary-200" class="font-semibold sm:text-2xl text-xl mb-4" > {{ $t('fe__profile_modal_activities') }} </ps-label>

                    <ps-route-link class="px-2 py-1 mt-2 flex hover:bg-fePrimary-50 dark:hover:bg-feAchromatic-800 cursor-pointer " :to="{name: 'fe_follower_list', query: { userId: loginUserId   }}" @click="psmodal.toggle(false)">
                        <ps-icon class="mt-1 mr-2" :w="24" :h="24" name="followers" textColor="text-feAchromatic-600 dark:text-feAchromatic-300"/>
                        <div class='flex flex-col'>
                            <ps-label textColor="text-feAchromatic-900 dark:text-feSecondary-200" class="font-semibold sm:text-xl text-lg"> {{ $t('user_setting_modal__followers') }} </ps-label>
                            <ps-label  textColor="text-feAchromatic-900 dark:text-feSecondary-200 font-light"  class="mt-1 text-sm"> {{ $t('user_setting_modal__followers_caption_new') }}</ps-label>
                        </div>
                    </ps-route-link>

                    <ps-route-link class="px-2 py-1 mt-2 flex hover:bg-fePrimary-50 dark:hover:bg-feAchromatic-800 cursor-pointer " :to="{name: 'fe_following_list' , query: { userId: loginUserId   }}" @click="psmodal.toggle(false)">
                        <ps-icon class="mt-1 mr-2" :w="24" :h="24" name="followings" textColor="text-feAchromatic-600 dark:text-feAchromatic-300"/>
                        <div class='flex flex-col'>
                            <ps-label textColor="text-feAchromatic-900 dark:text-feSecondary-200" class="font-semibold sm:text-xl text-lg"> {{ $t('user_setting_modal__followings') }} </ps-label>
                            <ps-label  textColor="text-feAchromatic-900 dark:text-feSecondary-200 font-light"  class="sm:mt-2 mt-0 text-sm"> {{ $t('user_setting_modal__followings_caption_new') }} </ps-label>
                        </div>
                    </ps-route-link>

                    <ps-route-link class="px-2 py-1 mt-2 flex hover:bg-fePrimary-50 dark:hover:bg-feAchromatic-800 cursor-pointer " :to="{name: 'fe_follower_items' }" @click="psmodal.toggle(false)" >
                        <ps-icon class="mt-1 mr-2" :w="24" :h="24" name="following_items" textColor="text-feAchromatic-600 dark:text-feAchromatic-300"/>
                        <div class='flex flex-col'>
                            <ps-label textColor="text-feAchromatic-900 dark:text-feSecondary-200" class="font-semibold sm:text-xl text-lg">{{ $t('user_setting_modal__follower_items') }} </ps-label>
                            <ps-label  textColor="text-feAchromatic-900 dark:text-feSecondary-200 font-light"  class="sm:mt-2 mt-0 text-sm"> {{ $t('user_setting_modal__follower_caption_new') }} </ps-label>
                        </div>
                    </ps-route-link>

                    <!-- <ps-route-link class="mt-2" :to="{name: 'fe_offer_list' }" @click="psmodal.toggle(false)">
                        <div class='mt-2 hover_bg-fePrimary-50 dark_hover_bg-feAchromatic-800 cursor-pointer  flex flex-col'>
                            <ps-label textColor="text-feAchromatic-900 dark_text-feSecondary-200" class="font-semibold sm:text-xl text-lg"> {{ $t('core__fe_offers_lists') }} </ps-label>
                            <ps-label  textColor="text-feAchromatic-900 dark_text-feSecondary-200 font-light"  class="sm:mt-2 mt-0 text-sm"> {{ $t('core__fe_your_offers') }} </ps-label>
                        </div>
                    </ps-route-link> -->
                </div>

                <div >
                    <ps-label textColor="text-feAchromatic-900 dark:text-feSecondary-200" class="font-semibold sm:text-2xl text-xl mb-4" > {{ $t('fe__profile_modal__privacy') }} </ps-label>
                    
                    <ps-route-link v-if="appInfoStore?.appInfo?.data.psAppSetting?.isBlockUser ==PsConst.ONE" class="px-2 py-1 mt-2 flex hover:bg-fePrimary-50 dark:hover:bg-feAchromatic-800 cursor-pointer " :to="{name: 'fe_block_user_list' }" @click="psmodal.toggle(false)">
                        <ps-icon class="mt-1 mr-2" :w="24" :h="24" name="blocked_users" textColor="text-feAchromatic-600 dark:text-feAchromatic-300"/>
                        <div class='flex flex-col'>
                            <ps-label textColor="text-feAchromatic-900 dark:text-feSecondary-200" class="font-semibold sm:text-xl text-lg">{{ $t('user_setting_modal__blocked_users') }} </ps-label>
                            <ps-label  textColor="text-feAchromatic-900 dark:text-feSecondary-200 font-light"  class="mt-1 text-sm"> {{ $t('user_setting_modal__blocked_users_caption_new') }} </ps-label>
                        </div>
                    </ps-route-link>

                    <div class='px-2 py-1 mt-2 flex hover:bg-fePrimary-50 dark:hover:bg-feAchromatic-800 cursor-pointer' @click="openPasswordUpdate">
                        <ps-icon class="mt-1 mr-2" :w="24" :h="24" name="change_pwd" textColor="text-feAchromatic-600 dark:text-feAchromatic-300"/>
                        <div class='flex flex-col'>
                            <ps-label textColor="text-feAchromatic-900 dark:text-feSecondary-200" class="font-semibold sm:text-xl text-lg">{{ $t('user_setting_modal__change_password') }}  </ps-label>
                            <ps-label  textColor="text-feAchromatic-900 dark:text-feSecondary-200 font-light"  class="sm:mt-2 mt-0 text-sm"> {{ $t('user_setting_modal__change_password_caption') }} </ps-label>
                        </div>
                    </div>

                    <div class='px-2 py-1 mt-2 flex hover:bg-fePrimary-50 dark:hover:bg-feAchromatic-800 cursor-pointer' @click="openUserDeactivate(LoginUserId)">
                        <ps-icon class="mt-1 mr-2" :w="24" :h="24" name="delete_account" textColor="text-feAchromatic-600 dark:text-feAchromatic-300"/>
                        <div class='flex flex-col'>
                            <ps-label textColor="text-feAchromatic-900 dark:text-feSecondary-200" class="font-semibold sm:text-xl text-lg"> {{ $t('user_setting_modal__delete_account') }} </ps-label>
                            <ps-label  textColor="text-feAchromatic-900 dark:text-feSecondary-200 font-light"  class="sm:mt-2 mt-0 text-sm"> {{ $t('user_setting_modal__delete_account_caption') }} </ps-label>
                        </div>
                    </div>

                </div>

                <div >

                    <ps-label textColor="text-feAchromatic-900 dark:text-feSecondary-200" class="font-semibold sm:text-2xl text-xl mb-4" > {{ $t('fe__profile_modal_settings') }} </ps-label>

                    <ps-route-link class="px-2 py-1 mt-2 flex hover:bg-fePrimary-50 dark:hover:bg-feAchromatic-800 cursor-pointer" :to="{name: 'fe_reported_items' }" @click="psmodal.toggle(false)">
                        <ps-icon class="mt-1 mr-2" :w="24" :h="24" name="reported_posts" textColor="text-feAchromatic-600 dark:text-feAchromatic-300"/>
                        <div class='flex flex-col'>
                            <ps-label textColor="text-feAchromatic-900 dark:text-feSecondary-200" class="font-semibold sm:text-xl text-lg">{{ $t('user_setting_modal__reported_posts') }}</ps-label>
                            <ps-label  textColor="text-feAchromatic-900 dark:text-feSecondary-200 font-light"  class="sm:mt-2 mt-0 text-sm"> {{ $t('user_setting_modal__reported_posts_caption_new') }} </ps-label>
                        </div>
                    </ps-route-link>

                    <div v-if="psValueStore.notiSetting == 'true'"  class='px-2 py-1 mt-2 flex cursor-pointer'>
                        <ps-icon class="mt-1 mr-2" :w="24" :h="24" name="notifications" textColor="text-feAchromatic-600 dark:text-feAchromatic-300"/>
                        <div class='flex flex-col'>
                            <ps-label textColor="text-feAchromatic-900 dark:text-feSecondary-200" class="font-semibold sm:text-xl text-lg">{{ $t('user_setting_modal__hide_show_noti') }}  </ps-label>
                            <ps-label  textColor="text-feAchromatic-900 dark:text-feSecondary-200 font-light"  class="sm:mt-2 mt-0 text-sm"> {{ $t('user_setting_modal__hide_show_noti_caption') }} </ps-label>
                        </div>
                        <ps-toggle class="mt-1 ml-4" @click="shownotiSetting('hide')" :selectedValue="true"/>
                    </div>
                    
                    <div v-else class='px-2 py-1 mt-2 flex cursor-pointer'>
                        <ps-icon class="mt-1 mr-2" :w="24" :h="24" name="notifications" textColor="text-feAchromatic-600 dark:text-feAchromatic-300"/>
                        <div>
                            <ps-label textColor="text-feAchromatic-900 dark:text-feSecondary-200" class="font-semibold sm:text-xl text-lg">{{ $t('user_setting_modal__hide_show_noti') }}  </ps-label>
                            <ps-label  textColor="text-feAchromatic-900 dark:text-feSecondary-200 font-light"  class="sm:mt-2 mt-0 text-sm"> {{ $t('user_setting_modal__hide_show_noti_caption') }} </ps-label>
                        </div>
                        <ps-toggle class="mt-1 ml-4" @click="shownotiSetting('true')" :selectedValue="false"/>
                    </div>
                </div>
            </div>

        </template>
        <template #footer>

        </template>
    </ps-modal>

    <password-update-modal ref="password_update_modal" />

    <ps-success-dialog ref="ps_success_dialog"/>

    <ps-confirm-dialog ref='ps_confirm_dialog' />

    <ps-loading-dialog ref="psloading"  :isClickOut='false'/>

</template>

<script lang='ts'>
import { defineComponent,ref } from 'vue';
import PsModal from '@template1/vendor/components/core/modals/PsModal.vue';
import PsLabel from '@template1/vendor/components/core/label/PsLabel.vue';
import PsRouteLink from '@template1/vendor/components/core/link/PsRouteLink.vue';
// import CreditCardModal from '@template1/vendor/components/modules/credit/CreditCardModal.vue';
import PasswordUpdateModal from '@template1/vendor/components/modules/password/PasswordUpdateModal.vue';
import PsConfirmDialog from '@template1/vendor/components/core/dialog/PsConfirmDialog.vue';
import PsLoadingDialog from '@template1/vendor/components/core/dialog/PsLoadingDialog.vue';
import { PsValueStore } from "@templateCore/store/modules/core/PsValueStore";
import UserDeleteItemParameterHolder from '@templateCore/object/holder/UserDeleteItemParameterHolder';
import { useUserStore } from "@templateCore/store/modules/user/UserStore";
import { usePsAppInfoStoreState } from '@templateCore/store/modules/appinfo/AppInfoStore';
import AppInfoParameterHolder from '@templateCore/object/holder/AppInfoParameterHolder';
import PsStatus from '@templateCore/api/common/PsStatus';
import { trans } from 'laravel-vue-i18n';
import PsUtils from '@templateCore/utils/PsUtils';
import { useNotiStoreState } from "@templateCore/store/modules/noti/NotificationStore";
import NotiRegisterHolder from '@templateCore/object/holder/NotiRegisterHolder';
import NotiUnRegisterHolder from '@templateCore/object/holder/NotiUnRegisterHolder';
import PsConst from '@templateCore/object/constant/ps_constants';
import PsIcon from '@template1/vendor/components/core/icons/PsIcon.vue';
import PsToggle from '@template1/vendor/components/core/toggle/PsToggle.vue';
import { router } from '@inertiajs/vue3';

import PsSuccessDialog from '@template1/vendor/components/core/dialog/PsSuccessDialog.vue';

export default defineComponent({
    name : 'UserSettingModal',
    components : {
        PsModal,
        PsLabel,
        PasswordUpdateModal,
        PsConfirmDialog,
        PsLoadingDialog,
        PsRouteLink,
        PsIcon,
        PsSuccessDialog,
        PsToggle
    },
    setup() {
        const psmodal = ref();
        const message = ref("Loading the data ....");
        const password_update_modal = ref();
        const ps_confirm_dialog = ref();
        const ps_success_dialog = ref();
        const psloading = ref();

        // Get Login User Id
        const psValueStore = PsValueStore();
        const loginUserId = psValueStore.getLoginUserId();

        const LoginUserId = psValueStore.getLoginUserId();
        const notiProvider = useNotiStoreState();
        const holder = new NotiRegisterHolder();
        const holder1 = new NotiUnRegisterHolder();

        //for block user setting
        const appInfoStore = usePsAppInfoStoreState();
        const appInfoParameterHolder = new AppInfoParameterHolder();
        appInfoParameterHolder.userId = LoginUserId;

        const userdelete = new UserDeleteItemParameterHolder();

        //Inject Provider
        const userProvider = useUserStore();

        function openModal() {

            psmodal.value.toggle(true);
        }

        function closeModal() {
            psmodal.value.toggle(false);
        }

        function setMessage(messageStr) {
            message.value = messageStr;
        }

        // confirm dialog for user deactivate

        function openUserDeactivate(LoginUserId) {
           ps_confirm_dialog.value.openModal(
               trans('user_setting_modal__deactivate_title'),
               trans('user_setting_modal__deactivate_dialog'),
               trans('user_setting_modal__confirm'),
               trans('user_setting_modal__cancel'),
                () => {

                    doDeactivate(LoginUserId);
                },
                () => {
                    PsUtils.log("Cancel");
                }
            );
        }

        // Deactive Account

        async function doDeactivate(LoginUserId) {

            userdelete.userId = LoginUserId;

            psloading.value.openModal();
            const returnData = await userProvider.postDeleteUser(userdelete);
            psloading.value.closeModal();

            if(returnData.status == PsStatus.ERROR) {

                return;
            }else if(returnData.status == PsStatus.SUCCESS) {

                router.post(route('logout'));

            }

        }

        //password update modal

        function openPasswordUpdate() {
            password_update_modal.value.openModal();
        }

        function showProfile(value){
            psValueStore.replaceshowProfile(value);
        }

        function shownotiSetting(value){
            if(value == 'hide') {

                holder1.platformName = PsConst.PLATFORM;
                holder1.deviceId = localStorage.deviceToken;
                holder1.userId = LoginUserId;
                notiProvider.unRegisterNotiToken(holder1);
                ps_success_dialog.value.openModal(
                trans('notification_list__notification_list'),
                trans('hide_notifications_successful')
                );

            }else {

                holder.platformName = PsConst.PLATFORM;
                holder.deviceId = localStorage.deviceToken;
                holder.loginUserId = LoginUserId;
                notiProvider.registerNotiToken(holder);
                ps_success_dialog.value.openModal(
                trans('notification_list__notification_list'),
                trans('show_notifications_successful')
                );

            }
            psValueStore.replaceNotiSetting(value);
        }
        return {
            showProfile,
            shownotiSetting,
            psmodal,
            openModal,
            closeModal,
            message,
            setMessage,
            password_update_modal,
            ps_success_dialog,
            openPasswordUpdate,
            openUserDeactivate,
            ps_confirm_dialog,
            psloading,
            LoginUserId,
            doDeactivate,
            psValueStore,
            appInfoStore,
            PsConst,
            loginUserId
        }
    },
})
</script>
