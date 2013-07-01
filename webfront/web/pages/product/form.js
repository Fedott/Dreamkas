define(function(require) {
    //requirements
    var Page = require('pages/page'),
        ProductModel = require('models/product'),
        Form_product = require('blocks/form/form_product/form_product');

    return Page.extend({
        pageName: 'page_product_form',
        templates: {
            '#content': require('tpl!./templates/form.html')
        },
        permissions: {
            products: 'all'
        },
        initialize: function(productId){
            var page = this;

            page.productId = productId;

            page.productModel = new ProductModel({
                id: page.productId
            });

            page.render();

            $.when(productId ? page.productModel.fetch() : {}).then(function(){
                new Form_product({
                    model: page.productModel,
                    el: document.getElementById('form_product')
                });
            })
        }
    });
});