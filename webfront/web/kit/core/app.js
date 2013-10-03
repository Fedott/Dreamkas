define(function(require) {
    //requirements
    var setter = require('../utils/setter'),
        getter = require('../utils/getter'),
        Backbone = require('backbone'),
        cookie = require('../utils/cookie');

    require('lodash');

    var router = new Backbone.Router();

    $(document).on('click', '[href]', function(e){
        e.preventDefault();
        var $target = $(e.currentTarget);

        router.navigate($target.attr('href'), {
            trigger: true
        });
    });

    return _.extend({
            permissions: {},
            apiUrl: null,
            currentPage: null,
            locale: cookie.get('locale'),
            isStarted: false,

            start: function(deps) {
                var app = this;

                requirejs({
                    locale: app.locale
                }, deps, function() {

                    Backbone.history.start({
                        pushState: true
                    });

                    app.set('isStarted', true);
                });
            },
            'set:locale': function(locale) {
                cookie.set('locale', locale);
                document.location.reload();
            }
        },
        getter,
        setter,
        Backbone.Events);
});