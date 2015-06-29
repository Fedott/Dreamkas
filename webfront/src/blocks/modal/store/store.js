define(function(require, exports, module) {
    //requirements
    var Modal = require('blocks/modal/modal');

    return Modal.extend({
        id: 'modal_store',
        storeId: 0,
        template: require('ejs!./template.ejs'),
        models: {
            store: function(){
                var StoreModel = require('resources/store/model');

                if (this.storeId != 0){
                    return PAGE.get('collections.stores').get(this.storeId);
                } else {
                    return new StoreModel;
                }
            }
        },
        blocks: {
            form_store: require('blocks/form/store/store')
        }
    });
});