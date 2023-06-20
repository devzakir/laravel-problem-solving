<template>
  <div class="activate__container">
    <h2>パスワード設定</h2>
    <div class="form__part">
      <div class="form__row">
        <label>メールアドレス</label>
        <p>
          <input type="email" :value="$route.query.email" disabled />
        </p>
      </div>
      <div class="form__row">
        <label>パスワード</label>
        <p>
          <input type="password" v-model="password" />
        </p>
      </div>
      <div class="form__row">
        <label>パスワード（確認用）</label>
        <p>
          <input type="password" v-model="confirm_password" />
        </p>
      </div>

      <div class="form__action">
        <button @click="setPassword">パスワード設定する</button>
      </div>
    </div>
  </div>
</template>
<script>
export default {
  data() {
    return {
      password: '',
      confirm_password: ''
    }
  },
  mounted() {
    // this.init()
  },
  methods: {
    async setPassword() {
      if (this.password.trim() == '') {
        return
      }

      if (this.password != this.confirm_password) {
        return
      }

      try {
        const { data } = await axios.post('/api/set_password', {
          password: this.password,
          email: this.$route.query.email,
          type: this.$route.query.type
        })
        if (data.flag == true) {
          this.$swal('', 'パスワードを設定しました！')
          if (this.$route.query.type == 1) {
            this.$router.push({ name: 'recipient.auth.login' })
          } else {
            this.$router.push({ name: 'client.auth.login' })
          }
        }
      } catch (error) {
      }
    },
    async init() {
        var email = this.$route.query.email
        var token = this.$route.query.token
        var type = this.$route.query.type
        try {
            const { data } = await axios.post('/api/activate_email', {
              email: email,
              token: token,
              type: type
            })
            if (data.status == 0) {
              this.$swal('', 'メールアドレスが有効ではありません。')
              this.$router.push({ name: 'admin.auth.login' })
            } else if (data.status == 1) {
              this.$swal('', 'すでに認証済みのアカウントです。')
              this.$router.push({ name: 'admin.auth.login' })
            } else if (data.status == 2) {
              this.$swal('', 'トークンが違います。')
              this.$router.push({ name: 'admin.auth.login' })
            } else if (data.status == 3) {
              this.$swal('', 'URLの有効期限が過ぎました。')
              this.$router.push({ name: 'admin.auth.login' })
            }
        } catch (error) {
        }
    }
  }
}
</script>
<style lang="scss" scoped>
.activate__container {
  display: flex;
  justify-content: center;
  flex-direction: column;
  align-items: center;
  padding-top: 100px;

  h2 {
    font-size: 24px;
    color: var(--text-color);
    margin-bottom: 10px;
    text-align: center;
    margin-bottom: 20px;
  }

  .form__part {
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

    .form__action {
      display: flex;
      justify-content: center;

      button {
        width: 200px;
        height: 30px;
        background-color: var(--accent-color);
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