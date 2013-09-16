define(function(require) {
        //requirements
        var deepExtend = require('./deepExtend.js');

        // Helper function to correctly set up the prototype chain, for subclasses.
        // Similar to `goog.inherits`, but uses a hash of prototype properties and
        // class properties to be extended.
        return function(protoProps, staticProps) {
            var parent = this;
            var child,
                instance = true;

            // The constructor function for the new subclass is either defined by you
            // (the "constructor" property in your `extend` definition), or defaulted
            // by us to simply call the parent's constructor.
            if (protoProps && _.has(protoProps, 'constructor')) {
                child = protoProps.constructor;
            } else {
                child = function() {
                    var args;
                    if (this instanceof child){
                        args = instance ? arguments : arguments[0];
                        instance = true;
                        return parent.apply(this, args);
                    } else {
                        instance = false;
                        return new child(arguments);
                    }
                };
            }

            // Add static properties to the constructor function, if supplied.
            deepExtend(child, parent, staticProps);

            // Set the prototype chain to inherit from `parent`, without calling
            // `parent`'s constructor function.
            var Surrogate = function() {
                this.constructor = child;
            };
            Surrogate.prototype = parent.prototype;
            child.prototype = new Surrogate;

            // Add prototype properties (instance properties) to the subclass,
            // if supplied.
            if (protoProps) deepExtend(child.prototype, protoProps);

            // Set a convenience property in case the parent's prototype is needed
            // later.
            child.__super__ = parent.prototype;

            return child;
        };
    }
);