define(function(require) {
    //requirements
    var Page = require('kit/page'),
        pageParams = require('pages/catalog/params'),
        CatalogCategoryBlock = require('blocks/catalogCategory/catalogCategory'),
        CatalogProductsCollection = require('collections/catalogProducts'),
        СatalogGroupModel = require('models/catalogGroup'),
        currentUserModel = require('models/currentUser'),
        Page403 = require('pages/403/403');

    var router = new Backbone.Router();

    return Page.extend({
        pageName: 'page_catalog_category',
        templates: {
            '#content': require('tpl!./templates/category.html')
        },
        initialize: function(catalogGroupId, catalogCategoryId, catalogSubCategoryId, params){
            var page = this;

            if (page.referer && page.referer.pageName.indexOf('page_catalog') >= 0){
                _.extend(pageParams, params);
            } else {
                _.extend(pageParams, {
                    editMode: false
                }, params)
            }

            if (currentUserModel.stores && currentUserModel.stores.length){
                pageParams.storeId = currentUserModel.stores.at(0).id;
            }

            if (!pageParams.storeId && !LH.isAllow('categories', 'GET::{category}')){
                new Page403();
                return;
            }

            if (pageParams.storeId && !LH.isAllow('stores/{store}/categories/{category}')){
                new Page403();
                return;
            }

            if (page.referer && page.referer.pageName === 'page_product_form'){
                pageParams.editMode = true;
            }

            if (!LH.isAllow('groups', 'POST')) {
                pageParams.editMode = false;
            }

            router.navigate(router.toFragment(document.location.pathname, {
                editMode: pageParams.editMode,
                storeId: pageParams.storeId
            }));

            page.catalogGroupModel = new СatalogGroupModel({
                id: catalogGroupId,
                storeId: pageParams.storeId
            });

            page.catalogProductsCollection = new CatalogProductsCollection([], {
                subCategory: catalogSubCategoryId,
                storeId: pageParams.storeId
            });

            $.when(page.catalogGroupModel.fetch(), catalogSubCategoryId ? page.catalogProductsCollection.fetch() : {}).then(function(){

                page.catalogCategoryModel = page.catalogGroupModel.categories.get(catalogCategoryId);
                page.catalogSubCategoriesCollection = page.catalogCategoryModel.subCategories;

                page.render();

                new CatalogCategoryBlock({
                    el: document.getElementById('catalogCategory'),
                    catalogCategoryModel: page.catalogCategoryModel,
                    catalogSubCategoriesCollection: page.catalogSubCategoriesCollection,
                    catalogSubCategoryId: catalogSubCategoryId,
                    catalogProductsCollection: page.catalogProductsCollection,
                    editMode: pageParams.editMode
                })
            });
        }
    });
});