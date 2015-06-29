define(function(require, exports, module) {
    //requirements
    var Modal = require('blocks/modal/modal');

    return Modal.extend({
        template: require('ejs!./template.ejs'),
        success: false,
        events: {
            'click .modal_receipt__reloadLink': function() {
                var block = this,
                    ReceiptModel = require('resources/receipt/model');

                block.hide();

                PAGE.models.receipt = new ReceiptModel();

                if (PAGE.params.firstStart == 1){
                    PAGE.setParams({
                        firstStart: 0
                    })
                }

                PAGE.render();
            }
        },
        blocks: {
            form_receipt: require('blocks/form/receipt/receipt')
        }
    });
});