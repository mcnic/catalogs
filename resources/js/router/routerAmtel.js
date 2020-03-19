const pref = "/" + process.env.MIX_AMTEL_PREFIX

export default [
    {
        path: pref,
        component: () => import('@@/components/Amtel/ListFirms.vue'),/* webpackChunkName: "router1" */
        name: "amtelHome"
    },
    {
        path: pref + "/:type/:firm",
        component: () => import('@@/components/Amtel/ListModels.vue'),
        name: "amtelFirms"
    },
    {
        path: pref + "/:type/:firm/:model",
        component: () => import('@@/components/Amtel/ListAutos.vue'),
        name: "amtelListAutos"
    },
    {
        path: pref + "/:type/:firm/:model/:auto",
        component: () => import('@@/components/Amtel/Goods.vue'),
        name: "amtelGoods",
    },
];