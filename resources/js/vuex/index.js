import Vue from 'vue'
import Vuex from 'vuex'
import config from '../config.js'
import token from './token.js'
import user from './user.js'
Vue.use(Vuex);
const store =new Vuex.Store({
	modules: {
		token: token,
		user: user
	},
	state: {
		config: config
	},
	mutations: {
		logout(state){
			localStorage.clear()
			window.location.href = "/"
		}
	},
	getters: {
		authorization: state => {
			var authorization = null
			if(state.token.access_token){
				authorization = state.token.token_type + " " + state.token.access_token
			}
			return authorization
		},
		isLoged:state => {
			return state.token.access_token !== null
		}
	}
})

export default store;