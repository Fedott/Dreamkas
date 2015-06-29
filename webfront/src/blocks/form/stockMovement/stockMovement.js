define(function(require, exports, module) {
    //requirements
    var Form = require('blocks/form/form');

    return Form.extend({
        collection: function() {
            return PAGE.get('collections.stockMovements');
        },
        collections: {
            stores: function() {
                return PAGE.get('collections.stores');
            },
            suppliers: function(){
                return PAGE.get('collections.suppliers');
            }
        },
        blocks: {
            inputDate: require('blocks/inputDate/inputDate'),
            select_store: function(options) {
                var block = this,
                    StoreSelect = require('blocks/select/store/store'),
                    stores = block.collections.stores,
                    store = block.model.get('store');

                if (store && !stores.findWhere({ id: store.id })) {

                    stores = stores.clone();
                    stores.add(store);

                    block.listenTo(block.collections.stores, {
                        'add': function(store) {
                            stores.add(store);
                        }
                    });
                }

                options.collection = stores;

                return new StoreSelect(options);
            },
            select_supplier: function(options) {
                var block = this,
                    SupplierSelect = require('blocks/select/supplier/supplier'),
                    suppliers = block.collections.suppliers,
                    supplier = block.model.get('supplier');

                if (supplier && !suppliers.findWhere({ id: supplier.id })) {

                    suppliers = suppliers.clone();
                    suppliers.add(supplier);

                    block.listenTo(block.collections.suppliers, {
                        'add': function(supplier) {
                            suppliers.add(supplier);
                        }
                    });
                }

                options.collection = suppliers;

                return new SupplierSelect(options);
            }
        },
        initialize: function() {
            var block = this;

            Form.prototype.initialize.apply(block, arguments);

            block.listenTo(block.model.collections.products, {
                'add': function() {
                    block.removeErrors();
                }
            });
        }
    });
});