const Encore = require('@symfony/webpack-encore');

Encore
    .setOutputPath('public/')
    .setPublicPath('/bundles/markocupiccontaoaltchaantispam')
    .setManifestKeyPrefix('')


    .copyFiles({
        from: './node_modules/altcha/dist',
        to: 'altcha/js/[path][name].[hash:8].[ext]',
        pattern: /(altcha\.js)$/,

    })

    .disableSingleRuntimeChunk()
    .cleanupOutputBeforeBuild()
    .enableSourceMaps()
    .enableVersioning()

    // enables @babel/preset-env polyfills
    .configureBabelPresetEnv((config) => {
        config.useBuiltIns = 'usage';
        config.corejs = 3;
    })

    //.enablePostCssLoader()
    // Preprocessing SCSS to CSS
    //.enableSassLoader()
    //.enablePostCssLoader()
    //.addStyleEntry('css/be_stylesheet', './assets/styles/backend/scss/backend_main.scss')
;

module.exports = Encore.getWebpackConfig();
