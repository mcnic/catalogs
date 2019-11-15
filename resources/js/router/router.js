import Vue from 'vue'
import VueRouter from 'vue-router'
Vue.use(VueRouter);

import routerAmtel from './routerAmtel'

const Home = () => import('$comp/Home.vue')
const NotFound = () => import('$comp/NotFound.vue')

let routes = [
    {
        path: "/",
        name: "Главная",
        component: Home,
    },
    //{ path: '/e', component: Example },
];

let routesEnds = [
    {
        path: '*', component: NotFound
    }
];

// add all catalog routers hire
routes = routes.concat(routerAmtel);

routes = routes.concat(routesEnds);

export default new VueRouter({
    mode: 'history',
    routes // short for `routes: routes`
});