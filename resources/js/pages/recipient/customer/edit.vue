<template>
  <div class="create__container">
    <h2 class="page__title">取引先ー編集</h2>
    <div class="main__part">
      <div class="form__part">
        <h3 class="page__title">取引先情報</h3>
        <div class="form__row">
          <label>会社名<span class="must">必須</span></label>
          <p>
            <input type="text" v-model="com_name" />
          </p>
        </div>
        <div class="form__row">
          <label>部署名</label>
          <p>
            <input type="text" v-model="department_name" />
          </p>
        </div>
        <div class="form__row">
          <label>担当者名<span class="must">必須</span></label>
          <p>
            <input type="text" placeholder="担当者名" v-model="name" />
          </p>
        </div>
        <div class="form__row">
          <label>電話番号<span class="must">必須</span></label>
          <p>
            <input type="text" v-model="telephone" />
          </p>
        </div>
        <div class="form__row">
          <label>メールアドレス<span class="must">必須</span></label>
          <p>
            <input type="email" v-model="email" disabled />
          </p>
        </div>
        <h3 class="page__title mt-30" v-if="recipienters.length > 0">社内情報</h3>
        <div class="form__row" v-if="recipienters.length > 0">
          <label>担当者</label>
          <p>
            <select v-model="recipienter_id">
              <option :value="$store.getters['recipienter_auth/user'].id">{{ $store.getters['recipienter_auth/user'].type == 1 ? 'マスターアカウント' : $store.getters['recipienter_auth/user'].tanto_name }}</option>
              <option v-for="(item, index) in recipienters" :key="index" :value="item.id">{{ item.tanto_name }}</option>
            </select>
          </p>
        </div>
      </div>
      <div class="action__part">
        <button @click="inviteUser">更新</button>
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
      com_name: null,
      department_name: null,
      name: null,
      telephone: null,
      email: '',
      recipienter_id: null,
      recipienters: []
    }
  },
  mounted() {
    this.init()
  },
  methods: {
    async init() {
      try {
        const { data } = await axios.post('/recipienter/get_customer_edit_init', {
          id: this.$route.query.id
        })
        this.recipienters = data.recipienters
        this.com_name = data.user.com_name
        this.department_name = data.user.department_name
        this.name = data.user.name
        this.telephone = data.user.telephone
        this.email = data.user.email
        this.recipienter_id = data.user.recipienter_id
      } catch (error) {
      }
    },
    async inviteUser() {
      // validation
      if (!this.com_name || !this.name || !this.telephone || !this.email) {
        return
      }

      try {
        const { data } = await axios.post('/recipienter/update_user', {
          com_name: this.com_name,
          department_name: this.department_name,
          name: this.name,
          telephone: this.telephone,
          email: this.email,
          recipienter_id: this.recipienter_id,
          id: this.$route.query.id
        })
        this.$swal('', '更新しました')
        this.$router.push({ name: 'recipient.customer' })
      } catch (error) {
      }
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

          input, select {
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
</style>