<template>
    <ps-card :class="['flex w-full flex-col lg:rounded-lg shadow-sm', color]" :enabledHover="true">
        <div class=" shadow-sm relative border border-feSecondary-50 dark:border-feSecondary-800 rounded-lg flex flex-col gap-4 p-4">
            <div class="flex flex-row space-x-4" >

                    <div class="w-20 h-20 flex-shrink-0 relative">
                        <img alt="Placeholder" width="15px" height="10px" class="w-20 h-20 rounded-full object-cover"
                            v-lazy=" { src: $page.props.thumb1xUrl + '/' + vendor?.logo?.imgPath, loading: $page.props.sysImageUrl+'/loading_gif.gif', error: $page.props.sysImageUrl+'/default_vendor_shop.png' }" >
                        <div v-if="vendor?.expireStatus == PsConst.VendorExpiryExpiredStatus" class="w-20 h-20 flex items-center justify-center absolute top-0 left-0">
                            <div class="w-20 h-20 absolute rounded-full top-0 left-0 bg-feAchromatic-900 opacity-60"></div>
                            <div class="w-20 h-20 flex items-center justify-center absolute">
                                <ps-label textColor="text-feAchromatic-50 text-sm font-semibold">{{$t('Closed')}}</ps-label>
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-col items-center justify-center">
                        <ps-label class=" text-lg font-semibold" textColor="text-feSecondary-800 dark:text-feSecondary-300" > {{ vendor?.name }} </ps-label>
                        <p class="w-full flex flex-col  items-start justify-start">
                            <ps-label class=" mt-1 p-1 text-xxs rounded-sm bg-feWarning-500" textColor="text-feAchromatic-50"> {{ $t('core_fe__vendor_indicator') }}</ps-label>
                        </p>
                    </div>

            </div>
            <ps-route-link :to="{ name: 'fe_vendor_info', params: { vendorId: vendor?.id }}" >
                <ps-button textSize="text-sm"
                            colors="bg-transparent text-feSecondary-800 dark:text-feSecondary-200 " border="border border-feAchrometic-200 dark:border-feAchrometic-500"
                            class="mt-2 w-full"
                            active="" hover="" focus="" >
                    {{ $t("core_fe__vendor_profile_card_visit_vendor") }}
                </ps-button>
            </ps-route-link>


        </div>
    </ps-card>
</template>

<script lang="ts">

import { ref } from 'vue'
import { router } from '@inertiajs/vue3';

import PsCard from '@template1/vendor/components/core/card/PsCard.vue';
import PsLabel from '@template1/vendor/components/core/label/PsLabel.vue'
import PsButton from '@template1/vendor/components/core/buttons/PsButton.vue';
import PsIcon from '@template1/vendor/components/core/icons/PsIcon.vue';
import PsLabelCaption from '@template1/vendor/components/core/label/PsLabelCaption.vue';
import PsRouteLink from '@template1/vendor/components/core/link/PsRouteLink.vue';
import PsConst from '@templateCore/object/constant/ps_constants';

import Vendor from '@templateCore/object/Vendor';
import moment from "moment";

export default {
    name : "VendorProfileCard",
    components : {
        PsCard,
        PsLabel,
        PsButton,
        PsIcon,
        PsLabelCaption,
        PsRouteLink,
    },
    props : {
        vendor : { type : Vendor } ,
        itemId : {
            type: String,
            default: ""
        },
        color : {
            type: String,
            default: "bg-feSecondary-50 dark:bg-feSecondary-800 mt-6"
        },
    },
    setup(props) {

        return {
            moment,
            PsConst
        }
    }
}
</script>
