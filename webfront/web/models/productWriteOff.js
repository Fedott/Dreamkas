define(function(require) {
    //requirements
    var Model = require('kit/core/model'),
        computeAttr = require('kit/utils/computeAttr'),
        currentUserModel = require('models/currentUser');

    require('moment');

    return Model.extend({
        modelName: 'productInvoice',
        urlRoot: function() {
            return LH.baseApiUrl + '/stores/' + currentUserModel.stores.at(0).id + '/products/' + this.get('product').id + '/writeOffProducts';
        },
        defaults: {
            acceptanceDateFormatted: computeAttr(['acceptanceDate'], function(acceptanceDate){
                return moment(acceptanceDate).format('DD.MM.YYYY');
            }),
            totalPriceFormatted: computeAttr(['totalPrice'], function(totalPrice){
                return LH.formatPrice(totalPrice);
            }),
            priceFormatted: computeAttr(['price'], function(price){
                return LH.formatPrice(price);
            })
        }
    });
});