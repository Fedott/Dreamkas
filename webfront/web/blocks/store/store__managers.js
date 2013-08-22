define(function(require) {
    //requirements
    var Block = require('kit/block'),
        Select_storeManagers = require('blocks/select/select_storeManagers/select_storeManagers');

    return Block.extend({
        __name__: 'store__managers',
        storeManagerCandidatesCollection: null,
        storeManagersCollection: null,
        storeModel: null,
        templates: {
            index: require('tpl!blocks/store/templates/store__managers.html'),
            store__managerItem: require('tpl!blocks/store/templates/store__managerItem.html')
        },
        events: {
            'click .store__managerRemoveLink': 'click .store__managerRemoveLink'
        },
        listeners: {
            storeManagersCollection: {
                remove: function(storeManagerModel) {
                    var block = this;

                    block.$('span[model_id="' + storeManagerModel.id + '"]').closest(".store__managerItem").remove();
                },
                add: function(storeManagerModel) {
                    var block = this;

                    block.$managerList.append(block.templates.store__managerItem({
                        storeManagerModel: storeManagerModel
                    }));
                }
            },
            storeManagerCandidatesCollection: {
                remove: function(){
                    var block = this;

                    if (!block.storeManagerCandidatesCollection.length){
                        block.$managersNotification.show();
                    }
                },
                add: function(){
                    var block = this;

                    block.$managersNotification.hide();
                }
            }
        },
        'click .store__managerRemoveLink': function(event) {
            event.stopPropagation();
            var block = this,
                $link = $(event.target),
                $item = $link.closest('.store__managerItem'),
                userId = $link.data('user_id'),
                userModel = block.storeManagersCollection.get(userId);

            $item.addClass('preloader_rows');

            block.storeModel.unlinkManager(userModel.url()).done(function(){
                $item.removeClass('preloader_rows');
                block.storeManagersCollection.remove(userModel);
                block.storeManagerCandidatesCollection.add(userModel);
            });
        },
        initialize: function(){
            var block = this;

            block.select_storeManagers = new Select_storeManagers({
                storeManagerCandidatesCollection: block.storeManagerCandidatesCollection,
                storeManagersCollection: block.storeManagersCollection,
                storeModel: block.storeModel,
                el: document.getElementById('select_storeManagers')
            });

            if (!block.storeManagerCandidatesCollection.length){
                block.$managersNotification.show();
            }
        },
        findElements: function(){
            var block = this;

            block.$managerList = block.$('.store__managerList');
            block.$managersNotification = block.$('.store__managersNotification');
        }
    });
});