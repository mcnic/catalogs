import Vue from 'vue'
import VueRouter from 'vue-router'
Vue.use(VueRouter);

import routerAmtel from './routerAmtel'

let routes = [
    {
        path: "/",
        name: "Каталоги",
        component: () => import('$comp/Home.vue'),
    },
    //{ path: '/e', component: Example },
];

let routesEnds = [
    {
        path: '*',
        component: () => import('$comp/NotFound.vue')
    }
];

// add all catalog routers hire
routes = routes.concat(routerAmtel);

routes = routes.concat(routesEnds);

export default new VueRouter({
    mode: 'history',
    routes // short for `routes: routes`
});