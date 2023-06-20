<template>
  <div class="main__container">
    <h2 class="page__title1">発注先一覧</h2>

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
          <div v-if="props.column.field == 'order'">
            <button v-if="props.row.is_public == 1" @click="orderProc(props.row.id)" class="detail_btn send">発注</button>
            <p v-else>現在ご利用いただけません。</p>
          </div>
          <div v-else-if="props.column.field == 'order_form_name'">
          {{ props.row.name }}
          </div>
          <div v-else-if="props.column.field == 'updated_at'">
          {{ calcLatestOrderTime(props.row) }}
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

    <ConfirmModal 
      v-if="isShowModal" 
      :note="'本当に発注しますか？'" 
      :cancelText="'キャンセル'" 
      :okText="'発注する'"
      @cancel="cancelOrderConfirm"
      @ok="okOrderConfirm"/>
  </div>
</template>
<script>
import moment from 'moment'
import ConfirmModal from '../../../components/common/ConfirmModal.vue'
export default {
  layout: 'client',
  components: {
    ConfirmModal
  },
  data() {
    return {
      columns: [
        {
          label: '最終発注日',
          field: 'updated_at',
          sortable: false,
        },
        {
          label: '注文フォーム名',
          field: 'order_form_name',
          sortable: false,
        },
        {
          label: '発注',
          field: 'order',
          sortable: false,
        },
      ],
      orderForms: [],
      rows: [],
      isShowModal: false,
      tempOrderId: null,
      pageCount: 1,
    }
  },
  mounted() {
    this.init()
  },
  methods: {
    calcLatestOrderTime(orderForm) {
      let orders = orderForm.orders
      if (!!orders && orders.length > 0) {
        return moment(orders[0].ordered_at).format('YYYY年MM月DD日')
      } else {
        return '発注なし'
      }
    },
    async init() {
      try {
        const { data } = await axios.post('/api/get_order_form')
        this.orderForms = data.orderForms
        this.rows = this.orderForms.filter((item, index) => {
          return index < 10
        })
        this.setPagination()
      } catch (error) {
      }
    },
    setPagination() {
      if (this.orderForms.length % 10 > 0) {
        this.pageCount = parseInt(this.orderForms.length / 10) + 1
      } else {
        this.pageCount = parseInt(this.orderForms.length / 10)
      }
    },
    clickPage(params) {
      this.rows = this.orderForms.filter((item, index) => {
        return index >= (params - 1) * 10 && index < params * 10
      })
    },
    orderProc(id) {
      // this.isShowModal = true
      // this.tempOrderId = id
      this.$router.push({ name: 'client.order.detail', query: { id: id } })
    },
    cancelOrderConfirm() {
      this.isShowModal = false
      this.tempOrderId = null
    },
    async okOrderConfirm() {
      // this.isShowModal = false
      // try {
      //   const { data } = await axios.post('/api/order_confirm_proc', {
      //     id: this.tempOrderId
      //   })
      //   this.init()
      // } catch (error) {
      // }
    }
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
    width: 650px;
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