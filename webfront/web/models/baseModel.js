define(function(require) {
    //requirements
    var Backbone = require('backbone'),
        _ = require('underscore'),
        cookie = require('utils/cookie');

    return Backbone.Model.extend({
        saveFields: [],
        initData: {},
        constructor: function() {
            var model = this;

            Backbone.Model.apply(model, arguments);

            _.each(model.initData, function(Class, key) {
                model[key] = new Class(model.get(key), {
                    parentModel: model,
                    parse: true
                });
            });

        },
        fetch: function(options) {
            return Backbone.Model.prototype.fetch.call(this, _.extend({
                wait: true,
                isFetch: true,
                error: function() {
                    console.log(arguments)
                }
            }, options));
        },
        save: function(attributes, options) {
            return Backbone.Model.prototype.save.call(this, attributes, _.extend({
                wait: true,
                isSave: true
            }, options));
        },
        destroy: function(options) {
            Backbone.Model.prototype.destroy.call(this, _.extend({
                wait: true
            }, options))
        },
        sync: function(method, model, options) {
            return Backbone.Model.prototype.sync.call(this, method, model, _.extend({
                headers: {
                    Authorization: 'Bearer ' + cookie.get('token')
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
        },
        set: function() {
            if (!Backbone.Model.prototype.set.apply(this, arguments)) {
                return false;
            }

            var model = this,
                changedAttributes = this.changedAttributes();

            if (changedAttributes) {
                _.each(changedAttributes, function(value, key) {
                    $('body')
                        .find('[model_id="' + model.id + '"]')
                        .filter('[model_attr="' + key + '"]')
                        .html(value);

                    if (model.initData[key] && model[key]) {
                        model[key].set(value);
                    }
                });
            }

            return this;
        }
    })
});