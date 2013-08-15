define(function(require) {
    //requirements
    var Model = require('kit/model');

    var RoundingsModel = Model.extend({
        modelName: 'roundings',
        urlRoot: LH.baseApiUrl + '/roundings'
    });

    var roundingsModel = new RoundingsModel();

    roundingsModel.fetch();

    return roundingsModel;
});