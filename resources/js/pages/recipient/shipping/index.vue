<template>
  <div class="main__container">
    <h2 class="page__title">出荷管理</h2>

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
          <div class="form__row d-flex">
            <label>納品希望日：</label>
            <input type="date" v-model="deadline" />
          </div>
          <div class="form__action">
            <button @click="search">検索</button>
          </div>
        </div>
      </div>
      <div class="exort__part">
        <div class="download_panel">
          <a @click="downloadPDFNouhin"><img src="/asset/icons/pdf.svg" />納品書</a>
          <a @click="downloadPDF"><img src="/asset/icons/pdf.svg" />出荷伝票</a>
          <a @click="downloadCSV"><img src="/asset/icons/csv.svg" />送り状</a>
          <a @click="createMultiInvoice"><img src="/asset/icons/slip.svg" />請求書作成</a>
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
          <div v-if="props.column.field == 'status'">
            <span class="shipping_slip" :class="{ off: !props.row.delivery == 1 }">{{ props.row.delivery == 1 ? '出荷済' : '出荷前' }}</span>
          </div>
          <div v-if="props.column.field == 'invoice'">
            <span class="shipping_slip" :class="{ off: !props.row.invoiced == 1 }">{{ props.row.invoiced == 1 ? '作成済' : '未作成' }}</span>
          </div>
          <div v-else-if="props.column.field == 'updated_at'">
            {{ props.row.updated_at | dateFormatFull }}
          </div>
          <div v-else-if="props.column.field == 'order_form'">
            {{ props.row.order_form.name }}
          </div>
          <div v-else-if="props.column.field == 'order_from'">
            {{ props.row.user.com_name }}
          </div>
          <div v-else-if="props.column.field == 'deadline'">
            {{ props.row.deadline | dateFormat }} {{ props.row.deadline_time }}
          </div>
          <div v-else-if="props.column.field == 'payment_status'">
            <span class="shipping_slip" :class="{ off: !props.row.payed == 1 }">{{ props.row.payed == 1 ? '支払済' : '未支払' }}</span>
          </div>
          <div v-else-if="props.column.field == 'detail'">
            <button @click="toDetailScreen(props.row.id)" class="detail_btn">詳細</button>
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
      <section slot="pdf-content">
        <section class="pdf-item" v-for="(page, index) in calcSelectedRows()" :key="'page' + index">
          <div class="pdf_container">
            <div class="pdf_heading">
              <p class="contact_to">{{ page.user.com_name }}<br/>{{ page.user.name }}</p>
              <div class="page_type">
                <p>納品書</p>
                <p>注文日：{{ page.ordered_at | dateFormat }}　注文番号：{{ page.hash || '' }}<br/></p>
                <p>出荷日：{{ page.delivery_at | dateFormat }}　出荷番号：{{ page.delivery_hash || '' }}<br/></p>
              </div>
            </div>
            <div class="order_detail_info">
              <div class="left">
                <div>
                  <p>この度はご注文いただきまして誠にありがとうございます。<br/>下記の通り納品させていただきます。</p>
                  <div class="d-flex">
                    <label>合計金額</label>
                   <p>￥{{ page.tax == 1 ? parseInt(page.price * 1.1) : parseInt(page.price * 1.08) | moneyFormat }}（税込）</p>
                  </div>
                </div>
              </div>
              <div class="right">
                <div class="client_info border">
                  <p>
                    {{ $store.getters['recipienter_auth/user'].name }}<br/>
                    {{ !!$store.getters['recipienter_auth/user'].zipcode ? '〒' + $store.getters['recipienter_auth/user'].zipcode : '' }} {{ $store.getters['recipienter_auth/user'].prefecture || '' }} {{ $store.getters['recipienter_auth/user'].city || '' }} <br/>{{ $store.getters['recipienter_auth/user'].building || '' }}<br/>
                    担当者：{{ $store.getters['recipienter_auth/user'].tanto_name || 'マスタアカウント様' }}<br/>
                    TEL: {{ $store.getters['recipienter_auth/user'].telephone }}
                  </p>

                  <span class="dump">
                    <img :src="$store.getters['recipienter_auth/user'].stamp_url" />
                  </span>
                </div>
              </div>
            </div>
            <OrderProductTable :id="page.id" />
            <div class="total_price">
              <table>
                <tr>
                  <td>小計(税抜)</td>
                  <td>￥{{ page.price | moneyFormat }}</td>
                </tr>
                <tr>
                  <td>消費税</td>
                  <td>￥{{ page.tax == 1 ? parseInt(page.price * 0.1) : parseInt(page.price * 0.08) | moneyFormat }}</td>
                </tr>
                <tr>
                  <td>合計</td>
                  <td>￥{{ page.tax == 1 ? parseInt(page.price * 1.1) : parseInt(page.price * 1.08) | moneyFormat }}</td>
                </tr>
              </table>
            </div>
          </div>
          <div class="html2pdf__page-break"/>
        </section>
      </section>
    </vue-html2pdf>

    <vue-html2pdf
      :show-layout="false"
      :float-layout="true"
      :enable-download="true"
      :preview-modal="false"
      :paginate-elements-by-height="1400"
      filename="発注書"
      :pdf-quality="2"
      :manual-pagination="false"
      pdf-format="a4"
      pdf-orientation="landscape"
      pdf-content-width="1132px"
      @progress="onProgress($event)"
      @hasStartedGeneration="hasStartedGeneration()"
      @hasGenerated="hasGenerated($event)"
      ref="html2Pdf2"
    >
      <section slot="pdf-content">
        <section class="pdf-item" v-for="(page, index) in calcSelectedRows()" :key="'page' + index">
          <div class="pdf_container">
            <div class="pdf_heading">
              <p class="contact_to">{{ $store.getters['recipienter_auth/user'].name }}　御中</p>
              <div class="page_type">
                <p>発注書</p>
                <p>注文日時：{{ page.ordered_at | productUpTimeFormat }}　注文番号：{{ page.hash || '' }}</p>
              </div>
            </div>
            <div class="order_detail_info">
              <div class="left">
                <div class="d-flex">
                  <label>希望納品日時</label>
                  <p>{{ page.deadline | dateFormat }} {{ page.deadline_time }}</p>
                </div>
                <div class="d-flex">
                  <label>メッセージ</label>
                  <p>{{ page.message || '' }}</p>
                </div>
              </div>
              <div class="right">
                <div class="client_info">
                  <label>発注者</label>
                  <p>
                    {{ page.user.com_name }}<br/>
                    {{ page.user.name }}<br/>
                    TEL: {{ page.user.telephone }}
                  </p>
                </div>
                <div class="client_info border">
                  <label>お届け先</label>
                  <p>
                    {{ page.user.com_name }}<br/>
                    {{ !!page.user.zipcode ? '〒' + page.user.zipcode : '' }} {{ page.user.prefecture || '' }} {{ page.user.city || '' }} {{ page.user.building || '' }}<br/>
                    TEL: {{ page.user.telephone }}
                  </p>
                </div>
              </div>
            </div>
            <OrderProductTable :id="page.id" />
            <div class="total_price">
              <table>
                <tr>
                  <td>小計(税抜)</td>
                  <td>￥{{ page.price | moneyFormat }}</td>
                </tr>
                <tr>
                  <td>消費税</td>
                  <td>￥{{ page.tax == 1 ? parseInt(page.price * 0.1) : parseInt(page.price * 0.08) | moneyFormat }}</td>
                </tr>
                <tr>
                  <td>合計</td>
                  <td>￥{{ page.tax == 1 ? parseInt(page.price * 1.1) : parseInt(page.price * 1.08) | moneyFormat }}</td>
                </tr>
              </table>
            </div>
          </div>
          <div class="html2pdf__page-break"/>
        </section>
      </section>
    </vue-html2pdf>

    <ConfirmModal 
      v-if="confirmPDFNouhin" 
      :note="'納品書をダウンロードしますか？'" 
      :cancelText="'キャンセル'" 
      :okText="'決定'"
      @cancel="() => { confirmPDFNouhin = false }"
      @ok="downloadPDFNouhinConfirm"/>
    <ConfirmModal 
      v-if="confirmPDF" 
      :note="'伝票をダウンロードしますか？'" 
      :cancelText="'キャンセル'" 
      :okText="'決定'"
      @cancel="() => { confirmPDF = false }"
      @ok="downloadPDFConfirm"/>
    <ConfirmModal 
      v-if="confirmCSV" 
      :note="'ダウンロードしますか？'" 
      :cancelText="'キャンセル'" 
      :okText="'決定'"
      @cancel="() => { confirmCSV = false }"
      @ok="downloadCSVConfirm"/>
    <ConfirmModal 
      v-if="confirmCreateInvoice" 
      :note="'請求書を作成しますか？'" 
      :cancelText="'キャンセル'" 
      :okText="'作成'"
      @cancel="() => { confirmCreateInvoice = false }"
      @ok="createMultiInvoiceConfirm"/>
  </div>
