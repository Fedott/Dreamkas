define(function(require) {
        //requirements
        var Block = require('kit/core/block.deprecated');

        return Block.extend({
            __name__: 'department',
            storeModel: null,
            departmentModel: null,
            template: require('ejs!blocks/department/templates/index.html')
        })
    }
);
