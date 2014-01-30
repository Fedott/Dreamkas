define(function(require) {
        //requirements
        var Block = require('../../core/block'),
            moment = require('moment'),
            Datepicker = require('../datepicker/datepicker'),
            Tooltip = require('../tooltip/tooltip');

        require('jquery.maskedinput');

        return Block.extend({
            __name__: 'inputDate',
            className: 'inputDate',
            tagName: 'input',
            date: null,
            noTime: false,
            templates: {
                datepicker__controls: require('tpl!kit/blocks/inputDate/templates/datepicker__controls.html')
            },

            initialize: function() {

                var block = this,
                    date;

                if (block.noTime){
                    block.dateFormat = 'DD.MM.YYYY';
                } else {
                    block.dateFormat = 'DD.MM.YYYY HH:mm'
                }

                date = moment(block.$el.val(), block.dateFormat);

                block.tooltip = new Tooltip({
                    $trigger: block.$el
                });

                block.datepicker = new Datepicker({
                    templates: {
                        controls: block.templates.datepicker__controls
                    },
                    noTime: block.noTime
                });

                block.tooltip.$el.addClass('inputDate__tooltip');
                block.datepicker.$el.attr({rel: block.$el.attr('name')});
                block.tooltip.$content.html(block.datepicker.$el);

                if (date){
                    block.set('date', date.valueOf());
                }

                if (block.noTime){
                    block.$el.mask('99.99.9999');
                } else {
                    block.$el.mask('99.99.9999 99:99');
                }

                block.listenTo(block.datepicker, {
                    'set:selectedDate': function(date){
                        block.set('date', date, {
                            updateDatepicker: false
                        });
                    }
                });
            },
            'set:date': function(date, extra) {
                var block = this;

                extra = _.extend({
                    updateInput: true,
                    updateDatepicker: true
                }, extra);

                if (extra.updateInput){
                    block.$el.val(date ? moment(date).format(block.dateFormat) : '');
                }

                if (extra.updateDatepicker){
                    block.datepicker.set('selectedDate', date);
                }
            },
            events: {
                'focus': function(e) {
                    var block = this;

                    block.showDatePicker();
                },
                'change': function(e){
                    var block = this,
                        date = moment(block.$el.val(), block.dateFormat);

                    if (date){
                        block.set('date', date.valueOf(), {
                            updateInput: false
                        });
                    }
                }
            },
            showDatePicker: function() {
                var block = this;

                block.tooltip.show();
            },
            remove: function(){
                var block = this;

                block.tooltip.remove();

                Block.prototype.remove.call(block);
            }
        });
    }
);