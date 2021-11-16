const Encore = require('@symfony/webpack-encore');

// Manually configure the runtime environment if not already configured yet by the "encore" command.
// It's useful when you use tools that rely on webpack.config.js file.
if (!Encore.isRuntimeEnvironmentConfigured()) {
    Encore.configureRuntimeEnvironment(process.env.NODE_ENV || 'dev');
}

Encore
    // directory where compiled assets will be stored
    .setOutputPath('public/build/')
    // public path used by the web server to access the output path
    .setPublicPath('/build')
    // only needed for CDN's or sub-directory deploy
    //.setManifestKeyPrefix('build/')

    /*
     * ENTRY CONFIG
     *
     * Each entry will result in one JavaScript file (e.g. app.js)
     * and one CSS file (e.g. app.css) if your JavaScript imports CSS.
     */
    .addEntry('app', './assets/app.js')
    .addEntry('style', './assets/css/style.css')
    .addEntry('pdf', './assets/css/pdf.css')
    .addEntry('calendarcss', './assets/css/calendar.css')
    .addEntry('calendarjs', './assets/js/calendar.js')
    .addEntry('off-canvas', './assets/js/off-canvas.js')
    .addEntry('hoverable-collapse', './assets/js/hoverable-collapse.js')
    .addEntry('template', './assets/js/template.js')
    .addEntry('dashboard', './assets/js/dashboard.js')
    .addEntry('data-table', './assets/js/data-table.js')
    .addEntry('jquery.dataTables', './assets/js/jquery.dataTables.js')
    .addEntry('dataTables.bootstrap4', './assets/js/dataTables.bootstrap4.js')
    .addEntry('usersList', './assets/js/usersList.js')
    .addEntry('activitiesList', './assets/js/activitiesList.js')
    .addEntry('citiesList', './assets/js/citiesList.js')
    .addEntry('socialWorkersList', './assets/js/socialWorkersList.js')
    .addEntry('orientationSheetsList', './assets/js/orientationSheetsList.js')
    .addEntry('attendanceSheetsList', './assets/js/attendanceSheetsList.js')
    .addEntry('placesList', './assets/js/placesList.js')
    .addEntry('educatorsList', './assets/js/educatorsList.js')
    .addEntry('structuresList', './assets/js/structuresList.js')
    .addEntry('tenantsList', './assets/js/tenantsList.js')
    .addEntry('holidaysList', './assets/js/holidaysList.js')

    // enables the Symfony UX Stimulus bridge (used in assets/bootstrap.js)
    .enableStimulusBridge('./assets/controllers.json')

    // When enabled, Webpack "splits" your files into smaller pieces for greater optimization.
    .splitEntryChunks()

    // will require an extra script tag for runtime.js
    // but, you probably want this, unless you're building a single-page app
    .enableSingleRuntimeChunk()

    /*
     * FEATURE CONFIG
     *
     * Enable & configure other features below. For a full
     * list of features, see:
     * https://symfony.com/doc/current/frontend.html#adding-more-features
     */
    .cleanupOutputBeforeBuild()
    .enableBuildNotifications()
    .enableSourceMaps(!Encore.isProduction())
    // enables hashed filenames (e.g. app.abc123.css)
    .enableVersioning(Encore.isProduction())

    .configureBabel((config) => {
        config.plugins.push('@babel/plugin-proposal-class-properties');
    })

    // enables @babel/preset-env polyfills
    .configureBabelPresetEnv((config) => {
        config.useBuiltIns = 'usage';
        config.corejs = 3;
    })

    // enables Sass/SCSS support
    .enableSassLoader()

    // uncomment if you use TypeScript
    //.enableTypeScriptLoader()

    // uncomment if you use React
    //.enableReactPreset()

    // uncomment to get integrity="..." attributes on your script & link tags
    // requires WebpackEncoreBundle 1.4 or higher
    //.enableIntegrityHashes(Encore.isProduction())

    // uncomment if you're having problems with a jQuery plugin
    .autoProvidejQuery()
;

module.exports = Encore.getWebpackConfig();
