define(function(require, exports, module) {
    //requirements
    var Block = require('kit/block/block'),
        Error = require('blocks/error/error'),
        router = require('router'),
        deepExtend = require('kit/deepExtend/deepExtend'),
        _ = require('lodash');

    var Page = Block.extend({

        el: '.page',
        template: require('ejs!./template.ejs'),

        collections: {},
        models: {},

        activeNavigationItem: 'main',

        content: function() {
            return '<h1>Добро пожаловать в Lighthouse!</h1>';
        },

        initialize: function() {
            var page = this;

            page.setStatus('starting');
            page.setStatus('loading');

            page.collections = _.transform(page.collections, function(result, collectionInitializer, key) {
                result[key] = page.get('collections.' + key);
            });

            page.models = _.transform(page.models, function(result, modelInitializer, key) {
                result[key] = page.get('models.' + key);
            });

            Page.previous = Page.current;
            Page.current = page;

            if (Page.previous){
                Page.previous.destroy();
            }

            Promise.resolve(page.fetch()).then(function() {
                try {
                    page.render();
                    page.setStatus('loaded');
                } catch (error) {
                    page.throw(error);
                }
            }, function(error) {
                page.throw(error);
            });
        },

        render: function(){
            var page = this,
                autofocus;

            if (Page.previous){
                Page.previous.removeBlocks();
            }

            Block.prototype.render.apply(page, arguments);

            autofocus = page.el.querySelector('[autofocus]');

            if (autofocus){
                setTimeout(function(){
                    autofocus.focus();
                }, 0);
            }

        },

        fetch: function(dataList) {
            var page = this;

            dataList = dataList || _.values(page.collections).concat(_.filter(page.models, function(model) {
                return model && model.id;
            }));

            var fetchList = _.map(dataList, function(data) {
                return (data && typeof data.fetch === 'function') ? data.fetch() : data;
            });

            return Promise.all(fetchList);
        },

        destroy: function(){
            var page = this;

            _.forEach(page.models, function(model){
                model.off();
                model.stopListening();
            });

            _.forEach(page.collections, function(collection){
                collection.off();
                collection.stopListening();
            });

            page.undelegateEvents();
            page.stopListening();
        },

        throw: function(error){
            new Error({
                jsError: error
            });
        },

        setStatus: function(status){
            var page = this;

            page.trigger('status:' + status);

            if (status === 'loading' && Page.current){
                document.body.removeAttribute('status');
            }

            setTimeout(function(){
                document.body.setAttribute('status', status);
            }, 0);
        },

        _initBlocks: function(){
            var block = this;

            Block.prototype._initBlocks.apply(block, arguments);
        }
    });

    return Page;
});