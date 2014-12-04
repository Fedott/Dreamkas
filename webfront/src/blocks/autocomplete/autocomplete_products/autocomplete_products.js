define(function(require, exports, module) {
    //requirements
    var Autocomplete = require('blocks/autocomplete/autocomplete');

    return Autocomplete.extend({
        template: require('ejs!./template.ejs'),
        placeholder: 'Наименование или артикул товара',
        source: CONFIG.baseApiUrl + '/products/search?properties[]=name&properties[]=sku',
        suggestionTemplate: require('ejs!./suggestion.ejs'),
        valueKey: 'name',
        inputName: 'product.name',
        selectedProduct: null,
        productCount: require('resources/product/count'),
        globalEvents: {
            'submit:success': function(data, block) {

                var modal = block.$el.closest('.modal')[0];

                if (modal && modal.id === 'modal_productForAutocomplete-' + this.cid) {

                    this.productCount.data = [data];

                    this.render({
                        value: data[this.valueKey]
                    });

                    this.select(data);
                }

            }
        },
        blocks: {
            modal_product: require('blocks/modal/product/product')
        }
    });
});