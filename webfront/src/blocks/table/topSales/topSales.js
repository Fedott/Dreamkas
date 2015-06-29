define(function(require, exports, module) {
    //requirements
    var Table = require('blocks/table/table');

    return Table.extend({
        template: require('ejs!./template.ejs'),
        events: {
            'change select[name="period"]': function(e) {
                var block = this,
                    $select = $(e.currentTarget);

                $select.addClass('loading');

                block.collection.fetch({
                    filters: {
                        period: $select.val()
                    }
                }).then(function() {
                    $select.removeClass('loading');
                });

            }
        },
        collection: function() {
            return PAGE.collections.topSales;
        },
        blocks: {
            select_period: function(options) {
                var block = this,
                    PeriodSelect = require('blocks/select/period/period');

                options.selected = block.collection.filters.period;

                return new PeriodSelect(options);
            }
        }
    });
});