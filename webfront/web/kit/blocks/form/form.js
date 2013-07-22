define(function(require) {
    //requirements
    var Block = require('kit/block');

    require('backbone.syphon');

    var router = new Backbone.Router();

    return Block.extend({
        blockName: 'form',
        className: 'form',
        tagName: 'form',
        model: null,
        collection: null,
        redirectUrl: null,
        events: {
            'submit': function(e) {
                e.preventDefault();

                var block = this,
                    data = Backbone.Syphon.serialize(block);

                block.$submitButton.addClass('preloader_rows');

                block.removeErrors();
                block.onSubmit(data);
            }
        },
        findElements: function() {
            var block = this;

            Block.prototype.findElements.apply(block, arguments);

            block.$submitButton = block.$el.find('[type="submit"]').closest('.button').add('input[form="' + block.$el.attr('id') + '"]');
        },
        onSubmit: function(data){
            var block = this;

            if (block.model){
                block.model.save(data, {
                    success: function(model) {
                        block.onSubmitSuccess(model);
                        block.trigger('submit:success', model);
                    },
                    error: function(model, response) {
                        block.onSubmitError(JSON.parse(response.responseText));
                        block.trigger('submit:error', data);
                    },
                    complete: function(){
                        block.$submitButton.removeClass('preloader_rows');
                    }
                });
            }

            block.trigger('submit', data);
        },
        onSubmitSuccess: function(model){
            var block = this;

            if (block.collection){
                block.collection.push(model);
            }

            if (block.redirectUrl){
                router.navigate(_.result(block, 'redirectUrl') , {
                    trigger: true
                });
            }
        },
        onSubmitError: function(data){
            var block = this;
            block.showErrors(data);
        },
        showErrors: function(errors) {
            var block = this;

            block.removeErrors();
            block.$submitButton.removeClass('preloader preloader_rows');

            if (errors.children) {
                _.each(errors.children, function(data, field) {
                    var fieldErrors;

                    if (data.errors) {
                        fieldErrors = data.errors.join('. ');
                        block.$el.find("[name='" + field + "']").closest(".form__field").attr("lh_field_error", LH.text(fieldErrors));
                    }
                });
            }

            if (errors.description){
                block.$controls.attr("lh_field_error", LH.text(errors.description));
            }

        },
        removeErrors: function() {
            var block = this;
            block.$el.find('[lh_field_error]').removeAttr("lh_field_error");
        },
        disable: function(disabled) {
            var block = this;
            if (disabled) {
                block.$el.find('[type=submit]').closest('.button').addClass('button_disabled');
            } else {
                block.$el.find('[type=submit]').closest('.button').removeClass('button_disabled');
            }
        },
        clear: function() {
            var block = this;

            block.removeErrors();
            block.$submitButton.removeClass('preloader preloader_rows');

            block.$el.find(':input').each(function() {
                switch (this.type) {
                    case 'password':
                    case 'select-multiple':
                    case 'select-one':
                    case 'text':
                    case 'textarea':
                    case 'hidden':
                        $(this).val('');
                        break;
                    case 'checkbox':
                    case 'radio':
                        this.checked = false;
                }
            });
        }
    })
});