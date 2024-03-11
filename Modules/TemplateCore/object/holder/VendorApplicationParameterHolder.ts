export default class VendorApplicationParameterHolder {

    id : string = '' ;
    email : string = '' ;
    vendorName : string = '' ;
    document : any[] = [] ;
    coverLetter : string = '' ;

    VendorApplicationParameterHolder() {
        this.id = '' ;
        this.email = '' ;
        this.vendorName = '' ;
        this.document = [] ;
        this.coverLetter = '' ;
    }

    resetParameterHolder() : VendorApplicationParameterHolder{
        this.id = '' ;
        this.email = '' ;
        this.vendorName = '' ;
        this.document = [] ;
        this.coverLetter = '' ;

        return this;
    }

    toMap(): {} {
        const map = {};
        map['id'] = this.id;
        map['email'] = this.email;
        map['store_name'] = this.vendorName;
        map['document'] = this.document;
        map['cover_letter'] = this.coverLetter;

        return map;
    }
}
