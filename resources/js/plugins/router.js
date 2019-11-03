import Vue from 'vue'
import VueRouter from 'vue-router'

import Home from '../components/Home.vue'
import Example from '../components/ExampleComponent.vue'

Vue.use(VueRouter)


let routes = [
    { path: '/', component: Home },
    { path: '/e', component: Example },
    // {path: '*', component: NotFoundView}
];

export default new VueRouter({
    mode: 'history',
    routes // short for `routes: routes`
});