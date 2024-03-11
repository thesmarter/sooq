export default class TouchCountParameterHolder {

    typeId: string = '';
    typeName: string = '';
    userId: string = '';

    TouchCountParameterHolder() {
        this.typeId = '';
        this.typeName = '';
        this.userId = '';
    }

    toMap(): {} {
        const map = {};
        map['type_id'] = this.typeId;
        map['type_name'] = this.typeName;
        map['user_id'] = this.userId;

        return map;
    }
}
