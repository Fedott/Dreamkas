define(function(require, exports, module) {
    //requirements
    var Collection = require('kit/core/collection'),
        Model = require('kit/core/model');

    return Collection.extend({
        __name__: module.id,
        model: function(data){
            return new Model({
                group: data
            });
        },
        storeId: null,
        url: function(){
            return LH.baseApiUrl + '/stores/' + this.storeId + '/groups';
        }
    });
});