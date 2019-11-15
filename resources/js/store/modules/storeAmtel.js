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
  count: 10,
  firms: {}, // list firms
  firmName: '', // firm name
  breadCrumbs: []
}

const getters = {
  debug: state => {
    return state.debug
  },
  count: state => {
    return state.count
  },
  lightCars: (state, getters) => {
    return state.firms.lightCars
    //return getters.firms.lightCars
  },
  trucks: (state, getters) => {
    return state.firms.trucks
    //return getters.firms.trucks
  },
  titleFirm: state => firm => {
    return firm
  },
  breadCrumbs: state => {
    return state.breadCrumbs
  },
  firmName: state => {
    return state.firmName
  }
}

const mutations = {
  increment(state) {
    state.count++
  },
  fillFirms(state, firms) {
    state.debug ? console.log('fillFirms') : ''
    state.firms = firms
  },
  setFirmName(state, name) {
    state.firmName = name
  },
  setBreadCrumbs(state, arr) {
    state.breadCrumbs = arr;
  }
}

const actions = {
  renewFirms(context) {
    context.state.debug ? console.log('renewFirms') : ''

    Api.getFirms((firms) => {
      context.commit('fillFirms', firms)
    })
  },
  getFirmByName(context, firm) {
    context.state.debug ? console.log('getFirmByName') : ''
    //console.log(firm)

    Api.getFirm(firm => {
      //console.log(firm)
      if (firm)
        context.commit('setFirmName', firm)
    }, firm)
  },
  renewBreadCrumbs({ dispatch, commit, state, getters }, { routeArray, pathArray, addBread }) {
    state.debug ? console.log('renewBreadCrumbs') : '';

    //console.log(routeArray)
    console.log(pathArray)

    if (pathArray.length >= 4) {
      //let name = 
      dispatch('getFirmByName', pathArray[3]);

      //breadCrumbsArray[1].name = state.firmName;
      //breadCrumbsArray[3].text = context.getters.firmName;
      //console.log(context.getters.firmName)
    }
    console.log(state)
    console.log(state.firmName)

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

      /*switch (i) {
        case '3': // firm name
          breadCrumbsArray[3].text = state.firmName
          break
        case '5':  // if (x === 'value2')
          break

        default:
          break
      }*/

      if (breadCrumbsArray[i]) {
        arr.push({
          text: breadCrumbsArray[i].name,
          disabled: false,
          href: to
        });
        //console.log(t)
      }
    });

    //addBread ? arr.push(addBread) : '';
    console.log(breadCrumbsArray);

    commit('setBreadCrumbs', arr);
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
