define(function(require) {

    var router = require('router');

    return function() {

        var FirstStartResource = require('resources/firstStart/firstStart'),
            firstStartResource = new FirstStartResource;

        if (window.PAGE) {
            window.PAGE.status = null;
            document.body.removeAttribute('status');
        }
        setTimeout(function() {
            document.body.setAttribute('status', 'loading');
        }, 0);

        firstStartResource.fetch().then(function() {

            if (firstStartResource.data.complete) {
                router.navigate('dashboard');
            } else {
                router.navigate('firstStart');
            }
        })
    };
});