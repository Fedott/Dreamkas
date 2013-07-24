define(function(require) {
    //requirements
    var Table = require('kit/blocks/table/table');

    return Table.extend({
        __name__: 'table_users',
        templates: {
            head: require('tpl!blocks/table/table_users/templates/head.html'),
            tr: require('tpl!blocks/table/table_users/templates/tr.html')
        }
    });
});