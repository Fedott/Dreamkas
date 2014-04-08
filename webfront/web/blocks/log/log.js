define(function(require) {
    //requirements
    var Block = require('kit/core/block.deprecated');

    return Block.extend({
        __name__: 'log',
        jobsCollection: null,
        logCollection: null,
        template: require('tpl!blocks/log/templates/index.html')
    });
});