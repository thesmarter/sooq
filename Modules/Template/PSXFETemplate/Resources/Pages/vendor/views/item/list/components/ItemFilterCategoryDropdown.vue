<template>
    <!-- Category -->
    <ps-label class="mt-6 lg:text-base font-medium text-sm" textColor="text-feSecondary-800 dark:text-feSecondary-300">
        {{ $t("category_list__title") }} </ps-label>
    <div class="rounded-md shadow-xs w-56 ">
        <div class="pt-2 z-30 w-full">
            <div v-if="categoryStore.itemList.data == null">
                <ps-label class='p-2 flex' @click="loadCategory"> {{ $t("list__loading") }} </ps-label>
            </div>
            <div v-else>
                <div v-for="selectData in categoryStore.itemList.data" :key="selectData.catId" class="py-2 px-1"
                    >
                    <!-- category dropdown start -->
                    <div class="relative text-left text-sm" >
        <button class="w-full bg-transparent">
         <div class=" flex flex-row justify-between items-center overflow-hidden ">
            <ps-label @click="categoryFilterClicked(selectData,false)" >{{ selectData.catName }}</ps-label>
        <ps-icon v-if="!isDropdownOpen[selectData.catId]" name="rightChervon" w="20" h="20" @click="loadSubCategory(selectData.catId),categoryFilterClicked(selectData,true)"
            class="ltr:mr-3 rtl:ml-3  ltr:block hidden text-feSecondary-500 dark:text-feSecondary-100" />
        <ps-icon v-if="!isDropdownOpen[selectData.catId]" name="leftChervon" w="20" h="20" @click=" loadSubCategory(selectData.catId),categoryFilterClicked(selectData,true)"
            class="ltr:mr-3 rtl:ml-3 rtl:block hidden text-feSecondary-500 dark:text-feSecondary-100" />
        <ps-icon v-else name="downChervon" w="20" h="20" @click="loadSubCategory(selectData.catId),categoryFilterClicked(selectData,true)"
            class="ltr:mr-3 rtl:ml-3 text-feSecondary-500 dark:text-feSecondary-100" />
    </div>
</button>
</div>
            <!-- subCategory -->
<div v-if="isDropdownOpen[selectData.catId]" class=" w-full overflow-hidden ">
    <div class="z-30 ">

<div v-if="subCategoryStore.subCategoryList.data == null">
    <ps-label class='p-2 flex'> {{ $t("list__loading") }} </ps-label>
</div>
<div v-else>
    <div class="mt-2 px-2">
        <div v-for="selectSubCat in subCategoryStore.subCategoryList.data"
            :key="selectSubCat.id" class="w-full flex py-3  cursor-pointer "
            @click="subCategoryFilterClicked(selectSubCat)">
            <ps-label class="text-sm font-medium  text-ellipsis overflow-hidden"
                :textColor="selectSubCat.id === itemProvider.paramHolder.subCatId ? 'text-fePrimary-500' : ''">
                {{ selectSubCat.name }}</ps-label>
        </div>
    </div>

</div>
</div>
</div>
    </div> 
   <div class="mb-2 w-56">
                    <!-- viewMore -->
                    <div v-if="categoryStore.itemList.data != null
                        && categoryStore.loading.value == true" class='mt-4 ms-4 flex'>
                        <ps-label-caption> {{ $t("list__loading") }} </ps-label-caption>
                    </div>
                    <ps-label v-if="categoryStore.loading.value == false"
                        :class="categoryStore.isNoMoreRecord.value ? 'hidden' : ''"
                        class="flex py-2  text-sm cursor-pointer " textColor="text-fePrimary-500"
                        @click.stop="loadCategory">{{ $t("filter_search_view_more") }}</ps-label>
                </div>
            </div>
        </div>

    </div>



    <ps-loading-dialog ref="ps_loading_dialog" class='z-40' />
</template>

