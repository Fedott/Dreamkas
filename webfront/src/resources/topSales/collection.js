define(function(require) {
        //requirements
        var Collection = require('kit/collection/collection');

        require('./mocks/get');

        return Collection.extend({
            url: Collection.baseApiUrl + '/reports/grossSalesTop',
            filters: {
                period: 1,
                limit: 3
            }
        });
    }
);