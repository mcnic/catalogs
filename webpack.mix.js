const mix = require('laravel-mix');
const VuetifyLoaderPlugin = require('vuetify-loader/lib/plugin');

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
   .sass('resources/sass/app.scss', 'public/css')
   .version()
   //.sourceMaps()
   .extract(["vue", "vuetify"]);

const BundleAnalyzerPlugin = require('webpack-bundle-analyzer')
   .BundleAnalyzerPlugin;

mix.webpackConfig({
   resolve: {
      extensions: ['.js', '.json', '.vue'],
      alias: {
         '~': path.join(__dirname, './resources/js'),
         '$comp': path.join(__dirname, './resources/js/components')
      }
   },
   plugins: [
      new VuetifyLoaderPlugin(),
      //new BundleAnalyzerPlugin()
   ],
   
})

mix.browserSync('127.0.0.1');