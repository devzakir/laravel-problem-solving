<template>
  <div class="order__detail_container">
    <div class="d-flex heading">
      <h2 class="page__title">見積書詳細</h2>
    </div>
    <div class="actions">
      <button @click="edit">編集</button>
      <button @click="downloadPDF">
        <img src="/asset/icons/pdf.svg" />
        見積書
      </button>
    </div>
    <div class="client__info" v-if="!!quote">
      <div class="left">
        <h3 class="page__title">{{ quote.com_name }}</h3>
        <p class="client_saki">{{ quote.name }} <span>様</span></p>
        <p class="content">
          この度はご注文いただきましてありがとうごいます。<br/>
          下記の通り納品させていただきます。
        </p>
        <p class="total_price">
          <span>合計金額</span>
          <span>￥{{parseInt(calcTotalPrice() + calcTaxFee8P() + calcTaxFee10P()) | moneyFormat }}（税込）</span>
        </p>
      </div>
      <div class="right">
        <div class="info__row">
          <label>見積書番号：</label>
          <p>{{ quote.hash }}</p>
        </div>
        <div class="info__row">
          <label>発行日：</label>
          <p>{{ quote.publish_date | dateFormat }}</p>
        </div>
        <div class="com_name">{{ quote.com_name }}</div>
        <div class="address">
          担当者：{{ quote.name }}
        </div>
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
            <div class="remark" v-if="props.column.field == 'remark'">
              <input type="text" disabled />
            </div>
            <div v-else-if="props.column.field == 'tax'">
              <input class="price" :checked="props.row.tax == 2" type="checkbox" disabled />
            </div>
            <div class="table_cell" v-else>
              {{ props.formattedRow[props.column.field] }}
            </div>
          </template>
        </vue-good-table>
      </div>
      <div class="total__part">
        <div class="message__part" v-if="!!quote">
          <h4>メッセージ</h4>
          <p>
            {{ quote.message }}
          </p>
        </div>
        <div class="calculator">
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
            <p>￥{{ parseInt(calcTotalPrice() + calcTaxFee8P() + calcTaxFee10P()) }}</p>
          </div>
          <div class="calc__action">
            <button @click="sendEmail" class="on">PDFを選択してメールで送信</button>
          </div>
          <div class="calc__action">
            <button @click="back" class="back">戻る</button>
          </div>
        </div>
      </div>
    </div>
    <vue-html2pdf
      :show-layout="false"
      :float-layout="true"
      :enable-download="true"
      :preview-modal="false"
      :paginate-elements-by-height="1400"
      filename="見積書"
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
      <section slot="pdf-content" v-if="!!quote">
        <section class="pdf-item">
          <div class="pdf_container">
            <div class="pdf_heading">
              <p class="contact_to">{{ quote.com_name }} {{ quote.department_name }}<br/>{{ quote.name }}様</p>
              <div class="page_type">
                <p>見積書</p>
                <p>発行日：{{ quote.dateFormat | dateFormat }}　見積番号：{{ quote.hash || '' }}<br/></p>
              </div>
            </div>
            <div class="order_detail_info">
              <div class="left">
                <div>
                  <p>案件名：{{ quote.subject_title }}</p>
                  <div class="d-flex">
                    <label>合計金額</label>
                   <p>￥{{ parseInt(calcTotalPrice() + calcTaxFee8P() + calcTaxFee10P()) | moneyFormat }}（税込）</p>
                  </div>
                </div>
              </div>
              <div class="right">
                <div class="client_info border">
                  <p class="contact_to">{{ $store.getters['recipienter_auth/user'].name }}<br/>担当者：{{ $store.getters['recipienter_auth/user'].type == 1 ? 'マスタアカウント様' : $store.getters['recipienter_auth/user'].tanto_name + '様' }}<br/>メールアドレス：{{ $store.getters['recipienter_auth/user'].email }}</p>

                  <span class="dump">
                    <img :src="$store.getters['recipienter_auth/user'].stamp_url" />
                  </span>
                </div>
              </div>
            </div>
            <QuoteProductTable :id="quote.id" />
            <div class="total_price">
              <table>
                <tr>
                  <td>小計(税抜)</td>
                  <td>￥{{ calcTotalPrice() | moneyFormat }}</td>
                </tr>
                <tr>
                  <td>消費税（8％）</td>
                  <td>￥{{ calcTaxFee8P() | moneyFormat }}</td>
                </tr>
                <tr>
                  <td>消費税（10％）</td>
                  <td>￥{{ calcTaxFee10P() | moneyFormat }}</td>
                </tr>
                <tr>
                  <td>合計</td>
                  <td>￥{{ parseInt(calcTotalPrice() + calcTaxFee8P() + calcTaxFee10P()) | moneyFormat }}</td>
                </tr>
              </table>
            </div>
          </div>
          <div class="html2pdf__page-break"/>
        </section>
      </section>
    </vue-html2pdf>
    <input type="file" class="dis-none" ref="pdf_selector" @change="selectedPDF" accept="application/pdf" />
    <ConfirmModal 
      v-if="isShowModal" 
      :note="'PDFを選択してメールで送信'" 
      :cancelText="'キャンセル'" 
      :okText="'送信する'"
      @cancel="() => {
        isShowModal = false
      }"
      @ok="confirmSend"/>
  </div>
