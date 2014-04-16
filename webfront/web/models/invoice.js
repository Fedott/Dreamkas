define(function(require) {
    //requirements
    var Model = require('kit/core/model'),
        cookies = require('cookies'),
        InvoiceProductsCollection = require('collections/invoiceProducts');

    return Model.extend({
        storeId: null,
        urlRoot: function() {
            return LH.baseApiUrl + '/stores/' + this.storeId + '/invoices'
        },
        defaults: {
            supplier: null,
            acceptanceDate: null,
            accepter: null,
            legalEntity: null,
            includesVAT: true,
            supplierInvoiceNumber: null,
            products: null,
            order: null
        },
        saveData: function(){
            var supplier = this.get('supplier');
            if (supplier instanceof Object) {
                supplier = supplier.id;
            }
            return {
                supplier: supplier,
                acceptanceDate: this.get('acceptanceDate'),
                accepter: this.get('accepter'),
                legalEntity: this.get('legalEntity'),
                includesVAT: this.get('includesVAT'),
                supplierInvoiceNumber: this.get('supplierInvoiceNumber'),
                products: this.get('products').map(function(productModel) {
                    return productModel.getData();
                })
            }
        },
        parse: function(data) {

            if (!this.get('products')){
                this.set('products', new InvoiceProductsCollection());
            }

            this.get('products').reset(data.products);

            delete data.products;

            return data;
        },
        validateProducts: function(){
            var model = this;

            return model.save(null, {
                url: this.url() + '?validate=1&validationGroups=products'
            });
        }
    });
});