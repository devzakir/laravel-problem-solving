<template>
  <div class="main__container">
    <h2 class="page__title">請求書管理</h2>

    <div class="table__header">
      <div class="search__form">
        <div class="d-flex mb-14">
          <div class="form__row d-flex">
            <input type="text" placeholder="キーワード" v-model="keyword" />
          </div>
          <div class="form__row mr-0 d-flex">
            <input type="date" v-model="from" />
          </div>
          <div class="deparate">~</div>
          <div class="form__row d-flex">
            <input type="date" v-model="to" />
          </div>
        </div>
        <div class="d-flex">
          <div class="form__row d-flex">
            <label>ステータス：</label>
            <select v-model="status">
              <option :value="1">すべて</option>
              <option :value="2">請求書作成済み</option>
              <option :value="3">請求書未作成</option>
            </select>
          </div>
          <div class="form__action">
            <button @click="search">検索</button>
          </div>
        </div>
      </div>
      <div class="exort__part">
        <div class="download_panel">
          <a @click="downloadPDF"><img src="/asset/icons/pdf.svg" />請求書</a>
        </div>
        <div class="total_count">
          {{ orders.length }}件
        </div>
      </div>
    </div>

    <div class="table__container">
      <vue-good-table
        :columns="columns"
        :rows="rows"
        :pagination-options="{
          enabled: false,
        }">
        <template slot="table-row" slot-scope="props">
          <div class="toggle" v-if="props.column.field == 'status'">
            <toggle-button :width="88" :value="props.row.status" :color="{unchecked: '#337DFD', checked: '#707070', disabled: '#909090'}" :labels="{unchecked: '未対応', checked: '対応済'}"/>
          </div>
          <div v-else-if="props.column.field == 'invoice_at'">
          {{ props.row.invoice_at | dateFormatFull }}
          </div>
          <div v-else-if="props.column.field == 'invoiced_date'">
          {{ props.row.pay_expire | dateFormat }}
          </div>
          <div v-else-if="props.column.field == 'price'">
          ￥{{ calcTotalPrice(props.row.products) | moneyFormat }}
          </div>
          <div v-else-if="props.column.field == 'order_from'">
            {{ props.row.user.com_name }}
          </div>
          <div v-else-if="props.column.field == 'invoiced'">
            <span class="shipping_slip" :class="{ off: !props.row.invoiced == 1 }">{{ props.row.invoiced == 1 ? '作成済' : '未作成' }}</span>
          </div>
          <div v-else-if="props.column.field == 'detail'">
            <button @click="toDeatilScreen(props.row.id)" class="detail_btn">詳細</button>
          </div>
          <div v-else-if="props.column.field == 'delete'">
            <a class="delete_btn"><img src="/asset/icons/delete.svg" /></a>
          </div>
          <div v-else-if="props.column.field == 'select'">
            <input @change="selectRows($event, props.row.id)" class="select" type="checkbox" />
          </div>
          <div class="table_cell" v-else>
            {{ props.formattedRow[props.column.field] }}
          </div>
        </template>
        <div slot="table-actions-bottom">
          <paginate
            :page-count="pageCount"
            :click-handler="clickPage"
            :prev-text="'次'"
            :next-text="'前'"
            :container-class="'my_custom_pagination'">
          </paginate>
        </div>
      </vue-good-table>
    </div>
    <vue-html2pdf
      :show-layout="false"
      :float-layout="true"
      :enable-download="true"
      :preview-modal="false"
      :paginate-elements-by-height="1400"
      filename="請求書"
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
      <section slot="pdf-content">
        <section class="pdf-item" v-for="(page, index) in calcSelectedRows()" :key="'page' + index">
          <div class="pdf_container">
            <div class="pdf_heading">
              <p class="contact_to">{{ page.user.com_name }}　御中</p>
              <div class="page_type">
                <p>請求書</p>
                <p>作成日：{{ page.invoice_at | dateFormat }}　請求書番号：{{ page.invoice_hash || '' }}<br/>支払い期日：{{ page.pay_expire | dateFormat }}</p>
              </div>
            </div>
            <div class="order_detail_info">
              <div class="left">
                <div class="d-flex">
                  <label>請求額</label>
                  <p v-if="page.tax_type == 1">￥{{ parseInt(calcTotalPrice1(page) + calcTaxFee8P(page) + calcTaxFee10P(page)) | moneyFormat }}（税込）</p>
                  <p v-if="page.tax_type == 2">￥{{ parseInt(calcPrice8P(page) + calcPrice10P(page)) | moneyFormat }}（税込）</p>
                  <p v-if="page.tax_type == 3">￥{{ calcTotalPrice1(page) | moneyFormat }}（税込）</p>
                </div>
              </div>
              <div class="right">
                <div class="client_info border">
                  <p>
                    {{ $store.getters['recipienter_auth/user'].name }}<br/>
                    {{ !!$store.getters['recipienter_auth/user'].zipcode ? '〒' + $store.getters['recipienter_auth/user'].zipcode : '' }} {{ $store.getters['recipienter_auth/user'].prefecture || '' }} {{ $store.getters['recipienter_auth/user'].city || '' }} {{ $store.getters['recipienter_auth/user'].building || '' }}<br/>
                    TEL: {{ $store.getters['recipienter_auth/user'].phone }}
                  </p>

                  <span class="dump">
                    <img :src="$store.getters['recipienter_auth/user'].stamp_url" />
                  </span>
                </div>
              </div>
            </div>
            <OrderProductTable :id="page.id" />
            <div class="total_price">
              <table v-if="page.tax_type == 1">
                <tr class="calc_row">
                  <td>小計（税抜）</td>
                  <td>￥{{ calcTotalPrice1(page) | moneyFormat }}</td>
                </tr>
                <tr class="calc_row">
                  <td>消費税（8％）</td>
                  <td>￥{{ calcTaxFee8P(page) | moneyFormat }}</td>
                </tr>
                <tr class="calc_row">
                  <td>消費税（10％）</td>
                  <td>￥{{ calcTaxFee10P(page) | moneyFormat }}</td>
                </tr>
                <tr class="calc_row">
                  <td>合計</td>
                  <td>￥{{ parseInt(calcTotalPrice1(page) + calcTaxFee8P(page) + calcTaxFee10P(page)) | moneyFormat }}</td>
                </tr>
              </table>
              <table v-if="page.tax_type == 2">
                <tr class="calc_row">
                  <td>小計（8％）</td>
                  <td>￥{{ calcPrice8P(page) | moneyFormat }}</td>
                </tr>
                <tr class="calc_row">
                  <td>小計（10％）</td>
                  <td>￥{{ calcPrice10P(page) | moneyFormat }}</td>
                </tr>
                <tr class="calc_row">
                  <td>合計(税込)</td>
                  <td>￥{{ parseInt(calcPrice8P(page) + calcPrice10P(page)) | moneyFormat }}</td>
                </tr>
              </table>
              <table v-if="page.tax_type == 3">
                <tr class="calc_row">
                  <td>合計</td>
                  <td>￥{{ calcTotalPrice1(page) | moneyFormat }}</td>
                </tr>
              </table>
            </div>
          </div>
          <div class="html2pdf__page-break"/>
        </section>
      </section>
    </vue-html2pdf>

    <ConfirmModal 
      v-if="confirmPDF" 
      :note="'請求書をダウンロードしますか？'" 
      :cancelText="'キャンセル'" 
      :okText="'決定'"
      @cancel="() => { confirmPDF = false }"
      @ok="downloadPDFConfirm"/>
  </div>
