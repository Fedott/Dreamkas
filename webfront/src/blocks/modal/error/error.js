define(function(require, exports, module) {
    //requirements
    var Modal = require('blocks/modal/modal'),
        _ = require('lodash');

    return Modal.extend({
        template: require('ejs!./template.ejs'),

        jsError: null,
        apiError: null,

        events: {
            'click .modal_error__continueButton': function(e) {
                this.hide();
            }
        },
        initialize: function(data) {
            var block = this;

            Modal.prototype.initialize.apply(block, arguments);

            if (window.ERROR) {

                _.extend(window.ERROR, data);

                window.ERROR.remove();
            }

            block.render();

            window.ERROR = block;
        },
        toggle: function(){
            var block = this;

            block.$el.addClass('modal_visible');
            block.showWrapper();
        },
        hide: function() {
            var block = this,
                modals = $('.modal_visible');

            if (modals.length == 1) {
                block.hideWrapper();
            }

            block.$el.removeClass('modal_visible');
            block.reset();

            delete window.ERROR;
        }
    });
});