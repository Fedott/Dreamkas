define(function(require, exports, module) {
    //requirements
    var Page = require('blocks/page/page');

    return Page.extend({
        content: require('ejs!./content.ejs'),
		collections: {
			stores: require('collections/stores/stores')
		},
		blocks: {
			select_stores: require('blocks/select/stores/stores')
		}
    });
});
