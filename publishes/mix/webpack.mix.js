let mix = require('laravel-mix');
let editors = require('./editors');

mix.extend('editors', (webpackConfig, ...args) => {
    (new editors(mix)).enable(...args);
});
mix.setPublicPath('build');

mix
    .js('resources/js/app.js', 'build')
    .js('resources/js/theme.js', 'build')
    .js('resources/js/vendor.js', 'build')
    .extract([
        'vue',
        'vue-clip',
        'vue-carousel',
        'fancybox',
        'axios',
        'element-ui',
        'tinymce',
    ])
    .sass('resources/sass/app.scss', 'build/app.css')
    .sass('resources/sass/vendor.scss', 'build/vendor.css')
    .less('resources/less/glyphicons.less', 'build/glyphicons.css')
    .editors('Medium')
    .copy('build', '../public/admin')
    .copy('resources/images', '../public/admin/images')
    .disableSuccessNotifications()
    .sourceMaps()
    .webpackConfig({
        resolve: {
            alias: {
                // Enable if using jquery-ui/sortable
                //'jquery-ui/sortable': 'jquery-ui/ui/widgets/sortable',
            },
        },
        /* Do not load MomentJS locales */
        plugins: [
            new (require('webpack')).IgnorePlugin(/^\.\/locale$/, /moment$/),
        ]
    });