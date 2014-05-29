define(function(require) {
    //requirements
    var Block = require('kit/block'),
        when = require('when'),
        _ = require('lodash');

    return Block.extend({
        el: document.body,
        init: function(){
            var page = this;

            page._super();

            page.set('status', 'loading');

            when(_.result(page, 'isAllow')).then(function(isAllow) {
                if (isAllow) {

                    page._initResources();

                    when(page.fetchAll()).then(function(data) {

                        window.PAGE && window.PAGE.destroy();
                        window.PAGE = page;

                        page.set(data);

                        page.set('status', 'loaded');
                    });
                } else {
                    page.showError({
                        statusCode: '403'
                    });
                }
            }, function(error) {
                page.showError(error);
            });
        },
        resources: {},
        isAllow: true,

        showError: function(error) {
            error.statusCode = error.statusCode || 'unknown error';
            alert('Error: ' + error.statusCode);
        },
        fetch: function(resourceName) {
            var page = this;

            page.set && page.set('status', 'loading');

            return when(page.resources[resourceName].fetch()).then(function(data) {

                page.set && page.set(resourceName, page.resources[resourceName].toJSON());
                page.set && page.set('status', 'loaded');

                return data;
            }, function(error) {
                page.showError(error);
            })
        },
        fetchAll: function(resourceNames) {
            var page = this,
                fetched = _.map(resourceNames || _.keys(page.resources), function(resourceName) {
                    return page.resources[resourceName].fetch();
                });

            page.set && page.set('status', 'loading');

            return when.all(fetched).then(function() {
                var data = _.transform(page.resources, function(result, resource, key) {
                    result[key] = resource.toJSON();
                });

                page.set && page.set(data);
                page.set && page.set('status', 'loaded');

                return data;
            }, function(errors) {
                page.showError(errors);
            });
        },
        save: function(resourceName) {
            var page = this;

            page.set && page.set('status', 'loading');

            return when(page.resources[resourceName].save(page.get(resourceName)), function(){
                page.set && page.set('status', 'loaded');
            });
        },
        _initResources: function() {
            var page = this;

            page.resources = _.transform(page.resources, function(result, ResourceClass, key) {
                result[key] = new ResourceClass();
            });
        }
    });
});