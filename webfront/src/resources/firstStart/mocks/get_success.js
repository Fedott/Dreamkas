define(function(require, exports, module) {
    //requirements
    var ajaxMock = require('kit/ajaxMock/ajaxMock');

    var count = 1;

    return ajaxMock({
        //request
        url: '/firstStart',
        type: 'GET',

        //response
        status: 200,
        responseText: {
            complete: true
        }
    });
});