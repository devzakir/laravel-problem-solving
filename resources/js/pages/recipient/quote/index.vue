<template>
  <div class="main__container">
    <h2 class="page__title">見積書管理</h2>

    <div class="table__header">
      <div class="search__form">
        <div class="d-flex mb-14">
          <div class="form__row d-flex">
            <input type="text" v-model="search.keyword" placeholder="キーワード" />
          </div>
          <div class="form__row mr-0 d-flex">
            <input type="date" v-model="search.fromDate" />
          </div>
          <div class="deparate">~</div>
          <div class="form__row d-flex">
            <input type="date" v-model="search.toDate"/>
          </div>
        </div>
        <div class="d-flex">
          <div class="form__row d-flex">
            <label>ステータス：</label>
            <select>
              <option>すべて</option>
              <option>すべて</option>
              <option>すべて</option>
            </select>
          </div>
          <div class="form__action">
            <button @click="init">検索</button>
          </div>
        </div>
      </div>
      <div class="exort__part">
        <router-link :to="{ name: 'recipient.quote.create' }" class="append_btn"><img src="/asset/icons/append.svg" />新規作成</router-link>
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
          <div class="toggle" v-if="props.column.field == 'status'">
            <toggle-button :width="88" :value="props.row.status == 1" disabled :color="{unchecked: '#707070', checked: '#337DFD'}" :labels="{unchecked: '送信前', checked: '送信済'}"/>
          </div>
          <div v-else-if="props.column.field == 'detail'">
            <button @click="toDetailScreen(props.row.id)" class="detail_btn">詳細</button>
          </div>
          <div v-else-if="props.column.field == 'publish_date'">
          {{ props.row.publish_date | dateFormatFull }}
          </div>
          <div v-else-if="props.column.field == 'delete'">
            <a @click="deleteProc" class="delete_btn"><img src="/asset/icons/delete.svg" /></a>
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
          label: 'ステータス',
          field: 'status',
        },
        {
          label: '見積書番号',
          field: 'hash',
        },
        {
          label: '作成日時',
          field: 'publish_date',
        },
        {
          label: '件名',
          field: 'subject_title',
        },
        {
          label: '宛先',
          field: 'com_name',
        },
        {
          label: '',
          field: 'detail',
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
      search: {
        keyword: null,
        fromDate: null,
        toDate: null
      },
      pageCount: 1,
      isShowModal: false,
      tempId: null
    }
  },
  mounted() {
    this.init()
  },
  methods: {
    async init() {
      try {
        const { data } = await axios.post('/recipienter/get_quote_list_info', this.search)
        this.tempRows = data.quotes
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
    clickPage(params) {
      this.rows = this.tempRows.filter((item, index) => {
        return index >= (params - 1) * 10 && index < params * 10
      })
    },
    toDetailScreen(id) {
      this.$router.push({ name: 'recipient.quote.detail', query: { id: id } })
    },
    deleteProc(id) {
      this.tempId = id
      this.isShowModal = true
    },
    cancelDeleteConfirm() {
      this.isShowModal = false
      this.tempId = null
    },
    async okDeleteConfirm() {
      this.isShowModal = false
      try {
        const { data } = await axios.post('/recipienter/quote_delete_proc', {
          id: this.tempId
        })
        this.$swal('', '削除しました')
        this.init()
      } catch (error) {}
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
</style>