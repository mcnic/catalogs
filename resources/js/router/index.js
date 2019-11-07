import Vue from 'vue'
import VueRouter from 'vue-router'

import Home from '../components/Home.vue'
//import Example from '../components/ExampleComponent.vue'
import AmtelListFirms from '../components/Amtel/ListFirms.vue'
import AmtelListModels from '../components/Amtel/ListModels.vue'
import AmtelListAutos from '../components/Amtel/ListAutos.vue'
import AmtelGoods from '../components/Amtel/Goods.vue'

import NotFound from '../components/NotFound.vue'

Vue.use(VueRouter)

const catlogBu = '/' + process.env.MIX_AMTEL_PREFIX

let routes = [
    { path: '/', component: Home },
    //{ path: '/e', component: Example },
    { path: catlogBu, component: AmtelListFirms },
    { path: catlogBu + '/:type/:firm', component: AmtelListModels },
    { path: catlogBu + '/:type/:firm/:model', component: AmtelListAutos },
    { path: catlogBu + '/:type/:firm/:model/:auto', component: AmtelGoods },

    { path: '*', component: NotFound }
];

export default new VueRouter({
    mode: 'history',
    routes // short for `routes: routes`
});