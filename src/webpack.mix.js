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
    .js('resources/js/web/app.js', 'public/js/web')
    .js('resources/js/mypage/app.js', 'public/js/mypage')
    .vue()
    .postCss('resources/css/web/app.css', 'public/css/web', [
        require('tailwindcss'),
    ])
    .postCss('resources/css/mypage/app.css', 'public/css/mypage', [
        require('tailwindcss'),
    ])
    .sourceMaps()
    .version();
