<template>
	<div class="account__bottom_tab_bar">
		<a @click="selectAccountBottomTab(1)" :class="{ active: flag == 1 }">購入履歴<i class="fas fa-arrow-right"></i></a>
		<a @click="selectAccountBottomTab(3)">退会する<i class="fas fa-arrow-right"></i></a>

		<sweet-modal ref="taikai__modal" overlay-theme="dark">
			<div class="comment_modal_inner">
				<h4>本当に退会しますか？</h4>

				<p class="pt-4">
					<button type="button" class="btn btn-primary mr-4" @click="taikai">退会する</button>
					<button type="button" class="btn btn-secondary" @click="cancel">キャンセル</button>
				</p>
			</div>
		</sweet-modal>
	</div>
</template>
<script>
export default {
	name: 'AccountBottomTabBar',

	data() {
		return {
			flag: 0
		}
	},

	methods: {
		selectAccountBottomTab(param) {
			if (param == 3) {
				// 退会
				this.$refs.taikai__modal.open()
				return
			}
			this.flag = param
			this.$emit('selectAccountBottomTab', param)
		},

		taikai() {
			this.logout()
		},

		cancel() {
			this.$refs.taikai__modal.close()
		},

		async logout() {
			this.$router.push({ name: 'buyer.login' })
			// Log out the user
			const { data } = await axios.post('/auth/taikai')
			if (data.flag) {
				this.$swal('', '退会しました。', 'success')
			}
		},
	}
}
</script>
<style lang="scss" scoped>
.comment_modal_inner {
	textarea {
		resize: none;
	}

	.comment_textarea {
		margin-top: 30px;
	}
}

.account__bottom_tab_bar {
	a {
		&.active {
			background-color: white;
			border: 1px solid #333;
			color: #333 !important;
		}
	}
}
</style>