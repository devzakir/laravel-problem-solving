<template>
	<div class="accout__part">
		<div class="avatar">
			<p class="img" @click="selectAvatar">
				<img :src="form.avatar_url" />
			</p>
			<a v-if="!!$store.getters['auth/user'].avatar" @click="removeAvatar" class="avatar_remove">
				<i class="far fa-times-circle"></i>
			</a>
			<input type="file" ref="avatar_input" class="dis-none" @change="changeAvatar" accept="image/x-png,image/gif,image/jpeg"/>
			<p class="edit_btn" v-if="isShowSaveBtn">
				<button @click="uploadAvatar">保存</button>
			</p>
		</div>

		<div class="account__info">
			<div class="account__row">
				<label>
					名前
				</label>
				<p>
					<span v-if="!nameEdit">{{ form.nickname }}</span>
					<input v-model="form.nickname" v-else type="text" :class="{ invalid: nameInvalid }" />
					<span v-if="nameInvalid" class="invalid_note">{{ nameInvalidText }}</span>
				</p>
				<a class="edit__btn" @click="editName">{{ nameEdit | buttonText }}</a>
				<a v-if="nameEdit" @click="cancelEditName" class="cancel__btn">キャンセル</a>
			</div>
			<div class="account__row">
				<label>
					メールアドレス
				</label>
				<p>
					<span v-if="!emailEdit">{{ form.email }}</span>
					<input v-model="form.email" type="email" v-else />
				</p>
			</div>
			<div class="account__row">
				<label>
					パスワード
				</label>
				<p>
					<span v-if="!passwordEdit">セキュリティ上表示できません</span>
					<input v-model="form.password" type="password" v-else :class="{ invalid: passwordInvalid }" />
					<span v-if="passwordInvalid" class="invalid_note">{{ passwordInvalidText }}</span>
				</p>
				<a class="edit__btn" @click="editPassword">{{ passwordEdit | buttonText }}</a>
				<a @click="cancelEditPassword" v-if="passwordEdit" class="cancel__btn">キャンセル</a>
			</div>
			<div class="account__row">
				<label>
					電話番号
				</label>
				<p>
					<span v-if="!telephoneEdit">{{ form.telephone }}</span>
					<input v-model="form.telephone" type="tel" v-else  :class="{ invalid: telephoneInvalid }"/>
					<span v-if="telephoneInvalid" class="invalid_note">{{ telephoneInvalidText }}</span>
				</p>
				<a class="edit__btn" @click="editTelephone">{{ telephoneEdit | buttonText }}</a>
				<a @click="cancelEditTelephone" v-if="telephoneEdit" class="cancel__btn">キャンセル</a>
			</div>
		</div>
	</div>
