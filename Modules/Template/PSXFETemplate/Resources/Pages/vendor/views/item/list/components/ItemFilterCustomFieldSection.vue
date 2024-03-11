<template>
    <div v-for="customFieldHeader in customFieldStore.customField.data?.customList" :key="customFieldHeader.id">
        <!-- dropdown-->

        <div class="mt-6"
            v-if="(customFieldHeader.coreKeysId == 'ps-itm00007' || customFieldHeader.coreKeysId == 'ps-itm00002') && customFieldHeader.uiType.coreKeysId === 'uit00001' && customFieldHeader.isVisible === '1' && customFieldHeader.isDelete === '0'">
            <ps-label class="mb-1 text-base">{{ $t(customFieldHeader.name) }}</ps-label>
            <ps-dropdown align="left" class='lg:mt-2 mt-1  w-full' @onClick="loadCustomField(customFieldHeader.coreKeysId)">
                <template #select>

                    <ps-dropdown-select placeholderLang='item_list__all' border="border dark:border-feSecondary-200"
                        :showCenter="true" :selectedValue="customizeUiStoreList.data.filter((customizeDetail) => customizeDetail.id === customFieldHeader.coreKeysId)[0]?.provider?.customizeUiList.data?.filter((customField) => customField.id === itemProvider.form.product_relation[customFieldHeader.coreKeysId])[0]?.name

                            " />

                </template>
                <template #list>
                    <div class="rounded-md shadow-xs w-56 ">
                        <div class="pt-2 z-30 ">
                            <div
                                v-if="customizeUiStoreList.data.filter((customizeDetail) => customizeDetail.id === customFieldHeader.coreKeysId)[0]?.provider?.customizeUiList.data == null">
                                <ps-label class='p-2 flex' @click="loadCustomField(customFieldHeader.coreKeysId)">{{
                                    $t("item_entry__loading") }} </ps-label>
                            </div>
                            <div v-else>
                                <div class="w-72 flex py-4 px-2 hover:bg-fePrimary-50 dark:hover:bg-feAchromatic-800 cursor-pointer items-center"
                                    @click="selectCustomDropdown(customFieldHeader.coreKeysId, '')">
                                    <ps-label class="ms-2"
                                        :class="itemProvider.form.product_relation[customFieldHeader.coreKeysId] == '' ? ' font-medium' : 'font-light'">
                                        {{ $t("item_list__all") }} </ps-label>
                                </div>
                                <div v-for="selectData in customizeUiStoreList.data.filter((customizeDetail) => customizeDetail.id === customFieldHeader.coreKeysId)[0]?.provider?.customizeUiList.data"
                                    :key="selectData.coreKeysId"
                                    class="w-56 flex py-4 px-2 hover:bg-fePrimary-50 dark:hover:bg-fePrimary-900 cursor-pointer items-center"
                                    @click="selectCustomDropdown(customFieldHeader.coreKeysId, selectData.id)">

                                    <ps-label class="ms-2"
                                        :class="itemProvider.form.product_relation[customFieldHeader.coreKeysId] == selectData.id ? 'font-bold' : ''">
                                        {{ selectData.name }} </ps-label>

                                </div>
                            </div>
                        </div>
                    </div>
                </template>
                <template #loadmore>

                    <div class="mb-2 w-56">

                        <div v-if="customizeUiStoreList.data.filter((customizeDetail) => customizeDetail.id === customFieldHeader.coreKeysId)[0]?.provider?.customizeUiList.data != null
                            && customizeUiStoreList.data.filter((customizeDetail) => customizeDetail.id === customFieldHeader.coreKeysId)[0]?.provider?.loading.value == true"
                            class='mt-4 ms-4 flex'>
                            <ps-label> {{ $t("item_entry__loading") }}</ps-label>
                        </div>

                        <ps-label class="flex mt-4 ms-4 mb-2 underline font-bold cursor-pointer"
                            v-if="!customizeUiStoreList.data.filter((customizeDetail) => customizeDetail.id === customFieldHeader.coreKeysId)[0]?.provider?.isNoMoreRecord"
                            @click="loadCustomField(customFieldHeader.coreKeysId)"> {{ $t("item_entry__load_more") }}
                        </ps-label>
                    </div>

                </template>
            </ps-dropdown>
        </div>




    </div>
    <ps-loading-dialog ref="ps_loading_dialog" class='z-40' />
