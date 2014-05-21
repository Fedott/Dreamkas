define(function(require, exports, module) {
    //requirements
    var Form = require('blocks/form/form'),
        form_barcodes__row = require('tpl!blocks/form/form_barcodes/form_barcodes__row.html'),
        stringToFragment = require('kit/stringToFragment/stringToFragment'),
        cookies = require('cookies'),
        Page = require('kit/page/page');

    require('lodash');

    return Form.extend({
        el: '.form_barcodes',
        redirectUrl: function(){
            return '/products/' + Page.current.models.product.id
        },
        events: {
            'click .form_barcodes__removeLink': function(e){

                e.preventDefault();

                var link = e.target,
                    barcodesCollection = Page.current.models.product.collections.barcodes,
                    model = barcodesCollection.get(link.dataset.barcode_cid);

                barcodesCollection.remove(model);
            }
        },
        startListening: function(){
            var block = this;

            Form.prototype.startListening.apply(block, arguments);

            block.listenTo(Page.current.models.product.collections.barcodes, {
                add: function(barcodeModel){
                    block.el.appendChild(stringToFragment(form_barcodes__row({
                        barcodeModel: barcodeModel
                    })));
                },
                remove: function(barcodeModel){
                    block.el.removeChild(block.el.querySelector('[data-barcode_cid="' + barcodeModel.cid + '"]'));
                }
            });
        },
        submit: function(formData){
            var block = this,
                barcodes = _.map(formData.barcode, function(barcode, index){
                    return {
                        barcode: barcode,
                        quantity: formData.quantity[index],
                        price: formData.price[index]
                    }
                });

            block.request = $.ajax({
                type: 'PUT',
                url: LH.baseApiUrl + '/products/' + Page.current.models.product.id + '/barcodes',
                dataType: 'json',
                headers: {
                    Authorization: 'Bearer ' + cookies.get('token')
                },
                data: {
                    barcodes: barcodes
                }
            });

            return block.request;
        },
        showErrors: function(errors){
            var block = this,
                errorString = "";

            errors = errors.children.barcodes.children;

            _.forEach(errors, function(error, index){
                _.forEach(error.children, function(data, fieldName){
                    if (data.errors){
                        block.el
                            .querySelectorAll('.form_barcodes__row')[index]
                            .querySelector('[name="' + fieldName + '[]"]')
                            .classList.add('inputText_error');

                        _.forEach(data.errors, function(error){
                            if (errorString.indexOf(error) < 0){
                                errorString += error + '. '
                            }
                        });
                    }
                });
            });


            document.getElementById('barcodesTable').dataset.error = errorString;
        },
        removeErrors: function(){
            var block = this;

            Form.prototype.removeErrors.apply(block, arguments);

            delete document.getElementById('barcodesTable').dataset.error;
        }
    });
});