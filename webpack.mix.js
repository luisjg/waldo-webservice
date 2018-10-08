let mix = require('laravel-mix');

mix.options({
    processCssUrls: false,
    uglify: {
        parallel: true
    }
});
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

mix.js('resources/assets/js/app.js', 'public/js')
    .extract([
        '@fortawesome/fontawesome-free',
        'popper.js',
        'jquery',
        'metaphor-theme/node_modules/bootstrap/dist/js/bootstrap.min.js',
        'metaphor-theme/dist/js/datepicker/datepicker.js'
    ])
    .sass('resources/assets/sass/app.scss', 'public/css');

mix.copyDirectory('node_modules/@fortawesome/fontawesome-free/webfonts', 'public/webfonts');