<template>
    <Head :title="$t('category_list__title')" />
    <ps-content-container>
        <template #content>
            <div class="sm:mt-32 lg:mt-36 mt-28">
                <div class="flex flex-col sm:flex-row">
                    <div class="flex flex-row sm:mt-0 mt-6">
                        <ps-breadcrumb-2 :items="breadcrumb" /></div>
                </div>

                <div class="flex flex-col md:flex-row mt-10">
                  <div class="flex-1 flex-row dark:bg-feAchromatic-800 mb-6">
                    <vendor-checkout-group-title :title="$t('delivery_infomation')"/>

                    <!------------ For Shipping Start ------------>
                    <div class="px-5 w-full">

                    <ps-label class="font-bold text-xl my-5" textColor="text-feSecondary-800 dark:text-feSecondary-300">
                        {{$t('shipping_address')}} </ps-label>

                    <div class="flex flex-col md:flex-row items-center">
                        <!-- Shipping First Name Start-->
                        <div class="flex-grow w-full mb-2 md:mb-3 md:mr-4">
                          <ps-label class="text-base"> {{ $t('first_name') }}
                            <span class="text-feError-800 font-medium ms-1">*</span>
                          </ps-label>
                          <ps-input type="text" class="w-full rounded" :placeholder="$t('first_name')"
                            v-model:value="vendorCheckoutStore.paramHolder.shippingFirstName" />
                          <ps-label class="lg:mt-2 mt-1  w-full text-xs" textColor="text-feError-500"
                            v-if="validation.shippingFirstNameStatus"> Shipping First Name is required </ps-label>
                        </div>
                        <!-- Shipping First Name End-->

                        <!-- Shipping Last Name Start-->
                        <div class="flex-grow w-full mb-2 md:mb-3">
                          <ps-label class="text-base">{{ $t('last_name') }}
                            <span class="text-feError-800 font-medium ms-1">*</span>
                          </ps-label>
                          <ps-input type="text" class="w-full rounded" :placeholder="$t('last_name')"
                            v-model:value="vendorCheckoutStore.paramHolder.shippingLastName" />
                          <ps-label class="lg:mt-2 mt-1  w-full text-xs" textColor="text-feError-500"
                            v-if="validation.shippingLastNameStatus"> Shipping Last Name is required </ps-label>
                        </div>
                        <!-- Shipping Last Name End-->
                    </div>

                    <div class="flex flex-col md:flex-row items-center">
                        <!-- Shipping Email Start-->
                        <div class="flex-grow w-full mb-2 md:mb-3 md:mr-4">
                            <ps-label class="text-base">{{ $t('credit_card_view__email') }}
                                <span class="text-feError-800 font-medium ms-1">*</span>
                            </ps-label>
                            <ps-input type="text" class="w-full rounded" :placeholder="$t('credit_card_view__email')"
                                v-model:value="vendorCheckoutStore.paramHolder.shippingEmail" />
                            <ps-label class="lg:mt-2 mt-1  w-full text-xs" textColor="text-feError-500"
                                v-if="validation.shippingEmailStatus">  Shipping Email is required  </ps-label>
                        </div>
                        <!-- Shipping Email End-->

                        <!-- Phone Number Start-->
                        <div class="flex-grow w-full mb-2 md:mb-3">
                            <ps-label class="text-base">{{ $t('phone_number') }}<span class="text-feError-800 font-medium ms-1">*</span></ps-label>
                            <div class="flex w-full">
                                <ps-dropdown align="left" class='' @onClick="loadPhone">
                                    <template #filter>
                                        <div class="w-56">
                                            <ps-input-with-right-icon class="rounded-xl flex-1 "
                                                @keyup.enter="filterPhoneUpdate(phoneKeyword)" v-model:value="phoneKeyword"
                                                v-bind:placeholder="$t('phone_code_by_country')">
                                                <template #icon>
                                                    <ps-icon textColor="text-feSecondary-400 dark:text-feAchromatic-500"
                                                        name="search" class='cursor-pointer'
                                                        @click="filterPhoneUpdate(phoneKeyword)" />
                                                </template>
                                            </ps-input-with-right-icon>
                                        </div>
                                    </template>
                                    <template #select>
                                        <ps-dropdown-select :showCenter="true" :selectedValue="shippingPhoneCode.code" />
                                    </template>
                                    <template #list>
                                        <div class="rounded-md shadow-xs w-56 ">
                                            <div class="pt-2 z-30 ">
                                                <div v-if="phoneStore.phoneList.data == null">
                                                    <ps-label class='p-2 flex' @click="loadPhone">{{
                                                        $t("item_entry__loading")
                                                    }} </ps-label>
                                                </div>
                                                <div v-else>
                                                    <div v-for="selectData in phoneStore.phoneList.data"
                                                        :key="selectData.id"
                                                        class="w-56 flex py-4 px-2 hover:bg-fePrimary-50 dark:hover:bg-fePrimary-900 cursor-pointer items-center"
                                                        @click="shippingPhoneFilterClicked(selectData)">

                                                        <ps-label class="ms-2"
                                                            :class="selectData.country_code == shippingPhoneCode.value ? ' font-bold' : ''">
                                                            {{ selectData.country_code }} </ps-label>

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                    <template #loadmore>

                                        <div class="mb-2 w-56">

                                            <div v-if="phoneStore.phoneList.data != null && phoneStore.loading.value == true"
                                                class='mt-4 ms-4 flex'>
                                                <ps-label> {{ $t("item_entry__loading") }}</ps-label>
                                            </div>

                                            <ps-label class="flex mt-4 ms-4 mb-2 underline font-bold cursor-pointer"
                                                v-if="!phoneStore.isNoMoreRecord.value" @click="loadPhone"> {{
                                                    $t("item_entry__load_more") }} </ps-label>
                                        </div>
                                    </template>

                                </ps-dropdown>
                                <ps-input class="w-full dark:bg-transparent" ref="phone" type="text"
                                    v-model:value="shippingPhone" :placeholder="$t('phone_number')" />
                            </div>
                            <ps-label class="lg:mt-2 mt-1  w-full text-xs" textColor="text-feError-500"
                                v-if="validation.shippingPhoneNoStatus">  Shipping Phone Number is required  </ps-label>
                        </div>
                        <!-- Phone Number End-->
                    </div>

                    <!-- Address Start -->
                    <div class=" mb-2 md:mb-3">
                        <ps-label class="text-base">{{ $t('address') }} <span class="text-feError-800 font-medium ms-1">*</span></ps-label>
                        <ps-textarea rows="4" v-model:value="vendorCheckoutStore.paramHolder.shippingAddress"
                            :placeholder="$t('address')"/>
                        <ps-label class="lg:mt-2 mt-1  w-full text-xs" textColor="text-feError-500"
                            v-if="validation.shippingAddressStatus">  Shipping Address is required
                        </ps-label>
                    </div>
                    <!-- Address End -->

                    <div class="flex flex-col md:flex-row items-center">
                        <!-- Shipping Country Start-->
                        <div class="flex-grow w-full mb-2 md:mb-3 md:mr-4">
                            <ps-label class="text-base">{{ $t('country') }}
                                <span class="text-feError-800 font-medium ms-1">*</span>
                            </ps-label>
                            <ps-input type="text" class="w-full rounded" :placeholder="$t('country')"
                                v-model:value="vendorCheckoutStore.paramHolder.shippingCountry" />
                            <ps-label class="lg:mt-2 mt-1  w-full text-xs" textColor="text-feError-500"
                                v-if="validation.shippingCountryStatus"> Shipping Country is required  </ps-label>
                        </div>
                        <!-- Shipping Country End-->

                        <!-- Shipping State Start-->
                        <div class="flex-grow w-full mb-2 md:mb-3">
                            <ps-label class="text-base">{{ $t('state') }}
                                <span class="text-feError-800 font-medium ms-1">*</span>
                            </ps-label>
                            <ps-input type="text" class="w-full rounded" :placeholder="$t('state')"
                                v-model:value="vendorCheckoutStore.paramHolder.shippingState" />
                            <ps-label class="lg:mt-2 mt-1  w-full text-xs" textColor="text-feError-500"
                                v-if="validation.shippingStateStatus"> Shipping State is required  </ps-label>
                        </div>
                        <!-- Shipping State End-->
                    </div>

                    <div class="flex flex-col md:flex-row items-center">
                        <!-- Shipping City Start-->
                        <div class="flex-grow w-full mb-2 md:mb-3 md:mr-4">
                            <ps-label class="text-base">{{ $t('city') }}
                                <span class="text-feError-800 font-medium ms-1">*</span>
                            </ps-label>
                            <ps-input type="text" class="w-full rounded" :placeholder="$t('city')"
                                v-model:value="vendorCheckoutStore.paramHolder.shippingCity" />
                            <ps-label class="lg:mt-2 mt-1  w-full text-xs" textColor="text-feError-500"
                                v-if="validation.shippingCityStatus"> Shipping City is required  </ps-label>
                        </div>
                        <!-- Shipping City End-->

                        <!-- Shipping PostalCode Start-->
                        <div class="flex-grow w-full mb-2 md:mb-3">
                            <ps-label class="text-base">{{ $t('postal_code') }}
                                <span class="text-feError-800 font-medium ms-1">*</span>
                            </ps-label>
                            <ps-input type="text" class="w-full rounded" :placeholder="$t('postal_code')"
                                v-model:value="vendorCheckoutStore.paramHolder.shippingPostalCode" />
                            <ps-label class="lg:mt-2 mt-1  w-full text-xs" textColor="text-feError-500"
                                v-if="validation.shippingPostalCodeStatus"> Shipping Postal Code is required  </ps-label>
                        </div>
                        <!-- Shipping PostalCode End-->
                    </div>

                    <!-- Address save checkout Start-->
                    <div class="me-2 flex items-center">
                         <input
                            type="checkbox"
                            v-model="isShippingSaveChecked"
                            class="form-checkbox rounded border text-fePrimary-500"/>
                        <ps-label class="ms-2" textColor="text-feSecondary-800 dark:text-feSecondary-300">{{ $t('save_this_address_for_next_time') }} </ps-label>
                    </div>
                    <!-- Address save checkout End-->
                    </div>
                    <!------------ For Shipping End ------------>

                    <!------------ For billing Start ------------>
                    <div class="px-5 w-full">

                        <ps-label class="font-bold text-xl my-5" textColor="text-feSecondary-800 dark:text-feSecondary-300">
                            {{ $t('billing_address') }} </ps-label>

                        <div class="me-2 mb-5 flex items-center">
                            <input
                                type="checkbox"
                                v-model="isSameChecked"
                                class="form-checkbox rounded border text-fePrimary-500"/>
                            <ps-label class="ms-2" textColor="text-feSecondary-800 dark:text-feSecondary-300"> {{ $t('same_as_shipping_address') }} </ps-label>
                        </div>
                        <!-- Address save checkout End-->

                        <div v-if="!isSameChecked">
                            <div class="flex flex-col md:flex-row items-center">
                            <!-- billing First Name Start-->
                            <div class="flex-grow w-full mb-2 md:mb-3 md:mr-4">
                                <ps-label class="text-base">{{ $t('first_name') }}
                                    <span class="text-feError-800 font-medium ms-1">*</span>
                                </ps-label>
                                <ps-input type="text" class="w-full rounded" :placeholder="$t('first_name')"
                                    v-model:value="vendorCheckoutStore.paramHolder.billingFirstName" />
                                <ps-label class="lg:mt-2 mt-1  w-full text-xs" textColor="text-feError-500"
                                    v-if="validation.billingFirstNameStatus"> Billing First Name is required  </ps-label>
                            </div>
                            <!-- billing First Name End-->

                            <!-- billing Last Name Start-->
                            <div class="flex-grow w-full mb-2 md:mb-3">
                                <ps-label class="text-base"> {{ $t('last_name') }}
                                    <span class="text-feError-800 font-medium ms-1">*</span>
                                </ps-label>
                                <ps-input type="text" class="w-full rounded" :placeholder="$t('last_name')"
                                    v-model:value="vendorCheckoutStore.paramHolder.billingLastName" />
                                <ps-label class="lg:mt-2 mt-1  w-full text-xs" textColor="text-feError-500"
                                    v-if="validation.billingLastNameStatus"> Billing Last Name is required </ps-label>
                            </div>
                            <!-- billing Last Name End-->
                            </div>

                            <div class="flex flex-col md:flex-row items-center">

                            <!-- billing Email Start-->
                            <div class="flex-grow w-full mb-2 md:mb-3 md:mr-4">
                                <ps-label class="text-base">{{ $t('credit_card_view__email') }}
                                    <span class="text-feError-800 font-medium ms-1">*</span>
                                </ps-label>
                                <ps-input type="text" class="w-full rounded" :placeholder="$t('credit_card_view__email')"
                                    v-model:value="vendorCheckoutStore.paramHolder.billingEmail" />
                                <ps-label class="lg:mt-2 mt-1  w-full text-xs" textColor="text-feError-500"
                                    v-if="validation.billingEmailStatus"> Billing Email is required </ps-label>
                            </div>
                            <!-- billing Email End-->

                            <!-- Phone Number Start-->
                            <div class="flex-grow w-full mb-2 md:mb-3">
                                <ps-label class="text-base">{{ $t('phone_number') }}<span class="text-feError-800 font-medium ms-1">*</span></ps-label>
                                    <div class="flex w-full">

                                        <ps-dropdown align="left" class='' @onClick="loadPhone">
                                            <template #filter>
                                                <div class="w-56">
                                                    <ps-input-with-right-icon class="rounded-xl flex-1 "
                                                        @keyup.enter="filterPhoneUpdate(phoneKeyword)" v-model:value="phoneKeyword"
                                                        v-bind:placeholder="$t('phone_code_by_country')">
                                                        <template #icon>
                                                            <ps-icon textColor="text-feSecondary-400 dark:text-feAchromatic-500"
                                                                name="search" class='cursor-pointer'
                                                                @click="filterPhoneUpdate(phoneKeyword)" />
                                                        </template>
                                                    </ps-input-with-right-icon>
                                                </div>
                                            </template>
                                            <template #select>
                                                <ps-dropdown-select :showCenter="true" :selectedValue="billingPhoneCode.code" />
                                            </template>
                                            <template #list>
                                                <div class="rounded-md shadow-xs w-56 ">
                                                    <div class="pt-2 z-30 ">
                                                        <div v-if="phoneStore.phoneList.data == null">
                                                            <ps-label class='p-2 flex' @click="loadPhone">{{
                                                                $t("item_entry__loading")
                                                            }} </ps-label>
                                                        </div>
                                                        <div v-else>
                                                            <div v-for="selectData in phoneStore.phoneList.data"
                                                                :key="selectData.id"
                                                                class="w-56 flex py-4 px-2 hover:bg-fePrimary-50 dark:hover:bg-fePrimary-900 cursor-pointer items-center"
                                                                @click="billingPhoneFilterClicked(selectData)">

                                                                <ps-label class="ms-2"
                                                                    :class="selectData.country_code == billingPhoneCode.value ? ' font-bold' : ''">
                                                                    {{ selectData.country_code }} </ps-label>

                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </template>
                                            <template #loadmore>

                                                <div class="mb-2 w-56">

                                                    <div v-if="phoneStore.phoneList.data != null && phoneStore.loading.value == true"
                                                        class='mt-4 ms-4 flex'>
                                                        <ps-label> {{ $t("item_entry__loading") }}</ps-label>
                                                    </div>

                                                    <ps-label class="flex mt-4 ms-4 mb-2 underline font-bold cursor-pointer"
                                                        v-if="!phoneStore.isNoMoreRecord.value" @click="loadPhone"> {{
                                                            $t("item_entry__load_more") }} </ps-label>
                                                </div>

                                            </template>

                                        </ps-dropdown>
                                        <ps-input class="w-full dark:bg-transparent" ref="phone" type="text"
                                            v-model:value="billingPhone" :placeholder="$t('phone_number')" />

                                    </div>
                                    <ps-label class="lg:mt-2 mt-1  w-full text-xs" textColor="text-feError-500"
                                    v-if="validation.billingPhoneNoStatus"> Billing Phone Number is required </ps-label>
                            </div>
                            <!-- Phone Number End-->
                            </div>

                            <!-- Address Start -->
                            <div class="mb-2 md:mb-3">
                            <ps-label class="text-base">{{ $t('address') }} <span class="text-feError-800 font-medium ms-1">*</span></ps-label>
                            <ps-textarea rows="4" v-model:value="vendorCheckoutStore.paramHolder.billingAddress"
                            :placeholder="$t('address')"/>
                            <ps-label class="lg:mt-2 mt-1  w-full text-xs" textColor="text-feError-500"
                                v-if="validation.billingAddressStatus"> Billing Address is required
                            </ps-label>
                            </div>
                            <!-- Address End -->

                            <div class="flex flex-col md:flex-row items-center">
                            <!-- billing Country Start-->
                            <div class="flex-grow w-full mb-2 md:mb-3 md:mr-4">
                                <ps-label class="text-base">{{ $t('country') }}
                                    <span class="text-feError-800 font-medium ms-1">*</span>
                                </ps-label>
                                <ps-input type="text" class="w-full rounded" :placeholder="$t('country')"
                                    v-model:value="vendorCheckoutStore.paramHolder.billingCountry" />
                                <ps-label class="lg:mt-2 mt-1  w-full text-xs" textColor="text-feError-500"
                                    v-if="validation.billingCountryStatus"> Billing Country is required </ps-label>
                            </div>
                            <!-- billing Country End-->

                            <!-- billing State Start-->
                            <div class="flex-grow w-full mb-2 md:mb-3">
                                <ps-label class="text-base">{{ $t('state') }}
                                    <span class="text-feError-800 font-medium ms-1">*</span>
                                </ps-label>
                                <ps-input type="text" class="w-full rounded" :placeholder="$t('state')"
                                    v-model:value="vendorCheckoutStore.paramHolder.billingState" />
                                <ps-label class="lg:mt-2 mt-1  w-full text-xs" textColor="text-feError-500"
                                    v-if="validation.billingStateStatus"> Billing State is required </ps-label>
                            </div>
                            <!-- billing State End-->
                            </div>

                            <div class="flex flex-col md:flex-row items-center">
                            <!-- billing City Start-->
                            <div class="flex-grow w-full mb-2 md:mb-3 md:mr-4">
                                <ps-label class="text-base">{{ $t('city') }}
                                    <span class="text-feError-800 font-medium ms-1">*</span>
                                </ps-label>
                                <ps-input type="text" class="w-full rounded" :placeholder="$t('city')"
                                    v-model:value="vendorCheckoutStore.paramHolder.billingCity" />
                                <ps-label class="lg:mt-2 mt-1  w-full text-xs" textColor="text-feError-500"
                                    v-if="validation.billingCityStatus"> Billing City is required </ps-label>
                            </div>
                            <!-- billing City End-->

                            <!-- billing PostalCode Start-->
                            <div class="flex-grow w-full mb-2 md:mb-3">
                                <ps-label class="text-base">{{ $t('postal_code') }}
                                    <span class="text-feError-800 font-medium ms-1">*</span>
                                </ps-label>
                                <ps-input type="text" class="w-full rounded" :placeholder="$t('postal_code')"
                                    v-model:value="vendorCheckoutStore.paramHolder.billingPostalCode" />
                                <ps-label class="lg:mt-2 mt-1  w-full text-xs" textColor="text-feError-500"
                                    v-if="validation.billingPostalCodeStatus"> Billing Postal Code is required </ps-label>
                            </div>
                            <!-- billing State End-->
                            </div>

                            <!-- Address save checkout Start-->
                            <div class="me-2 flex items-center">
                             <input
                                type="checkbox"
                                v-model="isBillingSaveChecked"
                                class="form-checkbox rounded border text-fePrimary-500"/>
                            <ps-label class="ms-2" textColor="text-feSecondary-800 dark:text-feSecondary-300">{{ $t('save_this_address_for_next_time') }}</ps-label>
                            </div>
                            <!-- Address save checkout End-->
                        </div>
                    </div>
                    <!------------ For billing End ------------>

                    <div class="my-6 border-t border-feAchromatic-100 dark:border-feAchromatic-800"></div>

                    <vendor-checkout-group-title :title="$t('payment_method')"/>

                    <div class="grid grid-cols-3 sm:grid-cols-3 gap-4 mt-6 mb-5 px-5">
                        <ps-button colors="bg-transparent dark:bg-feAchromatic-800" focus="none" border="border border-2 focus:border-fePrimary-500 hover:shadow" hover="none" v-if="vendorInfoStore.isPaypalEnabled()" class=""  rounded="rounded-lg" @click="paymentClicked('PAYPAL')" >
                            <img v-lazy="{ src: $page.props.sysImageUrl + '/paypal.png' }" alt=""
                                       class="w-full h-full object-contain bottom-0"
                                        >
                        </ps-button>

                        <ps-button colors="bg-transparent dark:bg-feAchromatic-800" focus="none" border="border border-2 focus:border-fePrimary-500 hover:shadow" hover="none" v-if="vendorInfoStore.isStripeEnabled()" class=""  rounded="rounded-lg" @click="paymentClicked('STRIPE')" >
                            <img v-lazy="{ src: $page.props.sysImageUrl + '/stripe.png' }" alt=""
                                       class="w-full h-full object-contain bottom-0"
                                        >
                        </ps-button>
                        <ps-button colors="bg-transparent dark:bg-feAchromatic-800" focus="none" border="border border-2 focus:border-fePrimary-500 hover:shadow" hover="none" v-if="vendorInfoStore.isRazorEnabled()" class="" rounded="rounded-lg" @click="paymentClicked('RAZOR')" >
                            <img v-lazy="{ src: $page.props.sysImageUrl + '/razorpay.png' }" alt=""
                                       class="w-full h-full object-contain bottom-0"
                                        >
                        </ps-button>
                        <ps-button colors="bg-transparent dark:bg-feAchromatic-800" focus="none" border="border border-2 focus:border-fePrimary-500 hover:shadow" hover="none" v-if="vendorInfoStore.isPaystackEnabled()" class=""  rounded="rounded-lg" @click="paymentClicked('PAYSTACK')" >
                            <img v-lazy="{ src: $page.props.sysImageUrl + '/paystack.png' }" alt=""
                                       class="w-full h-full object-contain bottom-0"
                                        >
                        </ps-button>

                        <ps-button v-show="false" colors="bg-transparent dark:bg-feAchromatic-800" focus="none" v-if="appInfoStore.appInfo.data?.offlineEnabled == '1'" class="" border="border border-2 focus:border-fePrimary-500 hover:shadow" hover="none" padding="px-8 py-4" rounded="rounded-lg" @click="paymentClicked('OFFLINE')" >
                            <ps-icon class=" dark:text-feSecondary-800 pr-1" name="wallet" w="20" h="20"/>
                                        <ps-label textColor=" font-semibold" >Offline</ps-label>
                        </ps-button>

                        <!-- <ps-button colors="bg-transparent dark:bg-feAchromatic-800" focus="none" border="border border-2 focus:border-fePrimary-500 hover:shadow" hover="none" v-if="appInfoStore.appInfo.data?.payStackEnabled == '1'" class=""  rounded="rounded-lg" @click="paymentClicked('DELIVERY')" >
                            <img v-lazy="{ src: $page.props.sysImageUrl + '/cashondelivery.png' }" alt=""
                                       class="w-full h-full object-contain bottom-0"
                                        >
                        </ps-button> -->
                    </div>

                  </div>

                  <!-- Order Summary Only show in md or more lager Screen size-->
                  <div class="w-full md:w-80 md:ml-5">
                    <div class="rounded-md shadow-md dark:bg-feAchromatic-800 px-3 pb-5 mb-8">
                        <ps-label class="font-bold text-2xl pt-3 mb-5 " textColor="text-feSecondary-800 dark:text-feSecondary-300">
                        {{ $t('order_summary') }} </ps-label>

                        <div class="container">
                            <div class="flex items-start">
                              <!-- Image -->
                                <img v-if="galleryProvider.galleryList.data != null"
                                  class="h-32 w-32 object-cover overflow-hidden mr-2"
                                  v-lazy="{ src: $page.props.uploadUrl + '/' + galleryProvider?.galleryList?.data[0].imgPath, loading: $page.props.sysImageUrl+'/loading_gif.gif', error: $page.props.sysImageUrl+'/default_photo.png' }"
                                />
                                <img v-else
                                  class="h-32 w-32 object-cover overflow-hidden mr-2"
                                  v-lazy="{ src: $page.props.sysImageUrl+'/loading_gif.gif', loading: $page.props.sysImageUrl+'/loading_gif.gif', error: $page.props.sysImageUrl+'/default_photo.png' }"
                                />
                                <div class="flex flex-col">
                                    <!-- Warning Msg -->
                                    <div v-if="inStock < 10" class="mb-1">
                                        <ps-label  class="mb-1 text-sm" textColor="text-feError-500">Only {{ inStock }} Items In Stock</ps-label>
                                    </div>
                                    <div v-if="productStore.isSoldOut(loginUserId)" class="mb-1">
                                        <ps-label  class="mb-1 text-sm" textColor="text-feError-500">{{ $t("item_list__sold_item") }}</ps-label>
                                    </div>

                                    <!-- Title -->
                                    <div class="max-w-auto overflow-hidden whitespace-nowrap mb-2">
                                        <ps-label class="font-normal overflow-ellipsis" textColor="text-feSecondary-500 dark:text-feSecondary-300">{{ productStore.item.data?.title }}</ps-label>
                                    </div>

                                    <div class="flex flex-row items-center mb-1">
                                        <!-- original price -->
                                        <div v-if="productStore.isItemDiscount()">
                                            <ps-label textColor="line-through font-sm font-semibold text-feAchromatic-700 dark:text-feAchromatic-200">
                                                {{ productStore.item?.data?.itemCurrency?.currencySymbol }}{{ productStore.item?.data?.originalPrice}}
                                            </ps-label>
                                        </div>

                                        <!-- Discount Price -->
                                        <ps-label textColor="font-semibold text-xl text-fePrimary-500 ml-1">{{ productStore.item?.data?.itemCurrency?.currencySymbol }} {{ productStore.item?.data?.price }}</ps-label>
                                    </div>
                                    <!-- Qty -->
                                    <ps-label class="font-base mb-1" textColor="text-feSecondary-600 dark:text-feSecondary-300">{{ $t('order_summary_qty') }} {{ query.qty }}x</ps-label>

                                    <!-- Edit Order -->
                                    <ps-route-link :to="{ name: 'fe_item_detail', query: { item_id: query.itemId } }">
                                        <ps-label class="font-normal underline underline-offset-4 mb-1 cursor-pointer" textColor="text-fePrimary-500">{{ $t('edit_order') }}</ps-label>
                                    </ps-route-link>

                                </div>
                            </div>
                        </div>

                        <!-- SubTotal -->
                        <div class="flex flex-row items-center justify-between mt-6">
                            <ps-label class="text-normal" textColor="text-feSecondary-800 dark:text-feSecondary-300">{{ $t('subtotal') }}</ps-label>
                            <ps-label class="text-normal" textColor="text-feSecondary-800 dark:text-feSecondary-300">{{ productStore.item?.data?.itemCurrency?.currencySymbol }}{{ getSubTotal() }}</ps-label>
                        </div>

                        <!-- Discount -->
                        <div class="flex flex-row items-center justify-between mt-5">
                            <ps-label class="text-normal" textColor="text-feSecondary-800 dark:text-feSecondary-300">{{ $t('discount') }}</ps-label>
                            <ps-label class="text-normal" textColor="text-fePrimary-500">-{{ productStore.item?.data?.itemCurrency?.currencySymbol }}{{ getDiscount() }}</ps-label>
                        </div>

                        <div class="my-6 border-t border-feAchromatic-100 dark:border-feAchromatic-800"></div>

                        <!-- Total -->
                        <div class="flex flex-row items-center justify-between my-5">
                            <ps-label class="text-lg font-bold" textColor="text-feSecondary-800 dark:text-feSecondary-300">{{ $t('total') }}</ps-label>
                            <ps-label class="text-lg font-bold" textColor="text-feSecondary-800 dark:text-feSecondary-300">{{ productStore.item?.data?.itemCurrency?.currencySymbol }}{{ getSubTotal() }}</ps-label>
                        </div>

                        <!-- Place Order -->
                        <ps-button class="w-full cursor-pointer" @click="clickPlaceOrder">{{ $t('place_order') }}</ps-button>

                        <div class="flex flex-col justify-center items-center mt-3">
                            <ps-label class="text-sm text-center">
                                {{ $t('read_and_agreed_to') }}
                                <span class="text-fePrimary-500 cursor-pointer" @click="termsAndConditionClick">
                                    {{$t('order_summary_terms_and_conditions')}}
                                </span>
                            </ps-label>
                            <ps-label class="cursor-pointer text-sm" >
                                <ps-route-link :to="{name: 'fe_privacy' }" textColor="text-fePrimary-500" textSize="text-sm">
                                    {{ $t("footer__privacy_policy") }}
                                </ps-route-link>
                            </ps-label>
                        </div>
                    </div>
                  </div>
                </div>
            </div>
            <ps-loading-dialog ref="ps_loading_dialog" class='z-40'/>
            <ps-warning-dialog-2 ref="ps_warning_dialog" />
            <vendor-paypal-modal ref="vendor_paypal_payment_modal" />
            <ps-error-dialog ref="ps_error_dialog" />
        </template>
    </ps-content-container>
