<template>
    <Head :title="$t('credit_card_view__card_entry')"/>
    <ps-content-container>
        <template #content>
            <!-- Item entry title -->
            <div class="flex flex-col items-center mt-36">
                <ps-label-header-4 class="font-medium"> {{ $t("credit_card_view__card_entry") }} </ps-label-header-4>
            </div>
            <!-- End Item entry title -->
            <!-- Start Input Field for md .. -->
            <div class="flex justify-between container w-full p-4">
                <!-- Start Left Screen -->
                <div class="md:flex flex-auto md:w-6/12 h-auto bg-feAchromatic-50 dark:bg-feAchromatic-900 border rounded-md border-feAchromatic-200 dark:border-black  pt-2">
                    <div class="flex flex-col w-full">

                        <!-- for Card Number -->
                        <ps-label class="mt-4 mx-8 "> {{ $t("credit_card_view__card_number") }} <span style="font-size: 17px; color: red;">*</span> </ps-label>
                        <ps-input class="mt-2 mx-8" type="text" v-bind:placeholder="$t('credit_card_view__number_placeholder')" v-model:value='paramHolder.cardNo' />
                        <!-- end Card Number -->

                        <!-- for Expiry Date -->
                        <ps-label class="mt-4 mx-8 "> {{ $t("credit_card_view__expired_date") }} <span style="font-size: 17px; color: red;">*</span> </ps-label>
                        <ps-input class="mt-2 mx-8" type="text" v-bind:placeholder="$t('credit_card_view__expired_date_placeholder')" v-model:value='paramHolder.expiryDate' />
                        <!-- end Expiry Date -->

                        <!-- for CVV -->
                        <ps-label class="mt-4 mx-8 "> {{ $t("credit_card_view__cvv") }} <span style="font-size: 17px; color: red;">*</span> </ps-label>
                        <ps-input class="mt-2 mx-8" type="text" v-bind:placeholder="$t('credit_card_view__cvv_placeholder')" v-model:value='paramHolder.cvv' />
                        <!-- end CVV -->

                        <!-- for Card Holder Name -->
                        <ps-label class="mt-4 mx-8 "> {{ $t("credit_card_view__card_holder_name") }} <span style="font-size: 17px; color: red;">*</span> </ps-label>
                        <ps-input class="mt-2 mx-8" type="text"  v-bind:placeholder="$t('credit_card_view__card_holder_name_placeholder')" v-model:value='paramHolder.nameOnCard' />
                        <!-- end Card Holder Name -->

                        <!-- for Email -->
                        <ps-label class="mt-4 mx-8 "> {{ $t("credit_card_view__email") }} <span style="font-size: 17px; color: red;">*</span> </ps-label>
                        <ps-input class="mt-2 mx-8" type="text" v-bind:placeholder="$t('credit_card_view__email_placeholder')" v-model:value='profileParamHolder.userEmail' />
                        <!-- end Email -->

                        <!-- for Phone -->
                        <ps-label class="mt-4 mx-8 "> {{ $t("credit_card_view__phone") }} <span style="font-size: 17px; color: red;">*</span> </ps-label>
                        <ps-input class="mt-2 mx-8" type="text" v-bind:placeholder="$t('credit_card_view__phone_placeholder')" @keypress="phoneNumber($event)" v-model:value='profileParamHolder.userPhone' />
                        <!-- end Phone -->

                        <!-- for Street -->
                        <ps-label class="mt-4 mx-8 "> {{ $t("credit_card_view__street") }} <span style="font-size: 17px; color: red;">*</span> </ps-label>
                        <ps-input class="mt-2 mx-8" type="text" v-bind:placeholder="$t('credit_card_view__street_placeholder')" v-model:value='profileParamHolder.streetName' />
                        <!-- end Street -->

                        <!-- for City -->
                        <ps-label class="mt-4 mx-8 "> {{ $t("credit_card_view__city") }} <span style="font-size: 17px; color: red;">*</span> </ps-label>
                        <ps-input class="mt-2 mx-8" type="text" v-bind:placeholder="$t('credit_card_view__city_placeholder')" v-model:value='profileParamHolder.city' />
                        <!-- end City -->

                        <!-- for State -->
                        <ps-label class="mt-4 mx-8 "> {{ $t("credit_card_view__state") }} <span style="font-size: 17px; color: red;">*</span> </ps-label>
                        <ps-dropdown align="left" class='mt-2 mx-8' @onClick="loadStateList()" >
                            <template #select>
                                <ps-dropdown-select :selectedValue="paramHolder.stateName" />
                            </template>
                            <template #list >
                                <div class="rounded-md shadow-xs w-56" >
                                    <div class="pt-2 z-30 absolute">
                                        <div v-if="userStateListProvider.userStateList.data == null">
                                            <p class='p-2' @click="loadStateList()" >{{ $t("credit_card_view__loading") }} </p>
                                        </div>
                                        <div v-else>
                                            <div v-for="selectedState in userStateListProvider.userStateList.data" :key="selectedState.id"
                                            class="w-56 flex py-4 px-2 hover:bg-fePrimary-50 dark:hover:bg-fePrimary-900 cursor-pointer items-center"
                                            @click="userStateFilterClicked(selectedState)" >
                                                <ps-label class="ms-2" :class="selectedState.id==paramHolder.stateId ? ' font-medium' : ''"  > {{selectedState.name}} </ps-label>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </template>
                        </ps-dropdown>
                        <!-- end model -->

                        <!-- for Zip Code -->
                        <ps-label class="mt-4 mx-8 "> {{ $t("credit_card_view__zip_code") }} <span style="font-size: 17px; color: red;">*</span> </ps-label>
                        <ps-input class="mt-2 mx-8" type="text" :maxlength="5" v-bind:placeholder="$t('credit_card_view__zip_code_placeholder')" @keypress="isNumber($event)" v-model:value='profileParamHolder.zipCode' />
                        <!-- end Zip Code -->

                        <!-- for Apartment Number -->
                        <ps-label class="mt-4 mx-8 "> {{ $t("credit_card_view__apartment_number") }} <span style="font-size: 17px; color: red;">*</span> </ps-label>
                        <ps-input class="mt-2 mx-8 mb-8" type="text" v-bind:placeholder="$t('credit_card_view__cvv_placeholder')" v-model:value='profileParamHolder.apartmentNo' />
                        <!-- end Apartment Number -->

                    </div>
                </div>
                <!-- End Left Screen -->

            </div>
            <!-- End Input Field -->

            <div class="flex items-center justify-center mb-4">
                <ps-button class="text-center w-60 mx-auto mt-8 py-4" > {{ $t("credit_card_view__submit")}} </ps-button>
                <!-- @click="clicked"> Submit </ps-button> -->
            </div>
        </template>
    </ps-content-container>
