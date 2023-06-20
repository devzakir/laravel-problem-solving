<template>
  <div class="main__container">
    <h2 class="page__title">取引先管理</h2>

    <div class="table__header">
      <div class="search__form">
        <div class="d-flex mb-14">
          <div class="form__row d-flex">
            <input type="text" placeholder="キーワード" v-model="search.keyword" />
          </div>
          <div class="form__row mr-0 d-flex">
            <input type="date" v-model="search.fromDate" />
          </div>
          <div class="deparate">~</div>
          <div class="form__row d-flex">
            <input type="date" v-model="search.toDate" />
          </div>
          <div class="form__action">
            <button @click="init">検索</button>
          </div>
        </div>
      </div>
      <div class="exort__part">
        <router-link :to="{ name: 'recipient.customer.create' }" class="append_btn"><img src="/asset/icons/append.svg" />取引先追加</router-link>
        <div class="total_count">
          {{ tempRows.length }}件
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
          <div v-if="props.column.field == 'created_at'">
            <p>{{ props.row.created_at | dateFormatFull }}</p>
            <p>{{ props.row.updated_at | dateFormatFull }}</p>
          </div>
          <div v-else-if="props.column.field == 'contact_info'">
            <p>{{ props.row.telephone }}</p>
            <p>{{ props.row.email }}</p>
          </div>
          <div v-else-if="props.column.field == 'edit'">
            <button @click="toEditScreen(props.row.id)" class="detail_btn">編集</button>
          </div>
          <div class="menu" v-else-if="props.column.field == 'menu'">
            <select @change="changeMenu($event, props.row.id)">
              <option>選択して下さい</option>
              <option :value="1">>見積作成</option>
              <option :value="2">>>フォーム案内</option>
            </select>
          </div>
          <div v-else-if="props.column.field == 'delete'">
            <a @click="deleteProc(props.row.id)" class="delete_btn"><img src="/asset/icons/delete.svg" /></a>
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
      :note="'削除します。よろしいでしょうか'" 
      :cancelText="'キャンセル'" 
      :okText="'削除する'"
      @cancel="cancelDeleteConfirm"
      @ok="okDeleteConfirm"/>
  </div>
</template>
<script>
import ConfirmModal from '../../../components/common/ConfirmModal.vue'
import moment from 'moment'
export default {
  layout: 'recipient',
  middleware: 'recipient',
  components: {
    ConfirmModal
  },
  data() {
    return {
      columns: [
        {
          label: '作成日時/最終変更日時',
          field: 'created_at',
        },
        {
          label: '取引先番号',
          field: 'id',
        },
        {
          label: '取引先名',
          field: 'com_name',
        },
        {
          label: '連絡先',
          field: 'contact_info',
        },
        {
          label: 'メニュー',
          field: 'menu',
          sortable: false,
        },
        {
          label: '編集',
          field: 'edit',
          sortable: false,
        },
        {
          label: '削除',
          field: 'delete',
          sortable: false,
        },
      ],
      rows: [],
      tempRows: [],
      isShowModal: false,
      pageCount: 1,

      search: {
        keyword: null,
        fromDate: null,
        toDate: null
      },
    }
  },
  methods: {
    clickPage(params) {
      this.rows = this.tempRows.filter((item, index) => {
        return index >= (params - 1) * 10 && index < params * 10
      })
    },
    toEditScreen(id) {
      this.$router.push({ name: 'recipient.customer.edit', query: { id: id } })
    },
    async init() {
      try {
        const { data } = await axios.post('/recipienter/get_customers', this.search)
        this.tempRows = data.users
        this.rows = this.tempRows.filter((item, index) => {
          return index < 10
        })
        this.setPagination()
      } catch (error) {
      }
    },
    setPagination() {
      if (this.tempRows.length % 10 > 0) {
        this.pageCount = parseInt(this.tempRows.length / 10) + 1
      } else {
        this.pageCount = parseInt(this.tempRows.length / 10)
      }
    },
    cancelDeleteConfirm() {
      this.isShowModal = false
    },
    async okDeleteConfirm() {
      try {
        const { data } = await axios.post('/recipienter/delete_customer', {
          id: this.temp_id
        })
        this.init()
      } catch (error) {
      }
    },
    deleteProc(id) {
      this.temp_id = id
      this.isShowModal = true
    },
    changeMenu(event, client_id) {
      if (event.target.value == 1) {
        this.$router.push({ name: 'recipient.quote.create', query: { client_id: client_id } })
      } else if (event.target.value == 2) {
        this.$router.push({ name: 'recipient.order_form.message.create', query: { client_id: client_id } })
      }
    }
  },
  mounted() {
    this.init()
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

.menu {
  select {
    width: 130px;
    height: 20px;
    background-color: var(--accent-color);
    color: #fff;
    font-size: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 0;
    text-align: center;
  }
}
</style>