const pref = "/" + process.env.MIX_AMTEL_PREFIX

export default [
    {
        path: pref,
        component: () => import('@@/components/Amtel/ListFirms.vue'),/* webpackChunkName: "router1" */
        name: "amtelHome"
    },
    {
        path: pref + "/:type/:firm",
        component: () => import('@@/components/Amtel/ListModelGroups.vue'),
        name: "amtelModelGroups"
    },
    {
        path: pref + "/:type/:firm/:group",
        component: () => import('@@/components/Amtel/ListModels.vue'),
        name: "amtelModels"
    },
    {
        path: pref + "/:type/:firm/:group/:model",
        component: () => import('@@/components/Amtel/Goods.vue'),
        name: "amtelListAutos"
    },
    /*{
        path: pref + "/:type/:firm/:group/:model/:auto",
        component: () => import('@@/components/Amtel/Goods.vue'),
        name: "amtelGoods",
    }*/
];