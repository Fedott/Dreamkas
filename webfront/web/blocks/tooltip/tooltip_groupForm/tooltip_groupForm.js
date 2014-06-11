define(function(require) {
        //requirements
        var Tooltip = require('blocks/tooltip/tooltip');

        return Tooltip.extend({
            model: null,
            collection: null,
            template: require('tpl!./template.ejs'),
            listeners: {
                'blocks.form_catalogGroup': {
                    'submit:success': function() {
                        var block = this;

                        block.hide();
                    }
                }
            },
            blocks: {
                form_catalogGroup: function(){
                    var block = this,
                        Form_catalogGroup = require('blocks/form/form_group/form_group');

                    return new Form_catalogGroup({
                        el: block.el.querySelector('form'),
                        model: block.model,
                        collection: block.collection
                    });
                }
            },
            show: function(opt) {
                var block = this;

                Tooltip.prototype.show.apply(this, arguments);

                block._startListening();

                block.el.querySelector('[type="text"]').focus();
            },
            align: function(){
                var block = this,
                    $el = $(block.el),
                    $trigger = $(block.trigger),
                    $container = $(block.container);

                $(block.el)
                    .css({
                        top: $trigger.offset().top + $container.scrollTop() - ($el.outerHeight() - $trigger.outerHeight()) / 2,
                        left: $trigger.offset().left - $container.offset().left
                    })
            }
        });
    }
);