</template>
<script>
import ConfirmModal from '../../../components/common/ConfirmModal.vue'
import VueHtml2pdf from 'vue-html2pdf'
import QuoteProductTable from '../../../components/recipient/QuoteProductTable.vue'
export default {
  layout: 'recipient',
  components: {
    VueHtml2pdf,
    QuoteProductTable,
    ConfirmModal
  },
  data() {
    return {
      columns: [
        {
          label: '商品名',
          field: 'name',
          sortable: false
        },
        {
          label: '単価',
          field: 'price',
          sortable: false
        },
        {
          label: '軽減税率',
          field: 'tax',
          sortable: false,
          width: '60px'
        },
        {
          label: '数量',
          field: 'amount',
          sortable: false
        },
        {
          label: '発注金額',
          field: 'total_price',
          sortable: false
        },
        {
          label: '備考',
          field: 'remark',
          sortable: false
        },
      ],
      rows: [],
      quote: null,
      isShowModal: false,
      tempFile: null
    }
  },
  mounted() {
    this.init()
  },
  methods: {
    async init() {
      try {
        const { data } = await axios.post('/recipienter/get_quote_detail_info', {
          id: this.$route.query.id
        })
        this.rows = data.quote.products
        this.quote = data.quote
      } catch (error) {
      }
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
    calcTotalPrice() {
      let price = 0
      this.rows.forEach(item => {
        if (!!item.price && !!item.amount) {
          price += parseInt(parseInt(item.price) * parseInt(item.amount))
        }
      })

      return price
    },
    calcTaxFee8P() {
      var temp = 0
      console.log(this.rows, "___123123123123")
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
    back() {
      this.$router.back()
    },
    edit() {
      this.$router.push({ name: 'recipient.quote.edit', query: { id: this.$route.query.id } })
    },
    async sendEmail() {
      this.$refs.pdf_selector.click()
    },
    async selectedPDF(event) {
      const file = event.target.files[0]
      if (!!file) {
        this.tempFile = file
        this.isShowModal = true
      }
    },
    async confirmSend() {
      let formData = new FormData()
      formData.append('pdf', this.tempFile)
      formData.append('id', this.$route.query.id)
      try {
        const { data } = await axios.post('/recipienter/send_quote_email', formData, { headers: { 'Content-Type': 'multipart/form-data' }})
        this.$swal('', '送信しました')
        this.$router.push({ name: 'recipient.quote' })
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
    border-top: 1px solid var(--border-color);

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
            width: 200px;
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

.flow__part {
  display: flex;
  position: relative;
  width: 355px;
  margin-left: auto;
  margin-right: auto;
  justify-content: space-between;
  margin-bottom: 60px;
  margin-top: 20px;

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

.actions {
  display: flex;
  align-items: center;
  justify-content: flex-end;
  padding-top: 50px;
  padding-bottom: 16px;

  button {
    width: 70px;
    height: 25px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 4px;
    border: 1px solid var(--border-color);
    box-shadow: 0 3px 6px rgba($color: #000000, $alpha: 0.16);
    font-size: 10px;

    &:first-of-type {
      margin-right: 16px;
    }

    img {
      width: 16px;
      margin-right: 6px;
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
</style>