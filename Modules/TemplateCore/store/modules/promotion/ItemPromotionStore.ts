import { reactive, ref } from 'vue';
import PsApiService from '@templateCore/api/PsApiService';
import PsResource from '@templateCore/api/common/PsResource';
import ItemPaidHistoryParameterHolder from '@templateCore/object/holder/ItemPaidHistoryParameterHolder';
import ItemPaidHistory from '@templateCore/object/ItemPaidHistory';

import { defineStore  } from 'pinia'
import makeSeparatedStore from '@templateCore/store/modules/core/PsSepetetedStore';

export const useItemPromotionStoreState = makeSeparatedStore((key: string) =>
defineStore(`itemPromotionStore/${key}`,
 () => {

    const reportedReportedItemList = reactive<PsResource<ItemPaidHistory>>(new PsResource());
    const paiditem = reactive<PsResource<ItemPaidHistory>>(new PsResource());
    const loading = reactive({
        value: false
    });

    let limit = ref(10);
    let offset: Number = 0;

    let id: string = "";

    async function postItemPromotion(holder:ItemPaidHistoryParameterHolder, loginUserId) : Promise<PsResource<ItemPaidHistory>>  {

        loading.value = true;

        paiditem.data = await PsApiService.postItemPromotion<ItemPaidHistory>(new ItemPaidHistory(), loginUserId, holder.toMap());

        loading.value = false;

        return paiditem;

    }

    return {
        reportedReportedItemList,
        paiditem,
        loading,
        limit,
        offset,
        id,
        postItemPromotion,
    }

}),
);
