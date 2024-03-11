<template>
    <div v-if="limitProvider.buyadList?.data == null && limitProvider.loading.value == false" class="w-full h-40 sm:col-span-2 lg:col-span-2 xl:col-span-3 bg-feWarning-50 rounded-lg py-5 mb-12">

        <div class="flex flex-row relative items-center no-underline text-feAchromatic-900">

            <img v-lazy="{ src: $page.props.sysImageUrl + '/rectangle.png' }" alt=""
                class="w-28 h-28 object-cover bottom-0 ms-6">
            <ps-label-header-4 class="font-medium"> {{ limitProvider.buyadList?.data }} </ps-label-header-4>
            <div class="flex flex-col ms-6">
                <ps-label class=" text-light md:text-lg" textColor="text-feSecondary-800 dark:text-feSecondary-300" > {{ $t("profile__purchased_package_title") }} </ps-label>
                <ps-label class=" font-light text-sm mt-3" textColor="text-feSecondary-500 dark:text-feSecondary-500"> {{ $t("profile__purchased_package_descriptin") }}  </ps-label>
                <ps-button class="flex flex-row w-32 mt-4" shadow="shadow-sm" rounded="rounded" hover="" focus=""
                    colors="bg-fePrimary-500 text-feSecondary-50" @click="buyAdClick()">
                    {{ $t("dashboard__buy_package") }}
                </ps-button>
            </div>
        </div>
    </div>
    <div v-else-if="limitProvider.buyadList?.data != null" class="w-full sm:col-span-2 lg:col-span-2 xl:col-span-3 pb-12">
        <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4">
            <ps-label-header-4 class="font-medium"> {{ $t("profile__purchased_package") }} </ps-label-header-4>
            <div class="flex justify-between gap-5">
                <ps-button class="flex flex-row " @click="buyAdClick()">
                    {{ $t("profile__buy_new_package") }}
                </ps-button>
                <ps-route-link :to="{name: 'fe_limit_ads' }">
                    <ps-button class="flex flex-row" padding="p-2 sm:px-4 sm:py-2" shadow="shadow-sm" rounded="rounded" hover=""
                        focus="" border="border border-feSecondary-200 dark:border-feSecondary-800"
                        colors="bg-feAchromatic-50 text-feSecondary-800 dark:bg-feSecondary-800 dark:text-feSecondary-200">
                        <ps-label class="hidden sm:inline">{{ $t("list_fe__view_all_label") }}</ps-label>
                        <ps-icon class="sm:ms-2 block rtl:hidden" textColor="dark:text-feSecondary-200" name="rightChervon"
                            h="24" w="24" />
                        <ps-icon class="sm:ms-2 hidden rtl:block" textColor="dark:text-feSecondary-200" name="leftChervon"
                            h="24" w="24" />
                    </ps-button>
                </ps-route-link>
            </div>
        </div>
        <div class="w-full mt-6 grid sm:grid-cols-2 md:grid-cols-2 lg:grid-cols-2 xl:grid-cols-3 gap-6 sm:gap-5 md:gap-6">
            <!-- Purchased Package List -->
                <div class="w-full hidden xl:block" v-for="buyad in limitProvider.buyadList?.data.slice(0, 6)" :key="buyad.id">
                    <limit-ad-horizontal-item  :buyad="buyad" padding="py-10 px-10 sm:px-3 lg:px-9 xl:px-4"/>
                </div>
                <div class="w-full hidden sm:block xl:hidden" v-for="buyad in limitProvider.buyadList?.data.slice(0, 4)" :key="buyad.id">
                    <limit-ad-horizontal-item  :buyad="buyad" padding="py-10 px-10 sm:px-3 lg:px-9 xl:px-4"/>
                </div>
                <div class="w-full sm:hidden" v-for="buyad in limitProvider.buyadList?.data.slice(0, 2)" :key="buyad.id">
                    <limit-ad-horizontal-item  :buyad="buyad" padding="py-10 px-10 sm:px-3 lg:px-9 xl:px-4"/>
                </div>
            <!-- END Purchased Package List -->
       </div>
    </div>

    <limit-item-modal ref="limit_item_modal" />

</template>

<script>

    import { ref, defineAsyncComponent } from 'vue';

    import PsLabelHeader4 from '@template1/vendor/components/core/label/PsLabelHeader4.vue';
    import PsButton from '@template1/vendor/components/core/buttons/PsButton.vue';
    import PsRouteLink from '@template1/vendor/components/core/link/PsRouteLink.vue';
    import PsIcon from '@template1/vendor/components/core/icons/PsIcon.vue';

    import LimitAdHorizontalItem from "@template1/vendor/components/modules/item/LimitAdHorizontalItem.vue";

    import { useLimitAdItemStoreState } from "@templateCore/store/modules/limit/LimitAdItemStore";
    import { usePsAppInfoStoreState } from '@templateCore/store/modules/appinfo/AppInfoStore';
    import PsUtils from '@templateCore/utils/PsUtils';

    const LimitItemModal = defineAsyncComponent(() => import('@template1/vendor/components/modules/item/LimitItemModal.vue'));

    export default {
        name : "ProfilePurchasedPackageHorizontalList",
        components : {
            PsLabelHeader4,
            PsButton,
            PsRouteLink,
            PsIcon,
            LimitAdHorizontalItem,
            LimitItemModal
        },
        setup(){

            const appInfoStore = usePsAppInfoStoreState();
            const limitProvider = useLimitAdItemStoreState();
            const loginUserId = localStorage.loginUserId;

            const limit_item_modal = ref();

            limitProvider.resetPaidAdItemList(loginUserId);

            async function buyAdClick(){
                await PsUtils.waitingComponent(limit_item_modal);
                limit_item_modal.value.openModal();
            }

            return {
                limitProvider,
                appInfoStore,
                limit_item_modal,
                buyAdClick
            }

        }
    }

</script>
