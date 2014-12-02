define(function(require, exports, module) {
    //requirements
    var ajaxMock = require('kit/ajaxMock/ajaxMock');

    return ajaxMock({
        //request
        url: '/cashFlows',
        type: 'GET',

        //response
        status: 200,
        responseText: []
    });
});