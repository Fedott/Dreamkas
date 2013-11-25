define(function(require) {
    //requirements
    var Model = require('kit/core/model'),
        compute = require('kit/utils/computeAttr');

    return Model.extend({
        modelName: 'product',
        urlRoot: LH.baseApiUrl + '/products',
        defaults: {
            inventory: 0,
            retailPricePreference: 'retailMarkup',
            rounding: {}
        },
        saveData: function(){
            return {
                name: this.get('name'),
                units: this.get('units'),
                vat: this.get('vat'),
                purchasePrice: this.get('purchasePrice'),
                retailPriceMin: this.get('retailPriceMin'),
                retailPriceMax: this.get('retailPriceMax'),
                retailMarkupMax: this.get('retailMarkupMax'),
                retailMarkupMin: this.get('retailMarkupMin'),
                retailPricePreference: this.get('retailPricePreference'),
                barcode: this.get('barcode'),
                sku: this.get('sku'),
                vendorCountry: this.get('vendorCountry'),
                vendor: this.get('vendor'),
                info: this.get('info'),
                subCategory: this.get('subCategory'),
                rounding: this.get('rounding') ? this.get('rounding').name : null
            }
        },
        parse: function(response, options) {
            var data = Model.prototype.parse.apply(this, arguments);

            if (data.product){
                data = data.product;
            }

            if (typeof data.subCategory == 'object') {
                data.group = data.subCategory.category.group;
                data.category = data.subCategory.category;
            }

            return data;
        }
    });
});
