define(function(require, exports, module) {
    //requirements
    var Page = require('kit/page/page'),
        router = require('router');

    return Page.extend({
        content: require('ejs!./content.ejs'),
        activeNavigationItem: 'stockMovement',
        params: {
            filterTypes: ''
        },
        collections: {
            suppliers: function(){
                var SuppliersCollection = require('collections/suppliers/suppliers');

                return new SuppliersCollection();
            },
            stores: function(){
                var StoresCollection = require('collections/stores/stores');

                return new StoresCollection();
            },
            stockMovements: function(){
                var page = this,
                    StockMovementsCollection = require('collections/stockMovements/stockMovements');

                return new StockMovementsCollection([], {
                    filterTypes: page.params.filterTypes,
                    dateFrom: page.params.dateFrom,
                    dateTo: page.params.dateTo
                });
            },
            groups: function(){
                var GroupsCollection = require('collections/groups/groups');

                return new GroupsCollection();
            }
        },
        models: {
            invoice: null
        },
        events: {
            'change [name="filterTypes"]': function(e) {
                var page = this;

                e.currentTarget.classList.add('loading');

                page.params.filterTypes = e.target.value;

                router.save(page.params);

                page.collections.stockMovements.filterTypes = page.params.filterTypes;
                page.collections.stockMovements.fetch().then(function() {
                    page.render();
                });
            },
            'change [name="dateFrom"]': function(e) {
                var page = this;

                if (e.currentTarget.classList.contains('loading')){
                    return;
                }

                e.currentTarget.classList.add('loading');

                page.params.dateFrom = e.target.value;

                router.save(page.params);

                page.collections.stockMovements.dateFrom = page.params.dateFrom;
                page.collections.stockMovements.fetch().then(function() {
                    page.render();
                });
            },
            'change [name="dateTo"]': function(e) {
                var page = this;

                if (e.currentTarget.classList.contains('loading')){
                    return;
                }

                e.currentTarget.classList.add('loading');

                page.params.dateTo = e.target.value;

                router.save(page.params);

                page.collections.stockMovements.dateTo = page.params.dateTo;
                page.collections.stockMovements.fetch().then(function() {
                    page.render();
                });
            }
        },
        blocks: {
            modal_invoiceAdd: function(){
                var block = this,
                    Modal_invoice = require('blocks/modal/modal_invoice/modal_invoice');

                return new Modal_invoice({
                    el: '#modal_invoiceAdd',
                    collections: {
                        invoices: block.collections.stockMovements,
                        suppliers: block.collections.suppliers
                    }
                });
            }
        }
    });
});