define(function(require, exports, module) {
    //requirements
    var numeral = require('kit/numeral');

    return function(sum){
        return numeral(sum).format('0,0.00');
    }
});