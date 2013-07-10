define(function(require) {
    //requirements
    var Form = require('blocks/form/form'),
        Backbone = require('backbone');

    var router = new Backbone.Router();

    return Form.extend({
        blockName: 'form_invoice',
        templates: {
            index: require('tpl!blocks/form/form_invoice/templates/index.html')
        },
        submit: function() {
            var block = this,
                deferred = $.Deferred(),
                formData = Backbone.Syphon.serialize(block);

            block.model.save(formData, {
                success: function(model) {
                    router.navigate('/invoices/' + model.id + '?editMode=true', {
                        trigger: true
                    });
                },
                error: function(model, response) {
                    deferred.reject(JSON.parse(response.responseText));
                }
            });

            return deferred.promise();
        }
    });
});