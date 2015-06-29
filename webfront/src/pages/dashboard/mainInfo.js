define(function(require, exports, module) {
    //requirements
    var Block = require('kit/block/block'),
        getText = require('kit/getText/getText'),
        moment = require('moment');

    return Block.extend({
        template: require('ejs!./mainInfo.ejs'),
        weekAgoDay: function() {
            return getText('for last week', [moment(this.resource.get('today').now.date).weekday()]);
        }
    });
});