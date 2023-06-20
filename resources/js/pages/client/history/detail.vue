<template>
  <div class="history__detail">
    <h2 class="page__title1">発注履歴</h2>

    <div class="recipienter_info" v-if="!!order_form">
      <h2 class="page__title1">発注先情報<span>株式会社{{ order_form.order_form.name }}</span></h2>
      <div class="info">
        <p>注文番号　：{{ order_form.hash }}</p>
        <p>注文日時　：{{ order_form.updated_at | dateFormatFull }}</p>
      </div>
    </div>

    <div class="table__part">
      <vue-good-table
        :columns="columns"
        :rows="rows"
        :pagination-options="{
          enabled: false,
        }">
        <template slot="table-row" slot-scope="props">
          <div v-if="props.column.field == 'mark'">
            <span class="mark">{{ props.row.mark }}</span>
          </div>
          <div class="toggle" v-else-if="props.column.field == 'is_public'">
            <toggle-button :width="88" :value="props.row.is_public == 0 ? false : true" :sync="true" :disabled="true" :color="{unchecked: '#707070', checked: '#337DFD'}" :labels="{unchecked: '非公開', checked: '公開'}"/>
          </div>
          <div v-else-if="props.column.field == 'name'">
            <span>{{ getValue('name', props.row.id) }}</span>
          </div>
          <div v-else-if="props.column.field == 'price'">
            <span>{{ props.row.price | moneyFormat }}円</span>
          </div>
          <div v-else-if="props.column.field == 'tax'">
            <span>{{ props.row.tax == 2 ? '対象' : '' }}</span>
          </div>
          <div v-else-if="props.column.field == 'amount'">
            <span>{{ props.row.amount | moneyFormat }}</span>
          </div>
          <div v-else-if="props.column.field == 'total_price'">
            <span>{{ (props.row.price * props.row.amount) | moneyFormat }}円</span>
          </div>
          <div v-else-if="props.column.field == 'remark'">
            <span>{{ getValue('remark', props.row.id) }}</span>
          </div>
          <div v-else-if="props.column.field == 'maker'">
            <span>{{ getValue('maker', props.row.id) }}</span>
          </div>
          <div v-else-if="props.column.field == 'color'">
            <span>{{ getValue('color', props.row.id) }}</span>
          </div>
          <div v-else-if="props.column.field == 'size'">
            <span>{{ getValue('size', props.row.id) }}</span>
          </div>
          <div v-else-if="props.column.field == 'unit'">
            <span>{{ getValue('unit', props.row.id) }}</span>
          </div>
          <div v-else-if="props.column.field == 'image'">
            <span class="img"><img :src="getImageUrl(props.row.id)" /></span>
          </div>
          <div v-else-if="props.column.field == 'jan'">
            <span>{{ getValue('jan', props.row.id) }}</span>
          </div>
          <div v-else-if="props.column.field == 'order'">
            <span>{{ getValue('order', props.row.id) }}</span>
          </div>
          <div v-else-if="props.column.field == 'free_space1'">
            <span>{{ getValue('free_space1', props.row.id) }}</span>
          </div>
          <div v-else-if="props.column.field == 'free_space2'">
            <span>{{ getValue('free_space2', props.row.id) }}</span>
          </div>
          <div v-else-if="props.column.field == 'free_space3'">
            <span>{{ getValue('free_space3', props.row.id) }}</span>
          </div>
          <div v-else-if="props.column.field == 'delete'">
            <a @click="cancelOrderedProduct(props.row.id)" class="delete_btn"><img src="/asset/icons/delete.svg" /></a>
          </div>
          <div class="table_cell" v-else>
            {{ props.formattedRow[props.column.field] }}
          </div>
        </template>
      </vue-good-table>
    </div>
    <div class="total__part" v-if="!!order_form">
        <div class="message__part">
          <h4 class="mb-20">メッセージ</h4>
          <p class="mb-20">
            {{ order_form.message }}
          </p>
          <div class="d-flex mb-20 align-center">
            <label>希望納品時間：</label>
            <p>
              {{ order_form.deadline | dateFormat }}
            </p>
          </div>
          <div class="d-flex mb-20 align-center">
            <label>希望納品時間：</label>
            <p>
              {{ order_form.deadline_time }}
            </p>
          </div>
          <div class="d-flex mb-20 payment_method">
            <label>支払方法</label>
            <p>
              {{ order_form.payment_method == 1 ? '銀行振込' : 'クレジットカード' }}
            </p>
          </div>
          <p>
            ＜振込先＞<br/>
            銀行名：{{ calcBankName() }}<br/>
            支店名：{{ calcBranchName() }}<br/>
            種類　：{{ calcAccountType() }}<br/>
            口座番号：{{ account_number }}<br/>
            口座名義：{{ account_name }}<br/>
          </p>
        </div>
        <div class="calculator">
          <div class="block" v-if="order_form.tax_type == 1">
            <div class="calc_row">
              <label>小計（税抜）</label>
              <p>￥{{ calcTotalPrice() }}</p>
            </div>
            <div class="calc_row">
              <label>消費税（8％）</label>
              <p>￥{{ calcTaxFee8P() }}</p>
            </div>
            <div class="calc_row">
              <label>消費税（10％）</label>
              <p>￥{{ calcTaxFee10P() }}</p>
            </div>
            <div class="calc_row">
              <label>合計</label>
              <p>￥{{ calcTotalPrice() + calcTaxFee8P() + calcTaxFee10P() }}</p>
            </div>
          </div>
          <div class="block" v-if="order_form.tax_type == 2">
            <div class="calc_row">
              <label>小計（8％）</label>
              <p>￥{{ calcPrice8P() }}</p>
            </div>
            <div class="calc_row">
              <label>小計（10％）</label>
              <p>￥{{ calcPrice10P() }}</p>
            </div>
            <div class="calc_row">
              <label>合計(税込)</label>
              <p>￥{{ calcPrice8P() + calcPrice10P() }}</p>
            </div>
          </div>
          <div class="block" v-if="order_form.tax_type == 3">
            <div class="calc_row">
              <label>合計</label>
              <p>￥{{ calcTotalPrice() }}</p>
            </div>
          </div>
          <div class="calc__action" v-if="order_form.invoiced == 1 && order_form.payed == 0">
            <button @click="bankPaymentFinish" class="on">支払完了連絡</button>
          </div>
          <div class="calc__action black">
            <button @click="reOrder"><img src="/asset/icons/output2.svg" />再発注</button>
          </div>
          <div class="calc__action">
            <button class="back" @click="back">戻る</button>
          </div>
        </div>
      </div>
      <ConfirmModal 
      v-if="isShowModal"
      :note="'支払完了連絡を完了しますか？'" 
      :cancelText="'キャンセル'" 
      :okText="'支払完了'"
      @cancel="cancelBankPayment"
      @ok="okBankPayment" />
  </div>
