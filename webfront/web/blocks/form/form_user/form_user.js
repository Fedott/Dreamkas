define(function(require) {
    //requirements
    var Form = require('blocks/form/form');

    return Form.extend({
        __name__: 'form_user',
        redirectUrl: '/users',
        template: require('tpl!blocks/form/form_user/templates/index.html'),
        initialize: function(){
            var block = this;

            if (block.model.id){
                block.redirectUrl = '/users/' + block.model.id
            }
        }
    });
});