</template>
<script>
import Form from 'vform'
export default {
	name: 'AccountPart',

	data() {
		return {
			nameEdit: false,
			nickNameEdit: false,
			emailEdit: false,
			passwordEdit: false,
			telephoneEdit: false,

			form: new Form({
				nickname: '',
				email: '',
				telephone: '',
				password: '',
				avatar: undefined,
				avatar_url: ''
			}),

			isShowSaveBtn: false,
			nameInvalid: false,
			nameInvalidText: '名前は必須項目です。',
			passwordInvalid: false,
			passwordInvalidText: 'パスワードは必須項目です。',
			telephoneInvalid: false,
			telephoneInvalidText: '電話番号は必須項目です。',
		}
	},
	
	created() {
		this.form = {
			nickname: this.$store.getters['auth/user'].name,
			email: this.$store.getters['auth/user'].email,
			telephone: !!this.$store.getters['auth/user'].buyer.telephone ? this.$store.getters['auth/user'].buyer.telephone : '',
			password: '',
			avatar_url: this.$store.getters['auth/user'].avatar_url,
			avatar: undefined
		}
	},

	mounted() {
		console.log(this.$store.getters['auth/user'])
	},

	methods: {
		async editName() {
			if (this.form.nickname.trim() == '') {
				this.nameInvalid = true
				this.nameInvalidText = '名前は必須項目です。'
				return
			}

			if (this.form.nickname.trim().length >= 20) {
				this.nameInvalid = true
				this.nameInvalidText = '名前は20文字以下を入力してください'
				return
			}

			this.nameEdit = !this.nameEdit
			
			if (!this.nameEdit) {
				const { data } = await axios.post('/api/buyer/edit_name', {
					name: this.form.nickname
				})

				this.$store.dispatch('auth/updateUser', { user: data.updated_buyer })
				this.nameInvalid = false
				this.$swal('', '保存しました！', 'success')
			}
		},

		cancelEditName() {
			this.nameEdit = false
			this.nameInvalid = false
			this.form.nickname = this.$store.getters['auth/user'].name
		},

		async editEmail() {
			
		},

		cancelEditEmail() {
			this.emailEdit = false
		},

		async editPassword() {
			if (this.passwordEdit) {
				if (this.form.password.trim() == '') {
					this.passwordInvalid = true
					this.passwordInvalidText = 'パスワードは必須項目です。'
					return
				}

				if (this.form.password.trim().length >= 20 || this.form.password.trim().length < 8) {
					this.passwordInvalid = true
					this.passwordInvalidText = 'パスワードは8文字～20文字を入力してください'
					return
				}
			}

			this.passwordEdit = !this.passwordEdit

			if (!this.passwordEdit) {
				const { data } = await axios.post('/api/buyer/edit_password', {
					password: this.form.password
				})

				this.$store.dispatch('auth/updateUser', { user: data.updated_buyer })
				this.passwordInvalid = false
				this.$swal('', '保存しました！', 'success')
			}
		},

		cancelEditPassword() {
			this.passwordEdit = false
			this.passwordInvalid = false
			this.form.password = ''

		},
		
		phoneValidate(telephone) {
			var phoneno = /^\(?([0-9]{3})\)?[-. ]?([0-9]{3})[-. ]?([0-9]{4})$/;
			if(telephone.match(phoneno))
				{
				return true;      
				}
			else
				{
				return false;
				}
		},

		async editTelephone() {
			if (this.telephoneEdit) {
				if (this.form.telephone.trim() == '') {
					this.telephoneInvalid = true
					this.telephoneInvalidText = '電話番号は必須項目です'
					return
				}

				if (!this.phoneValidate(this.form.telephone.trim())) {
					this.telephoneInvalid = true
					this.telephoneInvalidText = '電話番号形式が違います'
					return
				}
			}

			this.telephoneEdit = !this.telephoneEdit

			if (!this.telephoneEdit) {
				const { data } = await axios.post('/api/buyer/edit_telephone', {
					telephone: this.form.telephone
				})

				this.$store.dispatch('auth/updateUser', { user: data.updated_buyer })
				this.telephoneInvalid = false
				this.$swal('', '保存しました！', 'success')
			}
		},

		cancelEditTelephone() {
			this.telephoneEdit = false
			this.telephoneInvalid = false
			this.form.telephone = this.$store.getters['auth/user'].buyer.telephone
		},

		selectAvatar() {
			this.$refs.avatar_input.click()
		},

		changeAvatar(e) {
			const file = e.target.files[0];

			if (!this.isPhoto(file.name)) {
				this.$swal('', 'ファイル形式が違います', 'warning')
				return
			}

			this.form.avatar = file
			this.form.avatar_url = URL.createObjectURL(file)
			this.isShowSaveBtn = true
		},

		isPhoto(name) {
			return /\.(jpe?g|png|gif)$/i.test(name)
		},

		async removeAvatar() {
			if (!this.$store.getters['auth/user'].avatar) {
				return
			}

			const { data } = await axios.post('/api/buyer/remove_avatar')
			this.$store.dispatch('auth/updateUser', { user: data.updated_buyer })
			this.form.avatar_url = this.$store.getters['auth/user'].avatar_url,
			this.form.avatar = undefined
			this.isShowSaveBtn = false
			this.$swal('', 'プロフィール画像を削除しました！', 'success')
		},

		async uploadAvatar() {
			let formData = new FormData();

			formData.append('avatar', this.form.avatar);

			try {
				const { data } = await axios.post('/api/buyer/upload_avatar', formData, {
					headers: {
						'Content-Type': 'multipart/form-data'
					},
					onUploadProgress: function(event) {
					}.bind(this)
				})

				this.$store.dispatch('auth/updateUser', { user: data.updated_buyer })
				this.$swal('', '保存しました！', 'success')
				this.isShowSaveBtn = false
			} catch (error) {

			}
		}
	}
}
</script>
<style lang="scss" scoped>
.edit_btn {
	padding-top: 10px;
	button {
		padding: 0 15px;
		height: 30px;
		border-radius: 15px;
		border: 0;
		display: flex;
		align-items: center;
		justify-content: center;
		background-color: #808080;
		color: white;

		&:hover {
			background-color: #b7b7b7;
		}
	}
}

.avatar_remove {
	position: absolute;
	top: 0;
	right: 60px;
	width: fit-content;
	font-size: 20px;
	color: red !important;
}

.invalid_note {
	color: red;
	font-size: 12px;
	margin-left: 10px;
}

@media screen and (max-width: 768px) {
	.avatar_remove {
		font-size: 6vw;
		right: 25vw;
	}
}
</style>