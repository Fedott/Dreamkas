define(function(require, exports, module) {
    //requirements
    var Model = require('kit/model/model');

    require('./mocks/get');

    return Model.extend({
        url: Model.baseApiUrl + '/reports/cashFlows',
        defaults: {
            in: 0,
            out: 0,
            balance: 0
        }
    });
});