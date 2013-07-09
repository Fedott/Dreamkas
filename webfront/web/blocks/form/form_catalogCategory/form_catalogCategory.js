define(function(require) {
    //requirements
    var Form = require('kit/blocks/form/form'),
        CatalogGroupModel = require('models/catalogGroup');

    return Form.extend({
        blockName: 'form_catalogCategory',
        model: new CatalogGroupModel(),
        isAddForm: true,
        collection: null,
        templates: {
            index: require('tpl!blocks/form/form_catalogCategory/templates/index.html')
        },
        initialize: function(){
            var block = this;

            Form.prototype.initialize.call(block);

            if (block.model.id){
                block.isAddForm = false;
            }
        },
        submitSuccess: function(){
            var block = this;

            Form.prototype.submitSuccess.call(block);

            if (block.isAddForm){
                block.model = new CatalogGroupModel();
                block.clear();
                block.$el.find('[name="name"]').focus();
            }
        }
    });
});