define(function(require, exports, module) {
    //requirements
    var Page = require('kit/page'),
        currentUser = require('models/currentUser.inst'),
        UserModel = require('models/user');

    return Page.extend({
        partials: {
            content: require('ejs!./content.ejs'),
            localNavigation: require('ejs!blocks/localNavigation/localNavigation_users.ejs')
        },
        blocks: {
            form_user: require('blocks/form/form_user/form_user')
        }
    });
});
