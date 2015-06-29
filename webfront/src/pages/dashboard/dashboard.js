define(function(require, exports, module) {
    //requirements
    var Page = require('blocks/page/page'),
        MainInfo = require('./mainInfo');

    return Page.extend({
        content: require('ejs!./content.ejs'),
        activeNavigationItem: 'dashboard',
        collections: {
            storesGrossSales: require('resources/storesGrossSales/collection'),
            topSales: require('resources/topSales/collection')
        },
        resources: {
            grossSales: require('resources/grossSales/grossSales'),
            grossMargin: require('resources/grossMargin/grossMargin')
        },

        blocks: {
            grossSales: function(options) {

                options.title = 'Продажи по сети за сегодня на это время';
                options.resource = this.resources.grossSales;

                return new MainInfo(options);
            },
            grossMargin: function(options) {

                options.title = 'Прибыль по сети за сегодня на это время';
                options.resource = this.resources.grossMargin;

                return new MainInfo(options);
            },
            table_storesGrossSales: require('blocks/table/storesGrossSales/storesGrossSales'),
            table_topSales: require('blocks/table/topSales/topSales')
        }
    });
});
