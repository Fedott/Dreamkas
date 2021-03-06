define(function(require, exports, module) {
    //requirements
    var Block = require('kit/block/block'),
        Tether = require('bower_components/tether/tether'),
        checkKey = require('kit/checkKey/checkKey');

    return Block.extend({
        template: require('ejs!./template.ejs'),
        placeholder: '',
        source: '',
        data: [],
        query: '',
        request: null,
        valueKey: 'name',
        value: '',
        clearLink: 0,
        suggestionTemplate: function() {
        },
        autofocus: false,
        events: {
            'click .autocomplete__clearLink': function(e){

                var block = this;

                block.clear();
            },
            'keydown .autocomplete__input': function(e) {

                var block = this;

                if (e.keyCode === 13 && block.$tetherElement.is(':visible')) {

                    e.preventDefault();
                    e.stopPropagation();

                    return false;
                }

                if (checkKey(e.keyCode, ['UP'])) {
                    e.preventDefault();
                    e.stopPropagation();

                    return false;
                }

                if (checkKey(e.keyCode, ['DOWN'])) {

                    e.preventDefault();
                    e.stopPropagation();

                    return false;
                }
            },
            'keyup .autocomplete__input': function(e) {

                var block = this,
                    input = e.target;

                if (checkKey(e.keyCode, ['LEFT', 'RIGHT'])) {
                    return;
                }

                if (e.keyCode === 13) {

                    if (block.$tetherElement.is(':visible')) {
                        block.select();
                        return false;
                    }

                    return;
                }

                if (checkKey(e.keyCode, ['ESC'])) {
                    block.$input.val('');
                    block.hideSuggestion();
                    block.deselect();
                    return false;
                }

                if (checkKey(e.keyCode, ['UP'])) {

                    if (block.$tetherElement.is(':hidden')) {
                        block.showSuggestion();

                    } else if (block.data.length > 0) {

                        block.focusPrevItem();
                    }

                    return false;
                }

                if (checkKey(e.keyCode, ['DOWN'])) {

                    if (block.$tetherElement.is(':hidden')) {
                        block.showSuggestion();

                    } else if (block.data.length > 0) {
                         block.focusNextItem();
                    }

                    return false;
                }

                block.query = input.value;
                block.deselect();

                if (input.value.length >= 3) {

                    block.getData();

                } else {

                    block.data = [];

                    if (input.value.length) {
                        block.showSuggestion();
                    } else {
                        block.hideSuggestion();
                    }
                }

            },
            'blur .autocomplete__input': function() {

                var block = this;

                block.hideSuggestion();
            }
        },
        initialize: function() {
            var block = this;

            var init = Block.prototype.initialize.apply(block, arguments);

            block.source = block.get('source');

            return init;
        },
        isBodyFitsBelow: function(input, body) {

            var offsetTop = input.offset().top,
                aboveHeight,
                belowHeight;

            aboveHeight = offsetTop;
            belowHeight = $(window).height() - input.height() - offsetTop;

            return ($(body).height() < belowHeight) ? true : (belowHeight > aboveHeight);
        },
        render: function() {
            var block = this;

            var render = Block.prototype.render.apply(block, arguments),
                isBodyFitsBelow;

            block.$tetherElement = block.$('.autocomplete__tether');
            block.$input = block.$('.autocomplete__input');

            if (block.$tetherElement.length) {

                block.tether = new Tether({
                    element: block.$tetherElement,
                    target: block.$input,
                    attachment: 'top left',
                    targetAttachment: 'bottom left',
                    enabled: false
                });
            }

            block.$tetherElement.off();

            block.$tetherElement
                .on('mouseover', '.autocomplete__item', function() {
                    var index = block.$tetherElement.find('.autocomplete__item').index(this);

                    block.focusItemByIndex(index);
                })
                .on('mousedown', '.autocomplete__item', function() {
                    var index = block.$tetherElement.find('.autocomplete__item').index(this);

                    block.focusItemByIndex(index);
                    block.select();
                });

            return render;
        },
        focusNextItem: function() {

            var block = this,
                $currentFocusedItem = block.$tetherElement.find('.autocomplete__item_focused'),
                $items = block.$tetherElement.find('.autocomplete__item'),
                currentFocusedItemIndex = $items.index($currentFocusedItem);

            $currentFocusedItem = block.focusItemByIndex(currentFocusedItemIndex == $items.length - 1 ? 0 : currentFocusedItemIndex + 1);

            block.scrollToItem($currentFocusedItem);
        },
        focusPrevItem: function() {

            var block = this,
                $currentFocusedItem = block.$tetherElement.find('.autocomplete__item_focused'),
                $items = block.$tetherElement.find('.autocomplete__item'),
                currentFocusedItemIndex = $items.index($currentFocusedItem);

            $currentFocusedItem = block.focusItemByIndex(currentFocusedItemIndex == -1 ? $items.length - 1 : currentFocusedItemIndex - 1);

            block.scrollToItem($currentFocusedItem);
        },
        scrollToItem: function($item) {

            var offset = $item.position().top,
                $suggestion = $item.parent(),
                suggestionHeight = $suggestion.height(),
                $items = $suggestion.find('.autocomplete__item');

            if (offset < 0 || offset > suggestionHeight) {

                if (offset < 0) {

                    offset += $suggestion.scrollTop();

                } else {
                    offset = ($items.index($item) + 1) * $items.eq(0).outerHeight() - suggestionHeight;

                }

                $suggestion.scrollTop(offset);
            }
        },
        focusItemByIndex: function(index) {

            var block = this,
                $itemToFocus = block.$tetherElement.find('.autocomplete__item').eq(index);

            $itemToFocus
                .addClass('autocomplete__item_focused')
                .siblings('.autocomplete__item')
                .removeClass('autocomplete__item_focused');

            return $itemToFocus;
        },
        showSuggestion: function() {

            var block = this,
                isBodyFitsBelow;

            block.$tetherElement.html(block.suggestionTemplate(block));

            block.$tetherElement.width(block.$input.outerWidth());

            isBodyFitsBelow = block.isBodyFitsBelow(block.$input, block.$tetherElement);

            block.tether.setOptions({
                element: block.$tetherElement,
                target: block.$input,
                attachment: isBodyFitsBelow ? 'top left' : 'bottom left',
                targetAttachment: isBodyFitsBelow ? 'bottom left' : 'top left',
                enabled: false
            });

            block.tether.enable();
            block.tether.position();
        },
        hideSuggestion: function() {

            var block = this;

            block.tether.disable();
        },
        getData: function() {
            var block = this;

            block.request && block.request.abort();

            if (typeof block.source === 'string') {

                block.el.classList.add('loading');

                block.request = $.ajax({
                    url: block.source,
                    data: {
                        query: block.query
                    }
                });

                block.request.then(function(data) {

                    block.request = null;
                    block.data = data;

                    block.showSuggestion();

                    block.el.classList.remove('loading');
                });

                return block.request;
            }
        },
        highlight: function(string){

            var block = this;

            return _.escape(string).replace(new RegExp(block.query, 'gi'), '<b>' + block.query + '</b>');
        },
        select: function(data) {

            var block = this,
                $currentFocusedItem = block.$tetherElement.find('.autocomplete__item_focused'),
                currentFocusedItemIndex = block.$tetherElement.find('.autocomplete__item').index($currentFocusedItem),
                itemData = data || block.data[currentFocusedItemIndex];

            if (!itemData){
                return;
            }

            block.$input.val(itemData[block.valueKey]);

            block.hideSuggestion();
            block.data = [];

            block.el.classList.add('autocomplete_selected');

            block.trigger('select', itemData);

        },
        deselect: function(){

            var block = this;

            block.el.classList.remove('autocomplete_selected');

            block.trigger('deselect');
        },
        clear: function(){

            var block = this;

            block.deselect();
            block.hideSuggestion();

            block.el.querySelector('.autocomplete__input').value = '';

            block.trigger('clear');
        },
        remove: function() {

            var block = this;

            block.tether && block.tether.destroy();
            block.$tetherElement.remove();

            return Block.prototype.remove.apply(block, arguments);
        }
    });
});