<template>
	<div class="err-404">
		{{token}}
		<div>404  |  Not Found</div>
	</div>
</template>

<script>
import { mapMutations,mapState } from 'vuex';
export default {
	name: 'Err404',
	computed: {
		...mapState({
			token: state => state.user,
			config: state => state.config
		})
	}
};
</script>
<style scoped>
	.err-404{
		position: relative;
	}
	.err-404>div{
		top:50%;
		width: 100%;
		text-align: center;
		transform: translateY(-50%);
		position: absolute;
		font-size: 30px;
		text-align: center;
	}
</style>