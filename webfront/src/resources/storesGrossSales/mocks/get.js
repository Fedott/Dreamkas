define(function(require, exports, module) {
    //requirements
    var ajaxMock = require('kit/ajaxMock/ajaxMock');

    return ajaxMock({
        //request
        url: '/reports/grossSalesByStores',
        type: 'GET',

        //response
        status: 200,
        responseText: [
            {
                "store": {
                    "id": "sdfhjsgdgfi9wegfbfdfsd0",
                    "name": "Первый",
                    "address": "Курортная улица 35"
                },
                "today": {
                    "now": {
                        "date": "2013-11-25T16:37:25+0400",
                        "value": 645466.45
                    }
                },
                "yesterday": {
                    "now": {
                        "date": "2013-11-24T16:37:25+0400",
                        "value": 542234.45
                    }
                },
                "weekAgo": {
                    "now": {
                        "date": "2013-11-18T16:37:25+0400",
                        "value": 710126.45
                    }
                }
            },
            {
                "store": {
                    "id": "sdfhjsgdgfi9wegfbfdfsd1",
                    "name": "Второй",
                    "address": "Курортная улица 35"
                },
                "today": {
                    "now": {
                        "date": "2013-11-25T16:37:25+0400",
                        "value": 645466.45
                    }
                },
                "yesterday": {
                    "now": {
                        "date": "2013-11-24T16:37:25+0400",
                        "value": 54200034.45
                    }
                },
                "weekAgo": {
                    "now": {
                        "date": "2013-11-18T16:37:25+0400",
                        "value": 71126.45
                    }
                }
            },
            {
                "store": {
                    "id": "sdfhjsgdgfi9wegfbfdfsd2",
                    "name": "Третий",
                    "address": "Курортная улица 35"
                },
                "today": {
                    "now": {
                        "date": "2013-11-25T16:37:25+0400",
                        "value": 645466.45
                    }
                },
                "yesterday": {
                    "now": {
                        "date": "2013-11-24T16:37:25+0400",
                        "value": 542234.45
                    }
                },
                "weekAgo": {
                    "now": {
                        "date": "2013-11-18T16:37:25+0400",
                        "value": 710126.45
                    }
                }
            }
        ]
    });
});