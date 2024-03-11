<template>
    <ps-card class="flex w-full mt-6 bg-feAchromatic-50 dark:bg-feSecondary-800 flex-col lg:rounded-lg shadow-sm " :enabledHover="true">
        <div class="flex flex-col px-2 py-4 lg:p-4 border border-feSecondary-50 dark:border-feSecondary-800 rounded-lg"
            v-if="productStore.isUserItem(loginUserId)">
            <div class="flex items-center gap-1">
                <ps-icon name="statistic" w="24" h="24" />
                <ps-label
                    textColor="text-base font-semibold text-feSecondary-800 dark:text-feSecondary-50">{{
                        $t('item_detail__statistic') }}</ps-label>
            </div>
            <div class="grid grid-cols-2 mt-6">
                <div class="flex flex-col items-center border-e-2 border-feSecondary-500">
                    <ps-label
                        textColor="font-semibold text-xl text-feSecondary-800 dark:text-feSecondary-50">
                        {{ productStore.item?.data?.touchCount }} </ps-label>
                    <div
                        class="flex items-center gap-1 mt-1 text-feSecondary-800 dark:text-feSecondary-200">
                        <ps-icon name="eye-on" w="24" h="24" />
                        <ps-label textColor="font-medium text-base"> {{ $t('item_detail__views') }}
                        </ps-label>
                    </div>
                </div>
                <div class="flex flex-col items-center">
                    <ps-label
                        textColor="font-semibold text-xl text-feSecondary-800 dark:text-feSecondary-50">
                        {{ productStore.item?.data?.favouriteCount }} </ps-label>
                    <div
                        class="flex items-center gap-1 mt-1 text-feSecondary-800 dark:text-feSecondary-200">
                        <ps-icon name="heart-outline" w="24" h="24" />
                        <ps-label textColor="font-medium text-base"> {{ $t('item_detail__favourites') }}
                        </ps-label>
                    </div>
                </div>
            </div>
        </div>
    </ps-card>

</template>

<script>

import PsLabel from '@template1/vendor/components/core/label/PsLabel.vue';
import PsIcon from '@template1/vendor/components/core/icons/PsIcon.vue';

export default {
        name : 'ItemDetailStatisticCard',
        components : {
            PsLabel,
            PsIcon
        },
        props : {
            loginUserId : null,
            productStore : Object,
        },
    }
</script>