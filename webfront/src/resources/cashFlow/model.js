define(function(require, exports, module) {
    //requirements
    var Model = require('kit/model/model'),
        formatDate = require('kit/formatDate/formatDate'),
        normalizeNumber = require('kit/normalizeNumber/normalizeNumber');
    
    return Model.extend({
        urlRoot: Model.baseApiUrl + '/cashFlows',
        defaults: {
            direction: 'out'
        },
        saveData: function(){

            return {
                direction: this.get('direction'),
                date: this.get('date'),
                amount: normalizeNumber(this.get('amount')) || this.get('amount'),
                comment: this.get('comment')
            }
        },
        parse: function(data){
            var comment = data.comment;

            if (data.reason){
                switch (data.type){
                    case 'Invoice':
                        comment = 'Оплата приёмки';
                        break;

                    case 'Return':
                        comment = 'Возвраты';
                        break;

                    case 'Sales':
                        comment = 'Продажи';
                        break;

                    case 'SupplierReturn':
                        comment = 'Возврат поставщику';
                        break;
                }

                comment += ' от ' + formatDate(data.reason.day || data.date);
            }

            data.comment = comment;

            return Model.prototype.parse.call(this, data);
        }
    });
});