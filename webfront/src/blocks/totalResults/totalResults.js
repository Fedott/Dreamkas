define(function(require, exports, module) {
    //requirements
    var Block = require('kit/block/block');

    return Block.extend({
        template: require('ejs!./template.ejs'),
        models: {
            result: function() {

                var block = this,
                    model = this.model;

                block.listenTo(model, {
                    change: function() {
                        block.render();
                    }
                });

                return model;
            }
        }
    });
});