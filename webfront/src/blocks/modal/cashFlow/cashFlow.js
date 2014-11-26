define(function(require, exports, module) {
    //requirements
    var Modal = require('blocks/modal/modal');

    return Modal.extend({
        id: 'modal_cashFlow',
        cashFlowId: 0,
        template: require('ejs!./template.ejs'),
        models: {
            cashFlow: function(){
                var CashFlowModel = require('resources/cashFlow/model');

                return PAGE.get('collections.cashFlows').get(this.cashFlowId) || new CashFlowModel;
            }
        },
        blocks: {
            form_cashFlow: function() {
                var Form_cashFlow = require('blocks/form/cashFlow/cashFlow');

                return new Form_cashFlow({
                    cashFlowId: this.cashFlowId
                });
            }
        }
    });
});