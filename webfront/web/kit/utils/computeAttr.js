define(function(require) {
    //requirements
    var get = require();

    require('lodash');

    return function(depsArray, getter) {

        var computeAttr = function() {
            var object = this,
                depAttrs = _.map(depsArray, function(depPath) {
                    return object.get ? object.get(depPath) : get.call(object, depPath);
                });

            return getter.apply(object, depAttrs);
        };

        computeAttr.__dependencies__ = depsArray;

        return computeAttr;
    }
});