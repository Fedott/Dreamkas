define(function(require) {
    //requirements
    var set = require('./set'),
        _ = require('lodash');

    describe('utils/set', function() {
        it('set as function', function() {
            var object = {};

            set(object, {});
        });
    });
});