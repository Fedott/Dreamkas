define(function(require, exports, module) {
    //requirements
    var Block = require('kit/block/block');

    return Block.extend({
        template: require('ejs!./info.ejs'),
        resources: {
            firstStart: function() {
                return PAGE.resources.firstStart;
            }
        },
        initialize: function() {

            var block = this,
                initialize = Block.prototype.initialize.apply(block, arguments);

            block.listenTo(block.resources.firstStart, {
                reset: function() {
                    block.render();
                }
            });

            return initialize;
        }
    });
});