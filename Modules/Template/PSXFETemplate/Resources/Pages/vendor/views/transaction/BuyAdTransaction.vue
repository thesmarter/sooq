<template>
<div>
    <Head :title="$t('package__packages')"/>
    <ps-content-container>
        <template #content>
            <div class="mt-32 mb-10">
                <div class="mb-7">
                    <ps-breadcrumb-2 :items="breadcrumb" class="" />
                </div>
                <div class="w-full flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
                    <ps-label-header-4 class="font-medium">
                        {{ $t("profile__purchased_package") }}
                    </ps-label-header-4>
                    <div class="">
                        <ps-button @click="buyAdClick()">
                            {{ $t("profile__buy_new_package") }}
                        </ps-button>
                    </div>

                </div>

                <div class='w-full flex flex-col items-start mt-6' >
                    <div class="w-full">
                            <!-- paid & promote ads -->
                        <div v-if="limitProvider.buyadList?.data != null">
                            <div class="grid xl:grid-cols-4 lg:grid-cols-3 md:grid-cols-2 sm:grid-cols-2 grid-cols-1 gap-6">
                                <div class="w-full" v-for="buyad in limitProvider.buyadList.data" :key="buyad.id">
                                    <!-- <ps-route-link :to="{ name: 'limit'}"> -->
                                        <limit-ad-horizontal-item  :buyad="buyad"/>
                                    <!-- </ps-route-link> -->
                                </div>
                            </div>
                            <div class="flex flex-col items-center">
                                <ps-button v-if="limitProvider.loading.value == false" class="font-medium mx-auto mt-6" @click="loadMore" :class="limitProvider.isNoMoreRecord.value ? 'hidden' : ''"> {{ $t("list__load_more") }} </ps-button>
                                <ps-button v-else class="font-medium mx-auto mt-6" @click="loadMore" :disabled="true"> {{ $t("list__loading") }} </ps-button>
                            </div>
                        </div>
                        <!-- <div v-else class="w-full flex justify-center">
                            <ps-label textColor="text-feSecondary-500 dark:text-feAchromatic-50 lg:text-xl sm:text-lg text-base font-medium" class="mt-10 flex-grow-0 mx-auto"> {{ $t("list__no_result") }} </ps-label>
                        </div> -->
                        <!-- end paid ads -->
                        <ps-no-result v-if="limitProvider.loading.value == false && limitProvider.buyadList?.data == null" @onClick="loadMore" />

                    </div>
                    <!-- <ps-button v-if="limitProvider.loading.value == false" class="w-60 mx-auto mt-8" @click="loadMore" :class="limitProvider.isNoMoreRecord.value ? 'hidden' : ''">{{ $t("list__load_more") }} </ps-button>
                    <ps-button v-else class="w-60 mx-auto mt-8" @click="loadMore" :disabled="true"> {{ $t("list__loading") }}  </ps-button> -->

                </div>
            </div>
        </template>
    </ps-content-container>
    <limit-item-modal ref="limit_item_modal" />
</div>
</template>

<script lang="ts">
import { Head } from '@inertiajs/vue3';
import { ref } from 'vue';
import PsContentContainer from '@template1/vendor/components/layouts/container/PsContentContainer.vue';
import PsLabelHeader4 from '@template1/vendor/components/core/label/PsLabelHeader4.vue';
import PsLabel from '@template1/vendor/components/core/label/PsLabel.vue';
import PsButton from '@template1/vendor/components/core/buttons/PsButton.vue';
import PsIcon from '@template1/vendor/components/core/icons/PsIcon.vue';
import PsRouteLink from '@template1/vendor/components/core/link/PsRouteLink.vue';
import LimitItemModal from '@template1/vendor/components/modules/item/LimitItemModal.vue';
import PsBreadcrumb2 from '@template1/vendor/components/core/breadcrumbs/PsBreadcrumb2.vue';
import PsNoResult from "@template1/vendor/components/modules/list/PsNoResult.vue";

import LimitAdHorizontalItem from "@template1/vendor/components/modules/item/LimitAdHorizontalItem.vue";
import { PsValueStore } from "@templateCore/store/modules/core/PsValueStore";
//Models
import { useLimitAdItemStoreState } from "@templateCore/store/modules/limit/LimitAdItemStore";

import "vue-skeletor/dist/vue-skeletor.css";
import PsFrontendLayout from '@template1/vendor/components/layouts/container/PsFrontendLayout.vue';
import { trans } from 'laravel-vue-i18n';import PsUtils from '@templateCore/utils/PsUtils';

export default {
    name : "PaidItemListView",
    components : {
        PsContentContainer,
        PsLabelHeader4,
        PsLabel,
        LimitAdHorizontalItem,
        PsButton,
        PsIcon,
        PsRouteLink,
        LimitItemModal,
        PsBreadcrumb2,
        PsNoResult,
        Head
    },
    layout: PsFrontendLayout,
    setup() {

        const psValueStore = PsValueStore();
        const loginUserId = psValueStore.getLoginUserId();
        // Inject Provider
        const limitProvider = useLimitAdItemStoreState();
        const limit_item_modal = ref();

        limitProvider.resetPaidAdItemList(loginUserId);

        // Load User By ID List

        function loadMore() {
            limitProvider.loadPaidAdItemList(loginUserId);
        }
        function buyAdClick(){
            limit_item_modal.value.openModal();
        }


        return {
            limitProvider,
            loadMore,
            limit_item_modal,
            buyAdClick
        }
    },
     computed: {
        breadcrumb() {
            return [
                {
                    label: trans('ps_nav_bar__home'),
                    url: route('dashboard')
                },
                {
                    label: trans('ps_nav_bar__profile'),
                    url: route('fe_profile')
                },
                {
                    label: trans('package__packages'),
                    color: "text-fePrimary-500"
                }
            ]
        }
    },
}
</script>
