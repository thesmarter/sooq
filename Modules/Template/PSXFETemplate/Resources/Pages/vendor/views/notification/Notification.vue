<template>
    <Head :title="$t('notification__notification_detail')"/>
     <ps-content-container>
        <template #content>
            <div class="sm:mt-32 lg:mt-36 mt-28">


                <ps-breadcrumb-2 :items="breadcrumb" class="" />

                <div class="flex flex-col">
                    <div class="lg:my-12 mt-7 mb-12">
                        <div class="text-center sm:mb-10 mb-6">
                            <ps-label-header-3 textColor="dark:text-feSecondary-200 text-feSecondary-800" class="font-semibold sm:mb-8 mb-4"> {{notiStore.noti?.data?.message}} </ps-label-header-3>
                            <p class="text-sm font-medium text-feSecondary-500 dark:text-feSecondary-400">Admin <span class="mx-6">|</span> {{ moment(notiStore.noti?.data?.addedDate).format($page.props.dateFormat)}}</p>
                        </div>

                        <div class=" w-auto cursor-pointer">
                            <img alt="Placeholder" width="300px" height="200px" class="block w-full rounded-lg lg:h-96 h-56 sm:h-72  object-cover"
                            v-lazy=" { src: $page.props.thumb3xUrl + '/' + notiStore.noti.data?.defaultPhoto?.imgPath, loading: $page.props.sysImageUrl+'/loading_gif.gif', error: $page.props.sysImageUrl+'/default_photo.png' }"
                            >
                            <div class="lg:mt-8 mt-6 md:w-3/4 md:mx-auto">
                                <p v-if="notiStore.noti.data != null">
                                    <span class="font-normal text-base text-feSecondary-600 dark:text-feSecondary-200" v-html="notiStore.noti.data?.description"> </span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </ps-content-container>

    <!-- google adsense desktop view -->
    <div class="mx-auto">
        <ps-ad-sense adFormat="horizontal"></ps-ad-sense>
    </div>

</template>

<script lang="ts">
import { Head } from '@inertiajs/vue3';
//Vue
import PsLabelTitle from '@template1/vendor/components/core/label/PsLabelTitle.vue';
import PsLabel from '@template1/vendor/components/core/label/PsLabel.vue';
import PsIcon from '@template1/vendor/components/core/icons/PsIcon.vue';
import PsRouteLink from '@template1/vendor/components/core/link/PsRouteLink.vue';
import PsAdSense from '@template1/vendor/components/core/adsense/PsAdSense.vue';
import PsBreadcrumb2 from "@template1/vendor/components/core/breadcrumbs/PsBreadcrumb2.vue";
import PsContentContainer from "@template1/vendor/components/layouts/container/PsContentContainer.vue";
import PsLabelHeader3 from '@template1/vendor/components/core/label/PsLabelHeader3.vue';

//Models
import { useNotiStoreState } from "@templateCore/store/modules/noti/NotificationStore";
import { PsValueStore } from "@templateCore/store/modules/core/PsValueStore";
import PsFrontendLayout from '@template1/vendor/components/layouts/container/PsFrontendLayout.vue';
import { trans } from 'laravel-vue-i18n';
import moment from 'moment';

export default {
    name : "NotificationView",
    components : {
        PsLabelTitle,
        PsLabel,
        PsIcon,
        PsRouteLink,
        PsAdSense,
        PsBreadcrumb2,
        PsContentContainer,
        PsLabelHeader3,
        Head
    },
    props: ['query'],
    layout: PsFrontendLayout,
    setup(props) {

        const notificationId = props.query.notiId.toString();
        const psValueStore = PsValueStore();
        const loginUserId = psValueStore.getLoginUserId();

        const notiStore = useNotiStoreState();
        notiStore.loadNoti(notificationId,loginUserId);

        return {
            notiStore,
            notificationId,
            moment
        }
    },
    computed:{
        breadcrumb(){
            return [
                {
                    label: trans('notification__notification_list'),
                    url: route('fe_notification_list')
                },
                {
                    label: trans('notification__notification_detail'),
                    color: "text-fePrimary-500"
                }
            ]
        }
    }
}
</script>
