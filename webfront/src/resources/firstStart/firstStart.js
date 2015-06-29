define(function(require, exports, module) {
    //requirements
    var Resource = require('kit/resource/resource');

    return Resource.extend({
        url: CONFIG.baseApiUrl + '/firstStart'
    });
});