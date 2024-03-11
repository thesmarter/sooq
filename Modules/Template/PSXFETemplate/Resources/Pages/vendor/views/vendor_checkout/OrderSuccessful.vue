<template>
    <div class="mt-24 w-full">
        <div class="w-full h-[380px] bg-feSuccess-50 flex flex-col justify-center items-center">
            <div class="w-20 h-20 mb-5 flex bg-feSuccess-500 dark:bg-feSuccess-900 rounded-full items-center justify-center">
                <ps-icon name="bluemark" textColor="text-feSecondary-50" w="60" h="60" />
            </div>
            <ps-label class="text-2xl font-bold mb-5" textColor="text-feAchromatic-800 dark:bg-feAchromatic-50">{{ $t('order_successfully_completed') }}</ps-label>
            <ps-label class="w-100 mx-10 text-center md:w-140 lg:w-144 mb-5" textColor="text-feAchromatic-700 dark:bg-feAchromatic-100">{{ $t('order_successfully_completed_descripition') }}</ps-label>
            <ps-button @click="continueShopping">{{ $t('continue_shopping') }}</ps-button>
        </div>
        
        <!-- Order Summary -->
        <div class="xl:w-feLarge lg:w-large px-8 sm:px-8 md:w-full md:px-6 lg:px-0 mx-auto">
            <div class="flex flex-col mb-6 mt-3">
                <ps-label class="font-semibold text-xl my-6" textColor="text-feAchromatic-900 dark:bg-feAchromatic-50"> {{ $t("order_summary") }} </ps-label>
                <div class="flex flex-col md:flex-row lg:flex-row justify-between w-full">
                    <div class="w-full md:w-1/2 lg:w-1/2 flex flex-col justify-start">
                        <order-success-title-and-value :title="$t('order_id')" :value="orderSummary?.data?.orderSummary?.orderCode"/>
                        <order-success-title-and-value :title="$t('order_date')" :value="orderSummary?.data?.orderSummary?.paymentDate"/>
                        <order-success-title-and-value :title="$t('success_name')" :value="orderSummary?.data?.orderSummary?.shippingFirstName + ' ' +orderSummary?.data?.orderSummary?.shippingLastName"/>
                        <order-success-title-and-value :title="$t('success_email')" :value="orderSummary?.data?.orderSummary?.shippingEmail"/>
                        <order-success-title-and-value :title="$t('shipping__address')" :value="orderSummary?.data?.orderSummary?.shippingAddress"/>
                    </div>
                    <div class="w-full md:w-1/2 lg:w-1/2 flex flex-col justify-start">
                        <order-success-title-and-value :title="$t('success_payment_method')" :value="orderSummary?.data?.orderSummary?.paymentMethod"/>
                        <div class="flex flex-row mb-4">
                            <ps-label class="w-1/2" textColor="text-feAchromatic-600 dark:bg-feSuccess-900"> {{ $t('success_payment_status') }} </ps-label>
                            <div class="w-50 bg-feSuccess-50 px-3 rounded">
                                <ps-label class="w-1/2" textColor="text-feSuccess-500 dark:bg-feSuccess-100"> {{orderSummary?.data?.orderSummary?.paymentStatus}} </ps-label>
                            </div> 
                        </div>
                        <order-success-title-and-value :title="$t('success_total_order_amount')" :value="orderSummary?.data?.orderSummary?.total"/>
                        <order-success-title-and-value :title="$t('success_billing_address')" :value="orderSummary?.data?.orderSummary?.billingAddress"/>
                    </div>
                </div>
            </div>
            <div class="flex flex-col mb-8">
                <ps-label class="font-semibold text-xl my-6" textColor="text-feAchromatic-900 dark:bg-feAchromatic-50"> {{ orderSummary?.data?.orderSummary?.vendorName }} </ps-label>
                <div class="flex justify-between w-full">
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
                                <ps-label class="font-base mb-1" textColor="text-feSecondary-600 dark:text-feSecondary-300">Qty: {{ orderSummary?.data?.orderSummary?.itemInfo[0]?.quantity }}x</ps-label>
                                
                            </div>    
                        </div>
                        <div class="my-6 border-t border-feAchromatic-100"></div>
                        <div class="flex flex-row items-center justify-between mt-6">
                            <ps-label class="text-normal" textColor="text-feSecondary-800 dark:text-feSecondary-300">{{ $t('subtotal') }}</ps-label>
                            <ps-label class="text-normal" textColor="text-feSecondary-800 dark:text-feSecondary-300"> {{ orderSummary?.data?.orderSummary?.itemInfo[0]?.subTotal }} </ps-label>
                        </div>

                        <!-- Discount -->
                        <div class="flex flex-row items-center justify-between mt-5">
                            <ps-label class="text-normal" textColor="text-feSecondary-800 dark:text-feSecondary-300">{{ $t('discount') }}</ps-label>
                            <ps-label class="text-normal" textColor="text-fePrimary-500">-{{ orderSummary?.data?.orderSummary?.itemInfo[0]?.discountPrice }}</ps-label>
                        </div>

                        <div class="my-6 border-t border-feAchromatic-100"></div>

                        <!-- Total -->
                        <div class="flex flex-row items-center justify-between my-5">
                            <ps-label class="text-lg font-bold" textColor="text-feSecondary-800 dark:text-feSecondary-300">{{$t('success_total')}}</ps-label>
                            <ps-label class="text-lg font-bold" textColor="text-feSecondary-800 dark:text-feSecondary-300">{{ orderSummary?.data?.orderSummary?.total }}</ps-label>
                        </div>
                    </div>    
                </div>
            </div>
        </div>
        
    </div>
