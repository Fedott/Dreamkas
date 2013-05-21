define(
    [
        './baseCollection.js',
        '/models/writeOffProduct.js'
    ],
    function(baseCollection, writeOffProduct) {
        return baseCollection.extend({
            initialize: function(opt){
                this.writeOffId = opt.writeOffId;
            },
            model: writeOffProduct,
            url: function() {
                return baseApiUrl + '/writeoffs/'+ this.writeOffId  + '/products.json'
            }
        });
    }
);