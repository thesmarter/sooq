import PsApiService from '@templateCore/api/PsApiService';
import SubCategory from '@templateCore/object/SubCategory';
import PsResource from '@templateCore/api/common/PsResource';
import SubCategoryListParameterHolder from '@templateCore/object/holder/SubCategoryListParameterHolder';
import { defineStore  } from 'pinia'
import { reactive,ref } from 'vue';

import makeSeparatedStore from '@templateCore/store/modules/core/PsSepetetedStore';


export const useSubCategoryStoreState = makeSeparatedStore((key: string) =>
defineStore(`subCategory/${key}`,
 () => {

    const isNoMoreRecord = reactive({
        value: false
    })
    const subCategoryList = reactive<PsResource<SubCategory[]>>(new PsResource());
    let catId = '';
    const loading = reactive({
        value : false
    });

    let limit = ref(30);
    let offset: Number = 0;

    const paramHolder = reactive<SubCategoryListParameterHolder>(new SubCategoryListParameterHolder().SubCategoryListParameterHolder());

    function updateSubCategoryList(responseData : PsResource<SubCategory[]>) {

        if(subCategoryList != null
            && subCategoryList.data != null
            && subCategoryList.data.length > 0
            && offset != 0) {

            if(responseData.data != null && responseData.data.length > 0) {
                if(responseData.data?.length < limit.value){
                    isNoMoreRecord.value = true;
                } else {
                    isNoMoreRecord.value = false;
                }
                subCategoryList.data.push(...responseData.data);
            }else {
                isNoMoreRecord.value = true;
            }

            subCategoryList.code = responseData.code;
            subCategoryList.status = responseData.status;
            subCategoryList.message = responseData.message;

        }else {
            if(responseData.data?.length < limit.value || responseData.data == null){
                isNoMoreRecord.value = true;
            } else {
                isNoMoreRecord.value = false;
            }
            subCategoryList.data = responseData.data;
            subCategoryList.code = responseData.code;
            subCategoryList.status = responseData.status;
            subCategoryList.message = responseData.message;

        }

        if(subCategoryList != null && subCategoryList.data != null ) {
            offset = subCategoryList.data.length;
        }

    }

    async function loadSubCategoryList(catId: string) {

        if(catId != catId) {
            const tempResoirce = new PsResource();
            subCategoryList.data = tempResoirce.data;
            subCategoryList.code = tempResoirce.code;
            subCategoryList.status = tempResoirce.status;
            subCategoryList.message = tempResoirce.message;
            offset = 0;
        }

        catId = catId.toString();

        loading.value = true;

        const holder = new SubCategoryListParameterHolder();
        holder.catId = catId;

        const responseData = await PsApiService.searchSubCategoryList<SubCategory>(new SubCategory(), '1', limit.value, offset, holder.toMap());

        loading.value = false;

        updateSubCategoryList(responseData);


    }

    async function resetSubCategoryList(catId: string) {

        if(catId != catId) {
            const tempResoirce = new PsResource();
            subCategoryList.data = tempResoirce.data;
            subCategoryList.code = tempResoirce.code;
            subCategoryList.status = tempResoirce.status;
            subCategoryList.message = tempResoirce.message;
        }

        offset = 0;

        catId = catId.toString();

        loading.value = true;

        const holder = new SubCategoryListParameterHolder();
        holder.catId = catId;

        const responseData = await PsApiService.searchSubCategoryList<SubCategory>(new SubCategory(), '1', limit.value, offset, holder.toMap());

        updateSubCategoryList(responseData);

        loading.value = false;

    }

    function filtersubCatUpdate(loginUserId:string,holder: SubCategoryListParameterHolder) {
        resetSearchSubCategoryList(loginUserId,holder);
    }

    async function resetSearchSubCategoryList(loginUserId:string,holder: SubCategoryListParameterHolder) {

        offset = 0;

        loading.value = true;

        const responseData = await PsApiService.searchSubCategoryList<SubCategory>(new SubCategory(), loginUserId, limit.value, offset, holder.toMap());

        updateSubCategoryList(responseData);

        loading.value = false;

    }
    return{
        isNoMoreRecord,
        subCategoryList,
        catId,
        loading,
        limit,
        offset,
        paramHolder,
        loadSubCategoryList,
        resetSubCategoryList,
        filtersubCatUpdate,
        resetSearchSubCategoryList

    }


}),
);
