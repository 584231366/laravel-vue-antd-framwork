<template>
	<div>
		<a-button @click="showDrawer">
			<a-icon type="user" />
		</a-button>
		<a-drawer
			placement="right"
			:closable="false"
			:visible="visible"
			@close="onClose"
		>
		<div style="text-align: center;padding: 0px">
			 <a-avatar :size="64" icon="user" />
			 <div>
				 {{ user.name }}
			 </div>
			 <div>
				 {{ user.email }}
			 </div>
			</div>
			<ul>
				<li>
					<a href="javascript:void(0)" @click="showChangePasswordForm">
						<a-icon type="lock"></a-icon> 修改密码
					</a>
				</li>	
				<li>
					<a href="javascript:void(0)" @click="logout">
						<a-icon type="logout"></a-icon> 退出登录
					</a>
				</li>		
			</ul>
		</a-drawer>
		<a-modal v-model="showForm" title="密码修改" @ok="changePassword()">
			<a-form :form="form" :label-col="{ span: 5 }" :wrapper-col="{ span: 19 }" @submit="changePassword()">
				<a-form-item label="密码">
					<a-input-password
						v-decorator="['password',{ rules: [{ required: true, message: '请输入密码!' }] }]"
					>
					</a-input-password>
				</a-form-item>
				<a-form-item label="确认密码">
					<a-input-password
						v-decorator="['password_confirmation',{ rules: [{ required: true, message: '请输入确认密码!' }] }]"
					>
					</a-input-password>
				</a-form-item>
				<a-form-item :wrapper-col="{ span: 12, offset: 5 }" v-show="false" ref="submit">
						<a-button type="primary" html-type="submit">
							保存
						</a-button>
					</a-form-item>
			</a-form>
		</a-modal>
	</div>
</template>
<script>
import { mapMutations,mapState } from 'vuex';
export default {
	data() {
		return {
			visible: false,
			showForm: false,
			form: this.$form.createForm(this, { name: 'coordinated' }),
		};
	},
	computed: {
		...mapState({
			user: state => state.user
		})
	},
	methods: {
		...mapMutations(['logout']),
		showDrawer() {
			this.visible = true;
		},
		onClose() {
			this.visible = false;
		},
		showChangePasswordForm(){
			this.visible = false;
			this.showForm = true;
		},
		async changePassword (e) {
			if(e){
				e.preventDefault();
			}
			var _this = this
			await this.form.validateFields(async (err, values) => {
				if (!err) {
					await this.$axios.put("/api/user/password",{
						password: values.password,
						password_confirmation: values.password_confirmation
					})
					.then(function(res){
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
			});
		}
	},
};
</script>
<style type="text/css" scoped="">
	ul{
		list-style-type: none;
		padding: 0px;
	}
	ul li{
		border-bottom: 1px solid #e8e8e8;
		padding:12px 0px ;
	}
	ul li:nth-child(1){
		border-top: 1px solid #e8e8e8;
	}
</style>