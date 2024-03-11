import { PsObject } from "./core/PsObject";

export default class VendorInfo extends PsObject<VendorInfo>{

    vendorStripeEnabled: string = '';
    vendorPaypalEnabled: string = '';
    vendorRazorEnabled: string = '';
    vendorPaystackEnabled: string = '';
    vendorRazorKey: string = '';
    vendorStripePublishableKey: string = '';
    vendorPaystackKey: string = '';

    init(
        vendorStripeEnabled: string,
        vendorPaypalEnabled: string,
        vendorRazorEnabled: string,
        vendorPaystackEnabled: string,
        vendorRazorKey: string,
        vendorStripePublishableKey: string,
        vendorPaystackKey: string,

    ) {
        this.vendorStripeEnabled = vendorStripeEnabled;
        this.vendorPaypalEnabled = vendorPaypalEnabled;
        this.vendorRazorEnabled = vendorRazorEnabled;
        this.vendorPaystackEnabled = vendorPaystackEnabled;
        this.vendorRazorKey = vendorRazorKey;
        this.vendorStripePublishableKey = vendorStripePublishableKey;
        this.vendorPaystackKey = vendorPaystackKey

        return this;

    }

    getPrimaryKey(): string {
        return '';
    }


    fromMap(obj: any) {
        return new VendorInfo().init(
         obj.vendor_stripe_enabled,
         obj.vendor_paypal_enabled,
         obj.vendor_razor_enabled,
         obj.vendor_paystack_enabled,
         obj.vendor_razor_key,
         obj.vendor_stripe_publishable_key,
         obj.vendor_paystack_key,
        );
    }


    fromMapList(objList : any[] ) : VendorInfo[] {
        const vendorInfoList : VendorInfo[] = [];
        for(const obj in objList) {
            if(obj != null) {
                vendorInfoList.push(this.fromMap(obj));
            }
        }
        return vendorInfoList;
    }


    toMap(object: VendorInfo): any {
        const map = {};
        map['vendor_stripe_enabled'] = object.vendorStripeEnabled;
        map['vendor_paypal_enabled'] = object.vendorPaypalEnabled;
        map['vendor_razor_enabled'] = object.vendorRazorEnabled;
        map['vendor_paystack_enabled'] = object.vendorPaystackEnabled;
        map['vendor_razor_key'] = object.vendorRazorKey;
        map['vendor_stripe_publishable_key'] = object.vendorStripePublishableKey;
        map['vendor_paystack_key'] = object.vendorPaystackKey;

        return map;
    }

    toMapList(objectList: VendorInfo[]) : any[] {
        const mapList : any[] = [];
        for(let i = 0; i < objectList.length; i++) {
            if(objectList[i] != null) {
                mapList.push(this.toMap(objectList[i]));
            }
        }
        return mapList;
    }


}
