define(function(require) {
    //requirements
    var Form = require('blocks/form/form');

    var router = new Backbone.Router();

    return Form.extend({
        __name__: 'form_writeOff',
        template: require('tpl!blocks/form/form_writeOff/templates/index.html'),
        redirectUrl: function(){
            var block = this;

            return '/writeOffs/' + block.model.id + '?editMode=true';
        }
    });
});