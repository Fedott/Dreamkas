define(function(require) {
    //requirements
    var Table = require('kit/blocks/table/table');

    require('moment');

    return Table.extend({
        blockName: 'table_invoices',
        templates: {
            head: require('tpl!blocks/table/table_invoices/templates/head.html'),
            body: require('tpl!blocks/table/table_invoices/templates/body.html')
        }
    });
});