<template>
	<router-view style="height:100%"></router-view>
</template>
<script type="text/javascript">
	export default {
		name: 'App',
		watch: {
			"$store.getters.isLoged": function(bool){
				this.getUser(bool)
			}
		},
		created () {
			this.getUser(this.$store.getters.isLoged)
		},
		methods: {
			getUser(bool){
				if(bool){
					this.$store.commit("refreshUser")
				}
			}
		}
	}
</script>