define(function(require) {
        //requirements
        var Block = require('kit/core/block.deprecated');

        return Block.extend({
            __name__: 'product',
            template: require('ejs!blocks/product/templates/index.html')
        })
    }
);
