<template>
  <div class="order__detail_container">
    <div class="d-flex heading">
      <h2 class="page__title1" v-if="!!order_form">注文フォーム名　：{{ order_form.name }}</h2>
    </div>

    <!-- <div class="search__form">
      <div class="d-flex mb-14">
        <div class="form__row d-flex">
          <input type="text" placeholder="キーワード" />
        </div>
        <div class="d-flex mark">
          <label>マーク：</label>
          <p>
            <select>
              <option>すべて</option>
              <option>すべて</option>
              <option>すべて</option>
            </select>
          </p>
        </div>
        <div class="form__action">
          <button>検索</button>
        </div>
      </div>
    </div> -->

    <div class="detail__part">
      <div class="detail__table" v-if="isTableShow">
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
              <span>{{ getValue('price', props.row.id) | moneyFormat }}円</span>
            </div>
            <div v-else-if="props.column.field == 'tax'">
              <span>{{ props.row.tax == 2 ? '対象' : '' }}</span>
            </div>
            <div v-else-if="props.column.field == 'amount'">
              <span><input type="number" :min="0" @change="changedOrderAmount($event, props.index, props.row.id)" :value="props.row.ordered_amount" :max="parseInt(getValue('amount', props.row.id))" /></span>
            </div>
            <div v-else-if="props.column.field == 'total_price'">
              <span>{{ !!props.row.total_price ? props.row.total_price : '' | moneyFormat }}{{ !props.row.total_price ? '' : '円' }}</span>
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
      <div class="total__part">
        <div class="message__part" v-if="!!order_form">
          <h4>メッセージ</h4>
          <p>
            <textarea v-model="message" :disabled="step == 2"></textarea>
          </p>
          <div class="d-flex mb-20 align-center" v-if="order_form.has_deadline == 1">
            <label>希望納品日：</label>
            <p>
              <input type="date" v-model="deadline" :disabled="step == 2" />
            </p>
          </div>
          <div class="d-flex mb-20 align-center" v-if="order_form.has_deadline == 1">
            <label>希望納品時間：</label>
            <p>
              <select v-model="deadline_time" :disabled="step == 2">
                <option>時間指定なし</option>
                <option>午前中</option>
                <option>12時〜14時</option>
                <option>14時〜16時</option>
                <option>16時〜18時</option>
                <option>18時〜20時</option>
              </select>
              <!-- <input type="time" v-model="deadline_time" :disabled="step == 2" /> -->
            </p>
          </div>
          <div class="d-flex mb-20 payment_method">
            <label>支払方法<span>必須</span></label>
            <p v-if="step == 1">
              <label v-if="order_form.payment_method == 1 || order_form.payment_method == 3"><input type="radio" name="payment_method" :checked="payment_method == 1" @click="() => { payment_method = 1 }" />銀行振込</label>
              <label v-if="order_form.payment_method == 2 || order_form.payment_method == 3"><input type="radio" name="payment_method" :checked="payment_method == 2" @click="() => { payment_method = 2 }" />クレジットカード</label>
            </p>
            <p v-if="step == 2">
              {{ payment_method == 1 ? '銀行振込' : 'クレジットカード' }}
            </p>
          </div>
          <div class="card_info" v-show="payment_method == 1">
              ＜振込先＞<br/>
              銀行名：{{ calcBankName() }}<br/>
              支店名：{{ calcBranchName() }}<br/>
              種類　：{{ calcAccountType() }}<br/>
              口座番号：{{ account_number }}<br/>
              口座名義：{{ account_name }}<br/>
          </div>
          <div class="card_info" v-show="payment_method == 2">
            <payjp-checkout
                api-key="pk_live_fb2eaf140089677e891d772a"
                text="ポップアップを開く"
                submit-text="クレジットカードで支払い"
                name-placeholder="田中健太郎"
                v-on:created="onTokenCreated"
                v-on:failed="onTokenFailed">
            </payjp-checkout>
          </div>
        </div>
        <div class="calculator">
          <div class="block" v-if="tax_type == 1">
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
          <div class="block" v-if="tax_type == 2">
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
          <div class="block" v-if="tax_type == 3">
            <div class="calc_row">
              <label>合計</label>
              <p>￥{{ calcTotalPrice() }}</p>
            </div>
          </div>
          <div class="calc__action">
            <button @click="next" class="on">{{ step == 1 ? '確認画面' : '発注' }}</button>
          </div>
          <div class="calc__action">
            <button @click="back" class="back">戻る</button>
          </div>
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
  layout: 'client',
  middleware: 'auth',
  components: {
    ConfirmModal
  },
  data() {
    return {
      columns: [],
      rows: [],
      order_form: null,
      order: null,
      message: "",
      deadline: null,
      deadline_time: null,
      payment_method: 1,

      card_token: null,
      bank_code: null,
      branch_code: null,
      account_type: null,
      account_number: null,
      account_name: null,
      bankList: [],
      branchList: [],
      isShowModal: false,
      tax: 1,
      tax_type: 1,
      step: 1,
      isTableShow: false,
      recipienter: null
    }
  },
  async mounted() {
    await this.loadBankList()
    this.init()
  },
  methods: {
    payPrice() {
      if (this.tax_type == 1) {
        return this.calcTotalPrice() + this.calcTaxFee8P() + this.calcTaxFee10P()
      } else if (this.tax_type == 2) {
        return this.calcPrice8P() + this.calcPrice10P()
      } else {
        return this.calcTotalPrice()
      }
    },
    next() {
      if (this.step == 1) {
        if (this.order_form.has_deadline == 1) {
          if (!this.deadline || !this.deadline_time) {
            this.$swal('', '希望納品日と希望納品時間を入力して下さい')
            return
          }
        }

        if (this.payment_method == 2 && !this.card_token) {
          this.$swal('', 'クレジットカードの場合カード情報を入力してください')
          return
        }

        if (this.rows.filter(item => {
          return item.ordered_amount > 0
        }).length == 0) {
          this.$swal('', '購入する商品の個数を設定してください')
          return
        }

        this.step = 2
      } else {
        this.sendOrderProc()
      }
    },
    async sendOrderProc() {
      let total_price = 0
      if (this.tax_type == 1) {
        total_price = this.calcTotalPrice() + this.calcTaxFee8P() + this.calcTaxFee10P()
      } else if (this.tax_type == 2) {
        total_price = this.calcPrice8P() + this.calcPrice10P()
      } else {
        total_price = this.calcTotalPrice()
      }
      try {
        if (this.payment_method == 2) {
          const { data } = await axios.post('/api/provisional_payment', {
            token: this.card_token,
            id: this.$route.query.id,
            price: total_price
          })
          this.order = data.order
          this.sendOrderProcConfirm()
        } else {
          this.sendOrderProcConfirm()
        }
      } catch (error) {
      }
    },
    async sendOrderProcConfirm() {
      let total_price = 0
      if (this.tax_type == 1) {
        total_price = this.calcTotalPrice() + this.calcTaxFee8P() + this.calcTaxFee10P()
      } else if (this.tax_type == 2) {
        total_price = this.calcPrice8P() + this.calcPrice10P()
      } else {
        total_price = this.calcTotalPrice()
      }
      try {
        const { data } = await axios.post('/api/send_order_proc', {
          id: this.$route.query.id,
          message: this.message,
          deadline: this.deadline,
          deadline_time: this.deadline_time,
          order_id: !!this.order ? this.order.id : null,
          tax: this.tax,
          tax_type: this.tax_type,
          price: total_price
        })
        this.saveOrderProduct(data.order)
      } catch(error) {
      }
    },
    async saveOrderProduct(order) {
      await Promise.all(this.rows.map(async product => {
        const { data } = await axios.post('/api/save_order_product', {
          template_id: order.id,
          product_id: product.id,
          price: this.getValue('price', product.id),
          amount: product.ordered_amount,
          tax: product.tax,
          tax_type: product.tax_type,
          recipienter_id: this.order_form.recipienter_id
        })
      }))
      this.$swal('', '発注しました。')
      this.$router.push({ name: 'client.history' })
    },
    back() {
      if (this.step == 2) {
        this.step = 1
      } else {
        this.$router.back()
      }
    },
    cancelBankPayment() {
      this.isShowModal = false
    },
    changedOrderAmount(event, index, id) {
      let temp = this.rows.slice()
      temp = temp.map((item, idex) => {
        if (index == idex) {
          if (event.target.value > parseInt(this.getValue('amount', id))) {
            return item
          } else {
            return {
              ...item,
              ordered_amount: parseInt(event.target.value),
              total_price: parseInt(event.target.value) * parseInt(this.getValue('price', id))
            }
          }
        } else {
          return item
        }
      })
      this.rows = temp
    },
    async okBankPayment() {
      this.isShowModal = false
      try {
        const { data } = await axios.post('/api/bank_payment_proc', {
          id: this.$route.query.id
        })
        this.order_form = data.order_form
      } catch (error) {
      }
    },
    async loadBankList() {
      this.bankList = Object.values(zenginCode)
    },
    calcBankName() {
      var temp = this.bankList.find(item => {
        return item.code == this.bank_code
      })
      
      return !!temp ? temp.name : ""
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
    onTokenCreated(params) {
      if (!this.recipienter.tenant_id) {
        this.$swal('', '受注者のPayjpアカウントに問題があります')
        return 
      }
      this.card_token = params.id
      // this.provisionalPayment()
    },
    async provisionalPayment() {
      try {
        const { data } = await axios.post('/api/provisional_payment', {
          token: this.card_token,
          id: this.$route.query.id,
          price: this.payPrice()
        })
        this.order = data.order
      } catch (error) {
      }
    },
    calcTotalPrice() {
      var temp = 0
      this.rows.map(item => {
        temp += item.total_price
      })
      return parseInt(temp)
    },
    calcTaxFee8P() {
      var temp = 0
      this.rows.forEach(item => {
        if (item.tax == 2) {
          temp += item.total_price * 0.08
        }
      })
      return parseInt(temp)
    },
    calcTaxFee10P() {
      var temp = 0
      this.rows.forEach(item => {
        if (item.tax == 1) {
          temp += item.total_price * 0.1
        }
      })
      return parseInt(temp)
    },
    calcPrice8P() {
      var temp = 0
      this.rows.forEach(item => {
        if (item.tax == 2) {
          temp += item.total_price
        }
      })
      return parseInt(temp)
    },
    calcPrice10P() {
      var temp = 0
      this.rows.forEach(item => {
        if (item.tax == 1) {
          temp += item.total_price
        }
      })
      return parseInt(temp)
    },
    onTokenFailed() {
      this.$swal('', 'カード情報に問題があります')
    },
    async init() {
      this.isTableShow = false
      try {
        const { data } = await axios.post('/api/get_order_form_detail', {
          id: this.$route.query.id
        })
        this.tax_type = data.tax_type
        this.bank_code = data.recipienter.bank_code
        this.branch_code = data.recipienter.branch_code
        this.account_type = data.recipienter.account_type
        this.account_number = data.recipienter.account_number
        this.account_name = data.recipienter.account_name
        this.recipienter = data.recipienter
        this.order_form = data.order_form
        let temp2 = data.userColumns.find(item => {
          return item.nickname == '発注単価（税抜）'
        })
        if (!!temp2) {
          this.tax = temp2.tax
        }
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
            return {
              ...item,
            }
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
        this.rows = data.products.filter(item => {
          return item.is_public == 1
        }).map(item => {
          return {
            ...item,
            ordered_amount: 0,
            total_price: 0
          }
        })
        this.isTableShow = true
      } catch (error) {
      }
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
      return !!temp1 && temp1 != 'null' ? temp1 : ''
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
    bankPaymentFinish() {
      this.isShowModal = true
    }
  }
}
</script>
<style lang="scss" scoped>
.align-center {
  align-items: center;
}
.mb-20 {
  margin-bottom: 20px;
}
.order__detail_container {
  padding: 20px 30px;

  .heading {
    width: 910px;
    align-items: center;
  }

  .download_btn {
    border-radius: 5px;
    background-color: #fff;
    padding-left: 6px;
    padding-right: 6px;
    height: 25px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    margin-left: auto;
    margin-right: 20px;
    color: var(--text-color);
    border: 1px solid var(--border-color);

    img {
      margin-right: 6px;
    }
  }

  .status__part {
    padding-left: 50px;
    display: flex;
    padding-top: 20px;

    .panel {
      flex: 1;
      border: 1px solid var(--border-color);
      padding: 12px 16px;
      border-radius: 5px;
      background-color: #fff;
      &:first-of-type {
        margin-right: 30px;
      }

      .sub_title {
        margin-left: 30px;
        font-size: 14px;
      }

      .panel__info {
        margin-top: 12px;
        width: 100%;
        .panel__row {
          width: 100%;
          display: flex;
          align-items: center;
          margin-bottom: 4px;
          label {
            font-size: 12px;
            margin-right: 10px;
          }

          p {
            flex: 1;
          }
        }

        .contactClient {
          display: flex;
          align-items: center;
          font-size: 12px;
          color: #337DFD;
          margin-left: auto;
          width: fit-content;

          img {
            margin-right: 5px;
            font-size: 12px;
            color: 15px;
            margin-right: 5px;
          }
        }
      }
    }
  }

  .detail__part {
    .detail__table {
      padding-top: 25px;

      .remark input {
        height: 30px;
        border: 1px solid var(--border-color);
      }
    }

    .total__part {
      display: flex;

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
        }
      }
    }
  }
}

.search__form {
  width: 650px;
  border: 1px solid var(--border-color);
  padding: 8px 10px;
  background-color: #fff;
  border-radius: 5px;
  margin-bottom: 30px;
  margin-top: 20px;

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
    margin-right: auto;
    width: fit-content;

    button {
      width: 120px;
      height: 30px;
      background-color: var(--accent-color2);
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

  .mark {
    align-items: center;
    label {
      width: 80px;
    }

    p {
      select {
        width: 120px;
        height: 30px;
        border: 1px solid var(--border-color);
        padding-left: 6px;
        padding-right: 6px;
        font-size: 14px;
      }
    }
  }
}

.bank_payment_finish_btn {
  width: 180px;
  height: 35px;
  background-color: #D6A41A;
  color: #fff;
  display: flex;
  align-items: center;
  justify-content: center;
  border: 0;
  margin-top: 20px;
  margin-left: auto;
  margin-right: auto;
}

.img {
  display: flex;
  width: 100px;

  img {
    width: 100%;
  }
}
</style>