<script>
// Libs
import { ref, defineAsyncComponent, onMounted } from 'vue';
// Components
import PsLabel from '@template1/vendor/components/core/label/PsLabel.vue';
import PsDropdownSelect from '@template1/vendor/components/core/dropdown/PsDropdownSelect.vue';
import PsLabelCaption from '@template1/vendor/components/core/label/PsLabelCaption.vue';
import PsLoadingDialog from '@template1/vendor/components/core/dialog/PsLoadingDialog.vue';
// Providers
import { PsValueStore } from "@templateCore/store/modules/core/PsValueStore";
import { useCategoryStoreState } from "@templateCore/store/modules/category/CategoryStore";
import { useSubCategoryStoreState } from '@templateCore/store/modules/subCategory/SubCategoryStore';
import { useProductStore } from "@templateCore/store/modules/item/ProductStore";
//Utils
import PsUtils from '../../../../../../../../../TemplateCore/utils/PsUtils';
import PsIcon from '../../../../../../../../../../resources/js/Components/Core/Icons/PsIcon.vue';

export default {
    name: 'ItemFilterCategoryDropdown',
    components: {
        PsLabel,
        PsDropdownSelect,
        PsLabelCaption,
        PsLoadingDialog,
        PsIcon
    },
    setup() {
        const psValueStore = PsValueStore();
        const categoryStore = useCategoryStoreState('cat');
        const subCategoryStore = useSubCategoryStoreState();
        const itemProvider = useProductStore('list');
        const ps_loading_dialog = ref();
        const isDropdownOpen = ref({});


        function loadCategory() {
            categoryStore.loadItemList(psValueStore.getLoginUserId(), categoryStore.paramHolder);
        }
        onMounted(() => {
            loadCategory();
            if(itemProvider.paramHolder.catId !==null){
                loadSubCategory(itemProvider.paramHolder.catId)
            }

        })
       async function loadSubCategory(catId) {
            
          await  subCategoryStore.resetSubCategoryList(catId);
          clickDropDown(catId);
            // itemProvider.showPopUpFilter = true;
           
        }
        function clickDropDown(catId) {
        isDropdownOpen.value = {
        ...Object.fromEntries(Object.entries(isDropdownOpen.value).map(([id, _]) => [id, false])),
        [catId]: !isDropdownOpen.value[catId],
    
      };
     
    }
        async function subCategoryFilterClicked(value) {
     
            console.log("hello there")
            console.log(value);
            itemProvider.paramHolder.subCatId = value.id;
            itemProvider.paramHolder.subCatName = value.name;
            ps_loading_dialog.value.openModal();
            PsUtils.addParamToCurrentUrl(itemProvider.getURLforListByParam(itemProvider.paramHolder));
            await itemProvider.resetProductList(psValueStore.getLoginUserId(), itemProvider.paramHolder);
            ps_loading_dialog.value.closeModal();
            //hide popup filter
            itemProvider.showPopUpFilter = false;
            console.log( itemProvider.itemList.data);

        }

        async function categoryFilterClicked(value,showPopUpFilter) {

            itemProvider.paramHolder.catId = value.catId;
            itemProvider.paramHolder.catName = value.catName;
            itemProvider.paramHolder.subCatId = '';
            itemProvider.paramHolder.subCatName = '';

            PsUtils.addParamToCurrentUrl(itemProvider.getURLforListByParam(itemProvider.paramHolder));

            ps_loading_dialog.value.openModal();
            await itemProvider.resetProductList(psValueStore.getLoginUserId(), itemProvider.paramHolder);
            subCategoryStore.resetSubCategoryList(value.catId);
            ps_loading_dialog.value.closeModal();
            // clickDropDown(value.catId)
            //hide popup filter
            itemProvider.showPopUpFilter = showPopUpFilter;

        }

      
        return {
            categoryStore,
            itemProvider,
            loadCategory,
            categoryFilterClicked,
            ps_loading_dialog,
            loadSubCategory,
            subCategoryStore,
            subCategoryFilterClicked,
            isDropdownOpen,
            clickDropDown,
            
          
        }
    }

}

</script>

