<template>
  <div class="create__container">
    <h2 class="page__title">受注登録</h2>
    <div class="main__part">
      <div class="flow__part">
        <p class="step__item">
          <span class="circle"></span>
          <span class="step_text">取引先</span>
        </p>
        <p class="step__item">
          <span class="circle"></span>
          <span class="step_text">注文フォーム選択</span>
        </p>
        <p class="step__item">
          <span class="circle" :class="{ active: step == 1 }"></span>
          <span class="step_text">注文情報</span>
        </p>
        <p class="step__item">
          <span class="circle" :class="{ active: step == 2 }"></span>
          <span class="step_text">確認</span>
        </p>
        <p class="step__item">
          <span class="circle"></span>
          <span class="step_text">完了</span>
        </p>
      </div>

      <div class="main__inner_part">
        <div class="left_part">
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
                  <span><input type="number" :min="0" @change="changedOrderAmount($event, props.index, props.row.id)" :value="props.row.ordered_amount" :max="parseInt(getValue('amount', props.row.id))" :disabled="step == 2" /></span>
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
        </div>
        <div class="right_part" v-if="!!order_form">
          <h4 class="page__title">受注登録</h4>
          <div class="subtotal">
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
          </div>
          <h4 class="page__title">メッセージ・希望納品日</h4>
          <div class="form__part">
            <div class="form__row flex_start">
              <label>メッセージ</label>
              <p>
                <textarea v-model="message" :disabled="step == 2"></textarea>
              </p>
            </div>
            <div class="form__row" v-if="order_form.has_deadline == 1">
              <label>希望納品日：</label>
              <p>
                <input type="date" v-model="deadline" :disabled="step == 2" />
              </p>
            </div>
            <div class="form__row" v-if="order_form.has_deadline == 1">
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
              </p>
            </div>
            <div class="form__row">
              <label>支払方法　　：</label>
              <p>
                銀行振込<br/><span>※クレジットカード決済の場合は注文フォームの案内をお願い致します。</span>
              </p>
            </div>
          </div>
          <div class="actions">
            <button @click="back">戻る</button>
            <button @click="next">{{ step == 1 ? '確認画面へ' : '登録' }}</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
