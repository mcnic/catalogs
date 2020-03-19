import Vue from 'vue'
import Vuex from 'vuex'
import storeAmtel from '@@/store/modules/storeAmtel'

Vue.use(Vuex)

const debug = process.env.MIX_NODE_ENV !== 'production'

export default new Vuex.Store({
	modules: {
		storeAmtel
	},
	strict: debug,
	//plugins: debug ? [createLogger()] : [],
})