</template>

<script lang="ts">
import { Head } from '@inertiajs/vue3';
import PsContentContainer from '@template1/vendor/components/layouts/container/PsContentContainer.vue';
import { usePsAppInfoStoreState } from '@templateCore/store/modules/appinfo/AppInfoStore';
import { useVendorInfoStoreState } from '@templateCore/store/modules/vendor/VendorInfoStore';
import PsConst from '@templateCore/object/constant/ps_constants';

import {  ref,onMounted,computed,reactive, onUnmounted } from 'vue';
import PsWarningDialog2 from '@template1/vendor/components/core/dialog/PsWarningDialog2.vue';
import PsBreadcrumb2 from "@template1/vendor/components/core/breadcrumbs/PsBreadcrumb2.vue";
import PsButton from '@template1/vendor/components/core/buttons/PsButton.vue';
import PsLabel from '@template1/vendor/components/core/label/PsLabel.vue';
import PsLabelHeader4 from '@template1/vendor/components/core/label/PsLabelHeader4.vue';
import PsLoadingDialog from '@template1/vendor/components/core/dialog/PsLoadingDialog.vue';
import PsFrontendLayout from '@template1/vendor/components/layouts/container/PsFrontendLayout.vue';
import PsNoResult from "@template1/vendor/components/modules/list/PsNoResult.vue";
import PsInput from '@template1/vendor/components/core/input/PsInput.vue';
import PsDropdown from '@template1/vendor/components/core/dropdown/PsDropdown.vue';
import PsDropdownSelect from '@template1/vendor/components/core/dropdown/PsDropdownSelect.vue';
import PsInputWithRightIcon from '@template1/vendor/components/core/input/PsInputWithRightIcon.vue';
import PsTextarea from '@template1/vendor/components/core/textarea/PsTextarea.vue';
import PsRouteLink from '@template1/vendor/components/core/link/PsRouteLink.vue';
import PsErrorDialog from '@template1/vendor/components/core/dialog/PsErrorDialog.vue';
import VendorPaypalModal from './components/VendorPaypalModal.vue';
import { trans } from 'laravel-vue-i18n';

