define(function(require, exports, module) {
    //requirements
    var ajaxMock = require('kit/ajaxMock/ajaxMock');

    return ajaxMock({
        //request
        url: '/reports/grossSalesTop',
        type: 'GET',

        //response
        status: 200,
        responseText: [
            {
                "product": {
                    "typeUnits": "unit",
                    "type": "unit",
                    "id": "548adb6c2cde6ed60f8b45680",
                    "name": "Молоко",
                    "units": "л",
                    "typeProperties": {

                    },
                    "vat": 0,
                    "sku": "10001",
                    "retailPricePreference": "retailMarkup"
                },
                "quantity": 33,
                "grossSales": 10324.23
            },
            {
                "product": {
                    "typeUnits": "unit",
                    "type": "unit",
                    "id": "548adb6c2cde6ed60f8b45681",
                    "name": "Apple iPhone 6 Gold 64GB",
                    "units": "шт",
                    "typeProperties": {

                    },
                    "vat": 0,
                    "sku": "10001",
                    "retailPricePreference": "retailMarkup"
                },
                "quantity": 330,
                "grossSales": 10324.23
            },
            {
                "product": {
                    "typeUnits": "unit",
                    "type": "unit",
                    "id": "548adb6c2cde6ed60f8b45682",
                    "name": "Apple iPhone 6 Space Grey 64GB",
                    "units": "шт",
                    "typeProperties": {

                    },
                    "vat": 0,
                    "sku": "10001",
                    "retailPricePreference": "retailMarkup"
                },
                "quantity": 330,
                "grossSales": 10324.23
            }
        ]
    });
});