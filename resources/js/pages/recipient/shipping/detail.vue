<template>
  <div class="order__detail_container">
    <div class="d-flex heading">
      <h2 class="page__title">出荷詳細</h2>
    </div>
    <div class="status__part" v-if="!!order_form">
      <div class="panel">
        <h3 class="page__title">出荷情報</h3>
        <div class="panel__info">
          <div class="panel__row">
            <label>注文番号　　：{{ order_form.hash }}</label>
            <p></p>
          </div>
          <div class="panel__row">
            <label>最終更新日時：{{ order_form.updated_at | dateFormatFull }}</label>
            <p></p>
          </div>
          <!-- <div class="panel__row">
            <label>ステータス：</label>
            <p>
              <toggle-button :width="88" :color="{unchecked: '#337DFD', checked: '#707070', disabled: '#909090'}" :labels="{unchecked: '出荷前', checked: '出荷済'}"/>
            </p>
          </div> -->
        </div>
      </div>
      <div class="status__action">
        <button @click="downloadPDF"><img src="/asset/icons/pdf.svg" />明細</button>
      </div>
    </div>

    <div class="client__info" v-if="!!order_form">
      <div class="left">
        <h3 class="page__title">{{ order_form.user.com_name }}</h3>
        <p class="client_saki">{{ order_form.user.name }} <span>様</span></p>
        <p class="content">
          この度はご注文いただきましてありがとうごいます。<br/>
          下記の通り納品させていただきます。
        </p>
        <p class="total_price">
          <span>合計金額</span>
          <span>￥{{ calcTotalPrice() | moneyFormat }}(税込)</span>
        </p>
      </div>
      <div class="right">
        <div class="info__row">
          <label>出荷日：</label>
          <p>{{ order_form.delivery_at | dateFormat }}</p>
        </div>
        <div class="com_name">{{ order_form.recipienter.name || '' }}</div>
        <div class="address">
          住所　：{{ order_form.recipienter.prefecture || '' }}<br/>
          　　　　{{ order_form.recipienter.city || '' }}<br/>
          　　　　{{ order_form.recipienter.address || '' }}
        </div>
        <div class="address">
          担当者：{{ order_form.recipienter.tanto_name || '管理者' }}
        </div>
        <div class="address">
          電話番号: {{ order_form.recipienter.phone || '' }}
        </div>
        <router-link :to="{ name: 'recipient.shipping.message.create', query: { id: order_form.id } }" class="contact_btn">
          <img src="/asset/icons/email.svg" />
          出荷完了メールを送る
        </router-link>
      </div>
    </div>

    <div class="detail__part">
      <div class="detail__table">
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
          <h4>メッセージ</h4>
          <p>
            {{ order_form.message }}
          </p>
          <div class="d-flex mb-20">
            <label>希望納品時間：</label>
            <p>{{ order_form.deadline | dateFormat }}</p>
          </div>
          <div class="d-flex">
            <label>希望納品時間：</label>
            <p>{{ order_form.deadline_time }}</p>
          </div>
          <div class="d-flex">
            <label>支払方法　　：</label>
            <p>{{ order_form.payment_method == 1 ? '銀行振込' : 'クレジットカード' }}</p>
          </div>
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
          <!-- <div class="calc__action">
            <button class="on" @click="sendInvoiceEmail">メールで送信</button>
          </div> -->
          <div class="calc__action">
            <button class="back" @click="back">戻る</button>
          </div>
        </div>
      </div>
    </div>
    <ConfirmModal 
      v-if="isShowModal" 
      :note="'ダウンロードして対応済みにする'" 
      :cancelText="'キャンセル'" 
      :okText="'決定'"
      @cancel="cancelFinishDelivery"
      @ok="okFinishDelivery"/>
    
    <vue-html2pdf
      :show-layout="false"
      :float-layout="true"
      :enable-download="true"
      :preview-modal="false"
      :paginate-elements-by-height="1400"
      filename="納品書"
      :pdf-quality="2"
      :manual-pagination="false"
      pdf-format="a4"
      pdf-orientation="landscape"
      pdf-content-width="1132px"
      @progress="onProgress($event)"
      @hasStartedGeneration="hasStartedGeneration()"
      @hasGenerated="hasGenerated($event)"
      ref="html2Pdf"
    >
      <section slot="pdf-content" v-if="!!order_form">
        <section class="pdf-item">
          <div class="pdf_container">
            <div class="pdf_heading">
              <p class="contact_to">{{ order_form.user.com_name }}<br/>{{ order_form.user.name+ '様' }}</p>
              <div class="page_type">
                <p>納品書</p>
                <p>注文日：{{ order_form.ordered_at | dateFormat }}　注文番号：{{ order_form.hash || '' }}<br/></p>
                <p>出荷日：{{ order_form.delivery_at | dateFormat }}　出荷番号：{{ order_form.delivery_hash || '' }}<br/></p>
              </div>
            </div>
            <div class="order_detail_info">
              <div class="left">
                <div>
                  <p>この度はご注文いただきまして誠にありがとうございます。<br/>下記の通り納品させていただきます。</p>
                  <div class="d-flex">
                    <label>合計金額</label>
                   <p>￥{{ calcTotalPrice() | moneyFormat }}（税込）</p>
                  </div>
                </div>
              </div>
              <div class="right">
                <div class="client_info border">
                  <p>
                    {{ $store.getters['recipienter_auth/user'].name }}<br/>
                    {{ !!$store.getters['recipienter_auth/user'].zipcode ? '〒' + $store.getters['recipienter_auth/user'].zipcode : '' }} {{ $store.getters['recipienter_auth/user'].prefecture || '' }} {{ $store.getters['recipienter_auth/user'].city || '' }} <br/>{{ $store.getters['recipienter_auth/user'].building || '' }}<br/>
                    担当者：{{ $store.getters['recipienter_auth/user'].tanto_name }}<br/>
                    TEL: {{ $store.getters['recipienter_auth/user'].phone }}
                  </p>

                  <span class="dump">
                    <img :src="$store.getters['recipienter_auth/user'].stamp_url" />
                  </span>
                </div>
              </div>
            </div>
            <OrderProductTable :id="order_form.id" />
            <div class="total_price">
              <table v-if="order_form.tax_type == 1">
                <tr class="calc_row">
                  <td>小計（税抜）</td>
                  <td>￥{{ calcTotalPrice() }}</td>
                </tr>
                <tr class="calc_row">
                  <td>消費税（8％）</td>
                  <td>￥{{ calcTaxFee8P() }}</td>
                </tr>
                <tr class="calc_row">
                  <td>消費税（10％）</td>
                  <td>￥{{ calcTaxFee10P() }}</td>
                </tr>
                <tr class="calc_row">
                  <td>合計</td>
                  <td>￥{{ calcTotalPrice() + calcTaxFee8P() + calcTaxFee10P() }}</td>
                </tr>
              </table>
              <table v-if="order_form.tax_type == 2">
                <tr class="calc_row">
                  <td>小計（8％）</td>
                  <td>￥{{ calcPrice8P() }}</td>
                </tr>
                <tr class="calc_row">
                  <td>小計（10％）</td>
                  <td>￥{{ calcPrice10P() }}</td>
                </tr>
                <tr class="calc_row">
                  <td>合計(税込)</td>
                  <td>￥{{ calcPrice8P() + calcPrice10P() }}</td>
                </tr>
              </table>
              <table v-if="order_form.tax_type == 3">
                <tr class="calc_row">
                  <td>合計</td>
                  <td>￥{{ calcTotalPrice() }}</td>
                </tr>
              </table>
            </div>
          </div>
          <div class="html2pdf__page-break"/>
        </section>
      </section>
    </vue-html2pdf>
  </div>
