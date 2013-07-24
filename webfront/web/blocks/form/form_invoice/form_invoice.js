define(function(require) {
    //requirements
    var Form = require('kit/blocks/form/form');

    var router = new Backbone.Router();

    return Form.extend({
        __name__: 'form_invoice',
        templates: {
            index: require('tpl!blocks/form/form_invoice/templates/index.html')
        },
        onSubmitSuccess: function(model){
            router.navigate('/invoices/' + model.id + '?editMode=true', {
                trigger: true
            });
        }
    });
});