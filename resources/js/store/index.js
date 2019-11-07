import Vue from 'vue'
import Vuex from 'vuex'
//import cart from './modules/cart'
//import products from './modules/products'
//import createLogger from '../../../src/plugins/logger'

Vue.use(Vuex)

const debug = process.env.MIX_NODE_ENV !== 'production'

export default new Vuex.Store({
	/*modules: {
		cart,
		products
	},*/
	strict: debug,
	//plugins: debug ? [createLogger()] : [],
	state: {
		debug: debug,
		count: 10,
		firms: {}
	},
	getters: {
		debug(state) {
			return state.debug
		},
		count(state) {
			return state.count
		},
		lightCars(state, getters) {
			return state.firms.lightCars
			//return getters.firms.lightCars
		},
		trucks(state, getters) {
			return state.firms.trucks
			//return getters.firms.trucks
		}
	},
	mutations: {
		increment(state) {
			state.count++
		},
		fillFirms(state, firms) {
			state.debug ? console.log('fillFirms') : ''
			state.firms = firms
		}
	},
	actions: {
		renewFirms(context) {
			context.state.debug ? console.log('renewFirms') : ''

			let firms = {
				lightCars: [
					{
						title: "Acura",
						url: "/" + process.env.MIX_AMTEL_PREFIX + "/cars/acura"
					},
					{ title: "Ford", url: "/" + process.env.MIX_AMTEL_PREFIX + "/cars/ford" }
				],
				trucks: [
					{ title: "BAW", url: "/" + process.env.MIX_AMTEL_PREFIX + "/trucks/baw" },
					{ title: "BPW", url: "/" + process.env.MIX_AMTEL_PREFIX + "/trucks/bpw" }
				]
			}

			setTimeout(() => {
				context.commit('fillFirms', firms)
			}, 2000)

		}
	}
})
