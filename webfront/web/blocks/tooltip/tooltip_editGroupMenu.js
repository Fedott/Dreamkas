define(
    [
        './tooltip_editMenu.js',
        './tooltip_editGroup.js',
        '/models/catalogGroup.js'
    ],
    function(Tooltip_editMenu, Tooltip_editGroup, CatalogGroupModel) {
        return Tooltip_editMenu.extend({
            groupId: null,
            events: {
                'click .tooltip__editLink': function(e){
                    e.preventDefault();
                    var block = this,
                        $el = $(e.target);

                    block.hide();

                    block.tooltip_editGroup.show();
                },
                'click .tooltip__removeLink': function(e){
                    e.preventDefault();
                    var block = this,
                        groups = block.classModel.get('groups'),
                        groupModel = new CatalogGroupModel({
                            id: block.groupId
                        });

                    groupModel.destroy({
                        success: function(){
                            block.classModel.set('groups', _.reject(groups, function(group){
                                return group.id === block.groupId
                            }));
                        }
                    });

                    block.hide();
                }
            },
            initialize: function(){
                var block = this;

                Tooltip_editMenu.prototype.initialize.call(this);

                block.tooltip_editGroup = new Tooltip_editGroup({
                    $trigger: block.$trigger,
                    classModel: block.classModel,
                    groupId: block.groupId
                });
            }
        });
    }
);