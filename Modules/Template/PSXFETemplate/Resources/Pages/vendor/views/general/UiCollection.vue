<template>
    <Head :title="$t('ui_collection__select_location')"/>
    <ps-model-view ref='psmodal' />

    <ps-modal ref="psMapModal" :isClickOut="false" >
        <template #title>
            <ps-label-title> {{ $t('ui_collection__select_location') }} </ps-label-title>
        </template>
        <template #body>
            <GoogleMap id="map" ref="mapRef" :api-key="map.key"
                :center="map.center" :zoom="map.zoom"  style="width: 100%; height: 500px">

                <!-- For circle -->
                <Circle id="circle" :options="circleCenter" ref="cirRef" />
            </GoogleMap>
        </template>
        <template #footer>
            <div class="flex justify-end">
                <ps-button @click="psMapModal.toggle(false)"> {{ $t('ui_collection__ok') }} </ps-button>
            </div>
        </template>
    </ps-modal>

    <ps-content-container>
        <template #content>
            <div class="flex flex-col mt-36">

                <!-- White/Dark Mode -->
                <!-- <ps-label-title class="mt-4"> {{ $t('ui_collection__white_dark') }} </ps-label-title> -->


                <ps-line />
                <!-- Text -->
                <ps-label-title class="mt-4">{{ $t('ui_collection__label_ui') }} </ps-label-title>
                <ps-label-header-1>{{ $t('ui_collection__label_h1') }}</ps-label-header-1>
                <ps-label-header-2>{{ $t('ui_collection__label_h2') }}</ps-label-header-2>
                <ps-label-header-3>{{ $t('ui_collection__label_h3') }}</ps-label-header-3>
                <ps-label-header-4>{{ $t('ui_collection__label_h4') }}</ps-label-header-4>
                <ps-label-header-5>{{ $t('ui_collection__label_h5') }}</ps-label-header-5>
                <ps-label-header-6>{{ $t('ui_collection__label_h6') }} </ps-label-header-6>
                <ps-label-title>{{ $t('ui_collection__label_title1') }}</ps-label-title>
                <ps-label-title-2>{{ $t('ui_collection__label_title2') }}</ps-label-title-2>
                <ps-label-title-3>{{ $t('ui_collection__label_title3') }}</ps-label-title-3>
                <ps-label>{{ $t('ui_collection__body') }}</ps-label>
                <ps-label-caption>{{ $t('ui_collection___label_caption') }}</ps-label-caption>
                <ps-label-caption-2>{{ $t('ui_collection__label_caption2') }}</ps-label-caption-2>
                <ps-line />

                <!-- Input -->
                <ps-label-title class="mt-4 "> {{ $t('ui_collection__input_ui') }} </ps-label-title>

                <ps-input class="mb-2"   v-bind:placeholder="$t('ui_collection__email')"></ps-input>

                <ps-label class=""> Disabled </ps-label>
                <ps-input class="mt-1" :disabled="true"  v-bind:placeholder="$t('ui_collection__email')"></ps-input>

                <ps-label class=""> Error </ps-label>
                <ps-input class="mt-1" ref="error_input"  v-bind:placeholder="$t('ui_collection__email')"></ps-input>
                <!-- <ps-input class="mt-2" theme="input-error" type="email" v-bind:placeholder="$t('ui_collection__email')"></ps-input>
                <ps-input class="mt-2" theme="input-success" type="email" v-bind:placeholder="$t('ui_collection__email')"></ps-input>
                <ps-input class="mt-2" theme="input-second" type="email" v-bind:placeholder="$t('ui_collection__email')"></ps-input> -->
                <!-- <ps-input class="mt-2" type="password" v-bind:placeholder="$t('ui_collection__password')"></ps-input> -->

                <ps-input-with-right-icon class="mt-2"  v-bind:placeholder="$t('ui_collection__what_are_you_looking_for')" >
                    <template #icon>
                        <ps-icon name="search" class='cursor-pointer'/>
                    </template>
                </ps-input-with-right-icon>

                <!-- <ps-input-with-right-icon class="rounded-full flex-1 mt-2"  v-bind:placeholder="$t('ui_collection__what_are_you_looking_for')" >
                    <template #icon>
                        <ps-icon name="search" class='cursor-pointer'/>
                    </template>
                </ps-input-with-right-icon> -->

                <ps-input-with-left-icon class=" mt-2 mb-4"  v-bind:placeholder="$t('ui_collection__what_are_you_looking_for')" >
                    <template #icon >
                        <ps-icon name="search" class='cursor-pointer'/>
                    </template>
                </ps-input-with-left-icon>

                <!-- <ps-input-with-left-icon class="rounded-full flex-1 mt-2" v-bind:placeholder="$t('ui_collection__what_are_you_looking_for')" >
                    <template #icon >
                        <ps-icon name="search" class='cursor-pointer'/>
                    </template>
                </ps-input-with-left-icon> -->

                <ps-input-group class="w-80 h-9 mb-4"
                        value=""
                        type="text"
                        placeholder="Placeholder ..."
                         line="hidden"
                        background="border border-feSecondary-200 rounded"
                    >

                    <template #leftButton>
                        <ps-dropdown >
                            <template #select>
                                <ps-dropdown-select border="border border-e-feSecondary-200" rounded="rounded-l" :selectedValue=" 'Label ' + selectedIndex" w="w-26 h-9"  />
                            </template>
                            <template #filter>
                                <ps-dropdown-search placeholder="Search One" />
                            </template>
                            <template #list>
                                <div
                                    class="rounded-md bg-feAchromatic-50 dark:bg-feSecondary-800 shadow-xs w-32"
                                    role="menu"
                                    aria-orientation="vertical"
                                    aria-labelledby="options-menu"
                                >
                                    <div class="pt-2">
                                        <div
                                            v-for="i in 5"
                                            :key="i"
                                            class="flex py-4 px-2 hover:bg-fePrimary-100 cursor-pointer items-center"
                                            @click="onItemClick(i)"
                                        >
                                            <span class="ms-2" :class="i == selectedIndex ? 'text-fePrimary-500' : 'text-fePrimary-500'">
                                                Label {{ i }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="border-t border-feAchromatic-100"></div>
                                </div>
                            </template>
                        </ps-dropdown>
                    </template>




                </ps-input-group>

            <ps-textarea class="mb-4" rows="4" placeholder="Enter Text"></ps-textarea>

                 <ps-label class=""> Disabled </ps-label>
                <ps-textarea :disabled="true" class="mt-1 mb-4" rows="4" placeholder="Enter Text"></ps-textarea>

                <ps-label class=""> Error </ps-label>
                <ps-textarea class="mt-1 mb-4"  ref="error_textarea"  rows="4" placeholder="Enter Text"></ps-textarea>

                <ps-line />

                <!-- Button -->
                <ps-label-title class="mt-4"> {{ $t('ui_collection__button') }} </ps-label-title>

                <div class="flex flex-row  rtl:space-x-reverse space-x-4 mb-4">
                    <ps-button class="mt-2 w-"> {{ $t('ui_collection__primary_button') }}  </ps-button>
                    <ps-button class="mt-2 " :disabled="true"> {{ $t('ui_collection__disabled_button') }}  </ps-button>
                </div>

                <ps-label-title class="mt-4"> Secondary Buttons </ps-label-title>

                <div class="flex flex-row  rtl:space-x-reverse space-x-4 mb-4">
                    <ps-feSecondary-button class="mt-2"> {{ $t('ui_collection__primary_button') }}  </ps-feSecondary-button>
                    <ps-feSecondary-button class="mt-2 " :disabled="true"> {{ $t('ui_collection__disabled_button') }}  </ps-feSecondary-button>
                </div>

                <ps-label-title class="mt-4"> Text Buttons </ps-label-title>
                <div class="flex flex-row  rtl:space-x-reverse space-x-4 mb-4">
                    <ps-text-button class="mt-2"> {{ $t('ui_collection__primary_button') }}  </ps-text-button>
                    <ps-text-button class="mt-2 " :disabled="true"> {{ $t('ui_collection__disabled_button') }}  </ps-text-button>
                </div>

                <ps-line />

                <!-- CheckBox -->
                <ps-label-title class="mt-4"> {{ $t('ui_collection__checkbox_fixed') }}  </ps-label-title>
                <div class="flex items-center mt-4">
                    <ps-checkbox-value v-model:value="checkedFruits.apple" v-bind:title="$t('ui_collection__apple')" class="me-4" />
                    <ps-checkbox-value v-model:value="checkedFruits.ornage" v-bind:title="$t('ui_collection__orange')" class="me-4" />
                    <ps-checkbox-value v-model:value="checkedFruits.grape" v-bind:title="$t('ui_collection__grape')" class="me-4" />
                </div>

                <ps-label class="mt-4">{{checkedFruits}}</ps-label>

                <ps-label-title class="mt-4 mb-4"> {{ $t('ui_collection__checkbox_dynamic') }} </ps-label-title>

                <ps-checkbox v-for=" selectData  in checkedDataList"
                    :key="selectData.id"
                    :value="selectData"
                    v-model:selectedValue="checkedSelectedList"
                    :title="selectData.name"
                    class="focus:bg-fePrimary-500 dark:bg-feAccent-500" > {{selectData.name}} </ps-checkbox>

                <ps-label class="mt-4">{{checkedSelectedList}}</ps-label>

                <ps-line />

                <!-- Radio -->
                <ps-label-title class="mt-8">  {{ $t('ui_collection__radio_fixed') }} </ps-label-title>
                <div class="flex items-center mt-4" id="r1">
                    <ps-radio-value v-model:value="radioFruits" v-bind:title="$t('ui_collection__apple')" class="me-4" />
                    <ps-radio-value v-model:value="radioFruits" v-bind:title="$t('ui_collection__orange')" class="me-4" />
                    <ps-radio-value v-model:value="radioFruits" v-bind:title="$t('ui_collection__grape')" class="me-4" />
                    <ps-radio-value v-model:value="radioFruits" v-bind:title="$t('ui_collection__mango')" class="me-4" />
                </div>
                <ps-label class="mt-4">{{radioFruits}}</ps-label>

                <ps-label-title class="mt-4"> {{ $t('ui_collection__radio_dynamic') }} </ps-label-title>
                <div class="flex items-center mt-4" id="r1">
                    <ps-radio v-for=" selectData  in radioDataList"
                    :key="selectData.id"
                    :value="selectData"
                    v-model:selectedValue="radioSelectedList"
                    :title="selectData.name"
                    class="focus:bg-fePrimary-500 dark:bg-feAccent-500 me-4"></ps-radio>
                </div>
                <ps-label class="mt-4">{{radioSelectedList}}</ps-label>

                <ps-line />

                <!-- Select -->
                <!-- <ps-label-title class="mt-8"> {{ $t('ui_collection__select_dynamic') }} </ps-label-title> -->

                <!-- <ps-select v-model:value="selectedValue" class="mt-4" :dataList="selectDataList" /> -->

                <ps-label class="mt-4">{{selectedValue}}</ps-label>
                <div class="flex justify-between mb-4">
                    <!-- <p> {{ $t('ui_collection__test') }} </p> -->

                    <ps-dropdown align="right"  >
                        <template #select>
                            <ps-dropdown-select class="w-60" :selectedValue="'Transaction History ' + selectedIndex " />
                        </template>
                        <template #filter>
                            <ps-dropdown-search placeholder="Search One" />
                        </template>
                        <template #list>
                            <div
                                class="rounded-md bg-feAchromatic-50 dark:bg-feSecondary-800 shadow-xs"
                                role="menu"
                                aria-orientation="vertical"
                                aria-labelledby="options-menu">
                                <div class="pt-2">
                                    <div v-for="i in 5" :key="i" class="flex py-4 px-2 hover:bg-fePrimary-50 dark:hover:bg-feSecondary-500 cursor-pointer items-center"  @click="onItemClick(i)" >

                                        <img  width="300px" height="200px" alt="Placeholder"
                                            src="https://s.svgbox.net/hero-outline.svg?ic=currency-rupee"
                                            class="h-6 w-6"
                                        />
                                        <span class="ms-2 text-feSecondary-800 dark:text-feSecondary-200" :class="i==selectedIndex ? 'font-semibold' : ''"  > {{ $t("ui_collection__transaction_history") }} {{i}} </span>

                                    </div>
                                </div>
                            </div>
                        </template>
                    </ps-dropdown>

                </div>

                <div class="flex justify-between mb-4">
                    <!-- <p> {{ $t('ui_collection__test') }} </p> -->

                    <ps-dropdown align="right" :disabled="true" >
                        <template #select>
                            <ps-dropdown-select class="w-60" :disabled="true" :selectedValue="'Transaction History ' + selectedIndex " />
                        </template>
                        <template #list>
                            <div
                                class="rounded-md bg-feAchromatic-50 shadow-xs"
                                role="menu"
                                aria-orientation="vertical"
                                aria-labelledby="options-menu">
                                <div class="pt-2">
                                    <div v-for="i in 5" :key="i" class="flex py-4 px-2 hover:bg-fePrimary-100 cursor-pointer items-center"  @click="onItemClick(i)" >

                                        <img  width="300px" height="200px" alt="Placeholder"
                                            src="https://s.svgbox.net/hero-outline.svg?ic=currency-rupee"
                                            class="h-6 w-6"
                                        />
                                        <span class="ms-2" :class="i==selectedIndex ? 'text-fePrimary-500' : 'text-fePrimary-500'"  > {{ $t("ui_collection__transaction_history") }} {{i}} </span>

                                    </div>
                                </div>
                            </div>
                        </template>
                    </ps-dropdown>

                </div>
                <ps-line />

                <!-- Icons -->
                <ps-label-title class="mt-8"> {{ $t("ui_collection__icons") }} </ps-label-title>

                <div class="flex mt-8" >

                    <ps-icon name="downArrow" class="text-orange-500" />

                    <ps-icon name="heart" class='text-orange-500 ms-4'/>

                    <ps-icon name="plusCircle" class='ms-4' />

                    <ps-icon name="dashboard" class='ms-4'  />

                    <ps-icon name="apps" class='ms-4' />

                    <ps-icon name="rhombusMedium" class='ms-4' />

                    <ps-icon name="hexagonMultiple" class='ms-4' />

                    <ps-icon name="menu" class='ms-4' />

                    <ps-icon name="printer" class='ms-4' />

                    <ps-icon name="plus" class='ms-4' />

                    <ps-icon name="minus" class='ms-4' />

                    <ps-icon name="cogOutline" class='ms-4' />

                    <ps-icon name="search" class='ms-4' />


                </div>

                <!-- <ps-label class='mt-4'> {{ $t("ui_collection__icon_60_60") }} </ps-label>
                <ps-icon name="dashboard" class='mt-4' w='60' h='60'  />
                <ps-icon name="cogOutline" class='mt-4 text-purple-500' w='60' h='60'  /> -->

                <ps-line />
                 <ps-label-header-7 class="my-4"> BreadCrumb </ps-label-header-7>

                 <ps-breadcrumb-2 :items="breadcrumb" class="my-6" />

                 <ps-line />

                <!-- Images -->
                <!-- <ps-label-title class="mt-8"> {{ $t("ui_collection__images") }} </ps-label-title> -->

                <ps-label-header-7 class="my-4"> Action Modals </ps-label-header-7>
                <div class="flex flex-wrap  rtl:space-x-reverse space-x-2 mt-4" >
                    <ps-button @click="confirmClicked">Confirm</ps-button>
                    <ps-button @click="dangerClicked" colors="bg-fePrimary-500 text-feAchromatic-50"  hover="hover:outline-none hover:ring hover:ring-fePrimary-500-100" focus="focus:outline-none focus:ring focus:ring-fePrimary-500-300">Danger</ps-button>
                    <ps-button @click="warningClicked"  colors="bg-feWarning-500 text-feAchromatic-50">Warning</ps-button>
                    <ps-button @click="actionClicked" >Only Confirm</ps-button>
                </div>

                <ps-label-header-7 class="my-4"> Message Modals </ps-label-header-7>
                <div class="flex flex-wrap  rtl:space-x-reverse space-x-2 mt-4" >
                    <ps-button @click="successClicked" colors="bg-feSuccess-500 text-feAchromatic-50"  hover="hover:outline-none hover:ring hover:ring-feSuccess-100" focus="focus:outline-none focus:ring focus:ring-feSuccess-300">Success</ps-button>
                    <ps-button @click="errorClicked" colors="bg-fePrimary-500 text-feAchromatic-50"  hover="hover:outline-none hover:ring hover:ring-fePrimary-500-100" focus="focus:outline-none focus:ring focus:ring-fePrimary-500-300">Error</ps-button>
                    <ps-button @click="messageClicked">Message</ps-button>
                    <ps-button @click="loadingClicked">Loading</ps-button>
                </div>
                <!-- End Modals -->
                <!-- <ps-line /> -->

                <ps-line />

                <!-- Full Page Loading -->
                <ps-label-title class="mt-8"> {{ $t("ui_collection__full_page_loading") }} </ps-label-title>

                <ps-button class="mt-2" @click="loadMore">  {{ $t("ui_collection__full_page_loading") }} </ps-button>

                <!-- <ps-line /> -->

                <!-- Countdown -->
                <!-- <ps-label-title class="mt-8 "> {{ $t("ui_collection__countdown") }} </ps-label-title>
                {{countDown}} -->

                <ps-line />



                <!-- Speak -->
                <ps-label-title class="mt-8"> {{ $t("ui_collection__speak") }} </ps-label-title>

                <ps-button class="mt-2" @click="speak">  {{ $t("ui_collection__speak") }} </ps-button>

                <ps-line />


                <!-- Modal -->
                <!-- <ps-label-title class="mt-8">{{ $t("ui_collection__modal") }}  </ps-label-title>

                <ps-button class="mt-2" @click="psmodal.openModal()">  {{ $t("ui_collection__open") }} </ps-button>
                <ps-button class="mt-2" @click="psMapModal.toggle(true)">  {{ $t("ui_collection__open_map_modal") }} </ps-button> -->


                <ps-line />

                <!-- Skeletor -->
                <ps-label-title class="mt-8"> {{ $t("ui_collection__skeletor") }} </ps-label-title>

                <div class='w-96 my-8' >
                    <div class='flex items-start'>
                        <div>
                            <Skeletor circle size="48" />
                        </div>
                        <div class="w-52 rounded-md ms-4 mt-1">
                            <Skeletor height="15" class='rounded-md' />
                            <Skeletor height="20" class='rounded-md mt-2' />
                        </div>
                    </div>

                </div>

                <ps-line />

                <ps-no-result @onClick="loadingClicked" class="my-6" />

                <!-- gallery vertical swiper -->
                <div class="w-full">
                    <GalleryVerticalSwiper/>
                </div>


            </div>
            <ps-confirm-dialog ref="ps_confirm_dialog" />
            <ps-danger-dialog ref="ps_danger_dialog" />
            <ps-action-dialog ref="ps_action_dialog" />
            <ps-warning-dialog ref="ps_warning_dialog" />

            <ps-success-dialog ref="ps_success_dialog" />
            <ps-error-dialog ref="ps_error_dialog" />
            <ps-message-dialog ref="ps_message_dialog" />
            <ps-loading-dialog ref="ps_loading_dialog" />
        </template>
    </ps-content-container>

</template>

<script lang="ts">
import { Head } from '@inertiajs/vue3';
import { defineComponent, onUnmounted, reactive, ref} from 'vue';
import PsCheckbox from '@template1/vendor/components/core/checkbox/PsCheckbox.vue';
import PsRadio from '@template1/vendor/components/core/radio/PsRadio.vue';
import PsSelect from '@template1/vendor/components/core/select/PsSelect.vue';
import PsLabelTitle from '@template1/vendor/components/core/label/PsLabelTitle.vue';
import PsLabelTitle2 from '@template1/vendor/components/core/label/PsLabelTitle2.vue';
import PsLabelTitle3 from '@template1/vendor/components/core/label/PsLabelTitle3.vue';
import PsLabel from '@template1/vendor/components/core/label/PsLabel.vue';
import PsCheckboxValue from '@template1/vendor/components/core/checkbox/PsCheckboxValue.vue';
import PsRadioValue from '@template1/vendor/components/core/radio/PsRadioValue.vue';
import PsLabelHeader6 from '@template1/vendor/components/core/label/PsLabelHeader6.vue';
import PsLabelHeader5 from '@template1/vendor/components/core/label/PsLabelHeader5.vue';
import PsLabelHeader4 from '@template1/vendor/components/core/label/PsLabelHeader4.vue';
import PsLabelHeader3 from '@template1/vendor/components/core/label/PsLabelHeader3.vue';
import PsLabelHeader2 from '@template1/vendor/components/core/label/PsLabelHeader2.vue';
import PsLabelHeader1 from '@template1/vendor/components/core/label/PsLabelHeader1.vue';
import PsLabelCaption from '@template1/vendor/components/core/label/PsLabelCaption.vue';
import PsLabelCaption2 from '@template1/vendor/components/core/label/PsLabelCaption2.vue';
import PsLine from '@template1/vendor/components/core/line/PsLine.vue';
import PsInput from '@template1/vendor/components/core/input/PsInput.vue';
import PsTextarea from '@template1/vendor/components/core/textarea/PsTextarea.vue';
import PsInputWithRightIcon from '@template1/vendor/components/core/input/PsInputWithRightIcon.vue';
import PsInputWithLeftIcon from '@template1/vendor/components/core/input/PsInputWithLeftIcon.vue';
import PsDropdown from '@template1/vendor/components/core/dropdown/PsDropdown.vue';
import PsDropdownSelect from '@template1/vendor/components/core/dropdown/PsDropdownSelect.vue';
import PsDropdownSearch from '@template1/vendor/components/core/dropdown/PsDropdownSearch.vue';
import PsIcon from '@template1/vendor/components/core/icons/PsIcon.vue';
import PsButton from '@template1/vendor/components/core/buttons/PsButton.vue';
import PsSecondaryButton from '@template1/vendor/components/core/buttons/PsSecondaryButton.vue';
import PsTextButton from '@template1/vendor/components/core/buttons/PsTextButton.vue';

import PsContentContainer from '@template1/vendor/components/layouts/container/PsContentContainer.vue';
// import { useLoading } from 'vue3-loading-overlay';
// import 'vue3-loading-overlay/dist/vue3-loading-overlay.css';
// import $ from "cash-dom";
import { Skeletor } from "vue-skeletor";
import "vue-skeletor/dist/vue-skeletor.css";
import PsModal from '@template1/vendor/components/core/modals/PsModal.vue';
import PsModelView from '@template1/vendor/views/general/PsModelView.vue';
import { GoogleMap , Circle  } from 'vue3-google-map'; //
import PsUtils from '@templateCore/utils/PsUtils';
import PsFrontendLayout from '@template1/vendor/components/layouts/container/PsFrontendLayout.vue';
import PsIconToggle from '@template1/vendor/components/core/toggle/PsIconToggle.vue';
import PsConfirmDialog from "@template1/vendor/components/core/dialogs/PsConfirmDialog.vue";
import PsDangerDialog from "@template1/vendor/components/core/dialogs/PsDangerDialog.vue";
import PsLoadingDialog from "@template1/vendor/components/core/dialogs/PsLoadingDialog.vue";
import PsActionDialog from "@template1/vendor/components/core/dialogs/PsActionDialog.vue";
import PsWarningDialog from "@template1/vendor/components/core/dialogs/PsWarningDialog.vue";
import PsSuccessDialog from "@template1/vendor/components/core/dialogs/PsSuccessDialog.vue";
import PsErrorDialog from "@template1/vendor/components/core/dialogs/PsErrorDialog.vue";
import PsMessageDialog from "@template1/vendor/components/core/dialogs/PsMessageDialog.vue";
import PsBreadcrumb2 from "@template1/vendor/components/core/breadcrumbs/PsBreadcrumb2.vue";
import { trans } from 'laravel-vue-i18n';
import PsInputGroup from "@template1/vendor/components/core/input/PsInputGroup.vue";
import PsNoResult from "@template1/vendor/components/modules/list/PsNoResult.vue";
import GalleryVerticalSwiper from "@template1/vendor/components/modules/gallery/GalleryVerticalSwiper.vue";

export default defineComponent({
    name : "UiCollection",
    components: {
        PsInputGroup,
        PsTextarea,
        PsCheckbox,
        PsRadio,
        PsSelect,
        PsLabel,
        PsLabelTitle,
        PsLabelTitle2,
        PsLabelTitle3,
        PsCheckboxValue,
        PsRadioValue,
        PsLabelHeader6,
        PsLabelHeader5,
        PsLabelHeader4,
        PsLabelHeader3,
        PsLabelHeader2,
        PsLabelHeader1,
        PsLabelCaption,
        PsLabelCaption2,
        PsLine,
        PsInput,
        PsInputWithRightIcon,
        PsInputWithLeftIcon,
        PsIcon,
        PsButton,
        PsTextButton,
        PsSecondaryButton,
        PsContentContainer,
        Skeletor,
        PsDropdown,
        PsDropdownSelect,
        PsDropdownSearch,
        PsModal,
        GoogleMap,
        PsModelView,
        PsIconToggle,
        PsConfirmDialog,
        PsDangerDialog,
        PsLoadingDialog,
        PsActionDialog,
        PsWarningDialog,
        PsSuccessDialog,
        PsErrorDialog,
        PsMessageDialog,
        PsBreadcrumb2,
        PsNoResult,
        GalleryVerticalSwiper,
        // Marker,

        Circle,
        Head
    },
    layout: PsFrontendLayout,
    setup() {
        const ps_confirm_dialog = ref();
        const ps_danger_dialog = ref();
        const ps_loading_dialog = ref();
        const ps_action_dialog = ref();
        const ps_warning_dialog = ref();
        const ps_success_dialog = ref();
        const ps_error_dialog = ref();
        const ps_message_dialog = ref();
        const error_input = ref();
        const error_textarea= ref();

         setTimeout(() => {
                   error_input.value.isError = true;
                   error_textarea.value.isError = true;
                }, 5000);

        const isMenuOpen = ref(false);

        const checkedFruits = reactive({
            apple : true,
            ornage : true,
            grape : false
        });

        const checkedDataList = reactive([
            {
                id:1, name:"Apple"
            },
            {
                id:2, name:"Orange"
            },
            {
                id:3, name:"Grape"
            }
        ]);

        const checkedSelectedList = reactive([{id:1, name:"Apple"}]);

        const radioDataList = reactive([
            {
                id:1, name:"Apple"
            },
            {
                id:2, name:"Orange"
            },
            {
                id:3, name:"Grape"
            }
        ]);

        const radioSelectedList = reactive({id:2, name:"Apple"});

        // For Radio
        const radioFruits = ref("Apple");

        // For Select
        const selectDataList = reactive([
            {
                id:1, name:"$1,000"
            },
            {
                id:2, name:"$2,000"
            },
            {
                id:3, name:"$3,000"
            },
            {
                id:4, name:"$4,000"
            },
            {
                id:5, name:"$5,000"
            },
        ]);
        const selectedValue = ref("5");

        // Full Page Loading
        const fullPage = ref(true);
        const formContainer = ref(null);

         function onCancel() {

        }


        function loadMore() {
            // const loader = useLoading();
            // loader.show({
            //     // Optional parameters
            //     container: fullPage.value ? null : formContainer.value,
            //     canCancel: true,
            //     onCancel: onCancel,
            // });

            //        // simulate AJAX
            // setTimeout(() => {
            //     loader.hide()
            // },5000)
        }

        const endDate = new Date();
        endDate.setTime(endDate.getTime() + 100000);
        const color = ref('font-bold');
        function onTick() {

            if(color.value == 'font-bold') {
                color.value = 'font-normal';
            }else {
                color.value = 'font-bold'
            }

        }
        const countRef= ref(HTMLDivElement);

        // Speak
        const greetingSpeech = new window.SpeechSynthesisUtterance();
        const voices = window.speechSynthesis.getVoices();
        greetingSpeech.voice = voices[17];

        function speak() {
            greetingSpeech.text = "Hello! Welcome from SmartTeam. Your bidding is going to finish. 5, 4, 3, 2, 1. bidding completed. Thanks for using our service.";
            window.speechSynthesis.speak(greetingSpeech);
        }

        const psmodal = ref();
        const psMapModal = ref();

        //MAP
        const mcenter = ref({
            position : {
            lat: 38.735086,
            lng: -9.141247
            },
            draggable: false
        });

        function updateCoordinates(location) {
            PsUtils.log(location.latLng.lat());
            PsUtils.log(location.latLng.lng());
        }


        // Radius * 2 = meters
        const circleCenter = ({
            editable : true,
            center: mcenter.value.position,
            radius: 300,
            strokeColor: '#FF0000',
            strokeOpacity: 0.8,
            strokeWeight: 2,

            draggable: false,
            crossOnDrag: false
        })

        const map = {
            key: '000',
            center: mcenter.value.position,
            zoom: 15
        };

        const countDown = ref('');
        const toHHMMSS = (seconds) => {
            seconds = Number(seconds);
            const d = Math.floor(seconds / (3600*24));
            const h = Math.floor(seconds % (3600*24) / 3600);
            const m = Math.floor(seconds % 3600 / 60);
            const s = Math.floor(seconds % 60);

            const dDisplay = d > 0 ? d + (d == 1 ? " day, " : " days, ") : "";
            const hDisplay = h > 0 ? h + (h == 1 ? " hour, " : " hours, ") : "";
            const mDisplay = m > 0 ? m + (m == 1 ? " minute, " : " minutes, ") : "";
            const sDisplay = s > 0 ? s + (s == 1 ? " second" : " seconds") : "";
            return dDisplay + hDisplay + mDisplay + sDisplay;
        }
        // Interval
        const timer = setInterval(() => {
            const now = new Date();
            const end = new Date(2023, 1, 22, 19, 40, 1 );
            const distance = end.getTime() - now.getTime();

            if(distance < 0) {
                clearInterval(timer);
                return;
            }
            countDown.value = toHHMMSS(distance/1000);

        }, 1000);

        onUnmounted(() => {
            clearInterval(timer);
        })

        // Select
        const selectedIndex = ref(3);
        function onItemClick(i) {
            selectedIndex.value = i;
        }

        function confirmClicked(){
            ps_confirm_dialog.value.openModal('Confirmation modal','Non-disclosure agreement branding beta equity churn rate channels long tail paradigm shift validation strategy value proposition.Send message','Cofirm','cancel',
            ()=>{
                console.log('ok')
            },
            ()=>{
                console.log('cancel')
            });
        }
        function dangerClicked(){
            ps_danger_dialog.value.openModal('Danger modal','Non-disclosure agreement branding beta equity churn rate channels long tail paradigm shift validation strategy value proposition.','Confirm','Cancel',
            ()=>{
                console.log('ok')
            },
            ()=>{
                console.log('cancel')
            });
        }
        function loadingClicked(){
            ps_loading_dialog.value.openModal();

            setTimeout(()=> ps_loading_dialog.value.closeModal(),3000);

        }
        function actionClicked(){
            ps_action_dialog.value.openModal('Title','message text','Confirm',
            ()=>{
                console.log('ok')
            });
        }function successClicked(){
            ps_success_dialog.value.openModal('Awesome!','You have complete task successfully.','Complete Another task',
            ()=>{
                console.log('ok')
            });
        }
        function errorClicked(){
            ps_error_dialog.value.openModal('Something Went Wrong','We’re having technical issues at the moment. Please have some patients.','Retry',
            ()=>{
                console.log('ok')
            });
        }
        function messageClicked(){
            ps_message_dialog.value.openModal('Welcome to the team','You’ve completed the task successfully','Start Creating','View Guidelines',
            ()=>{
                console.log('ok')
            },
            ()=>{
                console.log('cancel')
            });
        }

        function warningClicked(){
            ps_warning_dialog.value.openModal('Warning modal','Non-disclosure agreement branding beta equity churn rate channels long tail paradigm shift validation strategy value proposition.','Button','Cancel',
            ()=>{
                console.log('ok')
            },
            ()=>{
                console.log('cancel')
            });
        }


        return {
            checkedFruits,
            checkedDataList,
            checkedSelectedList,
            radioFruits,
            radioDataList,
            radioSelectedList,
            selectedValue,
            selectDataList,
            loadMore,
            endDate,
            onTick,
            countRef,
            color,
            speak,
            psmodal,
            psMapModal,
            mcenter,
            circleCenter,
            map,
            countDown,
            isMenuOpen,
            selectedIndex,
            onItemClick,
            updateCoordinates,

            ps_confirm_dialog,
            ps_danger_dialog,
            ps_loading_dialog,
            ps_action_dialog,
            ps_warning_dialog,
            ps_success_dialog,
            ps_error_dialog,
            ps_message_dialog,

            confirmClicked,
            actionClicked,
            dangerClicked,
            loadingClicked,
            warningClicked,
            successClicked,
            errorClicked,
            messageClicked,

            error_input,
            error_textarea
        };
    },
    computed: {
        breadcrumb() {
            return [
                {
                    label: trans('item_list__home'),
                    url: route('dashboard')
                },
                {
                    label: trans('sub_category_list__category'),
                    url: route('fe_category.index'),
                },
                {
                    label: 'Subcategory',
                    color: "text-fePrimary-500"
                }
            ]
        }
    },
});
</script>

