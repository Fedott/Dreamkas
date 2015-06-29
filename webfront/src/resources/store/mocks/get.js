define(function(require, exports, module) {
    //requirements
    var ajaxMock = require('kit/ajaxMock/ajaxMock');

    return ajaxMock({
        //request
        url: '/stores/*',
        type: 'GET',

        //response
        status: 200,
        responseText: {
            "id": "544e281e2cde6ea7798b4588",
            "name": "Первый"
        }
    });
});