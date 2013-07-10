define(function(require) {
        //requirements
        var BaseModel = require('models/baseModel');

        return BaseModel.extend({
            modelName: 'catalogSubcategory',
            urlRoot: LH.baseApiUrl + '/subcategories',
            parentGroupModel: {},
            saveFields: [
                'name',
                'category'
            ],
            initialize: function(attrs, options){

                BaseModel.prototype.initialize.apply(this, arguments);

                if (!this.get('category')){
                    if (this.collection && this.collection.parentCategoryModel){
                        this.set('category', this.collection.parentCategoryModel.id);
                    }
                }
            },
            parse: function(response, options){
                var data = BaseModel.prototype.parse.apply(this, arguments);

                if (typeof data.category == 'object'){
                    data.category = data.category.id;
                }

                return data;
            }
        });
    }
);