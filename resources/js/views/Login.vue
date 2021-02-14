<template>
	<div class="login-container">
		<canvas id="space"></canvas>
		<a-form
			id="components-form-demo-normal-login"
			:form="form"
			class="login-form"
			@submit="handleSubmit"
		>
			<a-form-item>
				<a-input
					v-decorator="[
						'username',
						{ rules: [{ required: true, message: '请输入用户名!' }] },
					]"
					placeholder="邮箱"
				>
					<a-icon slot="prefix" type="user" style="color: rgba(0,0,0,.25)" />
				</a-input>
			</a-form-item>
			<a-form-item>
				<a-input
					v-decorator="[
						'password',
						{ rules: [{ required: true, message: '请输入密码!' }] },
					]"
					type="password"
					placeholder="密码"
				>
					<a-icon slot="prefix" type="lock" style="color: rgba(0,0,0,.25)" />
				</a-input>
			</a-form-item>
			<a-form-item>
				<a-button type="primary" html-type="submit" class="login-form-button" block :loading="logining">
					登录
				</a-button>
			</a-form-item>
		</a-form>
	</div>
</template>

<script>
import { mapMutations,mapState } from 'vuex';
export default {
	name: 'Login',
	data() {
		return {
			logining: false
		};
	},
	computed: {
		...mapState({
			token: state => state.token,
			config: state => state.config
		})
	},
	beforeCreate() {
		this.form = this.$form.createForm(this, { name: 'normal_login' });
	},
	created (){
		this.$nextTick(this.setBackGroundImg)
	},
	methods: {
		...mapMutations(['saveToken']),
		async login(values){
			let _this = this;
			await this.$axios({
				method: 'post',
				url: '/oauth/token',
				data: {
					'grant_type': this.config.oauth2.grant_type,
					'client_id': this.config.oauth2.client_id,
					'client_secret': this.config.oauth2.client_secret,
					'username': values.username,
					'password': values.password,
					'scope': ''
				}
			}).then(res => {
				_this.saveToken(res);
				setTimeout(function(){
					_this.$router.push({path: '/Dashboard'})
				},500)
			}).catch(error => {
				_this.$error({
					content: "用户名或密码错误"
				});
			});
		},
		handleSubmit(e) {
			let _this = this;
			e.preventDefault();
			if(!_this.logining){
				_this.form.validateFields((err, values) => {
					_this.logining = true
					if (!err) {
						_this.login(values)
					}
					_this.logining = false
				});
			}
		},
		setBackGroundImg() {
			window.requestAnimFrame = (function () {
				return window.requestAnimationFrame
			})();
			var canvas = document.getElementById("space");
			var c = canvas.getContext("2d");
			var numStars = 1900;
			var radius = '0.' + Math.floor(Math.random() * 9) + 1;
			var focalLength = canvas.width * 2;
			var warp = 0;
			var centerX, centerY;
			var stars = [], star;
			var i;
			var animate = true;
			initializeStars();
 
			function executeFrame() {
				if (animate)
				window.requestAnimFrame(executeFrame);
				moveStars();
				drawStars();
			}
 
			function initializeStars() {
				centerX = canvas.width / 2;
				centerY = canvas.height / 2;
 
				stars = [];
				for (i = 0; i < numStars; i++) {
					star = {
						x: Math.random() * canvas.width,
						y: Math.random() * canvas.height,
						z: Math.random() * canvas.width,
						o: '0.' + Math.floor(Math.random() * 99) + 1
					};
					stars.push(star);
				}
			}
 
			function moveStars() {
				for (i = 0; i < numStars; i++) {
					star = stars[i];
					star.z--;
 
					if (star.z <= 0) {
						star.z = canvas.width;
					}
				}
			}
 
			function drawStars() {
				var pixelX, pixelY, pixelRadius;
 
				// Resize to the screen
				if (canvas.width !== window.innerWidth || canvas.width !== window.innerWidth) {
					canvas.width = window.innerWidth;
					canvas.height = window.innerHeight;
					initializeStars();
				}
				if (warp === 0) { // 此代码块改背景色为渐变色
					var grd=c.createRadialGradient(canvas.width,canvas.height,canvas.width,canvas.width,canvas.height,1000);
					grd.addColorStop(0,"rgba(1, 9, 41, 0.6)");
					grd.addColorStop(1,"rgba(2, 8, 36, 0.6)");
					c.fillStyle = grd;
					c.fillRect(0, 0, canvas.width, canvas.height);
				}
				c.fillStyle = "rgba(209, 255, 255, " + radius + ")";
				for (i = 0; i < numStars; i++) {
					star = stars[i];
 
					pixelX = (star.x - centerX) * (focalLength / star.z);
					pixelX += centerX;
					pixelY = (star.y - centerY) * (focalLength / star.z);
					pixelY += centerY;
					pixelRadius = 1 * (focalLength / star.z);
 
					c.fillRect(pixelX, pixelY, pixelRadius, pixelRadius);
					c.fillStyle = "rgba(209, 255, 255, " + star.o + ")";
					//c.fill();
				}
			}
 
			executeFrame();
		}
	},
};
</script>
<style type="text/css" scoped>
	.ant-form-item{
		margin-bottom: 0px;
	}
	.login-container{
		width: 100%;
		position: relative;
	}
	.login-container>form{
		position: absolute;
			width: 200px;
			left: 50%;
			top: 40%;
			transform: translateX(-50%);
	}
	.ant-input-affix-wrapper,.ant-btn{
		margin-top: 16px;
	}

	#space {
		width: 100%;
		height: 100%;
		display: block;
		vertical-align: baseline;
		position: absolute;
		z-index: -5;
	}
</style>