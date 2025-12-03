const Encore = require('@symfony/webpack-encore');

Encore
    .setOutputPath('public/')
    .setPublicPath('/bundles/markocupiccontaoaltchaantispam')
    .setManifestKeyPrefix('')

    .copyFiles({
        from: './node_modules/altcha/dist',
        to: 'altcha/dist/[path][name].[hash:8].[ext]',
        pattern: /(altcha\.js|altcha\.i18n\.js)$/,
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
;

module.exports = Encore.getWebpackConfig();
