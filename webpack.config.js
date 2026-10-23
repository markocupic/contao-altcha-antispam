const Encore = require('@symfony/webpack-encore');

Encore
    .setOutputPath('public/')
    .setPublicPath('/bundles/markocupiccontaoaltchaantispam')
    .setManifestKeyPrefix('')

    // Use the "external" build of ALTCHA: It does not inject inline styles and
    // does not create its web worker from a blob: URL, so it works with a strict
    // Content Security Policy (style-src 'self'; worker-src 'self').
    .copyFiles({
        from: './node_modules/altcha/dist_external',
        to: 'altcha/dist_external/[path][name].[hash:8].[ext]',
        pattern: /(altcha\.js|altcha\.css|worker\.js)$/,
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
