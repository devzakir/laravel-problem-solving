<template>
  <div class="account_create">
    <div class="inner">
      <h2 class="page__title">プラットフォーム設定編集</h2>
      <div class="account_form">
        <div class="form__row">
          <label>プラットフォーム利用料率</label>
          <p>
            <input type="number" v-model="platform_fee" />
          </p>
          <span>%</span>
        </div>
        <div class="form__row">
          <label>最低入金額</label>
          <p>
            <input v-model="min_price" type="number" />
          </p>
          <span>円</span>
        </div>
      </div>
      <div class="form__actions">
        <button @click="closeModal">キャンセル</button>
        <button @click="invite">保存</button>
      </div>
    </div>
  </div>
</template>
<script>
export default {
  props: ['fee', 'price'],
  data() {
    return {
      platform_fee: null,
      min_price: null
    }
  },
  mounted() {
    this.platform_fee = this.$props.fee
    this.min_price = this.$props.price
  },
  methods: {
    closeModal() {
      this.$emit('closeModal')
    },

    async invite() {
        const { data } = await axios.post('/admin/save_platform_info', {
          platform_fee: this.platform_fee,
          min_price: this.min_price,
        })
        this.$swal('', '更新しました。')
        this.$emit('closeModal')
     
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

    label {
      font-size: 14px;
      display: flex;
      align-items: center;
      width: 180px;
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