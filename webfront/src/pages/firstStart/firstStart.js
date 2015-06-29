define(function(require, exports, module) {
    //requirements
    var Page = require('blocks/page/page'),
        Form_store = require('blocks/form/store/store'),
        Form_invoice = require('blocks/form/stockMovement/invoice/invoice'),
        Modal_store = require('blocks/modal/store/store'),
        Modal_invoice = require('blocks/modal/stockMovement/invoice/invoice'),
        router = require('router');

    return Page.extend({
        content: require('ejs!./content.ejs'),
        activeNavigationItem: 'dashboard',
        params: {
            firstStart: 1
        },
        events: {
            'click .page_dashboard__finishButton': function(e){
                var page = this;

                if (e.target.classList.contains('loading')){
                    return;
                }

                e.target.classList.add('loading');

                page.resources.firstStart.put({
                    complete: true
                }).then(function() {
                    router.navigate('dashboard');
                });
            }
        },
        collections: {
            stores: require('resources/store/collection'),
            suppliers: require('resources/supplier/collection'),
            groups: require('resources/group/collection')
        },
        resources: {
            firstStart: require('resources/firstStart/firstStart'),
            productCount: require('resources/product/count')
        },
        blocks: {
            progressbar: require('./progressbar'),
            steps: require('./steps'),
            info: require('./info'),
            modal_store: Modal_store.extend({
                blocks: {
                    form_store: Form_store.extend({
                        submit: function() {
                            return Form_store.prototype.submit.apply(this, arguments).then(function() {
                                return PAGE.resources.firstStart.fetch();
                            });
                        }
                    })
                }
            }),

            modal_invoice: Modal_invoice.extend({
                Form: Form_invoice.extend({
                    submit: function() {
                        return Form_invoice.prototype.submit.apply(this, arguments).then(function() {
                            return PAGE.resources.firstStart.fetch();
                        });
                    }
                })
            })
        },
        activeStep: function() {
            var page = this;

            var step1 = !page.resources.firstStart.data.length;

            var step2 = _.find(page.resources.firstStart.get('stores'), function(storeItem) {
                return storeItem.store;
            });

            var step3 = _.find(page.resources.firstStart.get('stores'), function(storeItem) {
                return storeItem.costOfInventory;
            });

            var step4 = _.find(page.resources.firstStart.get('stores'), function(storeItem) {
                return storeItem.sale;
            });

            var activeStep;

            if (step1) {
                activeStep = 1;
            }

            if (step2) {
                activeStep = 2;
            }

            if (step3) {
                activeStep = 3;
            }

            if (step4) {
                activeStep = 4;
            }

            return activeStep;
        }
    });
});
