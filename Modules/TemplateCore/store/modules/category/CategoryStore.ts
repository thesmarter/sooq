import { reactive,ref } from 'vue';

import CategoryListParameterHolder from '@templateCore/object/holder/CategoryListParameterHolder';
import PsApiService from '@templateCore/api/PsApiService';
import Category from '@templateCore/object/Category';
import PsResource from '@templateCore/api/common/PsResource';
import { defineStore, acceptHMRUpdate  } from 'pinia'
import { store } from '@templateCore/store/modules/core/PsStore';

import makeSeparatedStore from '@templateCore/store/modules/core/PsSepetetedStore';


export const useCategoryStoreState = makeSeparatedStore((key: string) =>
defineStore(`categoryStore/${key}`,
 () => {

    const isNoMoreRecord = reactive({
        value: false
    })
    const itemList = reactive<PsResource<Category[]>>(new PsResource());
    const item = reactive<PsResource<Category>>(new PsResource());
    const loading = reactive({
        value: false
    });

    let limit = ref(10);
    let offset: Number = 0;

    let id: string = "";
    let paramHolder = reactive<CategoryListParameterHolder>(new CategoryListParameterHolder().CategoryListParameterHolder());

    function hasData() {
        return itemList?.data != null && itemList!.data!.length > 0;
    }

    function updateCategoryList(responseData: PsResource<Category[]>) {
        if (itemList != null
            && itemList.data != null
            && itemList.data.length > 0
            && offset != 0) {

            if (responseData.data != null && responseData.data.length > 0) {

                if(responseData.data?.length < limit.value){
                    isNoMoreRecord.value = true;
                } else {
                    isNoMoreRecord.value = false;
                }

                itemList.data.push(...responseData.data);
            }else {
                isNoMoreRecord.value = true;
            }
            itemList.code = responseData.code;
            itemList.status = responseData.status;
            itemList.message = responseData.message;

        } else {

            if(responseData.data?.length < limit.value || responseData.data == null){
                isNoMoreRecord.value = true;
            } else {
                isNoMoreRecord.value = false;
            }

            itemList.code = responseData.code;
            itemList.status = responseData.status;
            itemList.message = responseData.message;
            itemList.data = responseData.data;

        }

        if (itemList != null && itemList.data != null) {
            offset = itemList.data.length;
        }

    }

    async function loadItemList(loginUserId:string, holder: CategoryListParameterHolder) {

        loading.value = true;

        const responseData = await PsApiService.getCategoryList<Category>(new Category(), limit.value, offset, loginUserId, holder.toMap());

        updateCategoryList(responseData);

        loading.value = false;


    }

    async function resetCategoryList(loginUserId:string,holder: CategoryListParameterHolder) {

        offset = 0;

        loading.value = true;

        const responseData = await PsApiService.getCategoryList<Category>(new Category(), limit.value, offset, loginUserId, holder.toMap());

        updateCategoryList(responseData);

        loading.value = false;

    }

    return{
        isNoMoreRecord,
        itemList,
        item,
        loading,
        limit,
        offset,
        id,
        paramHolder,
        updateCategoryList,
        loadItemList,
        resetCategoryList,
        hasData
    }

}),
);

// if (import.meta.hot) {
//     import.meta.hot.accept(acceptHMRUpdate(useCategoryStoreState, import.meta.hot))
//   }

// Need to be used outside the setup
export function useCategoryStoreStateWithOut() {
    return useCategoryStoreState(store);
  }
