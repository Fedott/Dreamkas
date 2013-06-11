define(function(require) {
        return Backbone.Block.extend({
            className: 'editor',
            editMode: false,

            events: {
                'click .editor__on': function(e) {
                    e.preventDefault();
                    var block = this;

                    block.set('editMode', true);
                },
                'click .editor__off': function(e) {
                    e.preventDefault();
                    var block = this;

                    block.set('editMode', false);
                }
            },
            initialize: function() {
                var block = this;

                Backbone.Block.prototype.initialize.call(block);

                block.set('editMode', block.editMode);
            },
            'set:editMode': function(editMode) {
                var block = this;

                if (editMode) {
                    block.$el.addClass('editor_editMode_on');
                    block.$el.removeClass('editor_editMode_off');
                } else {
                    block.$el.addClass('editor_editMode_off');
                    block.$el.removeClass('editor_editMode_on');
                }
            }
        });
    }
);