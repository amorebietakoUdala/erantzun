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
    .setPublicPath('/erantzun/build')
    // only needed for CDN's or sub-directory deploy
    .setManifestKeyPrefix('build/')

/*
 * ENTRY CONFIG
 *
 * Each entry will result in one JavaScript file (e.g. app.js)
 * and one CSS file (e.g. app.css) if your JavaScript imports CSS.
 */
.addEntry('app', './assets/app.js')
    .addEntry('login', './assets/js/login/login.js')
    .addEntry('zerbitzua_list', './assets/js/zerbitzua/list.js')
    .addEntry('zerbitzua_edit', './assets/js/zerbitzua/edit.js')
    .addEntry('erabiltzailea_list', './assets/js/erabiltzailea/list.js')
    .addEntry('erabiltzailea_edit', './assets/js/erabiltzailea/edit.js')
    .addEntry('enpresa_list', './assets/js/enpresa/list.js')
    .addEntry('enpresa_edit', './assets/js/enpresa/edit.js')
    .addEntry('egoera_list', './assets/js/egoera/list.js')
    .addEntry('egoera_edit', './assets/js/egoera/edit.js')
    .addEntry('eskakizun_mota_list', './assets/js/eskakizun_mota/list.js')
    .addEntry('eskakizun_mota_edit', './assets/js/eskakizun_mota/edit.js')
    .addEntry('jatorria_list', './assets/js/jatorria/list.js')
    .addEntry('jatorria_edit', './assets/js/jatorria/edit.js')
    .addEntry('eskatzailea_list', './assets/js/eskatzailea/list.js')
    .addEntry('eskatzailea_edit', './assets/js/eskatzailea/edit.js')
    .addEntry('eskakizuna_list', './assets/js/eskakizuna/list.js')
    .addEntry('eskakizuna_edit', './assets/js/eskakizuna/edit.js')
    .addEntry('estatistika_view', './assets/js/estatistika/view.js')


// enables the Symfony UX Stimulus bridge (used in assets/bootstrap.js)
// .enableStimulusBridge('./assets/controllers.json')

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
.enableIntegrityHashes(Encore.isProduction())

// uncomment if you're having problems with a jQuery plugin
.autoProvidejQuery()
    .copyFiles({
        from: './assets/images',
        to: 'images/[path][name].[hash:8].[ext]',
        from: './node_modules/tinymce/skins',
        to: 'skins/[path][name].[ext]'
    })
    // .copyFiles({

// })
;

module.exports = Encore.getWebpackConfig();