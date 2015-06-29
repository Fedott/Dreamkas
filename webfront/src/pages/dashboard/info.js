define(function(require, exports, module) {
    //requirements
    var Block = require('kit/block/block');

    return Block.extend({
        template: require('ejs!./info.ejs'),
        resources: {
            firstStart: function() {
                return PAGE.resources.firstStart;
            }
        }
    });
});