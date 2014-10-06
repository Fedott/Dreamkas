define(function(require, exports, module) {
    //requirements
    var Form = require('blocks/form/form'),
        formatMoney = require('kit/formatMoney/formatMoney'),
        normalizeNumber = require('kit/normalizeNumber/normalizeNumber');

    return Form.extend({
        template: require('ejs!./template.ejs'),
		model: require('resources/supplierReturnProduct/model'),
		models: {
			supplierReturn: require('resources/supplierReturn/model')
		},
		collection: require('resources/supplierReturnProduct/collection'),
		events: {
			'keyup input[name="quantity"]': function(e){
				var block = this;

				block.renderTotalSum();
			},
			'keyup input[name="price"]': function(e){
				var block = this;

				block.renderTotalSum();
			},
			'keyup input[name="product.name"]': function(e){
				var block = this;

				if (e.currentTarget.value.length){
					block.set('data.product', {
						id: 'xxx'
					});
				} else {
					block.set('data.product', {
						id: null
					});
				}
			},
			'click .delSupplierReturnProduct': function(e){
				var block = this,
					modelCid = e.currentTarget.dataset.modelCid;

				block.collection.remove(block.collection.get(modelCid));
			}
		},
        blocks: {
			autocomplete_products: function() {
				var block = this,
					ProductAutocomplete = require('blocks/autocomplete/autocomplete_products/autocomplete_products'),
					productAutocomplete;

				productAutocomplete = new ProductAutocomplete({
					resetLink: false
				});

				productAutocomplete.$el.on('typeahead:selected', function(e, product) {
					block.selectProduct(product);
				});

				return productAutocomplete;
            },
			totalPrice: function(){
				var TotalPrice = require('./totalPrice');

				return new TotalPrice({
					collection: this.collection
				});
			},
			productList: function(){
				var ProductList = require('./productList');

				return new ProductList({
					collection: this.collection
				});
			}
        },
		selectProduct: function(product){
            var block = this;

			setTimeout(function(){
				block.el.querySelector('input[name="price"]').focus();
			}, 0);

			block.set('data.product', {
				id: product.id
			});

            block.el.querySelector('input[name="product.name"]').value = product.name;

            if (product.purchasePrice){
                block.el.querySelector('input[name="price"]').value = formatMoney(product.purchasePrice);
            }

            block.el.querySelector('input[name="quantity"]').value = '1';
            block.$('.product__units').html(product.units || 'шт.');

            block.renderTotalSum();
        },
		getTotalSum: function() {
			var block = this,
				quantity = normalizeNumber(block.el.querySelector('input[name="quantity"]').value),
				purchasePrice = normalizeNumber(block.el.querySelector('input[name="price"]').value),
				totalPrice = quantity * purchasePrice;

			return typeof totalPrice === 'number' ? totalPrice : null;
		},
		renderTotalSum: function() {
            var block = this,
                totalPrice = block.getTotalSum();

            block.$('.totalSum').html(totalPrice ? formatMoney(totalPrice) : '');
        },
        submit: function() {
            var block = this;

            return block.collection.validateProduct(block.data);
        },
        submitSuccess: function(supplierReturn) {
            var block = this;

            block.collection.push(supplierReturn.products[0]);

            block.reset();
        },
		showErrors: function(error){
			var block = this,
				productErrors = error.errors.children.products.children[0].children;

			var fields = [],
				errorMessages = [];

			_.forEach(productErrors, function(error, field){
				if (error.errors){
					fields.push(field);
					errorMessages = _.union(errorMessages, error.errors);
				}
			});

			block.showGlobalError(errorMessages);

			_.forEach(fields, function(fieldName){

				if (fieldName === 'product'){
					fieldName = 'product.name';
				}

				block.el.querySelector('input[name="' + fieldName + '"]').classList.add('invalid');
			});
		},
        reset: function(){
            var block = this;

            Form.prototype.reset.apply(block, arguments);

            block.$('[name="product.name"]').typeahead('val', '');
            block.renderTotalSum();
            block.el.querySelector('[name="product.name"]').focus();
        }
    });
});