type SubCategoryScribeParameterHolderInterface = {
    userId: string;
    catId: string;
    subCatIds: string[];
}

export default class SubCategoryScribeParameterHolder implements SubCategoryScribeParameterHolderInterface{

    userId: string = '';
    catId: string = '';
    subCatIds: string[] = [];
    
    SubCategoryScribeParameterHolder() {
        this.userId = '';
        this.catId = '';
        this.subCatIds = [];

        return this;
    }

    toMap(): {} {
        const map = {
            "user_id": this.userId,
            "cat_id": this.catId,
            "sub_cat_ids": this.subCatIds
        }

        return map;
    }
}