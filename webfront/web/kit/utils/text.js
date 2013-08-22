define(function(require) {
    //requirements
    var dictionary = require('dictionary');

    return function(text) {
        var result = '';

        if (text && typeof text === 'string'){
            result = _.escape(dictionary[text] || text);
        }

        return result;
    };
});