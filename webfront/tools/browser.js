var webdriverio = require('webdriverio'),
    options = {
        desiredCapabilities: {
            browserName: 'chrome'
        }
    };

var resemble = require('resemblejs');

if (process.env.seleniumHost){
    options.host = process.env.seleniumHost;
}

if (process.env.seleniumPort){
    options.port = process.env.seleniumPort;
}

var browser = webdriverio
    .remote(options)
    .init();

browser.addCommand("screenDiff", function(screen, cb) {

    this.saveScreenshot('screenshots/' + screen, function(err){
        cb(err);
    });

});

after(function(done) {
    browser.end(done);
});

module.exports = browser;