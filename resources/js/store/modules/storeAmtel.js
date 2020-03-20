//const api = () => import('../../api/apiAmtel')
import Api from '../../api/apiAmtel'

const debug = process.env.MIX_NODE_ENV !== 'production'

let breadCrumbsArray = [
  {
    name: "Главная"
  },
  {
    name: process.env.MIX_AMTEL_NAME,
  },
  null,
  {
    name: "{firm}"
  },
  {
    name: "{model}"
  },
  {
    name: "{auto}"
  },
]

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
  modelGroups: [],
  modelGroup: '',
}

const getters = {
  debug: state => {
    return state.debug
  },
  lightCars: (state, getters) => {
    return state.firms.lightCars
    //return getters.firms.lightCars
  },
  trucks: (state, getters) => {
    return state.firms.trucks
    //return getters.firms.trucks
  },
  //titleFirm: state => firm => {
  //   return firm
  //},
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
  }

}

const mutations = {
  increment(state) {
    state.count++
  },
  fillFirms(state, firms) {
    state.debug ? console.log('mut fillFirms') : ''
    state.firms = firms
  },
  fillModelGroups(state, modelGroups) {
    state.debug ? console.log('mut fillModelGroups') : ''
    state.modelGroups = modelGroups
  },
  fillModels(state, models) {
    state.debug ? console.log('mut fillModels') : ''
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
  setModel(state, model) {
    state.model = model;
  }
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
  setModel(context, model) {
    context.commit('setModel', model);
  }

}

export default {
  strict: debug,
  //plugins: debug ? [createLogger()] : [],
  state,
  getters,
  mutations,
  actions
}
