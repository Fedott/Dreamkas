define(function(require) {
    //requirements
    var $ = require('jquery'),
        Backbone = require('backbone'),
        _ = require('underscore'),
        cookie = require('utils/cookie');

    require('utils');
    require('nls');

    $(function() {
        var router = new Backbone.Router();

        $('body').on('click', '[href]', function(e) {
            e.preventDefault();
            router.navigate($(this).attr('href'), {
                trigger: true
            });
        });
    });

    return {
        start: function() {
            $('body').removeClass('preloader_spinner');
            Backbone.history.start({
                pushState: true
            });
        },
        logout: function() {
            cookie.set('token', '', {path: '/'});
            document.location.reload();
        },
        login: function(token){
            cookie.set('token', token, {path: '/'});
            document.location.reload();
        }
    }
});