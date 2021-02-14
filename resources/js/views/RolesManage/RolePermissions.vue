<template>
	<a-layout-content :style="{ margin: '0px 16px 0'}">
		<div :style="{background: '#fff', minHeight: '360px' }">
			<a-page-header
				style="border: 1px solid rgb(235, 237, 240)"
				:title="$route.name"
			>
				<template slot="extra">
					<a-button @click="saveData" type="primary" :loading="submiting" icon="save">
						保存
					</a-button>
				</template>
				<a-row style="padding:16px;border-top: 1px solid #ebedf0">
					<a-col :span="3">
						模块
					</a-col>
					<a-col :span="21">
						权限
					</a-col>
				</a-row>
				<template v-for="(m,k) in modules">
					
					<a-row style="padding:16px;border-bottom: 1px solid #ebedf0">
						<a-col :span="3">
							<a-checkbox :indeterminate="indeterminate[k]" :checked="checkAll[k]" @click="checkAllChange(k)">
								{{ k }}
							</a-checkbox>
						</a-col>
						<a-col :span="21">
							<a-checkbox-group v-model="checkedList[k]" :options="m" name="permissions" @change="onChange"/>
						</a-col>
					</a-row>
				</template>
			</a-page-header>
		</div>
	</a-layout-content>
</template>

<script>
import { mapMutations,mapState } from 'vuex';
export default {
	name: 'RolePermissions',
	data () {
		return {
			permissions: [],
			checkedList: {},
			submiting: false
		}
	},
	computed: {
		modules () {
			var modules = {};
			for(var i in this.permissions){
				var p = this.permissions[i]
				if(typeof modules[p.split("-")[0]] == 'undefined'){
					modules[p.split("-")[0]] = [];
				}
				modules[p.split("-")[0]].push(p);
			}
			return modules;
		},
		indeterminate(){
			var indeterminate = {}
			for(var m in this.modules){
				indeterminate[m] = this.checkedList[m] && this.checkedList[m].length > 0 && this.checkedList[m].length != this.modules[m].length?true:false
			}
			this.$forceUpdate()
			return indeterminate
		},
		checkAll(){
			var checkAll = {}
			for(var m in this.modules){
				checkAll[m] = this.checkedList[m] && this.checkedList[m].length == this.modules[m].length?true:false
			}
			return checkAll
		}
	},
	created () {
		this.getPermissions()
		this.getRolePermissions()
	},
	methods: {
		async getRolePermissions(){
			var _this = this
			await this.$axios.get("/api/roles/"+this.$route.params.id+"/permissions?limit=1000")
			.then(function(res){
				for(var i in res.data){
					var p = res.data[i]
					if(typeof _this.checkedList[p.split("-")[0]] == 'undefined'){
						_this.$set(_this.checkedList,p.split("-")[0],[])
					}
					_this.checkedList[p.split("-")[0]].push(p);
				}
			})
		},
		async getPermissions(){
			var _this = this
			await this.$axios.get("/api/permissions?limit=1000")
			.then(function(res){
				var list = res.data
				for(var i in list.data){
					_this.permissions.push(list.data[i].name)
				}
			})
		},
		async saveData(){
			var _this = this
			if(!this.submiting){
				this.submiting = true
				var permissions = []
				for(var m in this.checkedList){
					for(var p in this.checkedList[m]){
						permissions.push(this.checkedList[m][p])
					}
				}
				await this.$axios.post("/api/roles/"+this.$route.params.id+"/permissions",{
					permissions: permissions
				})
				.then(function(res){
					_this.$message.success(res.message,1);
				})
				this.submiting = false
			}
		},
		onChange () {
			this.$forceUpdate()
		},
		checkAllChange(k) {
			if(!this.checkedList[k]){
				this.$set(this.checkedList,k,[])
			}
			if(this.checkedList[k].length){
				this.checkedList[k] = []
			}else{
				for(var p in this.modules[k]){
					this.checkedList[k].push(this.modules[k][p])
				}
			}
			this.$forceUpdate()
		},
	}
};
</script>