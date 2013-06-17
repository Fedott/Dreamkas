define(function(require) {
    //requirements
    var BaseModel = require('models/baseModel');

    return BaseModel.extend({
            modelName: 'invoiceProduct',
            urlRoot: function(){
                return LH.baseApiUrl + '/invoices/'+ this.get('invoice').id  + '/products';
            },
            saveFields: [
                'product',
                'quantity',
                'price'
            ]
        });
    });