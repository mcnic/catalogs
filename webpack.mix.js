const mix = require('laravel-mix');
const VuetifyLoaderPlugin = require('vuetify-loader/lib/plugin');

var path = require("path");

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
   //.sass('resources/sass/app.scss', 'public/css')
   .version()
   .sourceMaps()
//.extract(["vue", "vuetify"]);

//const BundleAnalyzerPlugin = require('webpack-bundle-analyzer')
//   .BundleAnalyzerPlugin;

mix.webpackConfig({
   resolve: {
      extensions: ['.js', '.json', '.vue'],
      alias: {
         //'vue$': isDev ? 'vue/dist/vue.runtime.js' : 'vue/dist/vue.runtime.min.js',
         '@': path.join(__dirname, './resources/js'),
         '$comp': path.join(__dirname, './resources/js/components')
      }
   },
   output: {
      chunkFilename: 'js/[name].[contenthash].js'
   },
   plugins: [
      new VuetifyLoaderPlugin(),
      //new BundleAnalyzerPlugin()
   ],
   module: {
      rules: [
         /*{
            test: /\.vue$/,
            loader: 'vue-loader',
            include: path.join(__dirname, './resources/js')
         },*/
         {
            /*test: /\.css$/,
            loaders: [
               "style-loader",
               {
                  loader: "css-loader",
                  options: { modules: true, importLoaders: 1 }
               }
            ]*/
         }
      ]
   },
   optimization: {
      minimize: true,
      //runtimeChunk: { name: 'common' },
      splitChunks: {
         chunks: 'async',
         minSize: 30000,
         maxSize: 0,
         minChunks: 5,
         maxAsyncRequests: 5,
         maxInitialRequests: 3,
         automaticNameDelimiter: '~',
         automaticNameMaxLength: 30,
         name: true,
         cacheGroups: {
            vendors: {
               test: /[\\/]node_modules[\\/]/,
               priority: -10
            },
            default: {
               minChunks: 2,
               priority: -20,
               reuseExistingChunk: true
            }
         }
      }
   }
})

/*mix.webpackConfig({
   module: {
      rules: [
         {
            test: /\.vue$/,
            loader: 'vue-loader',

            // For some reason I don't know why removing exclude does not compile properly
            exclude: /bower_components/,

            options: {
               loaders: {
                  js: {
                     loader: 'babel-loader',
                     options: Config.babel()
                  },

                  //  Here's where you put your extra configs
                  scss: [
                     { loader: 'vue-style-loader' },
                     { loader: 'css-loader' },
                     {
                        loader: 'sass-loader',
                        options: {
                           includePaths: ['node_modules']
                        }
                     }
                  ]
               }
            }
         }
      ]
   },
   resolve: {
      extensions: ['.js', '.json', '.vue'],
      alias: {
         '~': path.join(__dirname, './resources/js'),
         '$comp': path.join(__dirname, './resources/js/components')
      }
   }
});*/

mix.browserSync('127.0.0.1');
