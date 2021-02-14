import axios from 'axios'
import store from './index.js'
const user = {
	state: () => ({
		name: null,
		email: null,
		permissions: []
	}),
	mutations: {
		refreshUser (state, token) {
			axios.get('/api/user')
			.then(res => {
				state.name = res.data.name;
				state.email = res.data.email;
				state.permissions = res.data.permissions;
			}).catch(error => {
				console.error("用户信息获取失败")
			});
		}
	}
}
export default user;