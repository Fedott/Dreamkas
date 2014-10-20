define(function(require, exports, module) {
    //requirements
	var Block = require('kit/block/block'),
		URI = require('uri');

    return Block.extend({
        template: require('ejs!./group__breadcrumbs.ejs'),
		collections: {
			profit:	function() {
				return PAGE.collections.profit;
			}
		},
		models: {
			group: function() {
				return PAGE.models.group;
			}
		},
		initialize: function() {
			var block = this;

			var initialize = Block.prototype.initialize.apply(block, arguments);

			block.listenTo(block.collections.profit, {
				'change reset': function() {
					block.render();
				}
			});

			return initialize;
		},

		allGroupsUrl: function() {

			var filters = _.pick(PAGE.params, 'dateFrom', 'dateTo', 'store');

			return URI('/reports/profit/groups')
				.search(filters).toString();
		}
    });
});