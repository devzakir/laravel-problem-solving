<template>
  <div class="credit_container">
    <h2 class="page__title">クレジットカード</h2>
    
    <div class="main__part">
      <h3>売上詳細</h3>

      <div class="bank___info" v-if="!!payment_detail">
        <div class="info___row">
          <label>課金ID</label>
          <p class="value">{{ payment_detail.id }}</p>
        </div>
        <div class="info___row">
          <label>支払情報</label>
          <p class="value">{{ payment_detail.refunded ? '返金済み' : (payment_detail.paid ? '支払済み' : '') }}</p>
        </div>
        <div class="info___row">
          <label>カード手数料率</label>
          <p class="value">{{ payment_detail.fee_rate }}%</p>
        </div>
        <div class="info___row">
          <label>支払い額</label>
          <p class="value">{{ payment_detail.amount }}</p>
        </div>
        <div class="info___row">
          <label>支払日時</label>
          <p class="value">{{ formatDate(payment_detail.captured_at*1000) }}</p>
        </div>
        <div class="info___row">
          <label>プラットフォーム利用料率</label>
          <p class="value">{{ master.platform_fee }}%</p>
        </div>
        <div class="info___row">
          <label>プラットフォーム利用料金額</label>
          <p class="value">{{ parseInt(payment_detail.amount * master.platform_fee / 100) | moneyFormat }}</p>
        </div>
        <div class="info___row">
          <label>決済カードブランド</label>
          <p class="value">{{ payment_detail.card }}</p>
        </div>
      </div>

      <div class="action" v-if="!payment_detail.refunded">
        <button @click="() => { isShowModal = true }">返金手続き</button>
      </div>
    </div>
    <RefundModal :charge_id="$route.query.id" @refunded="refunded" @closeModal="() => { isShowModal = false }" v-if="isShowModal" />
  </div>
</template>
<script>
import RefundModal from '../../../components/recipient/RefundModal.vue'
import moment from 'moment'
export default {
  layout: 'recipient',
  components: {
    RefundModal
  },
  data() {
    return {
      payment_detail: null,
      master: null,
      isShowModal: false
    }
  },
  mounted() {
    this.init()
  },
  methods: {
    formatDate(timestamp) {
      return moment(timestamp).format('YYYY/MM/DD')
    },
    async init() {
      const { data } = await axios.post('/recipienter/get_payment_detail', {
        id: this.$route.query.id
      })
      this.payment_detail = data.payment_detail[0]
      this.master = data.master
    },
    refunded() {
      this.isShowModal = false
      this.init()
    },
    clickPage(params) {
      this.rows = this.tempRows.filter((item, index) => {
        return index >= (params - 1) * 10 && index < params * 10
      })
    },
    setPagination() {
      if (this.tempRows.length % 10 > 0) {
        this.pageCount = parseInt(this.tempRows.length / 10) + 1
      } else {
        this.pageCount = parseInt(this.tempRows.length / 10)
      }
    },
  }
}
</script>
<style lang="scss" scoped>
.credit_container {
  padding: 20px;

  .main__part {
    margin-top: 20px;
    width: 850px;
    border: 1px solid var(--border-color);
    padding: 14px 20px;

    h3 {
      font-size: 14px;
      font-weight: normal;
      border-left: 10px solid var(--main-color2);
      padding-left: 6px;
      margin-bottom: 26px;
    }
  }
}

.search__form {
  width: 700px;
  border: 1px solid var(--border-color);
  padding: 8px 10px;
  background-color: #fff;
  border-radius: 5px;

  .form__row {
    margin-right: 15px;
    width: 200px;

    &.mr-0 {
      margin-right: 0;
    }

    input, select {
      height: 30px;
      flex: 1;
      border: 1px solid var(--border-color);
      padding-left: 6px;
      padding-right: 6px;
      font-size: 14px;
    }
  }

  .deparate {
    font-size: 16px;
    width: 20px;
    text-align: center;
  }

  .form__action {
    margin-left: auto;
    margin-right: 85px;
    width: fit-content;

    button {
      width: 120px;
      height: 30px;
      background-color: var(--accent-color);
      color: #fff;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 12px;
      border: 0;
      font-weight: bold;
      cursor: pointer;
    }
  }
}

.table__container {
  margin-top: 20px;
}

.bank___info {
  margin-top: 10px;

  .info___row {
    display: flex;
    align-items: center;

    label {
      font-size: 14px;
      border: 1px solid #ccc;
      width: 200px;
      height: 35px;
      display: flex;
      align-items: center;
      padding-left: 10px;
    }

    .value {
      border: 1px solid #ccc;
      flex: 1;
      height: 35px;
      display: flex;
      align-items: center;
      padding-left: 10px;
    }
  }
}

.action {
  padding-top: 20px;

  button {
    width: 180px;
    height: 35px;
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: var(--accent-color);
    color: #fff;
    font-size: 14px;
    border: 0;
  }
}
</style>