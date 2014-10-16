define(function(require, exports, module) {
    //requirements
    var config = require('config'),
        get = require('kit/get/get'),
        set = require('kit/set/set'),
        _ = require('lodash'),
        makeClass = require('kit/makeClass/makeClass'),
        deepExtend = require('kit/deepExtend/deepExtend'),
        Backbone = require('backbone');

    var Model = makeClass(Backbone.Model, {
        filters: {},
        constructor: function(attributes, options){

            options = _.extend({
                parse: true
            }, options);

            this.__defaults = _.cloneDeep(this.defaults);

            Backbone.Model.call(this, attributes, options);
        },
        toJSON: function(options) {
            options = options || {};

            if (options.isSave) {
                return this.getData();
            }

            return Backbone.Model.prototype.toJSON.apply(this, arguments);
        },
        fetch: function(options) {

            options = _.extend({
                filters: {},
                data: {}
            }, options);

            _.extend(this.filters, options.filters);

            options.data = deepExtend({}, this.filters, options.data);

            this.request && this.request.abort();

            this.request = Backbone.Model.prototype.fetch.call(this, options);

            return this.request;
        },
        getData: function() {
            var saveData;

            if (_.isFunction(this.saveData)) {
                saveData = this.saveData();
            }

            if (_.isArray(this.saveData)) {
                saveData = _.pick(this.toJSON(), this.saveData);
            }

            return saveData;
        },
        save: function(attributes, options) {
            return Backbone.Model.prototype.save.call(this, attributes, _.extend({
                wait: true,
                isSave: true
            }, options));
        },
        destroy: function(options) {
            return Backbone.Model.prototype.destroy.call(this, _.extend({
                wait: true
            }, options))
        },
        get: function(path) {
            return get(this, 'attributes.' + path);
        },
        element: function(attr) {
            var model = this,
                uniqueId = _.uniqueId('modelElement'),
                nodeTemplate = '<span id="' + uniqueId + '">' + (_.escape(model.get(attr)) || '') + '</span>';

            model.on('change:' + attr, function() {
                document.getElementById(uniqueId).innerHTML = _.escape(model.get(attr)) || '';
            });

            return nodeTemplate;
        },
        parse: function(data) {
            var model = this;

            _.forEach(model.collections, function(collectionConstructor, key) {

                if (typeof collectionConstructor === 'function'){
                    model.collections[key] = collectionConstructor.call(model);
                }

                if (model.collections[key] instanceof Backbone.Collection){
                    model.collections[key].reset(data[key]);
                }
            });

            _.forEach(model.models, function(modelConstructor, key) {

                if (typeof modelConstructor === 'function'){
                    model.models[key] = modelConstructor.call(modelConstructor);
                }

                if (model.models[key] instanceof Backbone.Model){
                    model.models[key].set(data[key]);
                }
            });

            return data;
        },
        clear: function(){

            var model = this;

            Backbone.Model.prototype.clear.apply(model, arguments);

            model.set(model.__defaults);

            _.forEach(model.collections, function(nestedCollection) {
                nestedCollection.reset([]);
            });

            _.forEach(model.models, function(nestedModel) {
                nestedModel.clear();
            });

            return model;
        }
    });

    Model.baseApiUrl = config.baseApiUrl;
    Model.mockApiUrl = config.mockApiUrl;

    return Model;
});