</template>
<script>
import VueHtml2pdf from 'vue-html2pdf'
import moment from 'moment'
import OrderProductTable from '../../../components/recipient/OrderProductTable'
import ConfirmModal from '../../../components/common/ConfirmModal.vue'
export default {
  layout: 'recipient',
  components: {
    VueHtml2pdf,
    OrderProductTable,
    ConfirmModal
  },
  data() {
    return {
      columns: [
        {
          label: 'ステータス',
          field: 'status',
        },
        {
          label: '注文番号',
          field: 'hash',
        },
        {
          label: '注文日時',
          field: 'updated_at',
        },
        {
          label: '注文フォーム',
          field: 'order_form',
        },
        {
          label: '取引先',
          field: 'order_from',
        },
        {
          label: '希望納品日',
          field: 'deadline',
        },
        {
          label: '請求書',
          field: 'invoice',
        },
        {
          label: '支払ステータス',
          field: 'payment_status',
        },
        {
          label: '',
          field: 'detail',
          sortable: false,
        },
        // {
        //   label: '削除',
        //   field: 'delete',
        //   sortable: false,
        // },
        {
          label: '■選択',
          field: 'select',
          sortable: false,
        },
      ],
      rows: [],
      orders: [],
      pageCount: 1,

      confirmPDFNouhin: false,
      confirmPDF: false,
      confirmCSV: false,
      selectedIds: "",
      confirmCreateInvoice: false,
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
    async search() {
      try {
        const { data } = await axios.post('/recipienter/get_deliveried_order')
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
            return moment(item.ordered_at) > moment(this.from)
          })
        }

        if (!!this.to) {
          this.orders = this.orders.filter(item => {
            return moment(item.ordered_at) < moment(this.to)
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
    async init() {
      try {
        const { data } = await axios.post('/recipienter/get_deliveried_order')
        this.orders = data.orders
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
    toDetailScreen(id) {
      this.$router.push({ name: 'recipient.shipping.detail', query: { id: id } })
    },
    downloadPDFNouhin() {
      if (this.calcSelectedRows().length == 0) {
        this.$swal('エラー', 'チェックしたものがありません。')
        return
      }
      let temp = this.calcSelectedRows().filter(item => {
        return item.payed == 0
      })

      if (temp.length > 0) {
        this.$swal('エラー', '作成済のみを選択してください。')
        return
      }
      this.confirmPDFNouhin = true
    },
    downloadPDFNouhinConfirm() {
      this.confirmPDFNouhin = false
      this.$refs.html2Pdf.generatePdf()
    },
    downloadPDF() {
      if (this.calcSelectedRows().length == 0) {
        this.$swal('エラー', 'チェックしたものがありません。')
        return
      }
      let temp = this.calcSelectedRows().filter(item => {
        return item.delivery_card == 0
      })

      if (temp.length > 0) {
        this.$swal('エラー', '作成済のみを選択してください。')
        return
      }
      this.confirmPDF = true
    },
    downloadPDFConfirm() {
      this.confirmPDF = false
      this.$refs.html2Pdf2.generatePdf()
    },
    downloadCSV() {
      if (this.calcSelectedRows().length == 0) {
        this.$swal('エラー', 'チェックしたものがありません。')
        return
      }
      let temp = this.calcSelectedRows().filter(item => {
        return item.delivery_card == 0
      })

      if (this.calcSelectedRows().length == 0) {
        return
      }

      if (temp.length > 0) {
        this.$swal('エラー', '作成済のみを選択してください。')
        return
      }
      this.confirmCSV = true
    },
    downloadCSVConfirm() {
      this.confirmCSV = false
      window.open('/recipienter/orders/export/csv/'+this.selectedIds, '_blank');
    },
    createMultiInvoice() {
      if (this.calcSelectedRows().length == 0) {
        this.$swal('エラー', 'チェックしたものがありません。')
        return
      }
      if (this.calcSelectedRows().length == 0) {
        return
      }
      let temp = this.calcSelectedRows().filter(item => {
        return item.invoiced == 1
      })
      if (temp.length > 0) {
        this.$swal('エラー', '未作成のみを選択してください')
        return
      }
      this.confirmCreateInvoice = true
    },
    async createMultiInvoiceConfirm() {
      if (this.calcSelectedRows().length == 0) {
        this.$swal('エラー', 'チェックしたものがありません。')
        return
      }
      let temp = this.calcSelectedRows().map(item => {
        return item.id
      })
      this.confirmCreateInvoice = false
      try {
        const { data } = await axios.post('/recipienter/finish_multi_invoices', {
          ids: temp
        })
        this.$swal('', '作成しました。')
        this.init()
      } catch (error) {
      }
    },
    onProgress(event) {

    },
    hasStartedGeneration() {

    },
    hasGenerated() {

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
        margin-right: auto;
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