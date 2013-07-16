define(function(require) {
    //requirements
    var Page = require('pages/page'),
        Table_stores = require('blocks/table/table_stores/table_stores'),
        StoresCollection = require('collections/stores');

    return Page.extend({
        pageName: 'page_store_list',
        templates: {
            '#content': require('tpl!./templates/list.html')
        },
        permissions: {
            stores: 'GET'
        },
        initialize: function(){
            var page = this;

            page.storesCollection = new StoresCollection();

            $.when(page.storesCollection.fetch()).then(function(){
                page.render();

                new Table_stores({
                    collection: page.storesCollection,
                    el: document.getElementById('table_stores')
                });
            });
        }
    });
});