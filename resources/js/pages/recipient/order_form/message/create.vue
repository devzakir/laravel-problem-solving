<template>
  <div class="create__container">
    <h2 class="page__title">注文フォームの案内メール</h2>
    <div class="contact__info">
      <div class="d-flex contact_heading">
        <h2 class="page__title">取引先情報</h2>
      </div>
      <div class="contact__form">
        <div class="form__row" v-if="!$route.query.id">
          <label>注文フォーム</label>
          <p>
            <select v-model="order_form" :disabled="step == 2">
              <option v-for="(item, index) in forms" :key="index" :value="item.id">{{ item.name }}</option>
            </select>
          </p>
        </div>
        <div class="form__row">
          <label>差出人名</label>
          <p>
            <select v-model="from" @change="changeRecipienter" :disabled="step == 2">
              <option :value="$store.getters['recipienter_auth/user'].id">{{ $store.getters['recipienter_auth/user'].name }} {{ $store.getters['recipienter_auth/user'].type == 1 ? 'マスターアカウント' : $store.getters['recipienter_auth/user'].tanto_name }}</option>
              <option :value="recipienter.id" v-for="(recipienter, index) in childRecipienters" :key="index">{{ recipienter.type == 1 ? 'マスタアカウント' : recipienter.tanto_name }}</option>
            </select>
          </p>
        </div>
        <div class="form__row">
          <label>宛先</label>
          <p>
            <select v-model="to" :disabled="step == 2">
              <option v-for="(user, index) in users" :key="index" :value="user.id">{{ user.com_name }}</option>
            </select>
          </p>
        </div>
        <div class="form__row">
          <label>件名<span class="must">必須</span></label>
          <p>
            <input type="text" v-model="title" :disabled="step == 2" placeholder="【ご連絡】株式会社〇〇（注文番号：0000000-111）" />
          </p>
        </div>
      </div>
    </div>
    <div class="message__part">
      <p class="to_client">
        {{ !!calcCompany() ? calcCompany().com_name : '〇〇〇〇' }}<br/>
        {{ !!calcCompany() ? calcCompany().name : '〇〇〇〇' }}様
      </p>

      <textarea v-model="content" :disabled="step == 2" class="message_input" placeholder="メッセージ記入欄（在庫、納期等の連絡など）"></textarea>
      <div class="form__info">
          ご注文はこちらから→<a>https://ukerundesu.com/client/order/detail?id={{ $route.query.id }}</a><br/>
          ログインID：{{ calcLoginId() }}<br/>
        </div>
<div class="textarea">
  <textarea @keyup="keyupChange" v-model="address_info" class="message_input" :disabled="step == 2"></textarea>
  <p class="placeholder" v-if="toggle">
    署名や備考など記入欄<br/>
    —————————————————————————<br/>
    株式会社◯◯◯◯<br/>
    メールアドレス：<br/>
    電話番号：<br/>
    住所：<br/>
    —————————————————————————
  </p>
</div>
    </div>

    <div class="actions">
      <button @click="back">戻る</button>
      <button @click="next">{{ step == 1 ? '確認画面へ' : '送信' }}</button>
    </div>
  </div>
</template>
<script>
export default {
  layout: 'recipient',
  middleware: 'recipient',
  data() {
    return {
      childRecipienters: [],
      users: [],
      from: null,
      to: null,
      title: "",
      content: "",
      address_info: "",
      step: 1,
      order_form: null,
      forms: [],
      order_form: null,

      toggle: true
    }
  },
  mounted() {
    console.log(this.$store.getters['recipienter_auth/user'], "___123123123")
    this.init()
  },
  methods: {
    calcCompany() {
      if (!this.to) {
        return null
      } else {
        return this.users.find(item => {
          return item.id == this.to
        })
      }
    },
    calcLoginId() {
      if(!!this.to) {
        var temp = this.users.find(item => {
          return item.id == this.to
        })
        return temp.email
      } else {
        return ''
      }
    },
    calcUserName() {
      if(!!this.to) {
        var temp = this.users.find(item => {
          return item.id == this.to
        })
        return temp.name
      } else {
        return ''
      }
    },
    async init() {
      try {
        const { data } = await axios.post('/recipienter/get_message_create_screen_ini', {
          id: this.$route.query.id
        })
        this.childRecipienters = data.childRecipienters
        this.order_form = data.order_form
        this.forms = data.order_forms
        this.getClientInfo(this.from)
        this.content = `お取引先様
いつも大変お世話になっております。
株式会社${this.$store.getters['recipienter_auth/user'].name}の${this.$store.getters['recipienter_auth/user'].tanto_name || 'マスタ管理者'}です。

このたび、Web上にも注文フォームをと用意いたしました。
24時間いつでも注文可能となりますので、よろしければご利用ください。

ご不明な点がございましたらお気軽にご連絡ください。`
      } catch (error) {
      }
    },
    changeRecipienter() {
      this.getClientInfo(this.from)
    },
    async getClientInfo(recipienter_id) {
      try {
        const { data } = await axios.post('/recipienter/get_self_clients', {
          recipienter_id: recipienter_id
        })
        this.users = data.users
      } catch (error) {
      }
    },
    back() {
      if (this.step == 2) {
        this.step = 1
      } else {
        this.$router.back()
      }
    },
    next() {
      if (this.step == 1) {
        if (!this.from || !this.to || this.title == '' || this.content == '' || this.address_info == '') {
          this.$swal('', '必須項目を入力してください')
          return
        }

        if (!this.$route.query.id) {
          if (!this.order_form) {
            this.$swal('', '注文フォームを選択して下さい')
            return
          }
        }

        this.step = 2
      } else {
        this.sendMessage()
        // send Email template
      }
    },
    async sendMessage() {
      try {
        const { data } = await axios.post('/recipienter/create_order_form_message', {
          recipienter_id: this.from,
          user_id: this.to,
          user_name: this.calcUserName(),
          title: this.title,
          content: this.content,
          address_info: this.address_info,
          order_form_id: !!this.$route.query.id ? this.$route.query.id : this.order_form
        })
        this.$swal('', '送信しました')
        this.$router.push({ name: 'recipient.order_form' })
      } catch (error) {
      }
    },
    keyupChange() {
      if (this.address_info.length !== 0) {
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
        width: 100px;
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
      width: 180px;
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

.form__info {
  margin-bottom: 30px;
}

.textarea {
  position: relative;
  textarea {
    background: transparent;
    position: relative;
    z-index: 2;
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