<template>
  <div class="create__container">
    <h2 class="page__title">メール配信履歴詳細</h2>
    <div class="contact__info" v-if="!!mail">
      <div class="d-flex contact_heading">
        <h2 class="page__title">取引先情報</h2>
        <span class="company_name">{{ mail.user.com_name }}</span>
      </div>
      <div class="contact__form">
        <div class="form__row">
          <label>差出人名</label>
          <p>
            {{ mail.recipienter.name }}　{{ mail.recipienter.type == 1 ? 'マスターアカウント' : mail.recipienter.tanto_name }}
          </p>
        </div>
        <div class="form__row">
          <label>宛先</label>
          <p>{{ mail.user.com_name | '' }}　　　{{ mail.user.email }}</p>
        </div>
        <div class="form__row">
          <label>件名<span class="must">必須</span></label>
          <p>
            {{ mail.title }}
          </p>
        </div>
      </div>
    </div>
    <div class="message__part" v-if="!!mail">
      <p class="to_client">
        {{ mail.user.com_name }}<br/>
        {{ mail.user.name }}様
      </p>
      <p class="message_text">{{ mail.message }}<br/>{{ mail.mail_type == 2 ? mail.deadline : '' }}<br/>{{ mail.address }}
      </p>
    </div>

    <div class="actions">
      <button @click="back">戻る</button>
    </div>
  </div>
</template>
<script>
export default {
  layout: 'recipient',
  data() {
    return {
      mail: null
    }
  },
  mounted() {
    this.init()
  },
  methods: {
    async init() {
      try {
        const { data } = await axios.post('/recipienter/get_mail_history_detail', {
          id: this.$route.query.id
        })
        this.mail = data.mail
      } catch (error) {
      }
    },
    back() {
      this.$router.back()
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
      height: 100px;
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

    .message_text {
      padding-left: 16px;
      margin-top: 25px;
      font-size: 12px;
      word-break: break-all;
      white-space: pre-wrap;
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
    }
  }
}
</style>