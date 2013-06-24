define(function(require) {
    //requirements
    var Page = require('pages/page'),
        Table_balance = require('blocks/table/table_balance/table_balance'),
        ProductCollection = require('collections/products');

    return Page.extend({
        pageName: 'page_common_balance',
        templates: {
            '#content': require('tpl!./templates/balance.html')
        },
        initCollections: {
            products: function(){
                return new ProductCollection();
            }
        },
        initBlocks: function(){
            var page = this;

            new Table_balance({
                collection: page.collections.products,
                el: document.getElementById('table_balance')
            })
        }
    });
});