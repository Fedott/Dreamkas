define(function(require, exports, module) {
    //requirements
    var Modal = require('blocks/modal/modal');

    return Modal.extend({
        template: require('ejs!./template.ejs'),
        warningForDeletedSupplier: 'Операция для удаленного поставщика.',
        warningForDeletedStore: 'Операция для удаленного магазина.',
        itemId: null,
		Form: null,
		Form_products: null,
		Model: null,
        deleted: false,
        deletedTitle: 'Удаление прошло успешно',
        removeButtonText: 'Удалить операцию',
        isStoreDeleted: false,
        isSupplierDeleted: false,
        isFormDisabled: false,
        initialize: function(){

            Modal.prototype.initialize.apply(this, arguments);

            var block = this,
                supplier = block.model.get('supplier'),
                store = block.model.get('store');

            block.isSupplierDeleted = false;
            block.isStoreDeleted = false;
            block.isFormDisabled = false;

            if (supplier && supplier.deletedAt) {
                block.isSupplierDeleted = true;
            }

            if (store && store.deletedAt) {
                block.isStoreDeleted = true;
            }

            if (block.isStoreDeleted || block.isSupplierDeleted){
                block.isFormDisabled = true;
            }

        },
        partials: {
            deleted: require('ejs!./deleted.ejs')
        },
        model: function(){
            var Model = this.Model;

            if (this.itemId && this.itemId != 0){
                return PAGE.collections.stockMovements.get(this.itemId);
            } else {
                return new Model;
            }

        },
        events: {
            'click .form_stockMovement__removeLink': function(e){
                var block = this;

                e.target.classList.add('loading');

                block.model.destroy().then(function() {
                    e.target.classList.remove('loading');
                });
            }
        },
        blocks: {
            form: function(options){
                var Form = this.Form;

                options.model = this.model;

                return new Form(options);
            },
            form_products: function(){
                var Form_products = this.Form_products;

                return new Form_products({
                    collection: this.model.collections.products
                });
            },
            form_store: require('blocks/form/store/store'),
            removeButton: function(options){

                var block = this,
                    RemoveButton = require('blocks/removeButton/removeButton');

                return new RemoveButton(_.extend({
                    model: block.model
                }, options));
            }
        }
    });
});