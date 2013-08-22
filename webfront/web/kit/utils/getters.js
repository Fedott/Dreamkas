define(function(require) {
    //requirements
    var _ = require('underscore');

    return {
        get: function(path){
            var attr = this,
                segments = path.split('.');

            _.every(segments, function(segment){
                return attr = attr[segment];
            });

            return attr;
        }
    }
});