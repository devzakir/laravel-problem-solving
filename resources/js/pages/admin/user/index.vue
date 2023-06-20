<template>
  <div class="main__container">
    <div class="table__header">
      <div class="search__form">
        <div class="d-flex mb-14">
          <div class="form__row d-flex">
            <input v-model="search.keyword" type="text" placeholder="キーワード" />
          </div>
          <div class="form__row mr-0 d-flex">
            <input v-model="search.from" type="date" />
          </div>
          <div class="deparate">~</div>
          <div class="form__row d-flex">
            <input v-model="search.to" type="date" />
          </div>
          <div class="form__action">
            <button @click="searchProc">検索</button>
          </div>
        </div>
      </div>
      <div class="exort__part">
        <a class="append_btn" @click="showCreateModal"><img src="/asset/icons/append.svg" />アカウント登録</a>
        <a class="platform_btm" @click="toPlatformSettings">プラットフォーム設定</a>
        <div class="total_count">
          {{ rows.length }}件
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
          <div v-if="props.column.field == 'user_status'">
            <span class="status" :class="{ active: props.row.status === 1 }">{{ props.row.status === 1 ? "有効" : "停止中" }}</span>
          </div>
          <div v-if="props.column.field == 'user_amount'">
            {{ !!props.row.child_users ? props.row.child_users.length : 0 }}
          </div>
          <div v-if="props.column.field == 'transaction_amount'">
            {{ !!props.row.users ? props.row.users.length : 0 }}
          </div>
          <div v-else-if="props.column.field == 'detail'">
            <button @click="detail(props.row.id)" class="detail_btn">詳細</button>
          </div>
          <div v-else-if="props.column.field == 'stop'">
            <a class="block_user" @click="blockUser(props.row.id, props.row.status)">
              {{ props.row.status === 1 ? '機能停止' : '復元' }}
            </a>
          </div>
          <div class="menu" v-else-if="props.column.field == 'menu'">
            <select>
              <option>>見積作成</option>
              <option>>>フォーム案内</option>
              <option>>見積作成</option>
            </select>
          </div>
          <div v-else-if="props.column.field == 'delete'">
            <a @click="deleteUser(props.row.id)" class="delete_btn"><img src="/asset/icons/delete.svg" /></a>
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

    <AccountCreate @closeModal="closeModal" v-if="isShowCreateModal" />
  </div>
</template>
<script>
import AccountCreate from "../../../components/admin/modal/AccountCreate.vue"
export default {
  layout: 'admin',
  middleware: 'admin',
  components: {
    AccountCreate
  },
  data() {
    return {
      search: {
        keyword: '',
        from: null,
        to: null
      },
      isShowCreateModal: false,
      columns: [
        {
          label: 'ステータス',
          field: 'user_status',
        },
        {
          label: '会社名',
          field: 'name',
        },
        {
          label: 'ログインID',
          field: 'email',
        },
        {
          label: 'ユーザー数',
          field: 'user_amount',
        },
        {
          label: '取引先数',
          field: 'transaction_amount',
        },
        {
          label: '詳細',
          field: 'detail',
          sortable: false,
        },
        {
          label: '機能停止',
          field: 'stop',
          sortable: false,
        },
        {
          label: '削除',
          field: 'delete',
          sortable: false,
        },
      ],
      rows: [],
      pageCount: 1
    }
  },
  mounted() {
    this.init()
  },
  methods: {
    toPlatformSettings() {
      this.$router.push({ name: 'admin.platform' })
    },
    async searchProc() {
      this.init()
    },
    async init() {
      try {
        const { data } = await axios.post('/admin/get_recipienter_list', this.search)
        this.rows = data.recipienters
        this.setPagination()
      } catch (error) {
      }
    },  
    clickPage(params) {
      console.log(params)
    },

    detail(user_id) {
      this.$router.push({ name: 'admin.user.detail', query: { id: user_id } })
    },

    setPagination() {
      if (this.rows.length % 10 > 0) {
        this.pageCount = parseInt(this.rows.length / 10) + 1
      } else {
        this.pageCount = parseInt(this.rows.length / 10)
      }
    },

    showCreateModal() {
      this.isShowCreateModal = true
    },

    closeModal() {
      this.isShowCreateModal = false
    },

    async blockUser(user_id, status) {
      try {
        const { data } = await axios.post('/admin/block_user', {
          user_id: user_id,
          status: (1 + status) % 2
        })
        this.rows = data.recipienters
      } catch (error) {
      }
    },

    async deleteUser(user_id) {
      try {
        const { data } = await axios.post('/admin/delete_user', {
          user_id: user_id
        })
        this.rows = data.recipienters
      } catch (error) {
      }
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
        width: 150px;
        height: 30px;
        background-color: #985298;
        color: #fff;
        font-size: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0px 3px 6px rgba($color: #000000, $alpha: 0.16);
        font-weight: 600;
        margin-bottom: 10px;

        img {
          margin-right: 4px;
        }

        &:hover {
          color: #fff;
        }
      }

      .platform_btm {
        margin-left: auto;
        width: 150px;
        height: 30px;
        background-color: #fff;
        color: #333;
        font-size: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0px 3px 6px rgba($color: #000000, $alpha: 0.16);
        font-weight: 600;
        margin-bottom: 35px;
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
    width: 90px;
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

.status {
  width: 60px;
  height: 20px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #fff;
  font-size: 12px;
  background-color: #707070;

  &.active {
    background-color: #337DFD;
  }
}

.block_user {
  border: 1px solid var(--border-color);
  color: #333;
  padding-left: 10px;
  padding-right: 10px;
  height: 24px;
  line-height: 24px;
  display: flex;
  align-items: center;
  justify-content: center;
  width: fit-content;
}
</style>