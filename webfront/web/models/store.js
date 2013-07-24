define(function(require) {
        //requirements
        var Model = require('kit/model'),
            cookie = require('utils/cookie');

        return Model.extend({
            modelName: 'store',
            urlRoot: LH.baseApiUrl + '/stores',
            initData: {
                departments: require('collections/departments'),
                managers: require('collections/storeManagers')
            },
            saveFields: [
                'number',
                'address',
                'contacts'
            ],
            linkManager: function(userUrl) {
                return $.ajax({
                    url: this.url(),
                    dataType: 'json',
                    type: 'POST',
                    headers: {
                        Link: '<' + userUrl + '>; rel="ROLE_STORE_MANAGER"',
                        Authorization: 'Bearer ' + cookie.get('token')
                    },
                    data: {
                        _method: 'LINK'
                    }
                })
            },
            unlinkManager: function(userUrl) {
                return $.ajax({
                    url: this.url(),
                    dataType: 'json',
                    type: 'POST',
                    headers: {
                        Link: '<' + userUrl + '>; rel="ROLE_STORE_MANAGER"',
                        Authorization: 'Bearer ' + cookie.get('token')
                    },
                    data: {
                        _method: 'UNLINK'
                    }
                })
            }
        });
    }
);