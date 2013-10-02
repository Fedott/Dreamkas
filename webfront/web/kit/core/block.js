define(function(require) {
    //requirements
    var Backbone = require('backbone'),
        $ = require('jquery'),
        _ = require('lodash'),
        deepExtend = require('../utils/deepExtend'),
        classExtend = require('../utils/classExtend'),
        setter = require('../utils/setter'),
        getter = require('../utils/getter');

    require('jquery.require');

    var Block = Backbone.View
        .extend(setter)
        .extend(getter)
        .extend({
            __name__: null,
            template: function(){},

            className: null,
            addClass: null,
            tagName: 'div',

            events: null,
            listeners: null,
            page: null,

            constructor: function() {
                this.cid = _.uniqueId('cid');
                this._configure.apply(this, arguments);
                this._ensureElement();
                this.initialize.apply(this, arguments);
                this.delegateEvents();
            },
            _configure: function(options) {
                var block = this;

                block.defaults = _.clone(block);
                deepExtend(block, options);
            },
            _ensureElement: function() {
                var block = this;

                Backbone.View.prototype._ensureElement.apply(block, arguments);

                block.findElements();

                block.$el
                    .addClass(block.className)
                    .addClass(block.__name__)
                    .addClass(block.addClass)
                    .attr('block', block.__name__)
                    .data(block.__name__, block);

                if (!$.trim(this.$el.html())){
                    this.render();
                }
            },
            delegateEvents: function() {
                var block = this;

                Backbone.View.prototype.delegateEvents.apply(block, arguments);

                block.startListening();
            },
            initialize: function() {
            },
            render: function() {
                var block = this,
                    template = block.template(block);

                //block.removeBlocks();

                block.$el.html(template);
                block.$el.require();

                block.findElements();

                return block;
            },
            findElements: function() {
                var block = this,
                    $context = arguments[0] || block.$el,
                    elements = [];

                if (block.__name__) {
                    $context.find('[class*="' + block.__name__ + '__"]').each(function() {
                        var classes = _.filter($(this).attr('class').split(' '), function(className) {
                            return className.indexOf(block.__name__ + '__') === 0;
                        });

                        elements = _.union(elements, classes);
                    });

                    _.each(elements, function(className) {
                        var elementName = className.split('__')[1];

                        block['$' + elementName] = block.$el.find('.' + className);
                    });
                }
            },
            startListening: function() {
                var block = this;

                _.each(block.listeners, function(listener, property) {
                    if (typeof listener === 'function') {
                        block.listenTo(block, listener);
                    } else if (block[property]) {
                        block.listenTo(block[property], listener);
                    }
                });
            },
            destroy: function(){
                var block = this;

                block.stopListening();
            },
            'set:loading': function(loading) {
                var block = this;
                block.$el.toggleClass('preloader_spinner', loading);
            }
        });

    Block.extend = classExtend;

    return Block;
});