</template>
<script>
import VueHtml2pdf from 'vue-html2pdf'
import moment from 'moment'
import ConfirmModal from '../../../components/common/ConfirmModal.vue'
import OrderProductTable from '../../../components/recipient/OrderProductTable'
export default {
  layout: 'recipient',
  components: {
    VueHtml2pdf,
    ConfirmModal,
    OrderProductTable
  },
  data() {
    return {
      columns: [
        {
          label: '請求書番号',
          field: 'invoice_hash',
        },
        {
          label: '作成日時',
          field: 'invoice_at',
        },
        {
          label: '請求金額',
          field: 'price',
        },
        {
          label: '支払期日',
          field: 'invoiced_date',
        },
        {
          label: '取引先',
          field: 'order_from',
        },
        {
          label: 'ステータス',
          field: 'invoiced',
        },
        {
          label: '',
          field: 'detail',
          sortable: false,
        },
        {
          label: '■選択',
          field: 'select',
          sortable: false,
        },
      ],
      rows: [],
      orders: [],
      pageCount: 1,
      confirmPDF: false,
      keyword: null,
      from: null,
      to: null,
      status: 1,
      deadline: null,
    }
  },
  mounted() {
    this.init()
  },
  methods: {
    async init() {
      try {
        const { data } = await axios.post('/recipienter/get_invoces_list')
        this.orders = data.orders
        this.rows = this.orders.filter((item, index) => {
          return index < 10
        })
        this.setPagination()
      } catch (error) {
      }
    },
    calcTotalPrice1(order) {
      var temp = 0
      if (!!order.products) {
        order.products.map(item => {
          temp += item.price * item.amount
        })
      }
      return parseInt(temp)
    },
    calcTaxFee8P(order) {
      var temp = 0
      if (!!order.products) {
        order.products.forEach(item => {
          if (item.tax == 2) {
            temp += (item.price * item.amount) * 0.08
          }
        })
      }
      return parseInt(temp)
    },
    calcTaxFee10P(order) {
      var temp = 0
      if (!!order.products) {
        order.products.forEach(item => {
          if (item.tax == 1) {
            temp += (item.price * item.amount) * 0.1
          }
        })
      }
      return parseInt(temp)
    },
    calcPrice8P(order) {
      var temp = 0
      if (!!order.products) {
        order.products.forEach(item => {
          if (item.tax == 2) {
            temp += (item.price * item.amount)
          }
        })
      }
      
      return parseInt(temp)
    },
    calcPrice10P(order) {
      var temp = 0
      if (!!order.products) {
        order.products.forEach(item => {
          if (item.tax == 1) {
            temp += (item.price * item.amount)
          }
        })
      }
      return parseInt(temp)
    },
    async search() {
      try {
        const { data } = await axios.post('/recipienter/get_invoces_list')
        this.orders = data.orders.map(item => {
          return {
            ...item,
            selected: false
          }
        })
        if (!!this.keyword) {
          this.orders = this.orders.filter(item => {
            return item.order_form.name.includes(this.keyword) || item.user.com_name.includes(this.keyword)
          })
        }

        if (!!this.from) {
          this.orders = this.orders.filter(item => {
            return moment(item.created_at).format('YYYYMMDD') >= moment(this.from).format('YYYYMMDD')
          })
        }

        if (!!this.to) {
          this.orders = this.orders.filter(item => {
            return moment(item.created_at).format('YYYYMMDD') <= moment(this.to).format('YYYYMMDD')
          })
        }

        if (this.status == 2) {
          this.orders = this.orders.filter(item => {
            return item.invoiced == 1
          })
        } else if (this.status == 3) {
          this.orders = this.orders.filter(item => {
            return item.invoiced != 1
          })
        }

        if (!!this.deadline) {
          this.orders = this.orders.filter(item => {
            return moment(item.deadline).format('YYYY/MM/DD') == moment(this.deadline).format('YYYY/MM/DD')
          })
        }
        console.log(this.orders, "_____this.orderssdfasdf")
        this.rows = this.orders.filter((item, index) => {
          return index < 10
        })
        this.setPagination()
      } catch (error) {
      }
    },
    calcSelectedRows() {
      return this.rows.filter(item => {
        return item.selected
      })
    },
    downloadPDF() {
      if (this.calcSelectedRows().length == 0) {
        this.$swal('エラー', 'チェックしたものがありません。')
        return
      }
      let temp = this.calcSelectedRows().filter(item => {
        return item.invoiced == 0
      })

      if (temp.length > 0) {
        this.$swal('エラー', '作成済のみを選択してください。')
        return
      }
      this.confirmPDF = true
    },
    downloadPDFConfirm() {
      this.confirmPDF = false
      this.$refs.html2Pdf.generatePdf()
    },
    onProgress(event) {

    },
    calcTotalPrice(products) {
      let price = 0
      products.forEach(item => {
        if (item.tax_type == 1) {
          if (item.tax == 1) {
            price = price + parseInt(parseInt(item.price * item.amount) * 1.1)
          } else {
            price = price + parseInt(parseInt(item.price * item.amount) * 1.08)
          }
        } else if (item.tax_type == 2) {
          price = price + parseInt(item.price * item.amount)
        } else {
          price = price + parseInt(item.price * item.amount)
        }
      })

      return parseInt(price)
    },
    hasStartedGeneration() {

    },
    hasGenerated() {

    },
    setPagination() {
      if (this.orders.length % 10 > 0) {
        this.pageCount = parseInt(this.orders.length / 10) + 1
      } else {
        this.pageCount = parseInt(this.orders.length / 10)
      }
    },
    clickPage(params) {
      this.rows = this.orders.filter((item, index) => {
        return index >= (params - 1) * 10 && index < params * 10
      })
    },
    toDeatilScreen(id) {
      this.$router.push({ name: 'recipient.invoice.detail', query: { id: id } })
    },
    selectRows(event, id) {
      let temp = [...this.rows]
      temp = temp.map(item => {
        if (item.id == id) {
          return {
            ...item,
            selected: event.target.checked
          }
        } else {
          return item
        }
      })
      this.rows = temp
      let temp1 = this.rows.filter(item => {
        return item.selected == true
      }).map(item => {
        return item.id
      })
      this.selectedIds = temp1.join(',')
    },
  }
}
</script>
<style lang="scss" scoped>
.d-flex {
  display: flex;
  align-items: center;
}
.mb-14 {
  margin-bottom: 14px;
}
.main__container {
  padding: 20px 30px;

  .page__title {
    margin-bottom: 20px;
  }

  .table__header {
    display: flex;
    width: 1290px;
    align-items: flex-start;

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

    .exort__part {
      flex: 1;

      .append_btn {
        margin-left: auto;
        width: 120px;
        height: 30px;
        background-color: #985298;
        color: #fff;
        font-size: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0px 3px 6px rgba($color: #000000, $alpha: 0.16);
        font-weight: 600;

        img {
          margin-right: 4px;
        }

        &:hover {
          color: #fff;
        }
      }

      .download_panel {
        padding: 15px;
        background-color: var(--accent-color);
        border-radius: 5px;
        display: flex;
        width: fit-content;
        margin-left: auto;
        margin-top: 10px;
        box-shadow: 0 3px 6px rgba($color: #000000, $alpha: 0.16);
        position: relative;

        a {
          border-radius: 5px;
          background-color: #fff;
          padding-left: 6px;
          padding-right: 6px;
          height: 25px;
          display: flex;
          align-items: center;
          justify-content: center;
          margin-right: 14px;
          font-size: 12px;
          color: var(--text-color);

          &:last-of-type {
            margin-right: 0;
          }

          img {
            margin-right: 6px;
          }
        }

        &:after{
          content: "";
          position: absolute;
          width: 0;
          height: 0;
          border-top: 20px solid var(--accent-color);
          border-left: 10px solid transparent;
          border-right: 10px solid transparent;
          right: 35px;
          bottom: -20px;
        }
      }

      .total_count {
        padding-right: 130px;
        font-size: 12px;
        color: var(--text-color);
        text-align: right;
        padding-top: 10px;
      }
    }
  }

  .table__container {
    padding-top: 15px;
    width: 1290px;
  }
}

.table_cell {
  font-size: 12px;
}

.shipping_slip {
  width: 60px;
  height: 20px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #fff;
  font-size: 12px;
  background-color: var(--accent-color);

  &.off {
    background-color: #D34555;
  }
}

.detail_btn {
  width: 60px;
  height: 20px;
  display: flex;
  align-items: center;
  justify-content: center;
  border: 0;
  background-color: var(--border-color);
  color: #fff;
  font-size: 12px;
}

.delete_btn {
  display: flex;
}

.select {
  width: 15px;
  height: 15px;
  border: 1px solid var(--border-color);
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
        position: relative;
        margin-bottom: 20px;

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