var zenginCode = require('zengin-code')
export default {
  layout: 'recipient',
  middleware: 'recipient',
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
      order_form_id: null
    }
  },
  mounted() {
    this.loadBankList()
    this.init()
  },
  methods: {
    next() {
      if (this.step == 1) {
        if (this.order_form.has_deadline == 1) {
          if (!this.deadline || !this.deadline_time) {
            this.$swal('', '希望納品日と希望納品時間を入力して下さい')
            return
          }
        }

        if (this.payment_method == 2 && !this.order) {
          this.$swal('', 'クレジットカードの場合仮払いお願いします。')
          return
        }

        if (this.rows.filter(item => {
          return item.ordered_amount > 0
        }).length == 0) {
          this.$swal('', '購入する商品の個数を設定してください')
          return
        }

        if (this.payment_method == 2 && this.order.payed == 0) {
          this.$swal('', 'クレジットカードの場合仮払いお願いします。')
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
      const user_id = parseInt(localStorage.getItem('id'))
      try {
        const { data } = await axios.post('/recipienter/send_order_proc', {
          id: this.order_form_id,
          message: this.message,
          deadline: this.deadline,
          deadline_time: this.deadline_time,
          order_id: !!this.order ? this.order.id : null,
          tax: this.tax,
          tax_type: this.tax_type,
          price: total_price,
          user_id: user_id
        })
        this.saveOrderProduct(data.order)
      } catch (error) {
      }
    },
    async saveOrderProduct(order) {
      await Promise.all(this.rows.map(async product => {
        const { data } = await axios.post('/recipienter/save_order_product', {
          template_id: order.id,
          product_id: product.id,
          price: this.getValue('price', product.id),
          amount: product.ordered_amount,
          tax: product.tax,
          tax_type: product.tax_type,
          recipienter_id: this.order_form.recipienter_id
        })
      }))
      this.$router.push({ name: 'recipient.order1' })
    },
    async init() {
        const order_form_id = parseInt(localStorage.getItem('order_form'))
        this.order_form_id = order_form_id
        const { data } = await axios.post('/recipienter/get_order_form_detail2', {
          id: order_form_id
        })
        this.tax_type = data.tax_type
        this.bank_code = data.recipienter.bank_code
        this.branch_code = data.recipienter.branch_code
        this.account_type = data.recipienter.account_type
        this.account_number = data.recipienter.account_number
        this.account_name = data.recipienter.account_name
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
        this.rows = data.products.map(item => {
          return {
            ...item,
            ordered_amount: 0,
            total_price: 0
          }
        })
        this.isTableShow = true
    },
    cancelBankPayment() {
      this.isShowModal = false
    },
    changedOrderAmount(event, index, id) {
      let temp = this.rows.slice()
      temp = temp.map((item, idex) => {
        if (index == idex) {
          return {
            ...item,
            ordered_amount: parseInt(event.target.value),
            total_price: parseInt(event.target.value) * parseInt(this.getValue('price', id))
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
          id: this.order_form_id
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
      this.card_token = params.id
      this.provisionalPayment()
    },
    async provisionalPayment() {
      try {
        const { data } = await axios.post('/api/provisional_payment', {
          token: this.card_token,
          id: this.order_form_id
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
    back() {
      this.$router.back()
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
.mb-13 {
  margin-bottom: 13px;
}
.create__container {
  padding: 20px 30px;

  .flow__part {
    margin-left: 235px;
    top: 20px;
    display: flex;
    position: relative;
    width: 450px;
    justify-content: space-between;
    margin-bottom: 60px;

    &:after {
      content: '';
      position: absolute;
      left: 15px;
      width: 420px;
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
          width: 34px;
        }
      }
      &:nth-of-type(2) {
        .step_text {
          width: 86px;
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
      &:nth-of-type(5) {
        .step_text {
          width: 43px;
        }
      }
    }
  }

  .main__inner_part {
    display: flex;
    align-items: flex-start;

    .left_part {
      width: 860px;
      margin-right: 57px;

      .search__form {
        padding: 8px 24px 24px 10px;
        border: 1px solid var(--border-color);
        border-radius: 5px;
        width: fit-content;
        margin-left: 50px;

        .keyword {
          width: 200px;
          height: 30px;
          border: 1px solid var(--border-color);
          padding-left: 6px;
          padding-right: 6px;
          margin-right: 23px;
        }

        .amount_el {
          display: flex;
          align-items: center;

          label {
            font-size: 12px;
            width: 80px;
          }

          input {
            width: 30px;
            height: 30px;
            border: 1px solid var(--border-color);
          }

          span {
            width: 20px;
            text-align: center;
            margin-left: 7px;
            margin-right: 7px;
          }
        }

        .mark {
          align-items: center;

          label {
            width: 80px;
            font-size: 12px;
          }

          select {
            width: 120px;
            height: 30px;
            border: 1px solid var(--border-color);
            padding-left: 6px;
            padding-right: 6px;
          }
        }

        .search_btn {
          margin-left: auto;
          width: 120px;
          height: 30px;
          background-color: var(--accent-color);
          color: #fff;
          font-size: 12px;
          display: flex;
          align-items: center;
          justify-content: center;
          border: 0;
        }
      }
    }

    .right_part {
      width: 500px;
      border: 1px solid var(--border-color);
      padding: 40px 30px 20px 20px;
      box-shadow: 0 3px 6px rgba($color: #000000, $alpha: 0.16);
      border-radius: 5px;

      .subtotal {
        margin-top: 10px;
        display: flex;
        width: 100%;
        align-items: center;
        padding-left: 15px;
        padding-right: 15px;
        margin-bottom: 30px;

        .block {
          width: 100%;
        }

        .calc_row {
          display: flex;
          align-items: center;
          width: 100%;

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
      }

      .form__part {
        margin-top: 30px;

        .form__row {
          display: flex;
          align-items: center;
          margin-bottom: 16px;

          &.flex_start {
            align-items: flex-start;
          }

          label {
            font-size: 14px;
            width: 167px;
          }

          p {
            flex: 1;
            textarea {
              width: 100%;
              height: 145px;
              border: 1px solid var(--border-color);
              padding: 6px;
              border-radius: 5px;
            }

            input {
              width: 150px;
              height: 30px;
              padding-left: 6px;
              padding-right: 6px;
              border: 1px solid var(--border-color);
              border-radius: 5px;
            }

            span {
              color: #CD3C4B;
              font-size: 8px;
            }
          }
        }
      }

      .actions {
        display: flex;
        align-items: center;
        justify-content: center;
        padding-top: 70px;

        button {
          width: 180px;
          height: 35px;
          display: flex;
          align-items: center;
          justify-content: center;
          font-size: 14px;
          box-shadow: 0 3px 6px rgba($color: #000000, $alpha: 0.16);

          &:first-of-type {
            background-color: #fff;
            border: 1px solid var(--border-color);
            color: var(--text-color);
            margin-right: 54px;
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
}

.detail__table {
  padding-left: 50px;
  padding-top: 25px;

  .remark input {
    height: 30px;
    border: 1px solid var(--border-color);
  }
  
  .amount input {
    width: 100px;
    height: 30px;
    border: 1px solid var(--border-color);
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