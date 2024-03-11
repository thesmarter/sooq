
<template>
    <div class="mt-10  xl:w-feLarge lg:w-large md:w-full md:px-6 lg:px-0 mx-auto bg-feSecondary-800" v-if="packageStore.hasData()">
            <div class=" flex flex-col sm:flex-row justify-between gap-8 sm:gap-5 mt-1 px-4 py-6 sm:p-10">
                <div class="md:w-64 flex flex-col gap-4">
                <div class="">
                <ps-label-header-5 class="font-semibold" textColor="text-feSecondary-50">{{
            $t("dashboard__available_post_quota_packages") }}</ps-label-header-5>
                <div class="pt-2 pb-1 sm:mt-0">
                    <ps-label textColor="font-normal text-lg text-feSecondary-50">{{
                        $t("dashboard__quota_packages_description") }}</ps-label>
                </div>
                <ps-button class="flex flex-row mt-4" shadow="shadow-sm" rounded="rounded" hover="" focus=""
                    border="border border-feSecondary-200 dark:border-feSecondary-800"
                    colors="bg-fePrimary-500 text-feSecondary-50" @click="buyAdClick()">
                    {{ $t("dashboard__buy_package") }}
                </ps-button>
            </div>
        </div>
        <div class="w-full sm:max-w-sm md:max-w-md lg:max-w-xl xl:max-w-3xl">

            <splide :options="options" :has-track="false">
                <splide-track>
                    <splide-slide class=""
                        v-for="packageList in packageStore?.packageList?.data" :key="packageList.id">
                        <package-horizontal-package :packageList="packageList" @click="buyAdClick()" />
                    </splide-slide>
                </splide-track>
                <div :class="'splide__arrows splide__arrows--' + getDir()">
                    <button
                        class="bg-feSecondary-800 border border-feSecondary-500  w-9 h-9 rounded shadow-sm p-2 arrow splide__arrow--prev"
                        type="button" aria-label="Previous slide" aria-controls="splide01-track">
                       
                            <ps-icon  textColor="text-feSecondary-50" name="arrowNarrowRight" h="20" w="20"
                            viewBox="0 -3 9 20" />
                   
                        
                    </button>
                    <button
                        class="bg-feSecondary-800 border border-feSecondary-500 w-9 h-9 rounded shadow-sm p-2 arrow splide__arrow--next"
                        type="button" aria-label="Next slide" aria-controls="splide01-track">
                        <ps-icon textColor="text-feSecondary-50" name="arrowNarrowRight" h="20" w="20"
                            viewBox="0 -3 9 20" />
                    </button>
                </div>

            </splide>

        </div>
        </div>
        <limit-item-modal v-if="showLimitModel" ref="limit_item_modal" />
        <ps-confirm-dialog ref='ps_confirm_dialog' />
    </div>
</template>


<script>
// Libs
import { ref, onMounted, defineAsyncComponent } from 'vue';
import { trans } from 'laravel-vue-i18n';
import { router } from '@inertiajs/vue3';
import { Splide, SplideSlide, SplideTrack } from '@splidejs/vue-splide';
import '@splidejs/vue-splide/css';
// Components
import PsLabel from '@template1/vendor/components/core/label/PsLabel.vue';
import PsLabelHeader5 from '@template1/vendor/components/core/label/PsLabelHeader5.vue';
import PsButton from '@template1/vendor/components/core/buttons/PsButton.vue';
import PackageHorizontalPackage from '@template1/vendor/components/modules/packages/PackageHorizontalPackage.vue';
const LimitItemModal = defineAsyncComponent(() => import('@template1/vendor/components/modules/item/LimitItemModal.vue'));
import PsConfirmDialog from '@template1/vendor/components/core/dialog/PsConfirmDialog.vue';
import PsIcon from '@template1/vendor/components/core/icons/PsIcon.vue';
// Providers
import { usePackageStoreState } from '@templateCore/store/modules/package/PackageStore';
import { PsValueStore } from "@templateCore/store/modules/core/PsValueStore";