</template>
<script>
import ConfirmModal from '../../../components/common/ConfirmModal.vue'
import VueHtml2pdf from 'vue-html2pdf'
import OrderProductTable from '../../../components/recipient/OrderProductTable.vue'
var zenginCode = require('zengin-code')
export default {
  layout: 'recipient',
  components: {
    ConfirmModal,
    VueHtml2pdf,
    OrderProductTable
  },
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
    sendInvoiceEmail() {
      this.isShowModal = true
    },
    cancelFinishDelivery() {
      this.isShowModal = false
    },
    async okFinishDelivery() {
      try {
        const { data } = await axios.post('/recipienter/finish_delivery', {
          id: this.$route.query.id
        })
        if (data.flag) {
          this.$swal('', '出荷完了しました')
          this.$router.push({ name: 'recipient.invoice' })
        }
      } catch (error) {
      }
    },
    loadBankList() {
      this.bankList = Object.values(zenginCode)
      this.bankList = this.bankList.filter(item => {
        return (item.code >= '0001' && item.code < '0043') || item.name == 'ゆうちょ'
      })
    },
    calcBankName() {
      var temp = this.bankList.find(item => {
        return item.code == this.bank_code
      })
      
      return !!temp ? temp.name : ""
    },
    downloadPDF() {
      this.$refs.html2Pdf.generatePdf()
    },
    onProgress(event) {

    },
    hasStartedGeneration() {

    },
    hasGenerated() {

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
    calcTotalPrice() {
      var temp = 0
      this.rows.map(item => {
        temp += item.price * item.amount
      })
      return parseInt(temp)
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
    async init() {
      try {
        const { data } = await axios.post('/recipienter/get_order_detail', {
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
    }
  }
}
</script>
<style lang="scss" scoped>
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
    margin-left: 50px;
    display: flex;
    padding-top: 20px;
    border-bottom: 1px solid var(--border-color);
    padding-bottom: 20px;

    .panel {
      flex: 0.5;
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

    .status__action {
      flex: 0.5;
      display: flex;
      align-items: flex-end;
      justify-content: flex-end;

      button {
        width: 70px;
        height: 25px;
        border-radius: 5px;
        border: 1px solid var(--border-color);
        color: var(--text-color);
        font-size: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 3px 6px rgba($color: #000000, $alpha: 0.16);

        img {
          margin-right: 4px;
        }

        &:first-of-type {
          margin-right: 17px;
        }
      }
    }
  }

  .client__info {
    margin-left: 50px;
    width: 860px;
    padding-top: 25px;
    display: flex;

    .left {
      width: 300px;

      .client_saki {
        font-size: 12px;
        color: var(--text-color);
        width: 100%;
        border-bottom: 1px solid var(--border-color);
        position: relative;
        padding-right: 35px;
        margin-top: 6px;
        line-height: 25px;

        span {
          font-size: 18px;
          position: absolute;
          right: 15px;
        }
      }

      .content {
        font-size: 10px;
        margin-left: 20px;
        margin-bottom: 44px;
        padding-top: 18px;
      }

      .total_price {
        display: flex;
        border-bottom: 1px solid var(--border-color);
        line-height: 25px;

        span {
          font-size: 12px;

          &:first-of-type {
            margin-right: auto;
          }
        }
      }
    }

    .right {
      width: 270px;
      margin-left: auto;

      .info__row {
        display: flex;
        align-items: center;

        label {
          font-size: 10px;
          width: 120px;
        }

        p {
          flex: 1;
          font-size: 10px;
        }
      }

      .com_name {
        margin-top: 14px;
        font-size: 18px;
        color: var(--text-color);
        margin-bottom: 8px;
      }

      .address {
        margin-bottom: 5px;
        font-size: 10px;
      }

      .contact_btn {
        display: flex;
        align-items: center;
        font-size: 12px;
        color: #337DFD;

        img {
          width: 15px;
          margin-right: 20px;
        }
      }
    }
  }

  .detail__part {
    .detail__table {
      padding-left: 50px;
      padding-top: 25px;

      .remark input {
        height: 30px;
        border: 1px solid var(--border-color);
      }
    }

    .total__part {
      display: flex;
      padding-left: 50px;

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
              background-color: var(--accent-color);
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
.pdf_container {
  padding: 25px;

  .pdf_heading {
    display: flex;

    .contact_to {
      font-size: 16px;
      margin-right: auto;
    }

    .page_type {
      width: fit-content;

      p {
        &:first-of-type {
          background-color: #333;
          color: #fff;
          width: 100%;
          height: 40px;
          display: flex;
          align-items: center;
          justify-content: center;
          font-size: 16px;
          font-weight: bold;
        }

        &:last-of-type {
          width: fit-content;
          font-size: 12px;
        }
      }
    }
  }

  .order_detail_info {
    display: flex;
    border: 1px solid #333;
    padding: 10px;
    margin-top: 20px;

    .d-flex {
      display: flex;
      align-items: center;
      margin-bottom: 14px;
    }

    .left {
      flex: 1;
      border-right: 1px dashed #333;
      margin-right: 10px;

      label {
        width: 100px;
        height: 30px;
        background-color: #000;
        color: #fff;
        text-align: center;
        margin-right: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
      }

      p {
        font-size: 15px;
      }
    }

    .right {
      flex: 1;

      .client_info {
        margin-bottom: 20px;
        position: relative;

        .dump {
          position: absolute;
          right: 10px;
          bottom: 0;

          img {
            width: 60px;
            height: 60px;
            object-fit: cover;
          }
        }
        &.border {
          border: 1px solid #000;
          padding: 10px;
        }

        label {
          width: 100px;
          height: 30px;
          background-color: #000;
          color: #fff;
          text-align: center;
          margin-bottom: 10px;
          display: flex;
          align-items: center;
          justify-content: center;
        }

        p {
          font-size: 12px;
        }
      }
    }
  }
}

.total_price {
  width: fit-content;
  margin-left: auto;
  margin-top: 30px;

  table {
    border-collapse: collapse;

    td {
      border: 1px solid #000;
      padding-left: 10px;
      padding-right: 10px;
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