</template>

<script lang='ts'>
import { Head } from '@inertiajs/vue3';
import { defineComponent, reactive } from 'vue';
import PsContentContainer from '@template1/vendor/components/layouts/container/PsContentContainer.vue';
import PsLabelHeader4 from '@template1/vendor/components/core/label/PsLabelHeader4.vue';
import PsLabel from '@template1/vendor/components/core/label/PsLabel.vue';
import PsInput from '@template1/vendor/components/core/input/PsInput.vue';
import PsButton from '@template1/vendor/components/core/buttons/PsButton.vue';
import PsDropdown from '@template1/vendor/components/core/dropdown/PsDropdown.vue';
import PsDropdownSelect from '@template1/vendor/components/core/dropdown/PsDropdownSelect.vue';

import CreditCardPrameterHolder from '@templateCore/object/holder/CreditCardParameterHolder';
import ProfileUpdateViewHolder from '@templateCore/object/holder/ProfileUpdateViewHolder';
import { useUserStateStore } from '@templateCore/store/modules/user/UserStateStore';

export default defineComponent({
    class: "CreditCardView",
    components: {
        PsContentContainer,
        PsLabel,
        PsInput,
        PsButton,
        PsLabelHeader4,
        PsDropdown,
        PsDropdownSelect,
        Head
    },
    setup() {
        const paramHolder = reactive(new CreditCardPrameterHolder());
        const profileParamHolder = new ProfileUpdateViewHolder();

        const userStateListProvider = useUserStateStore();

        function loadStateList() {
            userStateListProvider.loadUserStateList();
        }

        function userStateFilterClicked(value) {
            paramHolder.stateId = value.id;
            paramHolder.stateName = value.name;
        }

        function phoneNumber(evt) {
            evt = (evt) ? evt : window.event;
            const charCode = (evt.which) ? evt.which : evt.keyCode;

            if ( charCode < 42 || charCode > 57 ) {
                evt.preventDefault();
            } else {
                return true;
            }
        }

        function isNumber(evt) {
            evt = (evt) ? evt : window.event;
            const charCode = (evt.which) ? evt.which : evt.keyCode;
            if ((charCode > 31 && (charCode < 48 || charCode > 57)) && charCode !== 46 ) {
                evt.preventDefault();
            } else {
                return true;
            }
        }

        return {
            paramHolder,
            profileParamHolder,
            userStateListProvider,
            userStateFilterClicked,
            loadStateList,
            phoneNumber,
            isNumber
        }


    },
})
</script>
