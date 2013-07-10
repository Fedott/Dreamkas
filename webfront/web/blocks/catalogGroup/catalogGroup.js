define(function(require) {
    //requirements
    var Backbone = require('backbone'),
        Editor = require('kit/blocks/editor/editor'),
        Catalog__categoryList = require('blocks/catalog/catalog__categoryList'),
        Catalog__groupNavigation = require('blocks/catalog/catalog__groupNavigation'),
        Tooltip_catalogGroupMenu = require('blocks/tooltip/tooltip_catalogGroupMenu/tooltip_catalogGroupMenu'),
        Tooltip_catalogCategoryForm = require('blocks/tooltip/tooltip_catalogCategoryForm/tooltip_catalogCategoryForm'),
        Tooltip_catalogCategoryMenu = require('blocks/tooltip/tooltip_catalogCategoryMenu/tooltip_catalogCategoryMenu'),
        CatalogCategoryModel = require('models/catalogCategory'),
        params = require('pages/catalog/params');

    var router = new Backbone.Router();

    return Editor.extend({
        blockName: 'catalogGroup',
        catalogGroupsCollection: null,
        catalogGroupModel: null,
        templates: {
            index: require('tpl!blocks/catalogGroup/templates/index.html'),
            catalog__categoryList: require('tpl!blocks/catalog/templates/catalog__categoryList.html'),
            catalog__categoryItem: require('tpl!blocks/catalog/templates/catalog__categoryItem.html'),
            catalog__groupNavigation: require('tpl!blocks/catalog/templates/catalog__groupNavigation.html')
        },
        events: {
            'click .catalog__editGroupLink': function(e) {
                e.preventDefault();

                var block = this,
                    $target = $(e.target);

                block.tooltip_catalogGroupMenu.show({
                    $trigger: $target,
                    catalogGroupModel: block.catalogGroupModel
                });
            },
            'click .catalog__addCategoryLink': function(e) {
                e.preventDefault();

                var block = this,
                    $target = $(e.target);

                block.tooltip_catalogCategoryForm.show({
                    $trigger: $target,
                    catalogCategoriesCollection: block.catalogGroupModel.categories,
                    catalogCategoryModel: new CatalogCategoryModel({
                        parentGroupModel: block.catalogGroupModel
                    })
                });
            }
        },
        listeners: {
            catalogGroupModel: {
                'destroy': function() {
                    var block = this;

                    router.navigate('/catalog', {
                        trigger: true
                    });
                }
            }
        },
        initialize: function() {
            var block = this;

            Editor.prototype.initialize.call(block);

            block.tooltip_catalogGroupMenu = new Tooltip_catalogGroupMenu({
                catalogGroupModel: block.catalogGroupModel
            });

            block.tooltip_catalogCategoryForm = new Tooltip_catalogCategoryForm({
                catalogCategoriesCollection: block.catalogGroupModel.categories,
                catalogCategoryModel: new CatalogCategoryModel({
                    parentGroupModel: block.catalogGroupModel
                })
            });

            block.tooltip_catalogCategoryMenu = new Tooltip_catalogCategoryMenu();

            new Catalog__categoryList({
                el: document.getElementById('catalog__categoryList'),
                catalogCategoriesCollection: block.catalogGroupModel.categories
            });

            new Catalog__groupNavigation({
                el: document.getElementById('catalog__groupNavigation'),
                catalogGroupModel: block.catalogGroupModel,
                catalogGroupsCollection: block.catalogGroupsCollection
            });
        },
        'set:editMode': function(editMode) {
            Editor.prototype['set:editMode'].apply(this, arguments);
            params.editMode = editMode;
        }
    });
});