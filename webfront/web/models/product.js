define(function(require) {
    //requirements
    var Model = require('kit/model'),
        BarcodesCollections = require('collections/barcodes'),
        numeral = require('numeral');

    return Model.extend({
        urlRoot: Model.baseApiUrl + '/products',
        defaults: {
            amount: 0,
            retailPricePreference: 'retailMarkup',
            rounding: {},
            type: 'unit'
        },
        initialize: function(){
            this.collections = {
                barcodes: new BarcodesCollections(this.get('barcodes'), {
                    productId: this.id
                })
            };
        },
        saveData: function() {

            var purchasePrice = parseFloat((this.get('purchasePrice') || '').toString()
                    .replace(' ', '', 'gi')
                    .replace(',', '.', 'gi')),
                retailPriceMin = parseFloat((this.get('retailPriceMin') || '').toString()
                    .replace(' ', '', 'gi')
                    .replace(',', '.', 'gi')),
                retailPriceMax = parseFloat((this.get('retailPriceMax') || '').toString()
                    .replace(' ', '', 'gi')
                    .replace(',', '.', 'gi')),
                retailMarkupMax = parseFloat((this.get('retailMarkupMax') || '').toString()
                    .replace(' ', '', 'gi')
                    .replace(',', '.', 'gi')),
                retailMarkupMin = parseFloat((this.get('retailMarkupMin') || '').toString()
                    .replace(' ', '', 'gi')
                    .replace(',', '.', 'gi'));

            if (!purchasePrice && this.get('purchasePrice') !== '0') {
                purchasePrice = this.get('purchasePrice');
            }

            if (!retailPriceMin && this.get('retailPriceMin') !== '0') {
                retailPriceMin = this.get('retailPriceMin');
            }

            if (!retailPriceMax && this.get('retailPriceMax') !== '0') {
                retailPriceMax = this.get('retailPriceMax');
            }

            if (!retailMarkupMax && this.get('retailMarkupMax') !== '0') {
                retailMarkupMax = this.get('retailMarkupMax');
            }

            if (!retailMarkupMin && this.get('retailMarkupMin') !== '0') {
                retailMarkupMin = this.get('retailMarkupMin');
            }

            return {
                name: this.get('name'),
                vat: this.get('vat'),
                purchasePrice: purchasePrice,
                retailPriceMin: retailPriceMin,
                retailPriceMax: retailPriceMax,
                retailMarkupMax: retailMarkupMax,
                retailMarkupMin: retailMarkupMin,
                retailPricePreference: this.get('retailPricePreference'),
                barcode: this.get('barcode'),
                vendorCountry: this.get('vendorCountry'),
                vendor: this.get('vendor'),
                subCategory: this.get('subCategory'),
                rounding: this.get('rounding') ? this.get('rounding').name : null,
                type: this.get('type'),
                typeProperties: this.get('type') === 'unit' ? null : this.get('typeProperties')
            }
        },
        parse: function(){
            var data = Model.prototype.parse.apply(this, arguments);

            if (this.collections){
                this.collections.barcodes.reset(data.barcodes);
            }

            return data;
        }
    });
});
