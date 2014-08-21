define(function(require, exports, module) {
    //requirements
    var Modal = require('blocks/modal/modal');

    return Modal.extend({
        template: require('ejs!./modal_stockIn.ejs'),
        models: {
            stockIn: require('models/stockIn/stockIn')
        },
        blocks: {
            form_stockIn: function(opt){
                var block = this,
                    Form_stockIn = require('blocks/form/form_stockIn/form_stockIn');

                return new Form_stockIn(_.extend(opt, {
                    model: block.models.stockIn
                }));
            },
            form_stockInProducts: function(opt){
                var block = this,
                    Form_stockInProducts = require('blocks/form/form_stockInProducts/form_stockInProducts');

                return new Form_stockInProducts(_.extend(opt, {
                    models: {
                        stockIn: block.models.stockIn
                    }
                }));
            }
        }
    });
});