</template>
<script>
var zenginCode = require('zengin-code')
import ConfirmModal from '../../../components/common/ConfirmModal.vue'
export default {
  components: {
    ConfirmModal
  },
  layout: 'client',
  data() {
    return {
      columns: [],
      rows: [],
      order_form: null,
      message: "",
      deadline: null,
      deadline_time: null,
      payment_method: null,

      card_token: null,
      bank_code: null,
      branch_code: null,
      account_type: null,
      account_number: null,
      account_name: null,
      bankList: [],
      branchList: [],
      isShowModal: false,

      step: 1,
      tax_type: 1
    }
  },
  mounted() {
    this.loadBankList()
    this.init()
  },
  methods: {
    back() {
      this.$router.back()
    },
    async loadBankList() {
      this.bankList = Object.values(zenginCode)
    },
    reOrder() {
      this.$router.push({ name: 'client.order.detail', query: { id: this.order_form.order_form.id } })
    },
    cancelBankPayment() {
      this.isShowModal = false
    },
    async okBankPayment() {
      this.isShowModal = false
      try {
        const { data } = await axios.post('/api/bank_payment_proc', {
          id: this.$route.query.id
        })
        this.$swal('', '支払完了連絡を送りました。')
        this.order_form = data.order_form
      } catch (error) {
      }
    },
    bankPaymentFinish() {
      this.isShowModal = true
    },
    getValue(slug, id) {
      let temp = this.rows.find(item => {
        return item.id == id
      })  
      let temp1 = null
      temp.product_values.forEach(item => {
        if (item.product_user_column.product_column.slug == slug) {
          if (item.product_user_column.product_column.type == 'number') {
            temp1 = parseInt(item.value)
          } else {
            temp1 = item.value
          }
        }
      })
      return temp1
    },
    getImageUrl(id) {
      let temp = this.rows.find(item => {
        return item.id == id
      })
      let temp1 = null
      temp.product_values.forEach(item => {
        if (item.product_user_column.product_column.slug == 'image') {
          temp1 = item.value_url
        }
      })

      return !!temp1 ? temp1 : '/asset/no_image.svg'
    },
    calcTotalPrice() {
      var temp = 0
      this.rows.map(item => {
        temp += item.price * item.amount
      })
      return parseInt(temp)
    },
    async init() {
      try {
        const { data } = await axios.post('/api/get_order_detail', {
          id: this.$route.query.id
        })
        this.bank_code = data.recipienter.bank_code
        this.branch_code = data.recipienter.branch_code
        this.account_type = data.recipienter.account_type
        this.account_number = data.recipienter.account_number
        this.account_name = data.recipienter.account_name
        this.order_form = data.order_form
        let picked_columns = data.columns.filter(item => {
          if(data.userColumns.length > 0) {
            let column = data.userColumns.find(it => {
              return it.product_column_id == item.id
            })
            return column.picked == 1
          } else {
            return item.picked == 1
          }
        }).map(item => {
          if(data.userColumns.length > 0) {
            let column = data.userColumns.find(it => {
              return it.product_column_id == item.id
            })
            return {
              ...item,
              order: column.order,
              tax: column.tax,
              tax_type: column.tax_type,
              name: column.nickname,
            }
          } else {
            return item
          }
        })
        picked_columns.sort((a, b) => a.order - b.order)
        picked_columns.forEach(item => {
          if (item.slug != 'total_price') {
            if (item.slug == 'price') {
              this.columns.push({
                label: item.name + (item.tax_type == 1 ? '（税抜）' : ( item.tax_type == 2 ? '（税込）' : '（非課税）' )),
                field: item.slug,
                sortable: item.type == 'number' ? true : false
              })
              if (item.tax_type != 3 && item.tax == 2) {
                this.columns.push({
                  label: '軽減税率',
                  field: 'tax',
                  sortable: false,
                  width: '60px'
                })
              }
            } else {
              this.columns.push({
                label: item.name,
                field: item.slug,
                sortable: item.type == 'number' ? true : false
              })
            }
          }
        })
        this.rows = data.order_form_products.map(item => {
          return {
            ...item.product,
            price: item.price,
            amount: item.amount,
            tax: item.tax,
            tax_type: item.tax_type
          }
        })
      } catch (error) {
      }
    },
    calcBankName() {
      var temp = this.bankList.find(item => {
        return item.code == this.bank_code
      })
      
      return !!temp ? temp.name : ""
    },
    calcTaxFee8P() {
      var temp = 0
      this.rows.forEach(item => {
        if (item.tax == 2) {
          temp += (item.price * item.amount) * 0.08
        }
      })
      return parseInt(temp)
    },
    calcTaxFee10P() {
      var temp = 0
      this.rows.forEach(item => {
        if (item.tax == 1) {
          temp += (item.price * item.amount) * 0.1
        }
      })
      return parseInt(temp)
    },
    calcPrice8P() {
      var temp = 0
      this.rows.forEach(item => {
        if (item.tax == 2) {
          temp += (item.price * item.amount)
        }
      })
      return parseInt(temp)
    },
    calcPrice10P() {
      var temp = 0
      this.rows.forEach(item => {
        if (item.tax == 1) {
          temp += (item.price * item.amount)
        }
      })
      return parseInt(temp)
    },
    calcBranchName() {
      var temp = this.bankList.find(item => {
        return item.code == this.bank_code
      })

      if (!!temp) {
        var branches = Object.values(temp.branches)

        var temp1 = branches.find(item => {
          return item.code == this.branch_code
        })

        return !!temp1 ? temp1.name : ""
      } else {
        return ""
      }
    },
    calcAccountType() {
      let res = ""
      switch (this.account_type) {
        case '1':
          res = '普通預金'
          break;
        case '2':
          res = '当座預金'
          break;
        case '3':
          res = '貯蓄預金'
          break;
      }

      return res
    },
  }
}
</script>
<style lang="scss" scoped>
.mb-20 {
  margin-bottom: 20px;
}
.history__detail {
  padding: 20px 30px;
}

