define(function(require, exports, module) {
    //requirements
    var Select = require('blocks/select/select');

    return Select.extend({
        template: require('ejs!./template.ejs'),
        items: {
            '1': 'Сегодня',
            '2': 'Вчера',
            '3': 'За неделю'
        }
    });
});