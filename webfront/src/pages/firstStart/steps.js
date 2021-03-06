define(function(require, exports, module) {
    //requirements
    var Block = require('kit/block/block');

    return Block.extend({
        template: require('ejs!./steps.ejs'),
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
        },
        posUrl: function(){
            var block = this;
            var stores = _.filter(block.resources.firstStart.get('stores'), function(storeItem) {
                return storeItem.costOfInventory;
            });

            return '/pos' + (stores.length == 1 ? '/stores/' + stores[0].store.id : '') + '?firstStart=1';
        }
    });
});