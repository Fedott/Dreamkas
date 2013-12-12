define(function(require) {
        //requirements
        var Tooltip_menu = require('blocks/tooltip/tooltip_menu/tooltip_menu'),
            CatalogSubcategoryModel = require('models/catalogSubcategory'),
            Tooltip_catalogSubcategoryForm = require('blocks/tooltip/tooltip_catalogSubcategoryForm/tooltip_catalogSubcategoryForm');

        return Tooltip_menu.extend({
            __name__: 'tooltip_catalogSubcategoryMenu',
            catalogSubcategoryModel: new CatalogSubcategoryModel(),
            events: {
                'click .tooltip__editLink': function(e) {
                    e.preventDefault();
                    var block = this,
                        $target = $(e.target);

                    block.tooltip_catalogSubcategoryForm.show({
                        model: block.catalogSubcategoryModel,
                        collection: null,
                        $trigger: $target
                    });

                    block.hide();
                },
                'click .tooltip__removeLink': function(e) {
                    e.preventDefault();
                    var block = this,
                        $target = $(e.target);

                    if ($target.hasClass('preloader_rows')) {
                        return;
                    }

                    $target.addClass('preloader_rows');
                    block.catalogSubcategoryModel.destroy({
                        complete: function() {
                            $target.removeClass('preloader_rows');
                            block.hide();
                        },
                        error: function(model, response) {
                            alert(LH.translate(response.responseJSON.message));
                        }
                    });
                }
            },
            initialize: function() {
                var block = this;

                block.tooltip_catalogSubcategoryForm = $('[block="tooltip_catalogSubcategoryForm"]').data('tooltip_catalogSubcategoryForm') || new Tooltip_catalogSubcategoryForm({
                    model: block.catalogSubcategoryModel
                });
            },
            remove: function() {
                var block = this;

                block.tooltip_catalogSubcategoryForm.remove();

                Tooltip_menu.prototype.remove.call(block);
            }
        });
    }
);