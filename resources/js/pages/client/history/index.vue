<template>
  <div class="main__container">
    <h2 class="page__title1">発注履歴</h2>

    <div class="table__header">
      <div class="search__form">
        <div class="d-flex">
          <div class="form__row d-flex">
            <input type="text" placeholder="キーワード" />
          </div>
          <div class="form__row mr-0 d-flex">
            <input type="date" />
          </div>
          <div class="deparate">~</div>
          <div class="form__row d-flex">
            <input type="date" />
          </div>
          <div class="form__action">
            <button>検索</button>
          </div>
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
          <div v-if="props.column.field == 'detail'">
            <button @click="detailScreen(props.row.id)" class="detail_btn">詳細</button>
          </div>
          <div v-else-if="props.column.field == 'delete'">
            <a class="delete_btn"><img src="/asset/icons/delete.svg" /></a>
          </div>
          <div v-else-if="props.column.field == 'payment_method'">
            <span>{{ props.row.payment_method == 1 ? '銀行振込' : 'クレジットカード' }}</span>
          </div>
          <div v-else-if="props.column.field == 'ordered_at'">
            {{ props.row.ordered_at | dateFormatFull }}
          </div>
          <div v-else-if="props.column.field == 'title'">
            {{ props.row.order_form.name }}
          </div>
          <div v-else-if="props.column.field == 'price'">
            ￥{{ props.row.price | moneyFormat }}
          </div>
          <div v-else-if="props.column.field == 'payment_status'">
            <span class="shipping_slip" :class="{ off: props.row.payed == 0 }">{{ props.row.payed == 1 ? '対応済' : '未対応' }}</span>
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
</template>
<script>
export default {
  layout: 'client',
  data() {
    return {
      columns: [
        {
          label: '注文番号',
          field: 'hash',
          sortable: false,
        },
        {
          label: '注文日時',
          field: 'ordered_at',
          sortable: false,
        },
        {
          label: '注文フォーム名',
          field: 'title',
          sortable: false,
        },
        {
          label: '発注金額',
          field: 'price',
          sortable: false,
        },
        {
          label: '支払い方法',
          field: 'payment_method',
          sortable: false,
        },
        {
          label: '支払いステータス',
          field: 'payment_status',
          sortable: false,
        },
        {
          label: '',
          field: 'detail',
          sortable: false,
        },
      ],
      rows: [],
      order_histories: [],
      pageCount: 1,
    }
  },
  mounted() {
    this.init()
  },
  methods: {
    async init() {
      try {
        const { data } = await axios.post('/api/get_order_history')
        this.order_histories = data.order_histories
        this.rows = this.order_histories.filter((item, index) => {
          return index < 10
        })
        this.setPagination()
      } catch (error) {
      }
    },
    setPagination() {
      if (this.order_histories.length % 10 > 0) {
        this.pageCount = parseInt(this.order_histories.length / 10) + 1
      } else {
        this.pageCount = parseInt(this.order_histories.length / 10)
      }
    },
    clickPage(params) {
      this.rows = this.order_histories.filter((item, index) => {
        return index >= (params - 1) * 10 && index < params * 10
      })
    },
    detailScreen(id) {
      this.$router.push({ name: 'client.history.detail', query: { id: id } })
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
    align-items: flex-end;
    margin-bottom: 12px;
    margin-top: 20px;

    .search__form {
      width: 850px;
      border: 1px solid var(--border-color);
      padding: 8px 10px;
      background-color: #fff;
      border-radius: 5px;
      margin-bottom: 30px;

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
        margin-bottom: 35px;

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
    padding-top: 0px;
    width: 800px;
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

  &.send {
    background-color: var(--accent-color);
  }
}

.delete_btn {
  display: flex;
}

.select {
  width: 15px;
  height: 15px;
  border: 1px solid var(--border-color);
}
</style>