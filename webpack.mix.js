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

mix.js('public/assets/dist/js/custom.js', 'assets/dist/js/custom.min.js')
    .css('public/assets/dist/css/custom.css', 'public/assets/dist/css/custom.min.css')
    .sourceMaps();
