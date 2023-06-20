<template>
  <div class="create__container">
    <h2 class="page__title">発注者に連絡</h2>
    <div class="contact__info">
      <div class="d-flex contact_heading">
        <h2 class="page__title">取引先情報</h2>
        <span class="company_name">{{ !!order_form ? order_form.user.com_name : "" }}</span>
      </div>
      <div class="contact__form">
        <div class="form__row">
          <label>差出人名</label>
          <p>
            <select v-model="form.from" :disabled="step == 2">
              <option :value="$store.getters['recipienter_auth/user'].id">{{ $store.getters['recipienter_auth/user'].name }}　{{ $store.getters['recipienter_auth/user'].type == 1 ? 'マスターアカウント' : $store.getters['recipienter_auth/user'].tanto_name }}</option>
            </select>
          </p>
        </div>
        <div class="form__row">
          <label>宛先</label>
          <p>{{ order_form.user.com_name | '' }}　　　{{ order_form.user.email }}</p>
        </div>
        <div class="form__row">
          <label>件名<span class="must">必須</span></label>
          <p>
            <input v-model="form.title" type="text" placeholder="【ご連絡】株式会社〇〇（注文番号：0000000-111）" :disabled="step == 2" />
          </p>
        </div>
      </div>
    </div>
    <div class="message__part">
      <p class="to_client">
        {{ order_form.user.com_name }}<br/>
        {{ order_form.user.name }}様
      </p>

      <textarea class="message_input" v-model="form.message" placeholder="メッセージ記入欄（在庫、納期等の連絡など）" :disabled="step == 2"></textarea>
<div class="textarea">
  <textarea @keyup="keyupChange" v-model="form.address" class="message_input" :disabled="step == 2"></textarea>
  <p class="placeholder" v-if="toggle">
    —————————————————————————<br/>
    株式会社〇〇<br/>
    〇〇 テスト<br/>
    info@conecle.com<br/>
    <br/>
    〒520-1111<br/>
    大阪府大阪市北区〇〇町1-1<br/>
    TEL:06-7777-0000<br/>
    —————————————————————————
  </p>
</div>
    </div>

    <div class="actions">
      <button @click="back">戻る</button>
      <button @click="next">{{ step == 1 ? 'PDFを選択して確認画面へ' : '送信' }}</button>
    </div>

    <input type="file" class="dis-none" ref="pdf_selector" @change="selectedPDF" accept="application/pdf" />
  </div>
</template>
<script>
export default {
  layout: 'recipient',
  data() {
    return {
      form: {
        from: null,
        title: null,
        message: null,
        address: null,
        client_id: null,
        order_id: null
      },
      order_form: null,
      step: 1,
      pdf: null,
      toggle: true
    }
  },
  mounted() {
    this.init()
  },
  methods: {
    async init() {
      const { data } = await axios.post('/recipienter/get_order_detail', {
        id: this.$route.query.id
      })
      this.order_form = data.order_form
      this.form.client_id = this.order_form.user_id
      this.form.order_id = this.order_form.id
    },
    back() {
      if (this.step == 2) {
        this.step = 1
      } else {
        this.$router.back()
      }
    },
    next() {
      if (!this.form.from || !this.form.title || !this.form.message || !this.form.address) {
        this.$swal('', '必須情報を入力してください')
        return
      }
      if (this.step == 1) {
        this.$refs.pdf_selector.click()
      } else if (this.step == 2) {
        this.sendMessage()
      }
    },
    async selectedPDF(event) {
      const file = event.target.files[0]
      if (!!file) {
        this.pdf = file
        this.step = 2
      }
    },
    async sendMessage() {
      if (!this.pdf) {
        this.$swal('', '請求書を選択して下さい')
        return
      }

      let formData = new FormData()
      formData.append('from', this.form.from)
      formData.append('title', this.form.title)
      formData.append('message', this.form.message)
      formData.append('address', this.form.address)
      formData.append('client_id', this.form.client_id)
      formData.append('order_id', this.form.order_id)
      formData.append('pdf', this.pdf)
      try {
        const { data } = await axios.post('/recipienter/send_invoice_message', formData, { headers: { 'Content-Type': 'multipart/form-data' }})
        this.$swal('', '送信しました。')
        if (data.flag) {
          this.$router.back()
        }
      } catch(error) {
      }
    },
    keyupChange() {
      if (this.form.address.length !== 0) {
        this.toggle = false
      } else {
        this.toggle = true
      }
    }
  }
}
</script>
<style lang="scss" scoped>
.create__container {
  padding: 20px 30px;

  .contact__info {
    width: fit-content;
    padding: 12px 14px;
    border: 1px solid var(--border-color);
    border-radius: 5px;
    background-color: #fff;
    margin-left: 50px;
    margin-top: 20px;
  }
  .contact_heading {
    align-items: center;
    margin-bottom: 14px;

    .company_name {
      margin-left: 30px;
      font-size: 12px;
    }
  }

  .contact__form {
    padding-left: 20px;

    .form__row {
      margin-bottom: 4px;
      display: flex;
      align-items: center;
      
      label {
        font-size: 12px;
        align-items: center;
        width: 65px;
        display: flex;

        .must {
          width: 26px;
          height: 12px;
          font-size: 10px;
          display: flex;
          align-items: center;
          justify-content: center;
          background-color: #D34555;
          color: #fff;
          margin-left: 3px;
        }
      }

      p {
        font-size: 12px;
        flex: 1;
        input, select {
          width: 100%;
          height: 30px;
          border: 1px solid var(--border-color);
          padding-left: 10px;
          padding-right: 10px;
          font-size: 12px;
          width: 316px;
        }
      }
    }
  }

  .message__part {
    width: 800px;
    border: 1px solid var(--border-color);
    margin-left: 50px;
    margin-top: 24px;
    padding: 25px 32px;
    background-color: #fff;

    .to_client {
      font-size: 12px;
      margin-bottom: 20px;
    }

    .message_input {
      display: block;
      width: 100%;
      height: 200px;
      border: 1px solid var(--border-color);
      padding: 4px 8px;
      font-size: 12px;
      margin-bottom: 26px;
      resize: none;
    }

    .condition_input {
      display: block;
      width: 100%;
      height: 240px;
      border: 1px solid var(--border-color);
      padding: 4px 8px;
      font-size: 12px;
      margin-bottom: 26px;
      resize: none;
    }
  }

  .actions {
    display: flex;
    align-items: center;
    justify-content: center;
    padding-top: 30px;

    button {
      width: 200px;
      height: 35px;
      box-shadow: 0 3px 6px rgba($color: #000000, $alpha: 0.16);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 14px;

      &:first-of-type {
        margin-right: 50px;
        background-color: #fff;
        border: 1px solid var(--border-color);
      }

      &:last-of-type {
        background-color: var(--accent-color);
        color: #fff;
        border: 0;
      }
    }
  }
}

.textarea {
  position: relative;
  textarea {
    background: transparent;
    position: relative;
  }
  .placeholder {
    position: absolute;
    left: 8px;
    top: 4px;
    font-size: 12px;
    z-index: 0;
    opacity: .7;
  }
}
</style>