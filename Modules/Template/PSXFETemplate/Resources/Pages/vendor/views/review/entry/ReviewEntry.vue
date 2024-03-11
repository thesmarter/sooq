<template>
    <Head :title="$t('review_entry__review_entry')"/>
    <ps-content-container >
        <template #content>
            <div class="sm:mt-32 lg:mt-36 mt-28 mb-8 ">
                <div class="flex flex-col items-start mt-20 ms-8">
                    <ps-label-header-4 class="font-medium"> {{ $t("review_entry__review_entry") }}</ps-label-header-4>
                </div>

                <!-- breadcrumb -->
                <div class=" mb-4 flex items-center mt-8 ms-8">
                    <ps-route-link :to="{ name: 'profile' }">
                    <ps-label
                        class="hover:underline cursor-pointer font-medium">
                            {{ breadcrumbuserProvider.user.data ? breadcrumbuserProvider.user.data.userName:'' }}
                    </ps-label>
                    </ps-route-link>

                    <ps-icon name="rightArrow" class='mx-4'/>
                    <ps-route-link :to="{ name: 'review-list',query: { user_id: loginUserId } }">
                        <ps-label class="hover:underline cursor-pointer font-medium"> {{ $t("review_entry__review_list") }} </ps-label>
                    </ps-route-link>

                    <ps-icon name="rightArrow" class='mx-4'/>

                    <ps-label class="font-light"> {{ $t("review_entry__review_entry") }} </ps-label>
                </div>
                <!-- end breadcrumb -->

                <!-- Entry -->
                <div class="flex justify-between sm:w-3/4 md:w-2/3 w-full p-4 ms-4">
                    <div class="w-full flex flex-col bg-feAchromatic-50 dark:bg-feAchromatic-800 border rounded-md border-feAchromatic-200 dark:border-black pt-4 py-4 px-8">

                        <ps-label >  {{ $t("review_entry__your_rating") }} </ps-label>
                        <rating-selected ref="rating" class="mt-5" :grade="0" :maxStars="5" :hasCounter="true" />

                        <ps-label class="mt-5">  {{ $t("review_entry__title") }} </ps-label>
                        <ps-input class="md:w-10/12 mt-2" type="text" v-bind:placeholder="$t('review_entry__title')" v-model:value="title"></ps-input>

                        <ps-label class="mt-5">  {{ $t("review_entry__message") }} </ps-label>
                        <ps-textarea class="md:w-10/12 mt-2" v-bind:placeholder="$t('review_entry__message')" :rows="5" v-model:value="description"></ps-textarea>

                        <div class="flex items-center justify-center mb-4">
                            <ps-button class="text-center w-60 mx-auto mt-8 py-4" @click="clicked">  {{ $t("review_entry__submit") }}</ps-button>
                        </div>
                    </div>
                </div>
            </div>

            <ps-success-dialog ref="ps_success_dialog" />
        </template>
    </ps-content-container>
</template>


<script lang="ts">
import { Head } from '@inertiajs/vue3';
//Vue
import {ref} from 'vue';

import PsContentContainer from '@template1/vendor/components/layouts/container/PsContentContainer.vue';
import PsLabelHeader4 from '@template1/vendor/components/core/label/PsLabelHeader4.vue';
import PsLabel from '@template1/vendor/components/core/label/PsLabel.vue';
import PsButton from '@template1/vendor/components/core/buttons/PsButton.vue';
import PsIcon from '@template1/vendor/components/core/icons/PsIcon.vue';
import PsInput from '@template1/vendor/components/core/input/PsInput.vue';
import RatingSelected from '@template1/vendor/components/core/rating/RatingSelected.vue';
import PsTextarea from '@template1/vendor/components/core/textarea/PsTextarea.vue';
import PsSuccessDialog from '@template1/vendor/components/core/dialog/PsSuccessDialog.vue';
import PsRouteLink from '@template1/vendor/components/core/link/PsRouteLink.vue';

// Models
import { PsValueStore } from "@templateCore/store/modules/core/PsValueStore";
import { useRatingStoreState } from "@templateCore/store/modules/rating/RatingStore";
import { createUserProviderState } from "@templateCore/store/modules/user/UserProvider";

// Params Holders
import RatingHolder from '@templateCore/object/holder/RatingHolder';
import { router } from '@inertiajs/vue3';

//language
import { trans } from 'laravel-vue-i18n';import PsConst from '@templateCore/object/constant/ps_constants';

export default {
    name : "ReviewEntryView",
    components : {
        PsContentContainer,
        PsLabelHeader4,
        PsLabel,
        PsButton,
        PsIcon,
        PsInput,
        PsTextarea,
        RatingSelected,
        PsSuccessDialog,
        PsRouteLink,
        Head
    },
    setup() {

        // const route = useRoute();
        const psValueStore = PsValueStore();
        const holder = new RatingHolder();
        const ratingProvider = useRatingStoreState();
        const breadcrumbuserProvider = createUserProviderState();
        const ps_success_dialog = ref();
        const loginUserId = psValueStore.getLoginUserId();

        if(loginUserId == null || loginUserId == '' || loginUserId == PsConst.NO_LOGIN_USER) {
            router.get(route('login'));
        }

        const toUserId = "route.query.user_id?.toString() ?? ''";

        const rating = ref();
        const title = ref();
        const description = ref();

        // Load User By ID List
        breadcrumbuserProvider.getUser(loginUserId);


        async function clicked() {
            holder.fromUserId = loginUserId;
            holder.toUserId = toUserId;
            holder.rating = rating.value.getRating();
            holder.title = title.value;
            holder.description = description.value;
            ratingProvider.postRating(holder,loginUserId);
            ps_success_dialog.value.openModal('',
            trans('review_entry__rating_successed'));
            clearAll();
        }

        async function clearAll() {
            rating.value = 0;
            description.value = '';
            title.value = '';
        }

        return{
            breadcrumbuserProvider,
            ratingProvider,
            clicked,
            description,
            rating,
            title,
            ps_success_dialog,
            loginUserId
        }
    }
}
</script>
