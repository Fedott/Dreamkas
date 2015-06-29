define(function(require, exports, module) {
    //requirements
    var Block = require('kit/block/block'),
        router = require('router'),
        deepExtend = require('kit/deepExtend/deepExtend'),
        cookies = require('cookies'),
        _ = require('lodash'),
        googleAnalytics = require('kit/googleAnalytics/googleAnalytics');

    var posWindowReference = null,
        previousPage;

    var openPos = function(opt) {

        opt = opt || {};

        var posStoreId = cookies.get('posStoreId'),
            posUrl = opt.posUrl;

        if (posWindowReference == null || posWindowReference.closed) {
            /* if the pointer to the window object in memory does not exist
             or if such pointer exists but the window was closed */
            posWindowReference = window.open(posUrl, 'pos', 'innerWidth=1000, innerHeight=800');
            /* then create it. The new window will be created and
             will be brought on top of any other window. */
        } else {
            posWindowReference.focus();
            /* else the window reference must exist and the window
             is not closed; therefore, we can bring it back on top of any other
             window with the focus() method. There would be no need to re-create
             the window or to reload the referenced resource. */
        }

        posWindowReference.addEventListener("beforeunload", function(e) {

            if (PAGE.resources.firstStart) {
                PAGE.resources.firstStart.fetch();
            }

        }, false);
    };

    $(document)
        .on('click', '.page__posLink', function(e) {
            e.preventDefault();

            openPos({
                posUrl: e.target.href
            });
        })
        .on('click', '.page__tab', function(e) {
            e.preventDefault();

            var selector = e.currentTarget.getAttribute('rel');

            $(e.currentTarget)
                .addClass('active')
                .siblings('.active')
                .removeClass('active');

            $(selector)
                .addClass('active')
                .siblings('.active')
                .removeClass('active');
        });

    return Block.extend({

        el: function() {
            return document.querySelector('.page') || document.createElement('div');
        },

        params: {},

        template: require('ejs!./template.ejs'),

        activeNavigationItem: 'main',

        currentUserModel: require('resources/currentUser/model.inst'),

        posUrl: function() {
            var posStoreId = cookies.get('posStoreId');

            return '/pos' + (posStoreId ? ('/stores/' + posStoreId) : '');
        },

        content: function() {
            return '<h1>Добро пожаловать в Lighthouse!</h1>';
        },

        initialize: function() {
            var page = this,
                modal__wrapper = document.getElementById('modal__wrapper');

            page.setStatus('starting');
            page.setStatus('loading');

            previousPage = window.PAGE;
            window.PAGE = page;

            modal__wrapper && modal__wrapper.classList.remove('modal__wrapper_visible');
            document.body.classList.remove('modal-open');

            Block.prototype.initialize.apply(page, arguments);

            return page.fetch();
        },

        render: function() {
            var page = this,
                args = [].slice.call(arguments),
                autofocus;

            Block.prototype.render.apply(page, args);

            if (previousPage) {
                previousPage.remove();
                previousPage = null;
            }

            autofocus = page.el.querySelector('[autofocus]');

            if (autofocus) {
                setTimeout(function() {
                    autofocus.focus();
                }, 0);
            }

            page.setStatus('loaded');

            googleAnalytics.sendPageview();
        },

        remove: function() {
            var page = this;

            page.unbind();
            page.removeBlocks();
        },

        setStatus: function(status) {
            var page = this;

            page.trigger(status);

            if (status === 'loading' && window.PAGE && window.PAGE.status === 'loading') {
                document.body.removeAttribute('status');
            }

            setTimeout(function() {
                page.status = status;
                document.body.setAttribute('status', status);
            }, 0);
        },

        setParams: function(params, opt) {
            var page = this;

            opt = deepExtend({
                render: false
            }, opt);

            deepExtend(page.params, params);

            router.save(_.transform(page.params, function(result, value, key) {
                result[key] = _.isPlainObject(value) ? JSON.stringify(value) : value;
            }));

            if (opt.render) {
                page.render();
            } else {
                googleAnalytics.sendPageview();
            }
        }
    });
});