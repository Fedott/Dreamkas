define(function(require) {
        //requirements
        var Tooltip = require('kit/blocks/tooltip/tooltip'),
            CatalogCategoryModel = require('models/catalogCategory'),
            Form_catalogCategory = require('blocks/form/form_catalogCategory/form_catalogCategory');

        return Tooltip.extend({
            blockName: 'tooltip_catalogCategoryForm',
            catalogCategoryModel: new CatalogCategoryModel(),
            catalogCategoriesCollection: null,
            isAddForm: true,
            templates: {
                content: require('tpl!blocks/tooltip/tooltip_catalogCategoryForm/templates/content.html')
            },
            listeners: {
                form: {
                    'submit:success': function() {
                        var block = this;
                        if (!block.form.isAddForm) {
                            block.hide();
                        }
                    }
                }
            },
            initialize: function() {
                var block = this;

                Tooltip.prototype.initialize.call(this);

                block.form = new Form_catalogCategory({
                    el: block.el.getElementsByClassName('form'),
                    model: block.catalogCategoryModel,
                    collection: block.catalogCategoriesCollection
                });
            },
            align: function() {
                var tooltip = this;

                tooltip.$el
                    .css({
                        top: tooltip.$trigger.offset().top - (tooltip.$el.outerHeight() - tooltip.$trigger.outerHeight()) / 2,
                        left: tooltip.$trigger.offset().left
                    })
            },
            show: function(opt) {
                var block = this;

                Tooltip.prototype.show.apply(block, arguments);

                block.form.$el.find('[type="text"]').eq(0).focus();
            }
        });
    }
);