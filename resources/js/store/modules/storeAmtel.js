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
  breadCrumbs: [],
  firms: {}, // list firms for ListFirm component
  models: [], //for ListModels component
  typeAutos: '', // cars/trucks
  firm: '',
  model: ''
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
  renewModels(context) {
    context.state.debug ? console.log('act renewModels') : ''

    Api.getModels(context.state.firm, context.state.typeAutos, (models) => {
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
  renewBreadCrumbs({ dispatch, commit, state, getters }, { routeArray, pathArray, addBread }) {
    if (pathArray.length >= 4) {
      //let name = 
      //dispatch('getFirmByName', pathArray[3]);
      commit('setTypeAutos', pathArray[2]);
      commit('setFirm', pathArray[3]);

      breadCrumbsArray[3].name = pathArray[3]

      //breadCrumbsArray[1].name = state.firmName;
      //breadCrumbsArray[3].text = pathArray[3];
      //console.log(context.getters.firmName)
    }

    if (state.debug) {
      console.log('renewBreadCrumbs');
      //console.log(routeArray);
      //console.log(pathArray);
      //console.log(addBread);
      //console.log(state);
      //console.log(state.firmName);
    }

    let arr = [];
    let to = "";

    pathArray.forEach(function (item, i) {
      //console.log(i)

      // current path elements
      if (item) {
        if (to == "/")
          to = "/" + item;
        else
          to = to + "/" + item;
      } else {
        to = "/"
      }

      if (breadCrumbsArray[i]) {
        arr.push({
          text: breadCrumbsArray[i].name,
          disabled: false,
          href: to
        });
      }
    });

    if (this.debug) {
      console.log(breadCrumbsArray);
    }

    commit('setBreadCrumbs', arr);
  },
  setTypeAutos(context, typeAutos) {
    context.commit('setTypeAutos', typeAutos);
  },
  setFirm(context, firm) {
    context.commit('setFirm', firm);
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
