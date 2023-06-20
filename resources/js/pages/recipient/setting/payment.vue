<template>
  <div class="credit_container">
    <h2 class="page__title">クレジットカード</h2>
    
    <div class="main__part">
      <h3>支払一覧</h3>
      <div class="search__form">
        <div class="d-flex mb-14">
          <div class="form__row mr-0 d-flex">
            <input type="date" v-model="search.fromDate" />
          </div>
          <div class="deparate">~</div>
          <div class="form__row d-flex">
            <input type="date" v-model="search.toDate"/>
          </div>
          <div class="form__action">
            <button @click="init">検索</button>
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
              {{ props.row.scheduled_date }} {{ props.row.status == 'pending' ? '入金予定' : '入金済み' }}
            </div>
            <div class="toggle" v-else-if="props.column.field == 'price'">
              ￥{{ props.row.amount | moneyFormat }}
            </div>
            <div class="toggle" v-else-if="props.column.field == 'captured_at'">
              {{ formatDate(props.row.captured_at*1000) }}
            </div>
            <div class="toggle" v-else-if="props.column.field == 'paid'">
              {{ props.row.refunded ? '返金済み' : ( props.row.paid ? '支払済み' : '' ) }}
            </div>
            <div class="toggle" v-else-if="props.column.field == 'detail'">
              <router-link :to="{ name: 'recipient.setting.payment_detail', query: { id: props.row.id } }">詳細</router-link>
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
    </div>
  </div>
</template>
<script>
import moment from 'moment'
export default {
  layout: 'recipient',
  data() {
    return {
      search: {
        fromDate: null,
        toDate: null
      },

      columns: [
        {
          label: '日付',
          field: 'captured_at'
        },
        {
          label: '金額',
          field: 'price'
        },
        {
          label: '課金ID',
          field: 'id'
        },
        {
          label: 'ステータス',
          field: 'paid'
        },
        {
          label: '詳細',
          field: 'detail'
        }
      ],
      pageCount: 1,
      rows: [],
      tempRows: []
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
      const { data } = await axios.post('/recipienter/get_payments', {
        id: this.$route.query.id
      })
      this.tempRows = data.res
      this.tempRows = this.tempRows.filter(item => {
        if (!!this.search.fromDate) {
          return moment(item.captured_at*1000) >= moment(this.search.fromDate)
        }
        if (!!this.search.toDate) {
          return moment(item.captured_at*1000) <= moment(this.search.toDate)
        }
        if (!!this.search.fromDate && !!this.search.toDate) {
          return moment(item.captured_at*1000) >= moment(this.search.fromDate) && moment(item.captured_at*1000) <= moment(this.search.toDate)
        }
        return true
      })
      this.rows = this.tempRows.filter((item, index) => {
        return index < 10
      })
      this.setPagination()
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
</style>