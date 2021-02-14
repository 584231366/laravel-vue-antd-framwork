import axios from 'axios'
import store from './index.js'
var tokenLocalStorage = localStorage.getItem('token') ? JSON.parse(localStorage.getItem('token')): null
const token = {
	state: () => ({
		access_token: tokenLocalStorage?tokenLocalStorage.access_token:null,
		refresh_token: tokenLocalStorage?tokenLocalStorage.refresh_token:null,
		expires_in: tokenLocalStorage?tokenLocalStorage.expires_in:null,
		token_type: tokenLocalStorage?tokenLocalStorage.token_type:null
	}),
	mutations: {
		saveToken (state, token) {
			state.token = token;
			localStorage.setItem('token', JSON.stringify(token));
		},
		refreshToken (state) {
			
		},
		clearToken (state) {
			state.access_token = null;
			state.refresh_token = null;
			state.expires_in = null;
			state.token_type = null;
			localStorage.removeItem('token')
		}
	}
}
export default token;