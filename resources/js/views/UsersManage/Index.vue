<template>
	<a-layout-content :style="{ margin: '0px 16px 0' }">
		<div :style="{background: '#fff', minHeight: '360px' }">
			<a-page-header
				style="border: 1px solid rgb(235, 237, 240)"
				:title="$route.name"
			>
				<template slot="extra">
					<a-button @click="showForm = true" type="primary" icon="user-add">
						新增
					</a-button>
				</template>
				<div class="content">
					<a-table :columns="table.columns" :data-source="table.data" :loading="table.loading" :pagination="table.pagination">
						<span slot="action" slot-scope="text, record">
							<a @click="editData(record)">编辑</a>
							<a @click="deleteData(record)">删除</a>
						</span>
						<a-pagination :value="1" :total="50" show-less-items />
					</a-table>
				</div>
			</a-page-header>
		</div>
		<a-modal v-model="showForm" title="编辑" @ok="saveData()">
			<a-form :form="form" :label-col="{ span: 5 }" :wrapper-col="{ span: 19 }" @submit="saveData()">
				<a-form-item label="用户名">
					<a-input
						v-decorator="['name', { rules: [{ required: true, message: '请输入角色名称!' }] }]"
					/>
				</a-form-item>
				<a-form-item label="邮箱">
					<a-input
						v-decorator="['email',{ rules: [{ required: true, message: '请输入邮箱!' }] }]"
					>
					</a-input>
				</a-form-item>
				<a-form-item label="分配角色">
					<a-select
						mode="multiple"
						v-decorator="['roles']"
					>
						<a-select-option v-for="r in roles" :key="r.value">
							{{ r.text }}
						</a-select-option>
					</a-select>
				</a-form-item>
				<a-form-item label="密码">
					<a-input
						v-decorator="['password',{ rules: [{ required: !editRecorde, message: '请输入密码!' }] }]"
					>
					</a-input>
				</a-form-item>
				<a-form-item label="确认密码">
					<a-input
						v-decorator="['password_confirmation',{ rules: [{ required: !editRecorde, message: '请输入确认密码!' }] }]"
					>
					</a-input>
				</a-form-item>
				<a-form-item :wrapper-col="{ span: 12, offset: 5 }" v-show="false" ref="submit">
					<a-button type="primary" html-type="submit">
						保存
					</a-button>
				</a-form-item>
			</a-form>
		</a-modal>
	</a-layout-content>
</template>
<script type="text/javascript">
	export default{
		name: 'UsersManage',
		data() {
			return {
				table: {
					columns: [
						{
							title: '#ID',
							dataIndex: 'key',
						},
						{
							title: '用户名',
							dataIndex: 'name',
						},
						{
							title: '邮箱',
							dataIndex: 'email',
						},
						{
							title: '创建时间',
							dataIndex: 'created_at',
						},
						{
							title: '操作',
							dataIndex: 'action',
								scopedSlots: { customRender: 'action' },
						}
					],
					data: [],
					pagination: {
						current: 1,
						total: 1
					},
					loading: false,
					roles: []
				},
				roles: [],
				form: this.$form.createForm(this, { name: 'coordinated' }),
				editRecorde: null,
				showForm: false,
			};
		},
		watch: {
			showForm (val) { // 关闭对话框时重置表单
				if(!val){
					this.form = this.$form.createForm(this, { name: 'coordinated' })
					this.editRecorde = null
				}
			}
		},
		created () {
			this.getData()
			this.getRoles()
		},
		methods: {
			async getData () {
				var _this = this
				if(!this.table.loading){
					this.table.loading = true
					await this.$axios.get("/api/users")
					.then(function(res){
						var list = res.data
						for(var i in list.data){
							res.data.data[i] = {
								key: list.data[i].id,
								name: list.data[i].name,
								email: list.data[i].email,
								roles: list.data[i].roles,
								created_at: list.data[i].created_at,
							}
						}
						_this.table.data = list.data
						_this.table.pagination.total = list.total
						_this.table.pagination.current = list.current_page
					})
					this.table.loading = false
				}
			},
			async saveData(e) {
				if(e){
					e.preventDefault();
				}
				var _this = this
				await this.form.validateFields(async (err, values) => {
					if (!err) {
						if(this.editRecorde){ // 更新
							await this.$axios.put("/api/users/"+this.editRecorde.key,{
								name: values.name,
								email: values.email,
								roles: values.roles,
								password: values.password,
								password_confirmation: values.password_confirmation
							})
							.then(function(res){
								_this.getData()
								_this.$message.success(res.message,1);
								_this.showForm = false
							})
							.catch(res=>{
								if(res.data.check_errors){
									var fields = {}
									var errors = res.data.check_errors;
									for(var i in errors){
										fields[i] = {
											errors: [new Error(errors[i][0])]
										}
									}
									_this.form.setFields(fields)
								}
							})
						}else{ // 创建
							await this.$axios.post("/api/users",{
								name: values.name,
								email: values.email,
								roles: values.roles,
								password: values.password,
								password_confirmation: values.password_confirmation
							})
							.then(function(res){
								_this.getData()
								_this.$message.success(res.message,1);
								_this.showForm = false
							})
							.catch(res=>{
								if(res.data.check_errors){
									var fields = {}
									var errors = res.data.check_errors;
									for(var i in errors){
										fields[i] = {
											errors: [new Error(errors[i][0])]
										}
									}
									_this.form.setFields(fields)
								}
							})
						}
					}
				});
			},
			async deleteData(record) {
				var _this = this
				await this.$axios.delete("/api/users/"+record.key)
				.then(function(res){
					_this.getData()
					_this.$message.success(res.message,1);
					_this.showForm = false
				})
			},
			editData(record){
				var _this = this
				setTimeout(function(){ // 因为放在对话框内显示 防止组件出现前先填充数据而报错的情况
					_this.form.setFieldsValue({ // 如果指定了表单没有的参数将报错
						name: record.name,
						email: record.email,
						roles: record.roles
					})
				},200)
				this.editRecorde = record
				this.showForm = true
			},
			async getRoles () {
				var _this = this
				await this.$axios.get("/api/roles?limit=1000")
				.then(function(res){
					var list = res.data
					for(var i in list.data){
						_this.roles.push({
							text: list.data[i].name,
							value: list.data[i].name,
						})
					}
				})
			}
		}
	}
</script>