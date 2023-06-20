<template>
  <div class="create__container">
    <h2 class="page__title">見積書作成</h2>
    <div class="main__part">
      <div class="flow__part">
        <p class="step__item">
          <span class="circle active"></span>
          <span class="step_text">基本情報</span>
        </p>
        <p class="step__item">
          <span class="circle"></span>
          <span class="step_text">明細</span>
        </p>
        <p class="step__item">
          <span class="circle"></span>
          <span class="step_text">確認</span>
        </p>
        <p class="step__item">
          <span class="circle"></span>
          <span class="step_text">完了</span>
        </p>
      </div>
      <div class="form__part">
        <div class="form__row">
          <label>案件名<span class="must">必須</span></label>
          <p>
            <input type="text" v-model="subject_title" />
          </p>
        </div>
        <div class="form__row">
          <label>発行日</label>
          <p>
            <input type="date" v-model="publish_date" />
          </p>
        </div>
        <h3 class="page__title">宛先<a class="append_btn" @click="showModal">取引先<br/>から選択</a></h3>
        <div class="form__row">
          <label>取引先<span class="must">必須</span></label>
          <p class="groupd">
            <input type="text" v-model="com_name" />
          </p>
        </div>
        <div class="form__row">
          <label>部署名</label>
          <p class="groupd">
            <input type="text" v-model="department_name" />
          </p>
        </div>
        <div class="form__row">
          <label>担当者名<span class="must">必須</span></label>
          <p>
            <input type="text" v-model="name" />
          </p>
        </div>
        <div class="form__row">
          <label>メールアドレス<span class="must">必須</span></label>
          <p>
            <input type="email" v-model="email" />
          </p>
        </div>
        <div class="form__action">
          <button @click="back">戻る</button>
          <button @click="next">次へ</button>
        </div>
      </div>
    </div>

    <ClientModal @appended="appended" @closeOnly="closeOnly" v-if="isShowModal" />
  </div>
</template>
<script>
import ClientModal from "../../../components/recipient/ClientModal.vue"
export default {
  layout: 'recipient',
  middleware: 'recipient',
  components: {
    ClientModal
  },
  data() {
    return {
      users: [],
      subject_title: null,
      publish_date: null,
      email: null,
      com_name: null,
      department_name: null,
      name: null,
      isShowModal: false
    }
  },
  mounted() {
    this.init()
  },
  methods: {
    closeOnly() {
      this.isShowModal = false
    },
    async init() {
      try {
        const { data } = await axios.post('/recipienter/get_quote_create_init')
        this.users = data.users

        if (!!this.$route.query.client_id) {
          this.appended(this.$route.query.client_id)
        }
      } catch (error) {
      }
    },
    showModal() {
      this.isShowModal = true
    },
    back() {
      this.$router.back()
    },
    appended(id) {
      const temp = this.users.find(item => {
        return item.id == id
      })
      this.isShowModal = false
      this.email = temp.email
      this.com_name = temp.com_name
      this.department_name = temp.department_name
      this.name = temp.name
    },
    next() {
      if (!this.subject_title || !this.publish_date || !this.email || !this.com_name || !this.department_name || !this.name) {
        return
      }

      localStorage.setItem('subject_title', this.subject_title)
      localStorage.setItem('publish_date', this.publish_date)
      localStorage.setItem('email', this.email)
      localStorage.setItem('com_name', this.com_name)
      localStorage.setItem('department_name', this.department_name)
      localStorage.setItem('name', this.name)

      this.$router.push({ name: 'recipient.quote.create_2' })
    }
  }
}
</script>
<style lang="scss" scoped>
.mt-30 {
  margin-top: 30px;
}
.create__container {
  padding: 20px 30px;

  .main__part {
    margin-left: 200px;
    margin-top: 20px;

    .flow__part {
      display: flex;
      position: relative;
      width: 355px;
      margin-left: auto;
      margin-right: auto;
      justify-content: space-between;
      margin-bottom: 60px;

      &:after {
        content: '';
        position: absolute;
        left: 15px;
        width: 325px;
        top: 7px;
        height: 3px;
        background-color: #8D8B8B;
      }

      .step__item {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        position: relative;
        z-index: 20;

        .circle {
          width: 15px;
          height: 15px;
          background-color: #8D8B8B;
          margin-bottom: 2px;
          border-radius: 50%;

          &.active {
            background-color: var(--accent-color);
          }
        }

        .step_text {
          font-size: 10px;
          text-align: center;
          position: absolute;
          top: 20px;
          left: 50%;
          transform: translate(-50%, 0);
        }

        &:nth-of-type(1) {
          .step_text {
            width: 47px;
          }
        }
        &:nth-of-type(2) {
          .step_text {
            width: 43px;
          }
        }
        &:nth-of-type(3) {
          .step_text {
            width: 43px;
          }
        }
        &:nth-of-type(4) {
          .step_text {
            width: 43px;
          }
        }
      
      }
    }

    .form__part {
      width: 500px;
      border: 1px solid var(--border-color);
      box-shadow: 0 3px 6px rgba($color: #000000, $alpha: 0.16);
      padding: 16px 20px;
      background-color: #fff;

      h3 {
        margin-bottom: 27px;
      }

      .form__row {
        display: flex;
        align-items: center;
        margin-bottom: 10px;

        label {
          font-size: 14px;
          display: flex;
          align-items: center;
          width: 175px;

          .must {
            width: 30px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            background-color: #D34555;
            font-size: 10px;
            margin-left: 10px;
          }
        }

        p {
          flex: 1;
          display: flex;
          align-items: center;

          input {
            flex: 1;
            width: 100%;
            height: 40px;
            border-radius: 5px;
            border: 1px solid var(--border-color);
            padding-left: 5px;
            padding-right: 5px;

            &:nth-of-type(2) {
              margin-left: 10px;
            }
          }
        }
      }

      .form__action {
        display: flex;
        align-items: center;
        justify-content: center;
        padding-top: 40px;

        button {
          width: 180px;
          height: 35px;
          display: flex;
          align-items: center;
          font-size: 14px;
          justify-content: center;
          box-shadow: 0 3px 6px rgba($color: #000000, $alpha: 0.16);
          
          &:first-of-type {
            margin-right: 50px;
            background-color: #fff;
            color: var(--text-color);
            border: 1px solid var(--border-color);
          }

          &:last-of-type {
            background-color: #529598;
            color: #fff;
            border: 0;
          }
        }
      }
    }
  }
}

.action__part {
  padding-top: 42px;
  display: flex;
  justify-content: center;

  button {
    width: 180px;
    height: 35px;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0px 3px 6px rgba($color: #000000, $alpha: 0.16);
    background-color: var(--accent-color);
    color: #fff;
    border: 0;
  }
}

.page__title {
  position: relative;
}

.append_btn {
  width: 53px;
  height: 30px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 4px;
  background-color: #fff;
  box-shadow: 0 3px 6px rgba($color: #000000, $alpha: 0.16);
  font-size: 8px;
  color: #000 !important;
  line-height: 1.2;
  text-align: center;
  padding-top: 4px;
  position: absolute;
  right: 0;
  top: 4px;
}
</style>