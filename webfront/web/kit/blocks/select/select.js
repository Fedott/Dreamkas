define(function(require) {
        //requirements
        var Block = require('kit/block');

        return Block.extend({
            __name__: 'select',
            className: 'select',

            initialize: function() {
                var block = this;

                Block.prototype.initialize.call(block);
                block.$el.val(block.$el.attr('value'));
            }
        });
    }
);