define(function(require, exports, module) {
    //requirements
    var CollectionBlock = require('kit/collectionBlock/collectionBlock');

    return CollectionBlock.extend({
        collection: function(){
            return PAGE.collections.receipts;
        },
        template: require('ejs!./receiptFinder__results.ejs')
    });
});