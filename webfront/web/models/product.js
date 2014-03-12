define(function(require) {
    //requirements
    var Model = require('kit/core/model'),
        compute = require('kit/utils/computeAttr'),
        numeral = require('numeral');

    return Model.extend({
        modelName: 'product',
        urlRoot: LH.baseApiUrl + '/products',
        defaults: {
            amount: 0,
            retailPricePreference: 'retailMarkup',
            rounding: {}
        },
        saveData: function(){

            var purchasePrice = numeral().unformat(this.get('purchasePrice')),
                retailPriceMin = numeral().unformat(this.get('retailPriceMin')),
                retailPriceMax = numeral().unformat(this.get('retailPriceMax')),
                retailMarkupMax = numeral().unformat(this.get('retailPriceMax')),
                retailMarkupMin = numeral().unformat(this.get('retailMarkupMin'));

            if (!purchasePrice && this.get('purchasePrice') !== '0'){
                purchasePrice = this.get('purchasePrice');
            }

            if (!retailPriceMin && this.get('retailPriceMin') !== '0'){
                retailPriceMin = this.get('retailPriceMin');
            }

            if (!retailPriceMax && this.get('retailPriceMax') !== '0'){
                retailPriceMax = this.get('retailPriceMax');
            }

            if (!retailMarkupMax && this.get('retailMarkupMax') !== '0'){
                retailMarkupMax = this.get('retailMarkupMax');
            }

            if (!retailMarkupMin && this.get('retailMarkupMin') !== '0'){
                retailMarkupMin = this.get('retailMarkupMin');
            }

            return {
                name: this.get('name'),
                units: this.get('units'),
                vat: this.get('vat'),
                purchasePrice: purchasePrice,
                retailPriceMin: retailPriceMin,
                retailPriceMax: retailPriceMax,
                retailMarkupMax: retailMarkupMax,
                retailMarkupMin: retailMarkupMin,
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
