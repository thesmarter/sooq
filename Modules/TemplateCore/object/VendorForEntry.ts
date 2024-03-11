import DefaultIcon from "./DefaultIcon";
import { PsObject } from "@templateCore/object/core/PsObject";
import UiType from "./UiType";
import DefaultPhoto from "./DefaultPhoto";

export default class VendorForEntry extends PsObject<VendorForEntry> {

    id : string = '';
    name : string = '';
    logo : DefaultPhoto = new DefaultPhoto();


    init(
        id : string,
        name : string,
        logo : DefaultPhoto,
     
    ) {
        this.id = id;
        this.name = name;
        this.logo = logo;

        return this;

    }

    getPrimaryKey(): string {
        return 'vendor';
    }

    toMap(object: VendorForEntry): any {
        const map = {};
        map['id'] = object.id;
        map['name'] = object.name;
        map['logo'] = new DefaultPhoto().toMap(object.logo);
        return map;
    }

    toMapList(objectList: VendorForEntry[]): any[] {
        const mapList: any[] = [];
        for (let i = 0; i < objectList.length; i++) {
            if (objectList[i] != null) {
                mapList.push(this.toMap(objectList[i]));
            }
        }

        return mapList;
    }

    fromMap(obj: any) {
        return new VendorForEntry().init(

            obj.id,
            obj.name,
            new DefaultPhoto().fromMap(obj.logo),
        );
    }

    fromMapList(objList: any[]): VendorForEntry[] {
        const productList: VendorForEntry[] = [];
        for (const obj of objList as Array<VendorForEntry>) {
            if (obj != null) {
                productList.push(this.fromMap(obj));
            }
        }

        return productList;
    }
}
