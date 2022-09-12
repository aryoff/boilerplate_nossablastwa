const dotenvExpand = require('dotenv-expand');
dotenvExpand(require('dotenv').config({ path: '../../.env'/*, debug: true*/}));

const mix = require('laravel-mix');
require('laravel-mix-merge-manifest');

mix.setPublicPath('../../public').mergeManifest();

mix.js(__dirname + '/Resources/assets/js/app.js', 'js/nossablastwa.js')
    .sass( __dirname + '/Resources/assets/sass/app.scss', 'css/nossablastwa.css')
    .browserSync({proxy: 'http://localhost:8000',host: 'localhost.infomedia.co.id',open:'external'})
    .disableNotifications();

if (mix.inProduction()) {
    mix.version().disableNotifications();
}
