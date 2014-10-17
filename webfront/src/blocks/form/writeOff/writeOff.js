define(function(require, exports, module) {
    //requirements
    var Form = require('blocks/form/form');

    return Form.extend({
        template: require('ejs!./template.ejs'),
        id: 'form_writeOff',
        model: require('resources/writeOff/model'),
        collection: function(){
            return PAGE.collections.stockMovements;
        },
        blocks: {
            inputDate: require('blocks/inputDate/inputDate'),
            select_store: require('blocks/select/store/store')
        }
    });
});