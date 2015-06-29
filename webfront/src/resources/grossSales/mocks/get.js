define(function(require, exports, module) {
    //requirements
    var ajaxMock = require('kit/ajaxMock/ajaxMock');

    return ajaxMock({
        //request
        url: '/reports/grossMargin',
        type: 'GET',

        //response
        status: 200,
        responseText: {
            today: {
                now: {
                    date: "2013-11-25T16:37:25+0400",
                    value: 645466.45
                }
            },
            yesterday: {
                now: {
                    date: "2013-11-24T16:37:25+0400",
                    value: 542234.45,
                    diff: 10.12
                }
            },
            weekAgo: {
                now: {
                    date: "2014-12-05T16:00:25+0400",
                    value: 710126.45,
                    diff: -9.25
                }
            }
        }
    });
});