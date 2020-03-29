//const api = () => import('../../api/apiAmtel')
import Api from '../../api/apiAmtel'

const debug = process.env.MIX_NODE_ENV !== 'production'

const state = {
  debug: debug,
  typeAutos: '', // cars/trucks
  firm: '',
  firms: {}, // list firms for ListFirm component
  models: {
    models: [],
    avail: {
      result: false
    }
  }, //for ListModels component
  model: '',
  modelId: '',
  modelGroups: [],
  modelGroup: '',
  goodsList: [],
  goods: {}
}

const getters = {
  debug: state => {
    return state.debug
  },
  lightCars: (state, getters) => {
    return state.firms.lightCars
  },
  trucks: (state, getters) => {
    return state.firms.trucks
  },
  breadCrumbs: state => {
    return state.breadCrumbs
  },
  typeAutos: state => {
    return state.typeAutos
  },
  firm: state => {
    return state.firm
  },
  modelGroups: state => {
    return state.modelGroups
  },
  modelGroup: state => {
    return state.modelGroup
  },
  models: state => {
    return state.models
  },
  model: state => {
    return state.model
  },
  modelId: state => {
    return state.modelId
  },
  goodsList: state => {
    return state.goodsList
  },
  goods: state => {
    return state.goods
  }

}

const mutations = {
  increment(state) {
    state.count++
  },
  fillFirms(state, firms) {
    state.firms = firms
  },
  fillModelGroups(state, modelGroups) {
    state.modelGroups = modelGroups
  },
  fillModels(state, models) {
    state.models = models
  },
  setBreadCrumbs(state, arr) {
    state.breadCrumbs = arr;
  },
  setTypeAutos(state, typeAutos) {
    state.typeAutos = typeAutos;
  },
  setFirm(state, firm) {
    state.firm = firm;
  },
  setModelGroup(state, modelGroup) {
    state.modelGroup = modelGroup;
  },
  setModel(state, payload) {
    state.model = payload.title;
    state.modelId = payload.id;
  },
  fillGoodsList(state, goodsList) {
    state.goodsList = goodsList
  },
  fillGoods(state, goods) {
    state.goods = goods
  },

}

const actions = {
  renewFirms(context) {
    context.state.debug ? console.log('act renewFirms') : ''

    Api.getFirms((firms) => {
      context.commit('fillFirms', firms)
    })
  },
  renewModelGroups(context) {
    context.state.debug ? console.log('act renewModelGroups') : ''

    Api.getModelGroups(context.state.firm, context.state.typeAutos, (modelGroups) => {
      context.commit('fillModelGroups', modelGroups)
    })
  },
  renewModels(context) {
    context.state.debug ? console.log('act renewModels') : ''

    Api.getModels(context.state.typeAutos, context.state.firm, context.state.modelGroup, (models) => {
      context.commit('fillModels', models)
    })
  },
  getFirmByName(context, firm) {
    if (context.state.debug) {
      console.log('act getFirmByName');
      console.log(firm);
    }

    context.commit('setFirmName', firm);
    /*Api.getFirm(firm => {
      //console.log(firm)
      if (firm)
        context.commit('setFirmName', firm)
    }, firm)*/
  },
  setTypeAutos(context, typeAutos) {
    context.commit('setTypeAutos', typeAutos);
  },
  setFirm(context, firm) {
    context.commit('setFirm', firm);
  },
  setModelGroup(context, modelGroup) {
    context.commit('setModelGroup', modelGroup);
  },
  setModel(context, modelUrl) {
    Api.getModel(modelUrl, (model) => {
      context.state.debug ? console.log('act setModel') : '';
      if (model) {
        context.commit('setModel', { title: model.title, id: model.id });

        Api.getGoodsList(context.state.modelId, (goodsList) => {
          if (goodsList) {
            context.state.debug ? console.log('act setModel-getGoodsList') : '';
            context.commit('fillGoodsList', goodsList);
          }
        })
      }
      //context.state.debug ? console.log('act setModel after: ' + context.state.modelId) : ''
    })
  },
  /*fillGoodsList(context) {
    context.state.debug ? console.log('act fillGoodsList_' + context.state.modelId) : ''
    Api.getGoodsList(context.state.modelId, (goods) => {
      if (context.state.debug & goods) {
        console.log('act fillGoods');
        console.log(goods);
        context.commit('fillGoods', goods);
      }
    })

  }*/
  getGoods(context, goodId) {
    context.state.debug ? console.log('act getGoods') : ''

    if (goodId == undefined) {
      context.commit('fillGoods', [])
    } else {
      Api.getGoods(context.state.modelId, goodId, (goods) => {
        context.commit('fillGoods', goods)
      })
    }
  },

}

export default {
  strict: debug,
  //plugins: debug ? [createLogger()] : [],
  state,
  getters,
  mutations,
  actions
}
