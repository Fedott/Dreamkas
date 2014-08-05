define(function(require) {
    //requirements
    var Form = require('kit/form/form'),
        config = require('config'),
        cookie = require('cookies');

    var authorizationHeader = 'Bearer ' + cookie.get('token'),
        configUrl = config.baseApiUrl + '/configs';

    return Form.extend({
        el: '.form_importSettings',
        successMessage: 'Настройки успешно сохранены',

        submit: function() {
            var block = this;

            return Promise.all([
                block.saveImportUrl(block.formData['set10-import-url']),
                block.saveImportLogin(block.formData['set10-import-login']),
                block.saveImportPassword(block.formData['set10-import-password']),
                block.saveImportInterval(block.formData['set10-import-interval'])
            ]).then(function(results){

                var importUrl = results[0],
                    importLogin = results[1],
                    importPassword = results[2],
                    importInterval = results[3];

                block.set('set10ImportUrl.id', importUrl[0].id);
                block.set('set10ImportLogin.id', importLogin[0].id);
                block.set('set10ImportPassword.id', importPassword[0].id);
                block.set('set10ImportInterval.id', importInterval[0].id);
            });
        },
        submitError: function() {
            var block = this;

            block.showErrors({error: 'Настройки не сохранены. Обратитесь к администратору.'})
        },
        saveImportUrl: function(url) {
            var block = this;

            return $.ajax({
                url: configUrl + (block.get('set10ImportUrl.id') ? '/' + block.get('set10ImportUrl.id') : ''),
                dataType: 'json',
                type: block.get('set10ImportUrl.id') ? 'PUT' : 'POST',
                data: {
                    name: 'set10-import-url',
                    value: url
                },
                headers: {
                    Authorization: authorizationHeader
                }
            })
        },
        saveImportLogin: function(login) {
            var block = this;

            return $.ajax({
                url: configUrl + (block.get('set10ImportLogin.id') ? '/' + block.get('set10ImportLogin.id') : ''),
                dataType: 'json',
                type: block.get('set10ImportLogin.id') ? 'PUT' : 'POST',
                data: {
                    name: 'set10-import-login',
                    value: login
                },
                headers: {
                    Authorization: authorizationHeader
                }
            })
        },
        saveImportPassword: function(password) {
            var block = this;

            return $.ajax({
                url: configUrl + (block.get('set10ImportPassword.id') ? '/' + block.get('set10ImportPassword.id') : ''),
                dataType: 'json',
                type: block.get('set10ImportPassword.id') ? 'PUT' : 'POST',
                data: {
                    name: 'set10-import-password',
                    value: password
                },
                headers: {
                    Authorization: authorizationHeader
                }
            })
        },
        saveImportInterval: function(interval) {
            var block = this;

            return $.ajax({
                url: configUrl + (block.get('set10ImportInterval.id') ? '/' + block.get('set10ImportInterval.id') : ''),
                dataType: 'json',
                type: block.get('set10ImportInterval.id') ? 'PUT' : 'POST',
                data: {
                    name: 'set10-import-interval',
                    value: interval
                },
                headers: {
                    Authorization: authorizationHeader
                }
            })
        }
    });
});