<template>
	<a-layout id="components-layout-demo-custom-trigger">
		<a-layout-sider v-model="collapsed" :trigger="null" collapsible>
			<div class="logo">LOGO</div>
			<a-menu 
				theme="dark" 
				mode="inline" 
				:default-selected-keys="defaultKey"
				:default-open-keys = "defaultOpenKeys"
			>
				<template v-for="m1 in menus">
					<template	v-if="m1.parent === null">
						<!-- 一级菜单 -->
						<a-sub-menu :key="m1.path" v-if="m1.hasChildren">
							<span slot="title"><a-icon :type="m1.icon" /><span>{{ m1.name }}</span></span>
							<!-- 二级菜单 -->
							<template v-for="m2 in menus">
								<template	v-if="m2.parent === m1.path">
									<a-sub-menu :key="m2.path" v-if="m2.hasChildren">
										<span slot="title">{{ m2.name }}</span></span>
										<!-- 三级菜单 -->
										<template v-for="m3 in menus">
											<template	v-if="m3.parent === m2.path">
												<a-menu-item :key="m3.path">
													<router-link :to="m3.path">
														<span>{{ m3.name }}</span>
													</router-link>
												</a-menu-item>
											</template>
										</template>
										<!-- 三级菜单 -->
									</a-sub-menu>
									<a-menu-item :key="m2.path" v-else>
										<router-link :to="m2.path">
											<span>{{ m2.name }}</span>
										</router-link>
									</a-menu-item>
								</template>
							</template>
							<!-- 二级菜单 -->
						</a-sub-menu>
						<a-menu-item :key="m1.path" v-else>
							<router-link :to="m1.path">
								<a-icon :type="m1.icon" />
								<span>{{ m1.name }}</span>
							</router-link>
						</a-menu-item>
						<!-- 一级菜单 -->
					</template>
				</template>
			</a-menu>
		</a-layout-sider>
		<a-layout>
			<a-layout-header style="background: #fff; padding: 0">
				<a-icon
					class="trigger"
					:type="collapsed ? 'menu-unfold' : 'menu-fold'"
					@click="() => (collapsed = !collapsed)"
				/>
				<v-drawer style="display: inline-block;float: right;margin-right: 16px;"></v-drawer>
			</a-layout-header>
			<a-breadcrumb style="margin: 16px 16px 16px 16px;">
				<a-breadcrumb-item v-for="b in breadcrumb">
					{{ b }}
				</a-breadcrumb-item>
			</a-breadcrumb>
			<router-view></router-view>
		</a-layout>
	</a-layout>
</template>
<script>
import drawer from './Drawer.vue'
import { mapMutations,mapState } from 'vuex';
export default {
	data() {
		return {
			collapsed: false
		};
	},
	computed: {
		...mapState({
			user: state => state.user
		}),
		menus(){
			var routes = this.$router.getRoutes()
			var menus = {}
			for(var i in routes){
				if(routes[i].meta.showInMenus && this.checkRoutePermission(this.user?this.user.permissions:[],routes[i])){
					menus[routes[i].path] = {
						name: routes[i].name,
						path: routes[i].path,
						icon: routes[i].meta.menuIcon,
						parent: routes[i].parent?routes[i].parent.path:null
					}
				}
			}
			for(var i in menus){
				if(menus[i].parent !== null){
					if(!menus[menus[i].parent]){
						menus[i].parent = null;
					}else{
						menus[menus[i].parent].hasChildren = true
					}
				}
			}
			return menus;
		},
		defaultKey () {
			return [this.$route.path]
		},
		defaultOpenKeys () {
			var _this = this
			var defaultOpenKeys = []
			var addOpenKey = function (key){
				if(key !== null && _this.menus[key]){
					defaultOpenKeys.push(key)
					addOpenKey(_this.menus[key].parent)
				}
			}
			for(var i in this.menus){
				if(this.$route.path === this.menus[i].path){
					addOpenKey(this.menus[i].parent)
				}
			}
			return defaultOpenKeys;
		},
		breadcrumb () {
			var breadcrumb = [];
			var routes = this.$route.matched
			for(var i in routes){
				if(routes[i].name){
					breadcrumb.push(routes[i].name)
				}
			}
			return breadcrumb;
		}
	},
	methods: {
		checkRoutePermission (userPermissions,route) {
			if(route.meta.permissions){
				for(var p1 in route.meta.permissions){
					for(var p2 in userPermissions){
						if(route.meta.permissions[p1] === userPermissions[p2]){
							return true;
						}
					}
				}
				return false;
			}else{
				return true
			}
		},
	},
	components: {
		"v-drawer":drawer
	}
};
</script>
<style>
#components-layout-demo-custom-trigger .trigger {
	font-size: 18px;
	line-height: 64px;
	padding: 0 24px;
	cursor: pointer;
	transition: color 0.3s;
}

#components-layout-demo-custom-trigger .trigger:hover {
	color: #1890ff;
}

#components-layout-demo-custom-trigger .logo {
	height: 32px;
	background: rgba(255, 255, 255, 0.2);
	margin: 16px;
	text-align: center;
	line-height: 32px;
	color:white;
}
</style>

