<template>
    <ps-content-container>
        <template #content>
            <div class="mt-32 mb-10">
                <div class="xl:mb-7 lg:mb-11 sm:mb-7 mb-9">
                    <ps-breadcrumb-2 :items="breadcrumb" class="" />
                </div>
                <div>
                    <div class="grid lg:grid-cols-12 sm:grid-cols-8 grid-cols-4  gap-4 sm:gap-3.5 lg:gap-4 ">
                        <div class='w-full col-span-4 sm:col-span-2 lg:col-span-3 mt-2 '>
                            <profile-card />
                            <other-user-action-section :userId="userId" />
                        </div>

                        <latest-item-listing :userId="userId" />
                    </div>
                </div>
            </div>
        </template>
    </ps-content-container>
</template>
<script>

//libs
import { trans } from 'laravel-vue-i18n';
//Components
import PsContentContainer from '@template1/vendor/components/layouts/container/PsContentContainer.vue';
import PsFrontendLayout from '@template1/vendor/components/layouts/container/PsFrontendLayout.vue';
import PsBreadcrumb2 from '@template1/vendor/components/core/breadcrumbs/PsBreadcrumb2.vue';
import ProfileCard from './components/OtherUserProfileInfoCard.vue';
import LatestItemListing from './components/ProfileLatestItemGrid.vue';
import OtherUserActionSection from './components/OtherUserActionSection.vue';
// Store
import { useUserStore } from "@templateCore/store/modules/user/UserStore";
import { PsValueStore } from "@templateCore/store/modules/core/PsValueStore";
import { onMounted, ref } from 'vue';
//holder
import UserParameterHolder from '@templateCore/object/holder/UserParameterHolder';

export default {
    name: "OtherProfileView",
    components: {
        PsBreadcrumb2,
        PsContentContainer,
        ProfileCard,
        LatestItemListing,
        OtherUserActionSection
    },
    layout: PsFrontendLayout,
    props: ['query'],
    setup(props) {
        const userId = props.query?.userId?.toString();
        const psValueStore = PsValueStore();
        const LoginUserId = psValueStore.getLoginUserId();

        const userStore = useUserStore('other');
        const paramHolder = new UserParameterHolder().getOtherUserData();

        paramHolder.loginUserId = LoginUserId;
        paramHolder.id = userId;
        let breadcrumb = ref([]);

        onMounted(async () => {
            await userStore.loadOtherUser(LoginUserId, paramHolder);
            breadcrumb.value = [
                {
                    label: trans('fe__other_profile'),
                    url: route('fe_profile')
                },
                {
                    label: userStore.user.data ? userStore.user.data.userName : '',
                    color: "text-fePrimary-500"
                }
            ];
        })


        return {
            userStore,
            userId,
            breadcrumb
        }
    },
}
</script>
