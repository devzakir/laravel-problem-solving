<template>
  <div class="kizon_order_modal">
    <div class="mask"></div>
    <div class="modal__inner">
      <div class="search_form">
        <input type="text" placeholder="キーワード" />
        <button>検索</button>
      </div>
      <div class="count">
        {{ rows.length }}件
      </div>
      <div class="order_history_table">
        <vue-good-table
          :columns="columns"
          :rows="rows"
          :pagination-options="{
            enabled: false,
          }">
          <template slot="table-row" slot-scope="props">
            <div class="created_updated" v-if="props.column.field == 'created_updated'">
              <p>{{ props.row.created_at | dateFormat }}</p>
              <p>{{ props.row.updated_at | dateFormat }}</p>
            </div>
            <div class="transaction_contact" v-else-if="props.column.field == 'transaction_contact'">
              <p>TEL:{{ props.row.telephone }}</p>
              <p>メール：{{ props.row.email }}</p>
            </div>
            <div class="action" v-else-if="props.column.field == 'action'">
              <button @click="select(props.row.id)">選択して次へ</button>
            </div>
            <div class="table_cell" v-else>
              {{ props.formattedRow[props.column.field] }}
            </div>
          </template>
        </vue-good-table>
      </div>
    </div>
  </div>
</template>
<script>
import Axios from 'axios'

export default {
  data() {
    return {
      columns: [
        {
          label: '作成日時/最終変更日時',
          field: 'created_updated',
        },
        {
          label: '取引先番号',
          field: 'id',
        },
        {
          label: '取引先名',
          field: 'name',
        },
        {
          label: '連絡先',
          field: 'transaction_contact',
        },
        {
          label: '',
          field: 'action',
          sortable: false
        },
      ],
      rows: []
    }
  },
  mounted() {
    this.init()
  },
  methods: {
    async init() {
      try {
        const { data } = await axios.post('/recipienter/get_client_list')
        this.rows = data.users
      } catch (error) {
      }
    },
    select(user_id) {
      this.$emit('onSelect', user_id)
    },
    clickPage() {

    }
  }
}
</script>
<style lang="scss" scoped>
.mask {
  position: fixed;
  left: 0;
  top: 0;
  width: 100%;
  height: 100%;
  background-color: rgba($color: #000000, $alpha: 0.7);
  z-index: 200;
}

.modal__inner {
  width: 800px;
  background-color: #fff;
  position: fixed;
  left: 50%;
  top: 50%;
  transform: translate(-50%, -50%);
  padding: 15px;
  z-index: 201;
  border-radius: 10px;

  .search_form {
    width: fit-content;
    display: flex;
    align-items: center;
    border: 1px solid var(--border-color);
    padding: 10px;

    input {
      height: 30px;
      width: 200px;
      border: 1px solid var(--border-color);
      padding-left: 10px;
      padding-right: 10px;
      font-size: 12px;
      margin-right: 40px;
    }

    button {
      width: 120px;
      height: 30px;
      display: flex;
      align-items: center;
      justify-content: center;
      background-color: var(--accent-color);
      color: #fff;
      font-size: 12px;
      border: 0;
    }
  }

  .count {
    text-align: right;
    font-size: 12px;
  }

  .action {
    button {
      width: 90px;
      height: 24px;
      display: flex;
      align-items: center;
      justify-content: center;
      background-color: var(--accent-color);
      color: #fff;
      font-size: 10px;
      border: 0;
    }
  }
}
</style>