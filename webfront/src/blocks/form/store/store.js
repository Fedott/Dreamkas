define(function(require, exports, module) {
    //requirements
    var Form = require('blocks/form/form');

    return Form.extend({
        template: require('ejs!./template.ejs'),
        id: 'form_store',
        blocks: {
            removeButton: function(){

                var block = this,
                    RemoveButton = require('blocks/removeButton/removeButton');

                return new RemoveButton({
                    model: block.model,
                    removeText: 'Удалить магазин'
                });
            }
        },
        model: function() {
            var StoreModel = require('resources/store/model');

            if (this.storeId != 0){
                return PAGE.get('collections.stores').get(this.storeId);
            } else {
                return new StoreModel;
            }
        },
        collection: function() {
            return PAGE.get('collections.stores');
        }
    });
});