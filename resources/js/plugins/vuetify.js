// for 'mdi' - import font from .html or import in this
// <link href="https://cdn.jsdelivr.net/npm/@mdi/font@4.x/css/materialdesignicons.min.css" rel="stylesheet"> - 37k
//import mdi from '@mdi/font/css/materialdesignicons.css' // Ensure you are using css-loader -add to app.js 330k !!!

import Vuetify from 'vuetify/lib'

//import 'material-design-icons-iconfont/dist/material-design-icons.css';
//import 'vuetify/dist/vuetify.min.css'


export default new Vuetify({
  icons: {
    iconfont: 'mdi', // 'mdi' || 'mdiSvg' || 'md' || 'fa' || 'fa4'
  },
})