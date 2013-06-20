define(function(require) {
        //requirements
        var Block = require('kit/block'),
            Form = require('kit/form/form'),
            PurchaseModel = require('models/purchase');

        return Block.extend({
            purchaseModel: new PurchaseModel(),
            className: 'saleBox',
            templates: {
                index: require('tpl!./templates/saleBox.html')
            },

            events: {
                'click .saleBox__removeProductLink': function(e) {
                    var block = this,
                        $link = $(e.target);

                    $link.closest('.saleBox__productRow').remove();
                },
                'submit .saleBox__productForm': function(e) {
                    e.preventDefault();
                    var block = this;

                    block.$productRow.clone().appendTo(block.$purchaseTable.find('tbody'));
                    block.$productForm.find('input').val('');
                }
            },
            autocompleteToInput: function($input) {
                var name = $input.attr('lh_product_autocomplete');
                $input.autocomplete({
                    source: function(request, response) {
                        $.ajax({
                            url: LH.baseApiUrl + "/products/" + name + "/search.json",
                            dataType: "json",
                            data: {
                                query: request.term
                            },
                            success: function(data) {
                                response($.map(data, function(item) {
                                    return {
                                        label: item[name],
                                        product: item
                                    }
                                }));
                            }
                        })
                    },
                    minLength: 3,
                    select: function(event, ui) {
                        $(this).parents("form").find("[lh_product_autocomplete='name']").val(ui.item.product.name);
                        $(this).parents("form").find("[name='product[]']").val(ui.item.product.id);

                        $(this).parents("form").find("[name='quantity[]']").focus();
                    }
                });
                $input.keyup(function(event) {
                    var keyCode = $.ui.keyCode;
                    switch (event.keyCode) {
                        case keyCode.PAGE_UP:
                        case keyCode.PAGE_DOWN:
                        case keyCode.UP:
                        case keyCode.DOWN:
                        case keyCode.ENTER:
                        case keyCode.NUMPAD_ENTER:
                        case keyCode.TAB:
                        case keyCode.LEFT:
                        case keyCode.RIGHT:
                        case keyCode.ESCAPE:
                            return;
                            break;
                        default:
                            var term = $(this).autocomplete('getTerm');
                            if (term != $(this).val()) {
                                $(this).parents("form").find("[name='product']").val('');
                            }
                    }
                });
            },
            render: function() {
                var block = this;

                Block.prototype.render.call(this);

                block.purchaseForm = new Form({
                    el: block.$purchaseForm[0],
                    submit: function() {
                        var deferred = $.Deferred();
                        var purchaseFormData = Backbone.Syphon.serialize(this),
                            products = [];

                        _.each(purchaseFormData.product, function(product, index) {
                            products.push({
                                product: product,
                                quantity: purchaseFormData.quantity[index],
                                sellingPrice: purchaseFormData.sellingPrice[index]
                            });
                        });

                        block.purchaseModel.save({
                            products: products
                        }, {
                            success: function() {
                                alert('Продано!');
                                document.location.reload();
                                deferred.resolve();
                            },
                            error: function() {
                                alert('Ошибка!');
                                deferred.reject();
                            }
                        });

                        return deferred.promise();
                    }
                });

                block.purchaseForm.$submitButton = block.$el.find('[form="saleBox__purchaseForm"]').closest('.button');

                block.autocompleteToInput(block.$productForm.find('[lh_product_autocomplete]'));
            }
        });
    }
);