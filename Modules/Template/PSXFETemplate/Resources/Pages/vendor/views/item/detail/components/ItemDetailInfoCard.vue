<template>
     <div class="flex items-center gap-3 mt-4"
        v-if="productStore.item?.data?.productRelation.filter((pr) => pr.coreKeysId == 'ps-itm00008').length != 0 && productStore.item?.data?.productRelation.filter((pr) => pr.coreKeysId == 'ps-itm00008')[0]?.selectedValue[0]?.value != ''">
        <ps-icon textColor="text-fePrimary-500 dark:text-fePrimary-500" name="case" w="24" h="24" />
        <ps-label textColor="text-base font-semibold text-feSecondary-600 dark:text-feSecondary-50">{{
            $t('item_detail__multi_item') }}</ps-label>
    </div>
    <div class="flex items-center gap-3 mt-4" v-else>
        <ps-icon textColor="text-fePrimary-500 dark:text-fePrimary-500" name="case" w="24" h="24" />
        <ps-label textColor="text-base font-semibold text-feSecondary-600 dark:text-feSecondary-50">{{
            $t('item_detail__single_item') }}</ps-label>
    </div>
    <div class="bg-feSecondary-50 dark:bg-feSecondary-800 rounded-lg px-2 py-4 lg:p-4 mt-6">
        <ps-label textColor="text-xl font-semibold text-feSecondary-800 dark:text-feSecondary-50">{{
            $t('item_detail__details') }}</ps-label>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-6">
            <div v-for="pr in productRelation" :key="pr.id" class="">
                <ps-label
                    textColor="text-base font-medium text-feSecondary-800 dark:text-feSecondary-50">{{
                        itemCustomFieldStore.customField.data?.customList.filter((cf) => cf.coreKeysId ==
                            pr.coreKeysId && cf.isVisible == '1')[0]?.name }}</ps-label>
                <ps-label
                    textColor="text-sm font-normal text-feSecondary-600 dark:text-feSecondary-200">{{
                        pr.selectedValue[0].value }}</ps-label>
            </div>
            <div class="">
                <ps-label
                    textColor="text-base font-medium text-feSecondary-800 dark:text-feSecondary-50">{{
                        $t('item_detail__stock') }}</ps-label>
                <ps-label
                    textColor="text-sm font-normal text-feSecondary-600 dark:text-feSecondary-200">{{
                        productStore.item?.data?.isSoldOut == '1' ? $t('item_list__sold_item') :
                        $t('item_detail__available') }}</ps-label>
            </div>
        </div>
    </div>

    <!-- description -->
    <div class="mt-6 px-2 py-4 lg:p-4 rounded-lg bg-feSecondary-50 dark:bg-feSecondary-800">
        <ps-label textColor="text-xl font-semibold text-feSecondary-800 dark:text-feSecondary-50">{{
            $t('item_detail__description') }}</ps-label>
        <div class="mt-6">
            <ps-label textColor="text-sm font-normal text-feSecondary-600 dark:text-feSecondary-200">
                {{ productStore.item?.data ? productStore.item?.data?.description : '' }}
            </ps-label>
        </div>
    </div>

    <!-- Other Info -->
    <div v-if="(productStore.item?.data?.productRelation.filter((pr) =>
        pr.coreKeysId == 'ps-itm-rpt00005' ||  pr.coreKeysId == 'ps-itm00003').lenght != 0 &&
        productStore.item?.data?.productRelation.filter((pr) =>
        pr.coreKeysId == 'ps-itm-rpt00005' ||  pr.coreKeysId == 'ps-itm00003')[0]?.selectedValue[0]?.value != '') ||
    (productStore.item?.data?.productRelation.filter((pr) =>
        pr.coreKeysId != 'ps-itm-rpt00005' &&  pr.coreKeysId != 'ps-itm00003' &&
        pr.coreKeysId != 'ps-itm00002' && pr.coreKeysId != 'ps-itm00004' &&  pr.coreKeysId != 'ps-itm00007' &&
        pr.coreKeysId != 'ps-itm00008' &&  pr.coreKeysId != 'ps-itm00009').length != 0 &&
        productStore.item?.data?.productRelation.filter((pr) =>
        pr.coreKeysId != 'ps-itm-rpt00005' &&  pr.coreKeysId != 'ps-itm00003' &&
        pr.coreKeysId != 'ps-itm00002' &&  pr.coreKeysId != 'ps-itm00004' &&  pr.coreKeysId != 'ps-itm00007' &&
        pr.coreKeysId != 'ps-itm00008' &&  pr.coreKeysId != 'ps-itm00009')[0]?.selectedValue[0]?.value != '') ||
        productStore.item?.data?.phone != null && productStore.item?.data?.phone != ''">

    <div class="col-span-8 mt-6">
        <div class="bg-feSecondary-50 dark:bg-feSecondary-800 rounded-lg px-2 py-4 lg:p-4 flex flex-col">
            <ps-label textColor="text-xl font-semibold text-feSecondary-800 dark:text-feSecondary-50">
                {{ $t('item_detail__other_info') }}
            </ps-label>
            <!-- <div class="flex grid-cols-2 gap-2">
                <div v-for="productRelation in productStore.item?.data?.productRelation.filter((pr) => pr.coreKeysId == 'ps-itm-rpt00005' || pr.coreKeysId == 'ps-itm00003')"
                    :key="productRelation.id">
                    <div v-if="productRelation.selectedValue[0].value != '' && itemCustomFieldStore.customField.data?.customList.filter((cf) => cf.coreKeysId == productRelation.coreKeysId && cf.isVisible == '1') != ''">
                        <ps-label textColor="text-base font-medium text-feSecondary-800 dark:text-feSecondary-50 mt-6">
                            {{ itemCustomFieldStore.customField.data?.customList.filter((cf) => cf.coreKeysId == productRelation.coreKeysId && cf.isVisible == '1')[0]?.name }}
                        </ps-label>
                        <ps-label textColor="text-sm font-normal text-feSecondary-600 dark:text-feSecondary-200 mt-2">
                            {{ productRelation.selectedValue[0].value }}
                        </ps-label>
                    </div>
                </div>
            </div> -->
            <div v-for="productRelation in productStore.item?.data?.productRelation.filter((pr) => pr.coreKeysId != 'ps-itm00002' && pr.coreKeysId != 'ps-itm00004' && pr.coreKeysId != 'ps-itm00007' && pr.coreKeysId != 'ps-itm00010')"
                :key="productRelation.id">
                <div v-if="productRelation.isVisible == '1' && productRelation.isDelete == '0'">
                    <ps-label textColor="text-base font-medium text-feSecondary-800 dark:text-feSecondary-50 mt-6">
                        <!-- {{ itemCustomFieldStore.customField.data?.customList.filter((cf) => cf.coreKeysId == productRelation.coreKeysId && cf.isVisible == '1')[0]?.name }} -->
                        {{ productRelation.coreKeyName }}
                    </ps-label>
                    <div class="grid grid-cols-1 sm:grid-cols-2">
                        <ps-label v-for="(selectedValue, index) in productRelation.selectedValue" :key="index" textColor="text-sm font-normal text-feSecondary-600 dark:text-feSecondary-200 mt-2">
                            {{ selectedValue.value }}
                        </ps-label>
                    </div>
                </div>
            </div>
            <div v-if="productStore.item?.data?.phone != null && productStore.item?.data?.phone != ''">
                <ps-label textColor="text-base font-medium text-feSecondary-800 dark:text-feSecondary-50 mt-6">
                    {{ $t('item_detail__contact_numbers') }}
                </ps-label>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3">
                    <div v-for="(phone, index) in productStore.item?.data.phone.split('#')" :key="index">
                        <ps-label textColor="text-sm font-normal text-feSecondary-600 dark:text-feSecondary-200 mt-2">
                            {{ phone }}
                        </ps-label>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</template>

<script>

    import PsLabel from '@template1/vendor/components/core/label/PsLabel.vue';
    import PsIcon from '@template1/vendor/components/core/icons/PsIcon.vue';

    import { useProductStore } from '@templateCore/store/modules/item/ProductStore';
    import { useCustomFieldStoreState } from '@templateCore/store/modules/customField/CustomFieldStore';
    import { trans } from 'laravel-vue-i18n';

    export default {
    name : "ItemDetailInfoCard",
        components: {
            PsLabel,
            PsIcon
        },
        props : {
            productRelation : []
        },
        setup(){

            const productStore = useProductStore('detail');
            const itemCustomFieldStore = useCustomFieldStoreState('detail');

            return {
                productStore,
                itemCustomFieldStore
            }
        }
    }
</script>
