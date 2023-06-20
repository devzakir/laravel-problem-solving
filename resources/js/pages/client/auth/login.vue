<template>
  <div class="main__container">
    <h2>ログイン</h2>
    <div class="login__form">
      <div class="form__row">
        <input type="email" v-model="email" placeholder="メールアドレス" />
      </div>
      <div class="form__row">
        <input type="password" v-model="password" placeholder="パスワード" />
      </div>
      <div class="form__check">
        <label>
          <input type="checkbox" v-model="persist" />
          ログイン状態を保持する
        </label>
      </div>
      <div class="form__action">
        <button @click="login">ログイン</button>
      </div>
    </div>
  </div>
</template>
<script>
export default {
  layout: 'client_auth',
  data() {
    return {
      email: "",
			password: "",
			persist: false
    }
  },
  methods: {
    invalid() {
			return this.email.trim() == '' || this.password.trim() == ''
		},
    async login() {
      if (this.invalid()) {
				return
			}
      try {
				const { data } = await axios.post('/api/client_login', {
					email: this.email,
					password: this.password
				})
				this.$store.dispatch('auth/saveToken', {
					token: data.token,
					remember: true
				})
				this.$router.push({ name: 'client.top' })
			} catch (error) {
				this.$swal('', 'エラーが発生しました')
			}
    }
  }
}
</script>
<style lang="scss" scoped>
.main__container {
  display: flex;
  justify-content: center;
  flex-direction: column;
  padding-top: 100px;

  h2 {
    font-size: 24px;
    color: var(--text-color);
    margin-bottom: 10px;
    text-align: center;
  }

  .login__form {
    border-radius: 5px;
    border: 1px solid var(--border-color);
    width: 690px;
    padding: 50px 0;
    background-color: #fff;
    box-shadow: 0px 3px 6px rgba($color: #000000, $alpha: 0.16);

    .form__row {
      width: 350px;
      margin-bottom: 35px;
      margin-left: auto;
      margin-right: auto;

      input {
        width: 100%;
        height: 50px;
        border-radius: 5px;
        padding-left: 13px;
        padding-right: 13px;
        border: 1px solid var(--border-color);
      }

      &:last-of-type {
        margin-bottom: 25px;
      }
    }

    .form__check {
      margin-bottom: 30px;
      margin-left: auto;
      margin-right: auto;
      width: fit-content;

      label {
        display: flex;
        align-items: center;
        font-size: 14px;
        color: var(--text-color);

        input {
          width: 20px;
          height: 20px;
          border: 1px solid var(--border-color);
          margin-right: 20px;
        }
      }
    }

    .form__action {
      display: flex;
      justify-content: center;

      button {
        width: 120px;
        height: 30px;
        background-color: var(--accent-color2);
        color: #fff;
        font-size: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 0;
        cursor: pointer;
      }
    }
  }
}
</style>