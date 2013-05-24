define(
    [
        '/kit/block.js',
        './tooltip.templates.js'
    ],
    function(Block, templates) {
        return Block.extend({
            $trigger: null,
            className: 'tooltip',
            templates: templates,

            initialize: function() {
                var block = this;

                block.$el.appendTo('body');

                block.render();

                $(document).on('click', function(e) {
                    if (block.$trigger && e.target != block.$trigger[0]) {
                        block.hide();
                    }
                });
            },
            events: {
                'click': function(e) {
                    e.stopPropagation();
                },
                'click .tooltip__closeLink': function(e){
                    e.preventDefault();

                    var block = this;

                    block.hide();
                }
            },
            show: function(opt) {
                var block = this;

                opt = _.extend({
                    $trigger: null
                }, opt);

                block.$trigger = opt.$trigger || block.$trigger;

                block.$el
                    .css({
                        top: block.$trigger.offset().top + block.$trigger.height(),
                        left: block.$trigger.offset().left
                    })
                    .show();
            },
            hide: function() {
                var block = this;

                block.$el.hide();
            }
        });
    }
);