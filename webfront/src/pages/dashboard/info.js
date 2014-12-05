define(function(require, exports, module) {
    //requirements
    var Block = require('kit/block/block');

    return Block.extend({
        template: require('ejs!./info.ejs'),
        resources: {
            firstStart: require('resources/firstStart/firstStart')
        },
        initialize: function(){

            var block = this;

            block.listenTo(this.resources.firstStart, {
                fetched: function(){
                    block.render();
                }
            });

            return Block.prototype.initialize.apply(block, arguments);
        },
        isUserReady: function() {
            return true;
        }
    });
});