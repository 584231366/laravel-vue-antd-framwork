import Vue from 'vue'
import App from './App.vue'
import router from './router/index.js'
import store from './vuex/index.js'
import Antd from 'ant-design-vue';
import axios from 'axios'
import 'ant-design-vue/dist/antd.css';

Vue.prototype.$axios = axios
Vue.config.productionTip = false;
Vue.use(Antd);

axios.defaults.headers['Authorization'] = store.getters.authorization
axios.interceptors.response.use( // 拦截器 统一错误处理内容
	response => {
		return response.data
	},
	error => {
		if (error.response) {
			if (error.response.status === 401) {
				window.location.href = '/'
			} else if (error.response.status === 403) {
				// 提示无权限等
				Vue.prototype.$warning({
					content: "您无权进行该操作"
				})
			} else if(error.response.status !== 400) {
				// 其他错误处理
				Vue.prototype.$error({
					content: error.response.status+"："+error.response.statusText
				})
			}
		}
		return Promise.reject(error.response)
	}
)

new Vue({
	el: '#app',
	router,
	store,
	render: h => h(App)
})