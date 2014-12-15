var expect = require('chai').expect,
    browser = require('../tools/browser');

describe.only('US 139: Дашборд', function() {

    this.timeout(10000);

    beforeEach(function(done) {

        browser
            .timeoutsAsyncScript(5000)
            .url(process.env.host + '/test.html')
            .executeAsync(function(done) {
                requirejs([
                    'app',
                    'resources/currentUser/mocks/get',
                    'resources/grossMargin/mocks/get',
                    'resources/grossSales/mocks/get',
                    'resources/storesGrossSales/mocks/get',
                    'resources/topSales/mocks/get',
                    'resources/firstStart/mocks/get_success',
                    'resources/store/mocks/get_0',
                    'resources/group/mocks/get_0',
                    'resources/supplier/mocks/get_0',
                    'resources/product/mocks/get_0'
                ], function(app) {
                    app.start('/');
                    done();
                });
            }, function(err){
                console.log(err);
            })
            .waitFor('body[status="loaded"]', 5000)
            .pause(1000, done)
    });

    it('Главная страница', function(done) {

        browser
            .diff('us-139_main')
            .call(done);

    });

});