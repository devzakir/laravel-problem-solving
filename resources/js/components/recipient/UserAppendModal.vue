<template>
  <div class="account_create">
    <div class="inner">
      <h2 class="page__title">ユーザー情報</h2>
      <div class="account_form">
        <div class="form__row">
          <label>種別<span>必須</span></label>
          <p>
            利用者アカウント
          </p>
        </div>
        <div class="form__row">
          <label>利用者名<span>必須</span></label>
          <p>
            <input type="text" v-model="tanto_name" />
          </p>
        </div>
        <div class="form__row">
          <label>メールアドレス<span>必須</span></label>
          <p>
            <input type="text" v-model="email" />
          </p>
        </div>
      </div>
      <div class="form__actions">
        <button @click="closeModal">戻る</button>
        <button @click="save">保存</button>
      </div>
    </div>
  </div>
</template>
<script>
export default {
  data() {
    return {
      tanto_name: null,
      email: null
    }
  },
  async mounted() {
  },
  methods: {
    closeModal() {
      this.$emit('closeModal')
    },

    async save() {
      try {
        const { data } = await axios.post('/recipienter/create_user_info', {
          tanto_name: this.tanto_name,
          email: this.email
        })
        this.$swal('', '招待しました')
        this.$emit('closeModal')
      } catch (error) {
      }
    }
  }
}
</script>
<style lang="scss" scoped>
.account_create {
  position: fixed;
  left: 0;
  top: 0;
  width: 100%;
  height: 100%;
  background-color: rgba($color: #000000, $alpha: 0.16);
  z-index: 100;

  .inner {
    position: fixed;
    left: 50%;
    top: 50%;
    width: 500px;
    transform: translate(-50%, -50%);
    background-color: #fff;
    border: 1px solid var(--border-color);
    box-shadow: 0 3px 6px rgba($color: #000000, $alpha: 0.16);
    padding: 28px 20px;
  }
}

.account_form {
  margin-top: 30px;
  background-color: #F3F3F3;
  border: 1px solid var(--border-color);
  padding: 25px 12px;
  margin-bottom: 25px;

  .form__row {
    display: flex;
    align-items: center;
    margin-bottom: 12px;

    &.flex-start {
      align-items: flex-start;
    }

    label {
      font-size: 14px;
      display: flex;
      align-items: center;
      width: 167px;
      padding-right: 10px;

      span {
        width: 30px;
        height: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: #D34555;
        color: #fff;
        margin-left: auto;
      }
    }

    p {
      flex: 1;
      input, select {
        width: 100%;
        height: 30px;
        border-radius: 5px;
        border: 1px solid var(--border-color);
      }
    }

    .zipcode {
      display: flex;
      align-items: center;
    }

    div{
      flex: 1;
    }

    p {
      margin-bottom: 10px;

      span {
        margin-right: 10px;
      }
    }

    .prefecture {
      width: 120px;
    }
  }
}

.form__actions {
  display: flex;
  align-items: center;
  justify-content: center;

  button {
    width: 180px;
    height: 35px;
    display: flex;
    align-items: center;
    justify-content: center;

    &:first-of-type {
      margin-right: 50px;
      border: 1px solid var(--border-color);
    }

    &:last-of-type {
      background-color: var(--accent-color);
      color: #fff;
      border: 0;
    }
  }
}
</style>