define(function(require, exports, module) {
    //requirements
    var Page = require('kit/page'),
        SignupModel = require('models/signup'),
        RestorePasswordModel = require('models/restorePassword');

    return Page.extend({
        data: {
            login: {
                username: null,
                password: null
            }
        },
        init: function(){
            this._super();

            if (this.get('params.signup')==='success'){
                this.set('login.username', SignupModel.email);
            } else if (this.get('params.restorePassword')==='success'){
                this.set('login.username', RestorePasswordModel.email);
            }

        },
        partials: {
            content: require('rv!./content.html'),
            globalNavigation: require('rv!./globalNavigation.html')
        },
        components: {
            'form-login': require('blocks/form/form_login/form_login')
        }
    });
});
