define(function(require) {
        //requirements
        var Form = require('blocks/form/form'),
            numeral = require('numeral');

        return Form.extend({
            __name__: 'form_product',
            defaultInputLinkText: 'Введите значение',
            model: null,
            subCategoryModel: null,
            template: require('tpl!blocks/form/form_product/templates/index.html'),
            productTypeSpecificFieldsTemplates: {
                unit: require('tpl!./templates/unit.html'),
                weight: require('tpl!./templates/weight.html')
            },
            events: {
                'click .productForm__inputLink': function(e) {
                    e.preventDefault;
                    var $link = $(e.currentTarget),
                        $linkedInput = $link.prev('.productForm__linkedInput');

                    switch ($linkedInput.attr('name')) {
                        case 'retailMarkup':
                            this.showRetailMarkupInput();
                            break;
                        case 'retailPrice':
                            this.showRetailPriceInput();
                            break;
                    }
                },
                'keyup [name="purchasePrice"]': function(e) {
                    this.renderPriceInputs();

                    if (this.$retailPriceSpan.is(':hidden')) {
                        this.calculateRetailPrice();
                    }

                    if (this.$retailMarkupSpan.is(':hidden')) {
                        this.calculateRetailMarkup();
                    }
                },
                'keyup [name="retailMarkupMin"], [name="retailMarkupMax"]': function() {
                    this.calculateRetailPrice();
                },
                'keyup [name="retailPriceMin"], [name="retailPriceMax"]': function() {
                    this.calculateRetailMarkup();
                },
                'change [name="retailMarkupMin"], [name="retailMarkupMax"]': function() {
                    this.renderRetailMarkupLink();
                },
                'change [name="retailPriceMin"], [name="retailPriceMax"]': function() {
                    this.renderRetailPriceLink();
                },
                'click .productForm__productTypeRadio input': function() {
                    this.renderProductTypeSpecificFields();
                }
            },

            initialize: function(){
                var block = this;

                if (block.model.id){
                    block.redirectUrl = '/products/' + block.model.id
                } else {
                    block.redirectUrl = '/catalog/' + block.model.get('group').id + '/' + block.model.get('category').id + '/' + block.model.get('subCategory').id
                }
            },
            findElements: function(){
                var block = this;
                Form.prototype.findElements.apply(block, arguments);

                block.$retailPricePreferenceInput = block.$el.find('[name="retailPricePreference"]');
                block.$retailPriceMinInput = block.$el.find('[name="retailPriceMin"]');
                block.$retailPriceMaxInput = block.$el.find('[name="retailPriceMax"]');
                block.$retailMarkupMinInput = block.$el.find('[name="retailMarkupMin"]');
                block.$retailMarkupMaxInput = block.$el.find('[name="retailMarkupMax"]');
                block.$retailPriceSpan = block.$el.find('span.retailPrice');
                block.$retailMarkupSpan = block.$el.find('span.retailMarkup');
                block.$purchasePriceInput = block.$el.find('[name="purchasePrice"]');

                block.$retailPriceLink = block.$retailPriceSpan.next('.productForm__inputLink');
                block.$retailMarkupLink = block.$retailMarkupSpan.next('.productForm__inputLink');

                block.$retailMarkupField = block.$('.productForm__retailMarkupField');
                block.$retailPriceField = block.$('.productForm__retailPriceField');

                block.$productTypePropertiesFields = block.$('.productForm__productTypePropertiesFields');
                block.$productTypeRadio = block.$('.productForm__productTypeRadio');
            },
            render: function(){
                var block = this;

                Form.prototype.render.call(this);

                block.renderPriceInputs();
                block.renderProductTypeSpecificFields();
            },
            showRetailMarkupInput: function() {
                this.$retailPriceSpan.addClass('productForm__hiddenInput');
                this.$retailMarkupSpan
                    .removeClass('productForm__hiddenInput')
                    .focus();

                this.$retailPricePreferenceInput.val('retailMarkup');
            },
            showRetailPriceInput: function() {
                this.$retailMarkupSpan.addClass('productForm__hiddenInput');
                this.$retailPriceSpan
                    .removeClass('productForm__hiddenInput')
                    .focus();

                this.$retailPricePreferenceInput.val('retailPrice');
            },
            calculateRetailPrice: function() {
                var purchasePriceVal = this.$purchasePriceInput.val(),
                    retailMarkupMinVal = this.$retailMarkupMinInput.val(),
                    retailMarkupMaxVal = this.$retailMarkupMaxInput.val(),
                    purchasePrice = $.trim(purchasePriceVal).length ? numeral().unformat(LH.formatMoney(purchasePriceVal)) : null,
                    retailMarkupMin = $.trim(retailMarkupMinVal).length ? numeral().unformat(LH.formatMoney(retailMarkupMinVal)) : null,
                    retailMarkupMax = $.trim(retailMarkupMaxVal).length ? numeral().unformat(LH.formatMoney(retailMarkupMaxVal)) : null,
                    calculatedMinVal, calculatedMaxVal;

                if (retailMarkupMin === null || !purchasePrice || _.isNaN(purchasePrice) || _.isNaN(retailMarkupMin)) {
                    calculatedMinVal = '';
                } else {
                    calculatedMinVal = LH.formatMoney(+(retailMarkupMin / 100 * purchasePrice).toFixed(2) + purchasePrice);
                }

                if (retailMarkupMax === null || !purchasePrice || _.isNaN(purchasePrice) || _.isNaN(retailMarkupMax)) {
                    calculatedMaxVal = '';
                } else {
                    calculatedMaxVal = LH.formatMoney(+(retailMarkupMax / 100 * purchasePrice).toFixed(2) + purchasePrice);
                }

                this.$retailPriceMinInput
                    .val(retailMarkupMin !== null ? numeral().unformat(calculatedMinVal) : '')
                    .change();
                this.$retailPriceMaxInput
                    .val(retailMarkupMax !== null ? numeral().unformat(calculatedMaxVal) : '')
                    .change();
            },
            calculateRetailMarkup: function() {
                var retailPriceMinVal = this.$retailPriceMinInput.val(),
                    retailPriceMaxVal = this.$retailPriceMaxInput.val(),
                    purchasePriceVal = this.$purchasePriceInput.val(),
                    retailPriceMin = $.trim(retailPriceMinVal).length ? numeral().unformat(LH.formatMoney(retailPriceMinVal)) : null,
                    retailPriceMax = $.trim(retailPriceMaxVal).length ? numeral().unformat(LH.formatMoney(retailPriceMaxVal)) : null,
                    purchasePrice = $.trim(purchasePriceVal).length ? numeral().unformat(LH.formatMoney(purchasePriceVal)) : null,
                    calculatedMinVal, calculatedMaxVal;

                console.log(retailPriceMin, retailPriceMax);

                if (retailPriceMin === null || !purchasePrice || _.isNaN(purchasePrice) || _.isNaN(retailPriceMin)){
                    calculatedMinVal = '';
                } else {
                    calculatedMinVal = LH.formatMoney(+(retailPriceMin * 100 / purchasePrice).toFixed(2) - 100);
                }

                if (retailPriceMax === null || !purchasePrice || _.isNaN(purchasePrice) || _.isNaN(retailPriceMax)){
                    calculatedMaxVal = '';
                } else {
                    calculatedMaxVal = LH.formatMoney(+(retailPriceMax * 100 / purchasePrice).toFixed(2) - 100);
                }

                this.$retailMarkupMinInput
                    .val(retailPriceMin !== null ? numeral().unformat(calculatedMinVal) : '')
                    .change();
                this.$retailMarkupMaxInput
                    .val(retailPriceMax !== null ? numeral().unformat(calculatedMaxVal) : '')
                    .change();
            },
            disablePriceInputs: function(){
                var block = this;

                block.$retailMarkupField.addClass('form__field_disabled');
                block.$retailPriceField.addClass('form__field_disabled');

                block.$retailPriceMinInput.show().prop('disabled', true);
                block.$retailPriceMaxInput.show().prop('disabled', true);
                block.$retailMarkupMinInput.show().prop('disabled', true);
                block.$retailMarkupMaxInput.show().prop('disabled', true);
            },
            enablePriceInputs: function(){
                var block = this;

                block.$retailMarkupField.removeClass('form__field_disabled');
                block.$retailPriceField.removeClass('form__field_disabled');

                block.$retailPriceMinInput.show().prop('disabled', false);
                block.$retailPriceMaxInput.show().prop('disabled', false);
                block.$retailMarkupMinInput.show().prop('disabled', false);
                block.$retailMarkupMaxInput.show().prop('disabled', false);
            },
            renderPriceInputs: function(){
                var block = this;

                if (!block.$purchasePriceInput.val()){
                    block.disablePriceInputs();
                } else {
                    block.enablePriceInputs();
                }

                block.renderRetailMarkupLink();
                block.renderRetailPriceLink();
            },
            renderRetailPriceLink: function() {
                var priceMin = $.trim(this.$retailPriceMinInput.val()),
                    priceMax = $.trim(this.$retailPriceMaxInput.val()),
                    text;

                if (priceMin && priceMax){
                    text = LH.formatMoney(priceMin) + " - " + LH.formatMoney(priceMax) + ' руб.'
                } else {
                    text = this.defaultInputLinkText;
                }

                this.$retailPriceLink
                    .find('.productForm__inputLinkText')
                    .html(text);
            },
            renderRetailMarkupLink: function() {
                var markupMin = $.trim(this.$retailMarkupMinInput.val()),
                    markupMax = $.trim(this.$retailMarkupMaxInput.val()),
                    text;

                if (markupMin && markupMax) {
                    text = LH.formatMoney(markupMin) + " - " + LH.formatMoney(markupMax) + '%'
                } else {
                    text = this.defaultInputLinkText;
                }

                this.$retailMarkupLink
                    .find('.productForm__inputLinkText')
                    .html(text);
            },
            renderProductTypeSpecificFields: function() {
                var block = this,
                    productTypeSelected = block.$productTypeRadio.find('input:checked').val();

                block.$productTypePropertiesFields.hide(100);
                block.$productTypePropertiesFields.html(block.productTypeSpecificFieldsTemplates[productTypeSelected]({
                    model: block.model
                }));
                block.$productTypePropertiesFields.show(100);
            }
        });
    }
);