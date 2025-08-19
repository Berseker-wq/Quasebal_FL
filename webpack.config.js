const Encore = require('@symfony/webpack-encore');

// Manually configure the runtime environment if not already configured yet by the "encore" command.
if (!Encore.isRuntimeEnvironmentConfigured()) {
    Encore.configureRuntimeEnvironment(process.env.NODE_ENV || 'dev');
}

Encore
    // directory where compiled assets will be stored
    .setOutputPath('public/build/')
    // public path used by the web server to access the output path
    .setPublicPath('/build')

    // Each entry will result in one JavaScript file (e.g. app.js) and one CSS file if CSS is imported
    .addEntry('app', './assets/app.js')
    .addEntry('app1', './react/app1/index.js')


    // When enabled, Webpack splits your files into smaller pieces for optimization
    .splitEntryChunks()

    // Use a single runtime chunk (recommended)
    .enableSingleRuntimeChunk()

    // Clean the output directory before building
    .cleanupOutputBeforeBuild()

    // Enable source maps in dev mode
    .enableSourceMaps(!Encore.isProduction())

    // Enable hashed filenames (cache busting) in production
    .enableVersioning(Encore.isProduction())

    // Configure Babel preset-env for polyfills
    .configureBabelPresetEnv((config) => {
        config.useBuiltIns = 'usage';
        config.corejs = '3.38';
    })

    // Uncomment if you use Sass/SCSS
    //.enableSassLoader()

    // Uncomment if you use TypeScript
    //.enableTypeScriptLoader()

    // Uncomment if you use React
    .enableReactPreset()

    // Uncomment to get integrity attributes (requires WebpackEncoreBundle 1.4+)
    //.enableIntegrityHashes(Encore.isProduction())

    // Uncomment if you're having problems with jQuery plugins
    //.autoProvidejQuery()

    // Copy images from assets/images to public/build/images
    //.copyFiles({
        //from: './assets/images',
        //to: 'images/[path][name].[ext]',
   // })
//;

module.exports = Encore.getWebpackConfig();

