define(function(require, exports, module) {
    //requirements
    var Model = require('kit/model/model');

    return Model.extend({
        url: Model.baseApiUrl + '/reports/gross',
        id: 'id'
    });
});