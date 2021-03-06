const Encore = require('@symfony/webpack-encore');
const CopyWebpackPlugin = require('copy-webpack-plugin');

Encore
    // directory where compiled assets will be stored
    .setOutputPath('public/build/')
    // public path used by the web server to access the output path
    .setPublicPath('/build')
    // only needed for CDN's or sub-directory deploy
    //.setManifestKeyPrefix('build/')
    // .splitEntryChunks()
    // .enableSingleRuntimeChunk()
    /*
     * ENTRY CONFIG
     *
     * Add 1 entry for each "page" of your app
     * (including one that's included on every page - e.g. "app")
     *
     * Each entry will result in one JavaScript file (e.g. app.js)
     * and one CSS file (e.g. app.css) if you JavaScript imports CSS.
     */
    .addEntry('js/app', './assets/js/app.js')
    .addEntry('js/profile', './assets/js/profile.js')
    .addEntry('js/period', './assets/js/period.js')
    .addEntry('js/project_edit', './assets/js/project_edit.js')
    .addEntry('js/websocket', './assets/js/websocket.js')
    .addEntry('js/clock', './assets/js/clock.js')
    .addStyleEntry('css/global', './assets/css/global.scss')
    .addStyleEntry('css/clock', './assets/css/clock.scss')

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

    // enables Sass/SCSS support
    .enableSassLoader()

    // uncomment if you use TypeScript
    //.enableTypeScriptLoader()

    // uncomment if you're having problems with a jQuery plugin
    .autoProvidejQuery()
    .addPlugin(new CopyWebpackPlugin([
      { from: './assets/images', to: 'images' }
    ]))
;

module.exports = Encore.getWebpackConfig();
