<template>
  <div class="account_create">
    <div class="inner">
      <h2 class="page__title">銀行口座情報</h2>
      <div class="account_form" v-if="isShowForm">
        <div class="form__row">
          <label>金融機関名<span>必須</span></label>
          <p>
            <!-- <select v-model="bank_code">
              <option v-for="(item, index) in bankList" :key="index" :value="item.code">
                {{ item.name }}
              </option>
            </select> -->
            <autocomplete :search="searchBank" :get-result-value="getResultValueWithBank" @submit="onSubmit1" :default-value="defaultBankName()" :debounce-time="1200"></autocomplete>
          </p>
        </div>
        <div class="form__row">
          <label>支店名<span>必須</span></label>
          <p>
            <!-- <select v-model="branch_code">
              <option v-for="(item, index) in calcBranchList()" :key="index" :value="item.code">
                {{ item.name }}
              </option>
            </select> -->
            <autocomplete :search="search" :get-result-value="getResultValue" @submit="onSubmit" :default-value="defaultBranchName()" :debounce-time="1200"></autocomplete>
          </p>
        </div>
        <div class="form__row">
          <label>口座種類<span>必須</span></label>
          <p>
            <select v-model="account_type">
              <option :value="1">普通預金</option>
              <option :value="2">当座預金</option>
              <option :value="3">貯蓄預金</option>
            </select>
          </p>
        </div>
        <div class="form__row">
          <label>口座番号<span>必須</span></label>
          <p>
            <input v-model="account_number" type="text" />
          </p>
        </div>
        <div class="form__row">
          <label>口座名義<span>必須</span></label>
          <p>
            <input v-model="account_name" type="text" />
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
import { PREFECTURES } from "../../const"
var zenginCode = require('zengin-code')
export default {
  data() {
    return {
      name: "",
      PREFECTURES: PREFECTURES,
      zipcode: null,
      prefecture: null,
      city: null,
      address: null,
      phone: null,
      bankList: [],
      branchList: [],
      bank_code: null,
      branch_code: null,
      account_type: null,
      account_number: null,
      account_name: null,
      isShowForm: false
    }
  },
  async mounted() {
    await this.loadBankList()
    this.bank_code = this.$store.getters['recipienter_auth/user'].bank_code
    this.branch_code = this.$store.getters['recipienter_auth/user'].branch_code
    this.account_type = this.$store.getters['recipienter_auth/user'].account_type
    this.account_number = this.$store.getters['recipienter_auth/user'].account_number
    this.account_name = this.$store.getters['recipienter_auth/user'].account_name
    this.isShowForm = true
  },
  methods: {
    async loadBankList() {
      this.bankList = Object.values(zenginCode)
      // this.bankList = this.bankList.filter(item => {
      //   return (item.code >= '0001' && item.code < '0043') || item.name == 'ゆうちょ'
      // })
    },

    onSubmit(result) {
      if (!!result) {
        this.branch_code = result.code
      }
    },

    onSubmit1(result) {
      if (!!result) {
        this.bank_code = result.code
      }
    },

    getResultValue(result) {
      console.log(result, "____result")
      return result.name
    },

    defaultBankName() {
      if (!!this.bank_code) {
        var temp = this.bankList.find(item => {
          return parseInt(item.code) == parseInt(this.bank_code)
        })
        return temp.name
      } else {
        return ''
      }
    },

    defaultBranchName() {
      if (!!this.bank_code) {
        var temp = this.bankList.find(item => {
          return parseInt(item.code) == parseInt(this.bank_code)
        })
        if (!!this.branch_code) {
          var temp1 = Object.values(temp.branches).find(item => {
            return parseInt(item.code) == parseInt(this.branch_code)
          })

          if (!!temp1) {
            return temp1.name
          } else {
            return ''
          }
        } else {
          return ''
        }
      } else {
        return ''
      }
    },

    getResultValueWithBank(result) {
      return result.name 
    },

    calcBranchList() {
      if (!!this.bank_code) {
        var temp = this.bankList.find(item => {
          return item.code == this.bank_code
        })

        return Object.values(temp.branches)
      } else {
        return []
      }
    },
    
    closeModal() {
      this.$emit('closeModal')
    },

    search(input) {
      if (input.length < 1) { return [] }
      if (!!this.bank_code) {
        var temp = this.bankList.find(item => {
          return item.code == this.bank_code
        })
        var temp1 = Object.values(temp.branches).filter(item => {
          return item.name.includes(input)
        })
        return temp1
      } else {
        return []
      }
    },

    searchBank(input) {
      if (input.length < 1) { return [] }
      var temp1 = this.bankList.filter(item => {
        return item.name.includes(input)
      })
      return temp1
    },

    async save() {
      try {
        const { data } = await axios.post('/recipienter/edit_account_bank_info', {
          bank_code: this.bank_code,
          branch_code: this.branch_code,
          account_type: this.account_type,
          account_number: this.account_number,
          account_name: this.account_name
        })
        this.$swal('', '更新しました')
        this.$store.dispatch('recipienter_auth/fetchUser')
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