.recipienter_info {
  margin-top: 20px;
  margin-left: 50px;
  width: 430px;
  border: 1px solid var(--border-color);
  padding: 12px 14px;
  border-radius: 4px;

  .page__title1 {
    span {
      margin-left: 40px;
      font-size: 12px;
    }
  }

  .info {
    margin-top: 12px;
    margin-left: 20px;
    font-size: 10px;
  }
}

.table__part {
  margin-top: 34px;
  width: 860px;
  margin-left: 50px;
}

.total__part {
  display: flex;
  margin-left: 50px;

  .message__part {
    width: 500px;
    border: 1px solid var(--border-color);
    padding: 15px;
    background-color: #fff;

    h4 {
      font-size: 14px;
    }

    textarea {
      width: 100%;
      display: block;
      height: 78px;
      border: 1px solid var(--border-color);
      border-radius: 5px;
      padding: 10px;
      margin-top: 4px;
      resize: none;
      margin-bottom: 16px;
    }
  }

  p {
    input, select {
      width: 200px;
      height: 30px;
      border: 1px solid var(--border-color);
      padding-left: 5px;
      padding-right: 5px;
    }
  }

  .payment_method {
    &>label {
      width: 100px;
      display: flex;
      align-items: center;
      span {
        margin-left: auto;
        width: 30px;
        height: 20px;
        background-color: #D34555;
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
      }
    }
    p {
      display: flex;
      align-items: center;
      label {
        display: flex;
        margin-right: 5px;
        font-size: 12px;
        input {
          margin-right: 4px;
          width: 15px;
          height: 15px;
        }
      }
    }
  }

  .calculator {
    margin-bottom: 15px;
    flex: 1;

    .calc_row {
      display: flex;
      align-items: center;

      label {
        flex: 1;
        height: 50px;
        border: 1px solid var(--border-color);
        line-height: 50px;
        padding-left: 10px;
      }

      p {
        text-align: right;
        flex: 1;
        height: 50px;
        border: 1px solid var(--border-color);
        line-height: 50px;
        padding-right: 10px;
      }
    }

    .calc__action {
      width: 100%;
      display: flex;
      justify-content: center;
      padding-top: 15px;

      button {
        width: 180px;
        height: 35px;
        display: flex;
        align-items: center;
        justify-content: center;

        &.on {
          background-color: var(--accent-color2);
          color: #fff;
          border: 0;
        }

        &.back {
          background-color: #fff;
          border: 1px solid var(--border-color);
        }
      }

      &.black {
        margin-top: 50px;

        button {
          background-color: #2A3039;
          color: #fff;
          border: 0;

          img {
            margin-right: 14px;
            width: 20px;
          }
        }
      }
    }
  }
}

.img {
  display: flex;
  width: 100px;

  img {
    width: 100%;
  }
}
</style>