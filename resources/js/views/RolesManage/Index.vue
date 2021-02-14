<template>
	<a-layout-content :style="{ margin: '0px 16px 0'}">
		<div :style="{background: '#fff', minHeight: '360px' }">
			<a-page-header
				style="border: 1px solid rgb(235, 237, 240)"
				:title="$route.name"
			>
				<template slot="extra">
					<a-button @click="showForm = true"  type="primary" icon="user-add">
						新增
					</a-button>
				</template>
				<div class="content">
					<a-table :columns="table.columns" :data-source="table.data" :loading="table.loading" :pagination="table.pagination">
						<span slot="action" slot-scope="text, record">
							<a @click="editPermissions(record)">权限</a>
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
				<a-form-item label="角色名称">
					<a-input
						v-decorator="['name', { rules: [{ required: true, message: '请输入角色名称!' }] }]"
					/>
				</a-form-item>
				<a-form-item label="模型">
					<a-select
						v-decorator="['guard_name',{ rules: [{ required: true, message: '请输入模型!' }] }]"
					>
					<a-select-option key="api">
				    	api
				    </a-select-option>
					</a-select>
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
		name: 'RolesManage',
		data() {
			return {
				table: {
					columns: [
						{
							title: '#ID',
							dataIndex: 'key',
						},
						{
							title: '角色名称',
							dataIndex: 'name',
						},
						{
							title: '模型',
							dataIndex: 'guard_name',
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
						total: 1,
						onChange: (page) =>{
							this.getData(page)
						}
					},
					loading: false,
				},
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
			this.getData(1)
		},
		methods: {
			async getData (page) {
				var _this = this
				if(!this.table.loading){
					this.table.loading = true
					await this.$axios.get("/api/roles?page="+page)
					.then(function(res){
						var list = res.data
						for(var i in list.data){
							res.data.data[i] = {
								key: list.data[i].id,
								name: list.data[i].name,
								guard_name: list.data[i].guard_name,
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
							await this.$axios.put("/api/roles/"+this.editRecorde.key,{
								name: values.name,
								guard_name: values.guard_name
							})
							.then(function(res){
								_this.getData(_this.table.pagination.current)()
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
							await this.$axios.post("/api/roles",{
								name: values.name,
								guard_name: values.guard_name
							})
							.then(function(res){
								_this.getData(_this.table.pagination.current)()
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
				this.$confirm({
					title: '确认提示',
					content: '是否确认删除数据?',
					async onOk() {
						await _this.$axios.delete("/api/roles/"+record.key)
						.then(function(res){
							_this.getData()
							_this.$message.success(res.message,1);
							_this.showForm = false
						})
					},
					onCancel() {}
				});
			},
			editData(record){
				var _this = this
				setTimeout(function(){ // 因为放在对话框内显示 防止组件出现前先填充数据而报错的情况
					_this.form.setFieldsValue({ // 如果指定了表单没有的参数将报错
						name: record.name,
						guard_name: record.guard_name
					})
				},200)
				this.editRecorde = record
				this.showForm = true
			},
			editPermissions(record){
				this.$router.push({path: '/Role/'+record.key+'/Permissions'})
			}
		}
	}
</script>