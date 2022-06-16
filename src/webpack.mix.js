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

mix.browserSync('http://localhost:80')
    .js('resources/js/admin/app.js', 'public/js/admin')
    .js('resources/js/mypage/app.js', 'public/js/mypage')
    .vue()
    .postCss('resources/css/admin/app.css', 'public/css/admin', [
        require('tailwindcss'),
    ])
    .postCss('resources/css/mypage/app.css', 'public/css/mypage', [
        require('tailwindcss'),
    ])
    .sourceMaps()
    .version();
