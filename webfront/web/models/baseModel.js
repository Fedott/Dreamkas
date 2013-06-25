define(function(require) {
    //requirements
    var Backbone = require('backbone'),
        _ = require('underscore');

    require('jquery.cookie');

    return Backbone.Model.extend({
        saveFields: [],
        save: function(attributes, options) {
            return Backbone.Model.prototype.save.call(this, attributes, _.extend({
                wait: true,
                isSave: true
            }, options));
        },
        destroy: function(options){
            Backbone.Model.prototype.destroy.call(this, _.extend({
                wait: true
            }, options))
        },
        sync: function(method, model, options){
            Backbone.Model.prototype.sync.call(this, method, model, _.extend({
                headers: {
                    Authorization: 'Bearer ' + $.cookie('token')
                }
            }, options));
        },
        toJSON: function(options) {
            options = options || {};

            var toJSON = Backbone.Model.prototype.toJSON;

            if (options.isSave) {
                return _.pick(toJSON.apply(this, arguments), this.saveFields);
            }

            return toJSON.apply(this, arguments);
        }
    })
});