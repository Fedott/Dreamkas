var Invoice = BasicModel.extend({
    modelName: 'invoice',
    urlRoot: "http://lighthouse/api/1/invoices",

    dateFormat: 'dd.mm.yy',
    datePrintFormat: "dd.mm.yyyy",
    timeFormat: 'HH:mm',
    invalidMessage: 'Вы ввели неверную дату',

    defaults: {
        id: null,
        sku: null,
        supplier: null,
        acceptanceDate: null,
        accepter: null,
        legalEntity: null,
        supplierInvoiceSku: null,
        supplierInvoiceDate: null,
        sumTotal: null
    }
});