import { reactive,ref } from 'vue';
import PsApiService from '@templateCore/api/PsApiService';
import PsResource from '@templateCore/api/common/PsResource';
import VendorInfo from '@templateCore/object/VendorInfo';
import { PsValueStore } from '@templateCore/store/modules/core/PsValueStore';
import AppInfoParameterHolder from '@templateCore/object/holder/AppInfoParameterHolder';
import PsConst from '@templateCore/object/constant/ps_constants';
import { defineStore } from 'pinia'

export const useVendorInfoStoreState = defineStore('VendorInfoStore',
 () => {
    let vendorInfo = reactive<PsResource<VendorInfo>>(new PsResource());
    let loading = reactive({
        value: false
    });

    let id: string = "";
    let realStartDate: string = '0';
    let realEndDate: string = '0';
    const psValueStore = PsValueStore();
    // const loginUserId = psValueStore.getLoginUserId();

    async function loadVendorInfo(vendorId: String) {

        loading.value = true;
        let loginUserId = psValueStore.getLoginUserId();

        const response = await PsApiService.getVendorInfo<VendorInfo>(new VendorInfo(), vendorId, loginUserId);
        console.log(response);
        vendorInfo.data = response.data;
        vendorInfo.code = response.code;
        vendorInfo.status = response.status;
        vendorInfo.message = response.message;

        loading.value = false;

        return vendorInfo;

    }

    function isPaypalEnabled(){
        return vendorInfo?.data?.vendorPaypalEnabled == PsConst.ONE ;
    }
    function isPaystackEnabled(){
        return vendorInfo?.data?.vendorPaystackEnabled == PsConst.ONE ;
    }
    function isRazorEnabled(){
        return vendorInfo?.data?.vendorRazorEnabled == PsConst.ONE ;
    }
    function isStripeEnabled(){
        return vendorInfo?.data?.vendorStripeEnabled == PsConst.ONE ;
    }

    return{
        vendorInfo,
        loading,
        id,
        realStartDate,
        realEndDate,
        loadVendorInfo,
        isPaypalEnabled,
        isPaystackEnabled,
        isRazorEnabled,
        isStripeEnabled,
    }

})
