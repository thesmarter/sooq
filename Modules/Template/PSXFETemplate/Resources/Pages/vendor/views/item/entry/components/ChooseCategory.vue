<template>
    <div class="text-center sm:mt-32 lg:mt-36 mt-28 w-mobile sm:w-median lg:w-large mx-auto mb-10" >
    <ps-label class="font-semibold text-4xl">{{ $t("category__choose") }}</ps-label>
    <ps-label class="text-xl text-gray-600 mt-5">{{ $t("category__choose_description") }}</ps-label>

    <div v-if="categoryStore.itemList?.data != null" class="mt-8 grid md:grid-cols-2 sm:grid-cols-1 gap-4">
      <div v-for="category in categoryStore.itemList.data" :key="category.catId" class="p-3">
        <div class="w-full h-62 bg-feSecondary-50 dark:bg-feAchromatic-900 p-6 relative flex items-center rounded-lg border-2 border-transparent hover:border-fePrimary-500"
        @click="clickCategory(category.catId,category.catName)">
            <div class="w-16 h-16 rounded-full shadow-md border-2 border-white overflow-hidden ">
                <img class="transform transition duration-500 hover:scale-110 w-full h-full object-cover "
                 v-lazy="{ src: $page.props.thumb2xUrl + '/' + category.defaultPhoto.imgPath,
                     error: $page.props.sysImageUrl+'/default_photo.png' }"
                 alt="">
            </div>
            <ps-label class="text-lg ml-6 font-bold">{{ category.catName }}</ps-label>
        </div>
      </div>
    </div>
    </div>

    <ps-loading-dialog ref="ps_loading_dialog" />

</template>

<script>

import { useCategoryStoreState } from "@templateCore/store/modules/category/CategoryStore";
import {  ref,onMounted } from 'vue';
import PsIcon from '@template1/vendor/components/core/icons/PsIcon.vue';

import PsLabel from '@template1/vendor/components/core/label/PsLabel.vue';
import PsLoadingDialog from '@template1/vendor/components/core/dialog/PsLoadingDialog.vue';
import { PsValueStore } from "@templateCore/store/modules/core/PsValueStore";
import { router } from '@inertiajs/vue3';

    export default {
        name : "ChooseCategory",
        components : {
            PsLabel,
            PsIcon,
            PsLoadingDialog
        },
        props :['mobileSetting'],
        setup (props){

        const categoryStore = useCategoryStoreState('cat');

        categoryStore.limit = props.mobileSetting?.default_loading_limit ?? 12;

        const ps_loading_dialog = ref();
        const loading = ref(false);
        const psValueStore = PsValueStore();
        const loginUserId = psValueStore.getLoginUserId();

        const initial = ref(true);

        setTimeout(async ()=>{

            await loadDataList();

        },1000);

        async function loadDataList() {

            loading.value = true;

            await categoryStore.resetCategoryList(loginUserId, categoryStore.paramHolder);

            ps_loading_dialog.value.closeModal();
            loading.value = false;
            initial.value = false;
        }

        function clickCategory(categoryId,categoryName) {
            router.get(route('fe_item_entry',{'categoryId': categoryId , 'categoryName' : categoryName}));
        }

        onMounted(() => {
            if(initial.value == true && categoryStore.itemList?.data == null){
                ps_loading_dialog.value.openModal();
            }
        })

        return {
            categoryStore,
            ps_loading_dialog,
            loading,
            clickCategory,
            initial
        }
    },

    }
</script>
