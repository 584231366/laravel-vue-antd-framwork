import Vue from 'vue'
import VueRouter from 'vue-router'
import store from '../vuex/index.js'
import App from '../App.vue'
import Dashboard from '../views/Dashboard.vue'
import Layout from '../components/Layout.vue'
import Login from '../views/Login.vue'
import PermissionsManage from '../views/PermissionsManage/Index.vue'
import RolePermissions from '../views/RolesManage/RolePermissions.vue'
import RolesManage from '../views/RolesManage/Index.vue'
import UsersManage from '../views/UsersManage/Index.vue'
import Err404 from '../views/Err404.vue'
Vue.use(VueRouter);


const router =new VueRouter({
	mode: 'history',
	routes: [
		{
			path: '/Login',
			component: Login,
		},
		{
			path: '/44',
			redirect: '/Dashboard',
			component: Layout,
			children: [
				{
					path: '/Dashboard',
					name: '后台面板',
					meta: {
						showInMenus: true,
						menuIcon: 'dashboard'
					},
					component:	Dashboard
				},
				{
					path: '/UsersManage',
					name: '用户管理',
					meta: {
						showInMenus: true,
						menuIcon: 'user',
						permissions: ['users-select','users-edit'],
					},
					component:	UsersManage
				},
				{
					path: '/RolesManage',
					name: '角色管理',
					meta: {
						showInMenus: true,
						menuIcon: 'team',
						permissions: ['roles-select','roles-edit'],
					},
					component:	RolesManage
				},
				{
					path: '/Role/:id/Permissions',
					name: '角色权限管理',
					meta: {
						permissions: ['roles-select','roles-edit'],
					},
					component:	RolePermissions
				},
				{
					path: '/PermissionsManage',
					name: '权限管理',
					meta: {
						showInMenus: true,
						menuIcon: 'bars',
						permissions: ['permissions-select','permissions-edit'],
					},
					component:	PermissionsManage
				}
			]
		},
		{
			path: "/*",
			component:	Err404
		}
	]
});
router.beforeEach((to, from, next) => {
	if(!store.getters.isLoged){ // 登录判断
		if(to.path != '/Login'){
			next({
				path: '/Login'
			})
			return;
		}
	}
	next();
});

export default router;