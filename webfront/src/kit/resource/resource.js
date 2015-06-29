define(function(require, exports, module) {
    //requirements
    var Backbone = require('backbone'),
        makeClass = require('kit/makeClass/makeClass'),
        deepExtend = require('kit/deepExtend/deepExtend'),
        get = require('kit/get/get'),
        _ = require('lodash');

    return makeClass(function(params) {

        var resource = this;

        deepExtend(resource, params);

    }, {
        get: function(path) {
            return path ? get(this, 'data.' + path) : this.data;
        },
        fetch: function(opt) {

            var resource = this;

            opt = deepExtend({
                url: get(this, 'url'),
                data: get(this, 'params')
            }, opt);

            resource.request = $.ajax(opt);

            return resource.request.then(function(data) {
                resource.data = data;
                resource.trigger('reset', data);
            });

        },
        post: function(data, opt) {

            var resource = this;

            opt = deepExtend({
                url: get(this, 'url')
            }, opt, {
                data: data,
                type: 'POST'
            });

            resource.request = $.ajax(opt);

            return resource.request.then(function(data) {
                resource.data = data;
                resource.trigger('reset', data);
            });
        },
        put: function(data, opt) {

            var resource = this;

            opt = deepExtend({
                url: get(this, 'url')
            }, opt, {
                data: data,
                type: 'PUT'
            });

            resource.request = $.ajax(opt);

            return resource.request.then(function(data) {
                resource.data = data;
                resource.trigger('reset', data);
            });
        }
    }, Backbone.Events);
});