</template>

<script>

// Libs
import { Head } from '@inertiajs/vue3';
import {ref, onMounted} from 'vue';

import PsLabel from '@template1/vendor/components/core/label/PsLabel.vue';
import PsIcon from '@template1/vendor/components/core/icons/PsIcon.vue';
import PsButton from '@template1/vendor/components/core/buttons/PsButton.vue';
import OrderSuccessTitleAndValue from './components/OrderSuccessTitleAndValue.vue';

import PsFrontendLayout from '@template1/vendor/components/layouts/container/PsFrontendLayout.vue';

// Providers
import { PsValueStore } from "@templateCore/store/modules/core/PsValueStore";
import { usePsAppInfoStoreState } from '@templateCore/store/modules/appinfo/AppInfoStore';
import { useVendorCheckoutStoreState } from "@templateCore/store/modules/vendor_checkout/VendorCheckoutStore";
import { useProductStore } from '@templateCore/store/modules/item/ProductStore';
import { useGalleryStoreState } from '@templateCore/store/modules/gallery/GalleryStore';

import PsConst from '@templateCore/object/constant/ps_constants';
import { router } from '@inertiajs/vue3';
import { trans } from 'laravel-vue-i18n';

export default {
    name: "OrderSuccessfulPage",
    components: {
        PsIcon,
        PsLabel,
        PsButton,
        OrderSuccessTitleAndValue,
        Head,
    },
    layout: PsFrontendLayout,
    props : ['query'],
    setup(props) {
        const psValueStore = PsValueStore();
        
        const galleryProvider = useGalleryStoreState('detail');
        const productStore = useProductStore('detail');

        const appInfoStore = usePsAppInfoStoreState();      
        const loginUserId = psValueStore.getLoginUserId();
        const vendorCheckoutStore = useVendorCheckoutStoreState();
        let orderSummary = ref();

        onMounted(async () => {
            await loadDataForItemDetail();
        });

        function continueShopping(){
            router.get(route('dashboard'));
        }

        async function loadDataForItemDetail() {
            orderSummary.value = await vendorCheckoutStore.loadOrderSummary(props.query.orderId,loginUserId);
            await productStore.loadItem(props.query.itemId, loginUserId);
            await galleryProvider.resetGalleryList(loginUserId, props.query.itemId, PsConst.ITEM__RELATED_TYPE);
        }

        return {
            appInfoStore,
            PsValueStore,
            galleryProvider,
            productStore,
            vendorCheckoutStore,
            orderSummary,
            continueShopping,
            loginUserId,
        };
    },
};
</script>