import { PsValueStore } from "@templateCore/store/modules/core/PsValueStore";
import { useVendorCheckoutStoreState } from "@templateCore/store/modules/vendor_checkout/VendorCheckoutStore";
import { useVendorItemBoughtStore } from "@templateCore/store/modules/vendor_checkout/VendorItemBoughtStore";
import { usePhoneStore } from '@templateCore/store/modules/item/PhoneStore';
import { useProductStore } from '@templateCore/store/modules/item/ProductStore';
import { useGalleryStoreState } from '@templateCore/store/modules/gallery/GalleryStore';
import OrderSummaryParameterHolder from "@templateCore/object/holder/OrderSummaryParameterHolder";
import VendorItemBoughtParameterHolder from '@templateCore/object/holder/VendorItemBoughtParameterHolder';

import VendorCheckoutGroupTitle from './components/VendorCheckoutGroupTitle.vue';
import PsUtils from '@templateCore/utils/PsUtils';
import PsStatus from '@templateCore/api/common/PsStatus';

import { router } from '@inertiajs/vue3';

export default {
    name: 'VendorCheckout',
    components : {
        PsButton,
        PsContentContainer,
        PsBreadcrumb2,
        PsLabel,
        PsInput,
        PsLabelHeader4,
        PsNoResult,
        PsLoadingDialog,
        VendorCheckoutGroupTitle,
        PsDropdownSelect,
        PsInputWithRightIcon,
        PsDropdown,
        PsTextarea,
        PsWarningDialog2,
        PsRouteLink,
        VendorPaypalModal,
        PsErrorDialog,
        Head
    },
    layout: PsFrontendLayout,
    props : ['query'],
    setup (props){

        const appInfoStore = usePsAppInfoStoreState();
        const vendorInfoStore = useVendorInfoStoreState();
        const vendorCheckoutStore = useVendorCheckoutStoreState();
        const vendorItemBoughtStore = useVendorItemBoughtStore();
        const phoneStore = usePhoneStore('phoneList');
        const galleryProvider = useGalleryStoreState('detail');
        const itemProvider = useProductStore();
        const productStore = useProductStore('detail');
        const orderSummaryParameterHolder =new OrderSummaryParameterHolder();
        const vendorItemBoughtParameterHolder = new VendorItemBoughtParameterHolder();
        const payment = ref('');

        const ps_loading_dialog = ref();
        const ps_warning_dialog = ref();
        const ps_error_dialog = ref();
        const vendor_paypal_payment_modal = ref();
        const loading = ref(false);
        const psValueStore = PsValueStore();
        const loginUserId = psValueStore.getLoginUserId();
        const phoneKeyword = ref("");
        const shippingPhone = ref('');
        const billingPhone = ref('');
        const shippingPhoneCode = reactive({ 'id': 0, 'value': '', 'code': '+1' });
        const billingPhoneCode = reactive({ 'id': 0, 'value': '', 'code': '+1' });
        const isShippingSaveChecked = ref(false);
        const isBillingSaveChecked = ref(false);
        const isSameChecked = ref(false);
        const orderId = ref();

        const inStock = ref(0);
        let isError= ref(true);

        const validation = ref({
            shippingFirstNameStatus : false,
            shippingLastNameStatus : false,
            shippingEmailStatus : false,
            shippingPhoneNoStatus : false,
            shippingAddressStatus : false,
            shippingCountryStatus : false,
            shippingStateStatus : false,
            shippingCityStatus : false,
            shippingPostalCodeStatus : false,
            billingFirstNameStatus : false,
            billingLastNameStatus : false,
            billingEmailStatus : false,
            billingPhoneNoStatus : false,
            billingAddressStatus : false,
            billingCountryStatus : false,
            billingStateStatus : false,
            billingCityStatus : false,
            billingPostalCodeStatus : false
        });

        onMounted(()=>{
            // vendorCheckoutStore.paramHolder = null;
        });

        onMounted(async () => {
            await loadDataForItemDetail();
            let quantity = productStore?.item?.data?.productRelation.filter((pr)=>pr.coreKeysId == 'ps-itm00010');
            inStock.value = quantity.length == 0 ? 1 : quantity[0].value;
        });

        async function loadDataForItemDetail() {
            await productStore.loadItem(props.query.itemId, loginUserId);
            await vendorInfoStore.loadVendorInfo(productStore.item.data?.vendor.id);
            await galleryProvider.resetGalleryList(loginUserId, props.query.itemId, PsConst.ITEM__RELATED_TYPE);
        }

        function getSubTotal(){
            return Number(productStore.item.data?.price * props.query.qty).toFixed(2);
        }

        function getDiscount(){
            return Number((productStore.item.data?.originalPrice - productStore.item.data?.price) * props.query.qty).toFixed(2);
        }

        function loadPhone() {
            phoneStore.loadPhoneCountryCode(loginUserId, itemProvider.phoneparamHolder);
        }

        function filterPhoneUpdate(value) {
            itemProvider.phoneparamHolder.keyword = value;
            phoneStore.filterPhoneUpdate(loginUserId, itemProvider.phoneparamHolder);
        }

        function shippingPhoneFilterClicked(value) {
            shippingPhoneCode.code = value.country_code
            console.log(shippingPhoneCode.code);
        }
        function billingPhoneFilterClicked(value) {
            billingPhoneCode.code = value.country_code
            console.log(billingPhoneCode.code);
        }

        async function clickPlaceOrder(){
            if(loginUserId == 'nologinuser'){
                router.get(route('login'));
            }else{

                for (const key in validation.value) {
                    validation.value[key] = false;
                }

                if (vendorCheckoutStore.paramHolder.shippingFirstName == undefined || vendorCheckoutStore.paramHolder.shippingFirstName == '') {
                    validation.value.shippingFirstNameStatus = true;
                }
                if (vendorCheckoutStore.paramHolder.shippingLastName == undefined || vendorCheckoutStore.paramHolder.shippingLastName == '') {
                    validation.value.shippingLastNameStatus = true;
                }
                if (vendorCheckoutStore.paramHolder.shippingEmail == undefined || vendorCheckoutStore.paramHolder.shippingEmail == '') {
                    validation.value.shippingEmailStatus = true;
                }
                if (shippingPhone.value == undefined || shippingPhone.value == '') {
                    validation.value.shippingPhoneNoStatus = true;
                }
                if (vendorCheckoutStore.paramHolder.shippingAddress == undefined || vendorCheckoutStore.paramHolder.shippingAddress == '') {
                    validation.value.shippingAddressStatus = true;
                }
                if (vendorCheckoutStore.paramHolder.shippingCountry == undefined || vendorCheckoutStore.paramHolder.shippingCountry == '') {
                    validation.value.shippingCountryStatus = true;
                }
                if (vendorCheckoutStore.paramHolder.shippingState == undefined || vendorCheckoutStore.paramHolder.shippingState == '') {
                    validation.value.shippingStateStatus = true;
                }
                if (vendorCheckoutStore.paramHolder.shippingCity == undefined || vendorCheckoutStore.paramHolder.shippingCity == '') {
                    validation.value.shippingCityStatus = true;
                }
                if (vendorCheckoutStore.paramHolder.shippingPostalCode == undefined || vendorCheckoutStore.paramHolder.shippingPostalCode == '') {
                    validation.value.shippingPostalCodeStatus = true;
                }
                console.log(isSameChecked.value);
                if(isSameChecked.value){
                    billingPhoneCode.code = shippingPhoneCode.code;
                    vendorCheckoutStore.paramHolder.billingFirstName = vendorCheckoutStore.paramHolder.shippingFirstName;
                    vendorCheckoutStore.paramHolder.billingLastName = vendorCheckoutStore.paramHolder.shippingLastName;
                    vendorCheckoutStore.paramHolder.billingEmail = vendorCheckoutStore.paramHolder.shippingEmail;
                    billingPhone.value = shippingPhone.value;
                    vendorCheckoutStore.paramHolder.billingAddress = vendorCheckoutStore.paramHolder.shippingAddress;
                    vendorCheckoutStore.paramHolder.billingCountry = vendorCheckoutStore.paramHolder.shippingCountry;
                    vendorCheckoutStore.paramHolder.billingState = vendorCheckoutStore.paramHolder.shippingState;
                    vendorCheckoutStore.paramHolder.billingCity = vendorCheckoutStore.paramHolder.shippingCity;
                    vendorCheckoutStore.paramHolder.billingPostalCode = vendorCheckoutStore.paramHolder.shippingPostalCode;
                }
                if (vendorCheckoutStore.paramHolder.billingFirstName == undefined || vendorCheckoutStore.paramHolder.billingFirstName == '') {
                    validation.value.billingFirstNameStatus = true;
                }
                if (vendorCheckoutStore.paramHolder.billingLastName == undefined || vendorCheckoutStore.paramHolder.billingLastName == '') {
                    validation.value.billingLastNameStatus = true;
                }
                if (vendorCheckoutStore.paramHolder.billingEmail == undefined || vendorCheckoutStore.paramHolder.billingEmail == '') {
                    validation.value.billingEmailStatus = true;
                }
                if (billingPhone.value == undefined || billingPhone.value == '') {
                    validation.value.billingPhoneNoStatus = true;
                }
                if (vendorCheckoutStore.paramHolder.billingAddress == undefined || vendorCheckoutStore.paramHolder.billingAddress == '') {
                    validation.value.billingAddressStatus = true;
                }
                if (vendorCheckoutStore.paramHolder.billingCountry == undefined || vendorCheckoutStore.paramHolder.billingCountry == '') {
                    validation.value.billingCountryStatus = true;
                }
                if (vendorCheckoutStore.paramHolder.billingState == undefined || vendorCheckoutStore.paramHolder.billingState == '') {
                    validation.value.billingStateStatus = true;
                }
                if (vendorCheckoutStore.paramHolder.billingCity == undefined || vendorCheckoutStore.paramHolder.billingCity == '') {
                    validation.value.billingCityStatus = true;
                }
                if (vendorCheckoutStore.paramHolder.billingPostalCode == undefined || vendorCheckoutStore.paramHolder.billingPostalCode == '') {
                    validation.value.billingPostalCodeStatus = true;
                }


                for (const key in validation.value) {
                    const value = validation.value[key];
                    if(value == false){
                        isError.value = false;
                    }else{
                        isError.value = true;
                        break;
                    }
                }

                if(isError.value == false){
                    vendorCheckoutStore.paramHolder.vendorId = productStore.item.data?.vendor.id;
                    vendorCheckoutStore.paramHolder.shippingPhoneNo = shippingPhoneCode.code.toString() + shippingPhone.value.toString();
                    vendorCheckoutStore.paramHolder.billingPhoneNo = billingPhoneCode.code.toString() + billingPhone.value.toString();
                    vendorCheckoutStore.paramHolder.isSaveShippingInfoForNextTime = isShippingSaveChecked.value ? "1" : "0";
                    vendorCheckoutStore.paramHolder.isSaveBillingInfoForNextTime = isBillingSaveChecked.value ? "1" : "0";
                    vendorCheckoutStore.paramHolder.totalPrice = getSubTotal();
                    orderSummaryParameterHolder.itemId = props.query.itemId;
                    orderSummaryParameterHolder.quantity = props.query.qty;
                    orderSummaryParameterHolder.originalPrice = productStore.item.data?.originalPrice;
                    orderSummaryParameterHolder.salePrice = productStore.item.data?.price;
                    orderSummaryParameterHolder.subTotal = getSubTotal();
                    orderSummaryParameterHolder.discountPrice = getDiscount();
                    vendorCheckoutStore.paramHolder.orderSummary=[
                    {
                      "item_id" : props.query.itemId,
                      "quantity" : props.query.qty,
                      "original_price" : productStore.item.data?.originalPrice,
                      "sale_price" : productStore.item.data?.price,
                      "sub_total" : getSubTotal().toString(),
                      "discount_price" : getDiscount().toString()
                    }
                    ];

                    console.log(vendorCheckoutStore.paramHolder);

                    await vendorCheckoutStore.loadOrder(loginUserId,vendorCheckoutStore.paramHolder);

                    orderId.value = vendorCheckoutStore.order.data?.orderId;

                    if(payment.value != ''){
                        console.log(payment.value);
                        vendorItemBought();
                    }else{
                        ps_error_dialog.value.openModal('', 'Choose Payment Method');
                    }
                }
            }
        }

        function vendorItemBought(){
            if(appInfoStore.appInfo.data.mobileSetting.is_demo_for_payment == 1){
                ps_warning_dialog.value.openModal(
                    trans('payment__warning_title'),
                    trans('payment__confirm_message'),
                    trans('payment__ok'),
                    trans('credit_card_modal__cancel'),
                    () => {
                        if(payment.value == 'PAYPAL'){
                            paypalClicked();
                        }else if(payment.value == 'STRIPE'){
                            stripeClicked();
                        }else if(payment.value == 'RAZOR'){
                            razorClicked();
                        }else if(payment.value == 'PAYSTACK'){
                            paystackClicked();
                        }else if(payment.value == 'OFFLINE'){
                            offlineClicked();
                        }else if(payment.value == 'DELIVERY'){
                            cashOnDeliveryClicked();
                        }
                    },
                    () => {
                        PsUtils.log("Cancel");
                    });


            }else{
                if(payment.value == 'PAYPAL'){
                    paypalClicked();
                }else if(payment.value == 'STRIPE'){
                    stripeClicked();
                }else if(payment.value == 'RAZOR'){
                    razorClicked();
                }else if(payment.value == 'PAYSTACK'){
                    paystackClicked();
                }else if(payment.value == 'OFFLINE'){
                    offlineClicked();
                }else if(payment.value == 'DELIVERY'){
                    cashOnDeliveryClicked();
                }
            }
        }

        function paymentClicked(value){
            if(value == 'PAYPAL'){
                payment.value = 'PAYPAL';
            }else if(value == 'STRIPE'){
                payment.value = 'STRIPE';
            }else if(value == 'RAZOR'){
                payment.value = 'RAZOR';
            }else if(value == 'PAYSTACK'){
                payment.value = 'PAYSTACK';
            }else if(value == 'OFFLINE'){
                payment.value = 'OFFLINE';
            }else if(value == 'DELIVERY'){
                payment.value = 'DELIVERY';
            }
        }

        function paypalClicked(){
            console.log('paypal modal open now');
            vendor_paypal_payment_modal.value.openModal(
                () => {
                    const payment = PsConst.PAYMENT_PAYPAL_METHOD.toString();
                    doPayment(payment);
                },
                () => {
                    PsUtils.log("Cancel");
                },
                vendorCheckoutStore.paramHolder.vendorId
            );
        }

        function cashOnDeliveryClicked(){

        }

        function stripeClicked(){

        }

        function razorClicked(){

        }

        function paystackClicked(){

        }

        function offlineClicked(){

        }

        async function doPayment(payment) {

            vendorItemBoughtParameterHolder.userId = loginUserId;
            vendorItemBoughtParameterHolder.paymentMethod = payment;
            vendorItemBoughtParameterHolder.paymentMethodNounce =vendorItemBoughtStore.paymentNonce;
            vendorItemBoughtParameterHolder.paymentAmount = productStore.item.data?.price * props.query.qty;
            vendorItemBoughtParameterHolder.razorId = '';
            vendorItemBoughtParameterHolder.isPaystack = '';
            vendorItemBoughtParameterHolder.orderId = orderId.value;
            vendorItemBoughtParameterHolder.vendorId = vendorCheckoutStore.paramHolder.vendorId;
            vendorItemBoughtParameterHolder.currencyId = productStore.item?.data?.itemCurrency?.id;

            const resopne = await vendorItemBoughtStore.postVendorItemBought(loginUserId,vendorItemBoughtParameterHolder);
            if(resopne.status == PsStatus.ERROR){
                ps_error_dialog.value.openModal('', resopne.message);
            }else{
                router.get(route('fe_order_successful'),{
                    'orderId' : orderId.value,
                    'itemId' : props.query.itemId
                });
            }
        }


        function termsAndConditionClick(){
            router.get(route('fe_terms_and_conditions'));
        }

        const breadcrumb = computed(() => {
            if (appInfoStore?.isShowSubCategory() || productStore.item.data?.subCategory?.id == '') {
                return [
                    {
                        label: trans('ps_nav_bar__home'),
                        url: route('dashboard')
                    },
                    {
                      label: productStore.item.data?.category?.catName,
                      url: route('fe_item_list', {
                        cat_id: productStore.item.data?.category?.catId,
                        cat_name: productStore.item.data?.category?.catName,
                        status: 1
                      })
                    },
                    {
                      label: productStore.item.data?.title,
                    },
                    {
                      label: 'Checkout',
                      color: "text-fePrimary-500"
                    }
                    ];
            } else {
                  return [
                    {
                      label: trans('ps_nav_bar__home'),
                      url: route('dashboard')
                    },
                    {
                      label: productStore.item.data?.category?.catName,
                      url: route('fe_item_list', {
                        cat_id: productStore.item.data?.category?.catId,
                        cat_name: productStore.item.data?.category?.catName,
                        status: 1
                      })
                    },
                    {
                      label: productStore.item.data?.subCategory?.name,
                      url: route('fe_item_list', {
                        cat_id: productStore.item.data?.category?.catId,
                        cat_name: productStore.item.data?.category?.catName,
                        sub_cat_id: productStore.item.data?.subCategory?.id,
                        sub_cat_name: productStore.item.data?.subCategory?.name,
                        status: 1
                      })
                    },
                    {
                      label: productStore.item.data?.title,
                    },
                    {
                      label: 'Checkout',
                      color: "text-fePrimary-500"
                    }
                ];
            }
        });

        return {
            PsConst,
            breadcrumb,
            loginUserId,
            vendorCheckoutStore,
            ps_loading_dialog,
            ps_error_dialog,
            vendor_paypal_payment_modal,
            loading,
            appInfoStore,
            vendorInfoStore,
            productStore,
            loadPhone,
            shippingPhone,
            billingPhone,
            filterPhoneUpdate,
            phoneKeyword,
            shippingPhoneCode,
            billingPhoneCode,
            shippingPhoneFilterClicked,
            billingPhoneFilterClicked,
            phoneStore,
            galleryProvider,
            clickPlaceOrder,
            inStock,
            getSubTotal,
            getDiscount,
            termsAndConditionClick,
            validation,
            isShippingSaveChecked,
            isBillingSaveChecked,
            isSameChecked,
            paymentClicked,
            ps_warning_dialog
        }
    },

}
</script>
