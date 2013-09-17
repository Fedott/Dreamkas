define(function(require) {
    //requirements
    var app = require('kit/core/app'),
        $ = require('jquery'),
        Backbone = require('backbone'),
        _ = require('underscore'),
        currentUserModel = require('models/currentUser'),
        cookie = require('kit/utils/cookie');

    app.locale = 'root';

    var sync = Backbone.sync,
        loading,
        routers;

    Backbone.sync = function(method, model, options) {
        var syncing = sync.call(this, method, model, _.extend({}, options, {
            headers: {
                Authorization: 'Bearer ' + cookie.get('token')
            }
        }));

        syncing.fail(function(res) {
            switch (res.status) {
                case 401:
                    if (app.isStarted) {
                        document.location.reload();
                    }
                    break;
            }
        });

        return syncing;
    };

    loading = currentUserModel.fetch();

    loading.done(function() {
        app.set('permissions', currentUserModel.permissions.toJSON());
        console.log(app.permissions);
        routers = 'routers/authorized';
    });

    loading.fail(function() {
        routers = 'routers/unauthorized';
    });

    loading.always(function() {
        app.start([
            'LH',
            'blocks/navigationBar/navigationBar',
            'blocks/page/page',
            'libs/lhAutocomplete',
            routers
        ]);
    });

    return app;
});