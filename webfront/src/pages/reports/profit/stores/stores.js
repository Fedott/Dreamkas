define(function(require, exports, module) {
    //requirements
	var Page_profit = require('blocks/page/profit/profit');

    return Page_profit.extend({
        content: require('ejs!./content.ejs'),
		ProfitCollection: require('resources/storesProfit/collection'),
		params: {
			dateFrom: function(){
				var page = this,
					currentTime = Date.now();

				return page.formatDate(moment(currentTime).subtract(1, 'week'));
			}
		},
		models: {
			profit: function() {
				var page = this,
					ProfitModel = require('resources/profit/model'),
					profitModel = new ProfitModel;

				profitModel.filters = {
					dateFrom: page.params.dateFrom,
					dateTo: page.formatDate(moment(page.params.dateTo, 'DD.MM.YYYY').add(1, 'days'))
				}

				return profitModel;
			}
		},
		blocks: {
			total: require('./stores__total'),
			table_storesProfit: require('blocks/table/storesProfit/storesProfit')
		},
		inputDateRangeHandler: function(e) {

			var dateFromInput = e.target.querySelector('[name="dateFrom"]'),
				dateToInput = e.target.querySelector('[name="dateTo"]'),
				dateFrom = dateFromInput.value || undefined,
				dateTo = dateToInput.value || undefined,
				_dateTo = this.formatDate(moment(dateTo, 'DD.MM.YYYY').add(1, 'days'));

			this.setParams({
				dateFrom: dateFrom,
				dateTo: dateTo
			});

			dateFromInput.classList.add('loading');
			dateToInput.classList.add('loading');

			$.when(this.collections.profit.fetch({
				filters: {
					dateFrom: dateFrom,
					dateTo: _dateTo,
				}
			}), this.models.profit.fetch({
				filters: {
					dateFrom: dateFrom,
					dateTo: _dateTo,
				}
			})).then(function() {
				dateFromInput.classList.remove('loading');
				dateToInput.classList.remove('loading');
			});
		},

	});
});