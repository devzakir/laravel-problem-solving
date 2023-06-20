<template>
  <div class="form__container">
    <h2 class="page__title" v-if="!!order_form">注文フォーム（{{ order_form.name }}注文フォーム）</h2>
    <div class="form__part">
      <div class="form__row">
        <label>フォーム名<span class="must">必須</span></label>
        <p>
          <input type="text" placeholder="〇〇〇〇の注文フォーム" v-model="name" />
        </p>
      </div>
      <div class="form__row">
        <label>URL</label>
        <p v-if="!!order_form">
          https://ukerundesu.com/client/order/detail?id={{ order_form.id }}
        </p>
      </div>
      <div class="form__row area">
        <label>お取引条件</label>
        <p>
          <textarea placeholder="例）締切時間、送料、休業日、キャンペーン期間など" v-model="condition">
          </textarea>
        </p>
      </div>
      <div class="form__row">
        <label>希望納品日</label>
        <p class="radio_btns">
          <a @click="() => { has_deadline = 1 }" :class="{ active: has_deadline == 1 }">
            <span></span>設定する
          </a>
          <a @click="() => { has_deadline = 2 }" :class="{ active: has_deadline == 2 }">
            <span></span>設定しない
          </a>
        </p>
      </div>
      <div class="form__row">
        <label>支払方法</label>
        <p class="radio_btns">
          <a @click="selectPaymentMethod(1)" :class="{ active: (payment_method == 1 || payment_method == 3) }">
            <span></span>銀行振込
          </a>
          <a class="credit" @click="selectPaymentMethod(2)" :class="{ active: (payment_method == 2 || payment_method == 3) }">
            <span></span>クレジットカード
          </a>
        </p>
      </div>
      <div class="form__row area">
        <label>社内メモ</label>
        <p>
          <textarea v-model="memo" placeholder="社内管理用のメモとしてご利用ください">
          </textarea>
        </p>
      </div>
      <div class="form__actions">
        <button @click="back">戻る</button>
        <button @click="save">保存</button>
      </div>
    </div>
  </div>
</template>
<script>
export default {
  layout: 'recipient',
  middleware: 'recipient',
  data() {
    return {
      name: "",
      condition: "",
      has_deadline: 0,
      payment_method: 0,
      memo: "",
      order_form: null
    }
  },
  mounted() {
    this.init()
  },
  methods: {
    back() {
      this.$router.back()
    },
    async save() {
      if (this.name.trim() == '') {
        return
      }

      try {
        const { data } = await axios.post('/recipienter/edit_order_form', {
          name: this.name,
          condition: this.condition,
          has_deadline: this.has_deadline,
          payment_method: this.payment_method,
          memo: this.memo,
          id: this.order_form.id
        })
        this.$swal('', '更新しました')
        this.$router.push({ name: 'recipient.order_form' })
      } catch (error) {
      }
    },
    selectPaymentMethod(flag) {
      if (this.payment_method == 1) {
        if (flag == 1) {
          this.payment_method = 0
        } else {
          this.payment_method = 3
        }
      } else if (this.payment_method == 2) {
        if (flag == 1) {
          this.payment_method = 3
        } else {
          this.payment_method = 0
        }
      } else if (this.payment_method == 3) {
        if (flag == 1) {
          this.payment_method = 2
        } else {
          this.payment_method = 1
        }
      } else if (this.payment_method == 0) {
        if (flag == 1) {
          this.payment_method = 1
        } else {
          this.payment_method = 2
        }
      }
    },
    async init() {
      try {
        const { data } = await axios.post('/recipienter/get_order_form_info', {
          id: this.$route.query.id
        })
        this.order_form = data.order_form
        this.name = this.order_form.name
        this.condition = this.order_form.condition
        this.has_deadline = this.order_form.has_deadline
        this.payment_method = this.order_form.payment_method
        this.memo = this.order_form.memo
      } catch (error) {
      }
    }
  }
}
</script>
<style lang="scss" scoped>
.form__container {
  padding: 20px 30px;

  .form__part {
    width: 800px;
    border: 1px solid var(--border-color);
    background-color: #fff;
    padding: 35px;
    border-radius: 5px;
    margin-top: 20px;
    margin-left: 50px;

    .form__row {
      display: flex;
      align-items: center;
      margin-bottom: 12px;

      &.area {
        align-items: flex-start;
      }

      label {
        width: 167px;
        font-size: 14px;
        display: flex;
        align-items: center;

        .must {
          width: 30px;
          height: 20px;
          background-color: #D34555;
          color: #fff;
          display: flex;
          align-items: center;
          justify-content: center;
          margin-left: auto;
          margin-right: 10px;
        }
      }

      p {
        flex: 1;

        input {
          width: 250px;
          height: 30px;
          border: 1px solid var(--border-color);
          padding-left: 5px;
          padding-right: 5px;
          font-size: 12px;
        }

        textarea {
          width: 100%;
          height: 100px;
          border: 1px solid var(--border-color);
          padding: 2px 5px;
          border-radius: 5px;
        }

        &.radio_btns {
          display: flex;

          a {
            width: 120px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            border: 1px solid var(--border-color);
            border-radius: 5px;
            color: #000;

            &.credit {
              width: 150px;
            }

            &:first-of-type {
              margin-right: 10px;
            }

            span {
              width: 10px;
              height: 10px;
              border-radius: 50%;
              background-color: #fff;
              border: 1px solid var(--border-color);
              margin-right: 15px;
            }

            &.active {
              background-color: #3AADF0;
              color: #fff;

              span {
                background-color: #006AFF;
                border: 1px solid #fff;
              }
            }
          }
        }
      }
    }

    .form__actions {
      display: flex;
      align-items: center;
      justify-content: center;
      padding-top: 20px;

      button {
        width: 180px;
        height: 35px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        box-shadow: 0 3px 6px rgba($color: #000000, $alpha: 0.16);

        &:first-of-type {
          margin-right: 50px;
          background-color: #fff;
          border: 1px solid var(--border-color);
          color: var(--text-color);
        }

        &:last-of-type {
          background-color: var(--accent-color);
          color: #fff;
          border: 0;
        }
      }
    }
  }
}
</style>