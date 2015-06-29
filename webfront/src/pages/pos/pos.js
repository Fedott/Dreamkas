define(function(require, exports, module) {
    //requirements
    var Page = require('blocks/page/page');

    return Page.extend({
        template: require('ejs!./template.ejs'),
        events: {
            'change select[name="store"]': function(e){
                var page = this;

                page.$('[href^="/pos/stores/"]').attr('href', page.getPosUrl(e.target.value));
            }
        },
        collections: {
            stores: require('resources/store/collection')
        },
        blocks: {
            select_stores: require('blocks/select/store/store')
        },
        getPosUrl: function(storeId){

            var page = this;

            storeId = storeId || page.collections.stores.at(0).id;

            return '/pos/stores/' + storeId + (page.params.firstStart ? '?firstStart=1' : '');
        }
    });
});