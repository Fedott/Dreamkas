define(function(require, exports, module) {
    //requirements
    var Table = require('blocks/table/table');

    return Table.extend({
        template: require('ejs!./template.ejs'),
        sortBy: 'name',
        collection: function(){
            return PAGE.collections.profit
        },
        initData: function() {
            var totalGrossSales = 0,
                totalGrossMargin = 0;

            Table.prototype.initData.apply(this, arguments);

            this.collection.forEach(function(profitModel) {
                totalGrossSales += profitModel.get('grossSales');
                totalGrossMargin += profitModel.get('grossMargin');
            });

            this.totalGrossSales = totalGrossSales;
            this.totalGrossMargin = totalGrossMargin;

        },
        calculateGrossSalesPercent: function(grossSales) {
            var percent = 100 * grossSales / this.totalGrossSales;

            return this.formatNumber(percent.toFixed(1));
        },
        calculateGrossMarginPercent: function(grossMargin) {
            var percent = 100 * grossMargin / this.totalGrossMargin;

            return this.formatNumber(percent.toFixed(1));
        }
    });
});