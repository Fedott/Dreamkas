var expect = require('chai').expect,
    browser = require('../tools/browser');

describe('Store page with no stores', function() {

    this.timeout(10000);

    beforeEach(function(done) {

        browser
            .url(process.env.host + '/test.html')
            .execute(function(){
                require([
                    'app',
                    'resources/currentUser/mocks/get',
                    'resources/store/mocks/get_0'
                ], function(app){
                    app.start('/stores');
                });
            })
            .waitFor('body[status="loaded"]', 5000, done)
    });
    
    it.only('Store page', function(done) {

        browser
            .getText('.alert-info', function(err, text){
                expect(text).to.equal('У вас ещё нет ни одного магазина');
            })
            .screenDiff('test')
            .call(done);

    });

});