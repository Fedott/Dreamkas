define(function(require, exports, module) {
    //requirements
    var Page = require('kit/page');

    return Page.extend({
        resources: {
            stores: require('collections/stores')
        },
        partials: {
            globalNavigation: require('rv!pages/globalNavigation_main.html'),
            content: require('rv!./content.html')
        }
    });
});