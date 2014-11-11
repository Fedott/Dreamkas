define(function(require, exports, module) {
    //requirements
    var Page_outside = require('pages/outside/outside');

    return Page_outside.extend({
        formBlock: 'form_login',
        blocks: {
            form_login: require('blocks/form/login/login')
        }
    });
});