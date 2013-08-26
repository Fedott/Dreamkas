define(function(require) {
    //requirements
    var Block = require('kit/block'),
        Backbone = require('backbone'),
        Router = require('kit/router'),
        isAllow = require('kit/utils/isAllow'),
        _ = require('underscore');

    var router = new Router();

    var Page = Block.extend({
        el: document.body,
        permissions: null,
        referrer: {},
        _configure: function(params, route) {

            Block.prototype._configure.apply(this, arguments);

            var page = this,
                accessDenied;

            page.route = route;

            if (Page.current) {
                page.referrer = _.clone(Page.current);
                Page.current.stopListening();
            }

            Page.current = page;

            switch (typeof page.permissions) {
                case 'object':
                    accessDenied = _.some(page.permissions, function(value, key) {
                        return !isAllow(key, value);
                    });
                    break;
                case 'function':
                    accessDenied = page.permissions();
                    break;
                case 'string':
                    accessDenied = isAllow(page.permissions);
                    break;
            }

            if (accessDenied){
                router.navigate('/403', {
                    trigger: true
                });
            }

        },
        _ensureElement: function() {

            Backbone.View.prototype._ensureElement.apply(this, arguments);

            var page = this;

            page.$el
                .removeAttr(page.referrer.__name__)
                .addClass(page.__name__);
            
            page.set('loading', true);
        },
        initialize: function() {
            var page = this;
            page.render();
        },
        render: function() {
            var page = this;

            _.each(page.templates, function(template, selector) {
                var $renderContainer = $(selector);

                page.removeBlocks($renderContainer);

                $renderContainer.html(template(page));
            });

            page.set('loading', false);
        },
        removeBlocks: function($container) {
            var blocks = [];

            $container.find('[block]').each(function() {
                var $block = $(this),
                    __name__ = $block.attr('block');

                blocks.push($block.data(__name__));
            });

            _.each(blocks, function(block) {
                block.remove();
            });
        },
        toUrl: function(){
            var page = this,
                pageParams = {},
                pathname;

            _.each(page, function(prop, key){
                if (!(key==='defaults' || key==='route' || _.isFunction(prop) || (_.isObject(prop) && !_.isPlainObject(prop)))){
                    pageParams[key] = prop;
                }
            });

            pathname = page.route.replace(/:\w+/g, function(placeholder) {
                var key = placeholder.replace(':', ''),
                    string = pageParams[key];

                delete pageParams[key];

                return _.isObject(string) ? placeholder : string;
            });

            _.each(pageParams, function(value, key){
                if (page.defaults[key] === value){
                    delete pageParams[key];
                }
            });

            return router.toFragment(pathname, pageParams);
        },
        save: function(data){

            data = data || this;

            var page = this,
                route = page.route.replace(/:\w+/g, function(placeholder) {
                    var key = placeholder.replace(':', ''),
                        string = data[key];

                    delete data[key];

                    return string || placeholder;
                });

            _.each(data, function(value, key){
                if (page.defaults[key] === value){
                    delete data[key];
                }
            });

            router.navigate(router.toFragment(route, data));
        }
    });

    Page.extend = require('kit/utils/classExtend');

    return Page;
});