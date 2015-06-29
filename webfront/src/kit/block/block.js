define(function(require, exports, module) {
    //requirements
    var Backbone = require('backbone'),
        get = require('kit/get/get'),
        set = require('kit/set/set'),
        deepExtend = require('kit/deepExtend/deepExtend'),
        makeClass = require('kit/makeClass/makeClass'),
        rivets = require('kit/rivets/rivets'),
        globalEvents = require('kit/globalEvents/globalEvents'),
        _ = require('lodash');

    var View = Backbone.View;

    // Cached regex to split keys for `delegate`.
    var delegateEventSplitter = /^(\S+)\s*(.*)$/;

    return makeClass(View, {

        constructor: function() {

            var block = this;

            var initialize = this.initialize,
                render = this.render;

            this.initialize = function(data){

                block.stopListening();
                block.delegateGlobalEvents();

                data && deepExtend(block, data);

                this._ensureElement();

                return $.when(initialize.apply(block, arguments)).then(function(){
                    block.render();
                });
            };

            this.render = function(data){

                data && deepExtend(block, data);

                return render.apply(block, arguments);
            };

            this.cid = _.uniqueId('block');

            this.initialize.apply(this, arguments);
        },

        bindings: null,

        formatMoney: require('kit/formatMoney/formatMoney'),
        formatAmount: require('kit/formatAmount/formatAmount'),
        formatDate: require('kit/formatDate/formatDate'),
        formatNumber: require('kit/formatNumber/formatNumber'),
        formatTime: require('kit/formatTime/formatTime'),
        formatDateTime: require('kit/formatDateTime/formatDateTime'),
        normalizeNumber: require('kit/normalizeNumber/normalizeNumber'),

        initialize: function(data){

            var block = this;

            //save data constructors in hidden fields

            block.__collections = block.__collections || block.collections;
            block.__models = block.__models || block.models;
            block.__resources = block.__resources || block.resources;

            block.__collection = block.__collection || block.collection;
            block.__model = block.__model || block.model;

            if (data){

                _.extend(block.__collections, data.collections);
                _.extend(block.__models, data.models);
                _.extend(block.__resources, data.resources);

                block.__collection = data.collection || block.__collection;
                block.__model = data.model || block.__model;
            }

            //get data from initial constructors

            block.collections = _.transform(block.__collections, function(result, collectionInitializer, key) {
                result[key] = block.get('__collections.' + key);
            });

            block.models = _.transform(block.__models, function(result, modelInitializer, key) {
                result[key] = block.get('__models.' + key);
            });

            block.resources = _.transform(block.__resources, function(result, resourceInitializer, key) {
                result[key] = block.get('__resources.' + key);
            });

            block.collection = block.get('__collection');
            block.model = block.get('__model');
        },

        render: function() {
            var block = this;

            if (typeof block.template !== 'function') {
                this.delegateEvents();
                return;
            }

            block.removeBlocks();

            block.bindings && block.bindings.unbind();

            block.setElement($(block.template(block)).replaceAll(block.el));

            block.bindings = rivets.bind(block.el, block);

            block.initBlocks();

            block.el.block = block;

        },

        get: function() {
            var args = [this].concat([].slice.call(arguments));

            return get.apply(null, args);
        },

        set: function() {
            var args = [this].concat([].slice.call(arguments));

            return set.apply(null, args);
        },

        initBlocks: function() {
            var block = this,
                $blocks = block.$('[block]');

            block.__blocks = {};

            $blocks.each(function() {
                var placeholder = this,
                    blockName = placeholder.getAttribute('block'),
                    params = _.extend({}, placeholder.dataset, {el: placeholder});

                var __block = block.get('blocks.' + blockName, [params]);

                if (__block && __block.el) {

                    __block.el.removeAttribute('block');

                    block.__blocks[blockName] = block.__blocks[blockName] || [];

                    block.__blocks[blockName].push(__block);

                    if (!block.$(__block.el).length && block.$(placeholder).length) {
                        __block.$el.replaceAll(placeholder);
                    }
                }
            });
        },

        fetch: function() {
            var block = this;

            var dataList = _.values(block.collections).concat(_.filter(block.models, function(model) {
                return model && model.id;
            })).concat(_.values(block.resources));

            var fetchList = _.map(dataList, function(data) {
                return (data && typeof data.fetch === 'function') ? data.fetch() : data;
            });

            return $.when.apply($, fetchList);
        },

        unbind: function() {
            var block = this;

            block.stopListening();
            block.undelegateEvents();
            block.bindings && block.bindings.unbind();
        },

        remove: function() {
            var block = this;

            block.unbind();
            block.removeBlocks();

            return View.prototype.remove.apply(block, arguments);
        },

        removeBlocks: function() {
            var block = this;

            _.each(block.__blocks, function(blockList, blockName) {

                _.each(blockList, function(blockToRemove) {
                    if (blockToRemove && typeof blockToRemove.remove === 'function') {
                        blockToRemove.remove();
                    }
                });

            });

            block.__blocks = {};
        },

        trigger: function(event, data) {
            var block = this;

            View.prototype.trigger.apply(block, arguments);

            block.$el.trigger(event, data);
            globalEvents.trigger(event, data, block);
        },

        delegateGlobalEvents: function() {

            for (var key in this.globalEvents) {
                var method = this.globalEvents[key];

                var match = key.match(delegateEventSplitter);

                var eventName = match[1], selector = match[2];

                method = _.bind(method, this);

                this.listenTo(globalEvents, eventName, method);
            }

            return this;
        }
    });
});