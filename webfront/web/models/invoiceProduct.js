define(function(require) {
    //requirements
    var Model = require('kit/core/model'),
        compute = require('kit/utils/computeAttr'),
        currentUserModel = require('models/currentUser'),
        numeral = require('libs/numeral');

    var templates = {
        amount: require('tpl!blocks/amount/amount.html')
    };

    return Model.extend({
        modelName: 'invoiceProduct',
        urlRoot: function() {
            return LH.baseApiUrl + '/stores/' + currentUserModel.stores.at(0).id + '/invoices/' + this.get('invoice').id + '/products';
        },
        saveData: function(){
            return {
                product: this.get('product'),
                quantity: numeral().unformat(this.get('quantity')),
                priceEntered: numeral().unformat(this.get('priceEntered'))
            }
        },
        defaults: {
            quantityElement: compute(['quantity'], function(quantity){
                return templates.amount({value: quantity});
            }),
            productTotalAmountVATFormatted: compute(['totalAmountVAT'], function(totalAmountVAT){
                return LH.formatMoney(totalAmountVAT) + ' р.'
            })
        }
    });
});