</template>

<script>
// Libs
import { ref, defineAsyncComponent, onMounted, onUnmounted, reactive } from 'vue';

// Components
import PsLabel from '@template1/vendor/components/core/label/PsLabel.vue';
const PsDropdown = defineAsyncComponent(() => import('@template1/vendor/components/core/dropdown/PsDropdown.vue'));
import PsDropdownSelect from '@template1/vendor/components/core/dropdown/PsDropdownSelect.vue';
import PsLoadingDialog from '@template1/vendor/components/core/dialog/PsLoadingDialog.vue';
// Providers
import { PsValueStore } from "@templateCore/store/modules/core/PsValueStore";
import { useCustomFieldStoreState } from '@templateCore/store/modules/customField/CustomFieldStore';
import { useCustomizeUiStoreState } from '@templateCore/store/modules/customField/CustomizeUiStore';
import { useProductStore } from "@templateCore/store/modules/item/ProductStore";
//Utils
import PsUtils from '../../../../../../../../../TemplateCore/utils/PsUtils';

export default {
    name: 'ItemFilterCustomFieldSection',
    components: {
        PsLabel,
        PsDropdown,
        PsDropdownSelect,
        PsLoadingDialog
    },
    setup() {
        const psValueStore = PsValueStore();
        const customFieldStore = useCustomFieldStoreState('list');
        const itemProvider = useProductStore('list');
        
        const customizeUiStoreList = reactive({
            data: [{
                'id': 'default',
                'provider': useCustomizeUiStoreState('default')
            }]
        });
        const ps_loading_dialog = ref();

        function loadCustomField(coreKeysId) {
            customizeUiStoreList.data.filter((customizeDetail) => customizeDetail.id === coreKeysId)[0].provider.loadCustomFieldList('1', coreKeysId);
        }

        async function selectCustomDropdown(coreKeysId, id) {
            itemProvider.form.product_relation[coreKeysId] = id;

            itemProvider.paramHolder.productRelation = [];

            let coreKeysIds = Object.keys(itemProvider.form.product_relation);
            coreKeysIds.forEach((coreKeysId) => {
                itemProvider.paramHolder.productRelation.push({
                    "core_keys_id": coreKeysId,
                    "value": itemProvider.form.product_relation[coreKeysId]
                })
            });

            ps_loading_dialog.value.openModal();
            PsUtils.addParamToCurrentUrl(itemProvider.getURLforListByParam(itemProvider.paramHolder));
            await itemProvider.resetProductList(psValueStore.getLoginUserId(), itemProvider.paramHolder);
            ps_loading_dialog.value.closeModal();
            //hide popup filter
            itemProvider.showPopUpFilter = false;
        }

        onMounted(async () => {
            await customFieldStore.loadCustomFieldList(psValueStore.getLoginUserId());

            for (const customField of customFieldStore.customField.data?.customList) {
                // for dropdown
                if (customField.isVisible == '1' && customField.isDelete == '0' && customField.uiType.coreKeysId == 'uit00001') {
                    customizeUiStoreList.data.push({
                        'id': customField.coreKeysId,
                        'provider': useCustomizeUiStoreState(customField.coreKeysId)
                    })
                }
            }
            const productRelation = itemProvider.paramHolder.productRelation;

            if (productRelation != null && productRelation.length > 0) {
                productRelation.forEach((customField) => {

                    itemProvider.form.product_relation[customField.core_keys_id] = customField.value;

                    loadCustomField(customField.core_keys_id);

                });
            }


        })
        onUnmounted(() => {
            customFieldStore.$reset;
            itemProvider.form.product_relation = {};

            for (const customField of customFieldStore.customField.data?.customList) {
                if (customField.isVisible == '1' && customField.isDelete == '0' && customField.uiType.coreKeysId == 'uit00001') {
                    customizeUiStoreList.data.filter((customizeDetail) => customizeDetail.id === customField.coreKeysId)[0]?.provider.$reset;
                }
            }
        })

        return {
            customFieldStore,
            customizeUiStoreList,
            itemProvider,
            loadCustomField,
            selectCustomDropdown,
            ps_loading_dialog
        }
    }

}

</script>
