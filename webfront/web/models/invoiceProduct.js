define(function(require) {
    //requirements
    var Model = require('kit/model');

    return Model.extend({
        modelName: 'invoiceProduct',
        urlRoot: function() {
            return LH.baseApiUrl + '/invoices/' + this.get('invoice').id + '/products';
        },
        saveData: [
            'product',
            'quantity',
            'price'
        ]
    });
});