define(function(require) {
    //requirements
    var Form = require('kit/form/form');

    return Form.extend({
        el: '.form_groupSettings',
        model: null,
        successMessage: 'Свойства успешно сохранены'
    });
});