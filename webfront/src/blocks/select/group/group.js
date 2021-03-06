define(function(require, exports, module) {
    //requirements
    var Select = require('blocks/select/select');

    return Select.extend({
        template: require('ejs!./template.ejs'),
        disabled: false,
        all: false,
        add: false,
        globalEvents: {
            'submit:success': function(data, block) {

                var modal = block.$el.closest('.modal')[0];

                if (modal && modal.id === 'modal_group-' + this.cid) {

                    this.selected = data.id;
                    this.render();
                }
            }
        },
        collection: function(){
            return PAGE.collections.groups;
        },
        blocks: {
            modal_group: require('blocks/modal/group/group')
        }
    });
});