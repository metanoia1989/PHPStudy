const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.js('resources/js/app.js', 'public/js')
    .sourceMaps(true, 'source-map')
    .sass('resources/sass/app.scss', 'public/css')
    .styles([
        'resources/css/reset.css',
        'resources/css/default.css',
        'public/css/app.css',
        'node_modules/muse-ui/dist/muse-ui.css',
    ], 'public/css/app.css');