import PsUtils from '@templateCore/utils/PsUtils';
import PsConst from '@templateCore/object/constant/ps_constants';

export default {
    name: 'DashboardPackageHorizontalList',
    components: {
        Splide,
        SplideSlide,
        SplideTrack,
        PsIcon,
        PsLabel,
        PsLabelHeader5,
        PackageHorizontalPackage,
        PsButton,
        LimitItemModal,
        PsConfirmDialog
    },
    setup() {
        const limit_item_modal = ref();
        const showLimitModel = ref(false);
        const ps_confirm_dialog = ref();

        const psValueStore = PsValueStore();
        const loginUserId = psValueStore.getLoginUserId();
        const packageStore = usePackageStoreState('dashboard_packages');

        const options = {
                rewind: true,
                gap   : '1rem',
                perPage: 4,
                focus  : 0,
                omitEnd: true,
                pagination: false,
                direction: getDir(),
                breakpoints:{
                    1536:{
                        perPage: 4,
                    },
                    1280:{
                        perPage:4,
                      
                    },
                    1024:{
                        perPage:4,
                     
                    },
                    996:{
                        perPage:3,
                      
                    },
                    640:{
                        perPage:3,
                       
                    },
                    360:{
                        perPage:2,
                    },
                    200:{
                        perPage:2
                    }
                }
            };
            const options2 = {
                rewind: true,
                gap   : '1.5rem',
                perPage: 4,
                focus  : 0,
                omitEnd: true,
            };
            const options3 = {
                rewind: true,
                gap   : '1.5rem',
                perPage: 4,
                focus  : 0,
                omitEnd: true,
            };
            const options4 = {
                rewind: true,
                gap   : '1.5rem',
                perPage: 4,
                focus  : 0,
                omitEnd: true,
            };

        function getDir() {
            if (localStorage.activeLanguage != null && localStorage.activeLanguage != undefined && localStorage.activeLanguage == 'ar') {
                return 'rtl';
            } else {
                return 'ltr';
            }
        }

        async function buyAdClick() {

            if (loginUserId != '' && loginUserId != PsConst.NO_LOGIN_USER) {
                showLimitModel.value = true;
                await PsUtils.waitingComponent(limit_item_modal, 20);
                limit_item_modal.value.openModal();
            } else {
                ps_confirm_dialog.value.openModal(
                    trans('Login'),
                    trans('Please login to buy package'),
                    trans('Ok'),
                    trans('cancel'),
                    () => {
                        router.get(route('login'));
                    },
                    () => {
                        PsUtils.log("Cancel");
                    }
                );
            }
        }

        onMounted(() => {
            packageStore.packageListWithPurchasedCount(loginUserId);
        });

        return {
            packageStore,
            buyAdClick,
            limit_item_modal,
            showLimitModel,
            ps_confirm_dialog,
            options, getDir
        }
    }

}
</script>


<style scoped>
.splide__arrow--prev{
    position:absolute;
    top:50%;
    transform:translateY(-50%);
}
.splide__arrow--next{
    position:absolute;
    top:50%;
    transform:translateY(-50%);
}
/* right to left */
.splide__arrows--rtl .splide__arrow--prev{left:auto;right:-1em}
.splide__arrows--rtl .splide__arrow--next{right:auto;left:-1em}

/* left to right */
.splide__arrows--ltr .splide__arrow--prev{right:auto;left:-1em}
.splide__arrows--ltr .splide__arrow--next{left:auto;right:-1em}

    /* .splide{z-index: 0; padding: 0;}
@media (max-width: 640px) {

    .splide__arrow--next{
        left: calc(100% - 1.5rem);
    }
}
@media (max-width: 786px) {
    .splide__arrow--prev{
        left: -1rem;
    }
    .splide__arrow--next{
        right: -1rem;
    }
} */
</style>



