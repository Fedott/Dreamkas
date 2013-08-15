// Karma configuration
// Generated on Wed Aug 07 2013 21:58:33 GMT+0400 (MSK)


// base path, that will be used to resolve files and exclude
basePath = '';


// list of files / patterns to load in the browser
files = [
    JASMINE,
    JASMINE_ADAPTER,
    REQUIRE,
    REQUIRE_ADAPTER,
    'web/config.js',
    'web/libs/jquery/jquery.min.js',
    'web/libs/jquery-ui/ui/minified/jquery-ui.min.js',
    'web/libs/underscore/underscore.min.js',
    'web/libs/backbone/backbone.min.js',
    'karma.main.js',

    {pattern: 'web/**/*.spec.js', included: false}
];


// list of files to exclude
exclude = [];


// test results reporter to use
// possible values: 'dots', 'progress', 'junit'
reporters = ['dots', 'coverage'];

preprocessors = {
    // source files, that you wanna generate coverage for
    // do not include tests or libraries
    // (these files will be instrumented by Istanbul)
    '**/utils/*.js': ['coverage']
};

coverageReporter = {
    type : 'html',
    dir : 'coverage/'
};


// web server port
port = 9876;


// cli runner port
runnerPort = 9100;


// enable / disable colors in the output (reporters and logs)
colors = true;


// level of logging
// possible values: LOG_DISABLE || LOG_ERROR || LOG_WARN || LOG_INFO || LOG_DEBUG
logLevel = LOG_INFO;


// enable / disable watching file and executing tests whenever any file changes
autoWatch = false;


// Start these browsers, currently available:
// - Chrome
// - ChromeCanary
// - Firefox
// - Opera
// - Safari (only Mac)
// - PhantomJS
// - IE (only Windows)
browsers = ['Chrome'];


// If browser does not capture in given timeout [ms], kill it
captureTimeout = 60000;


// Continuous Integration mode
// if true, it capture browsers, run tests and exit
singleRun = false;
