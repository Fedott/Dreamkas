define(function(require) {
        //requirements
        var Block = require('kit/core/block');

        return Block.extend({
            __name__: 'department',
            storeModel: null,
            departmentModel: null,
            template: require('tpl!blocks/department/templates/index.html')
        })
    }
);
