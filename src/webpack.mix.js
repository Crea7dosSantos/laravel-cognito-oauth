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
    .ts('resources/js/web/app.ts', 'public/js/web')
    .ts('resources/js/mypage/app.ts', 'public/js/mypage')
    .vue()
    .postCss('resources/css/web/app.css', 'public/css/web', [
        require('tailwindcss'),
    ])
    .postCss('resources/css/mypage/app.css', 'public/css/mypage', [
        require('tailwindcss'),
    ])
    .sourceMaps()
    .version();
