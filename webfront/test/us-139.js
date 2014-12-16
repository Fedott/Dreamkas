var expect = require('chai').expect,
    browser = require('../tools/browser');

describe.only('US 139: Дашборд', function() {

    this.timeout(10000);

    beforeEach(function(done) {
        browser
            .url(process.env.host + '/pages/dashboard/mocks/1.html')
            .waitFor('body[status="loaded"]', 5000)
            .pause(1000, done)
    });

    it('Главная страница', function(done) {

        browser
            .diff('us-139_main')
            .call(done);

    });

});