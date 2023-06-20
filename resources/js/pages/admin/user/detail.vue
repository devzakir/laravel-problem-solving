<template>
  <div class="setting__container">
    <div class="main__part" v-if="!!recipienter">
      <div class="menu_part">
        <h3>{{ recipienter.name }}<br/>{{ recipienter.type == 1 ? 'マスタアカウント' : recipienter.tanto_name }}様</h3>
        <a :class="{ current: tab == 1 }" @click="() => { tab = 1 }">ユーザー一覧<span v-if="tab == 1"></span></a>
        <a :class="{ current: tab == 2 }" @click="() => { tab = 2 }">取引先一覧<span v-if="tab == 2"></span></a>
      </div>
      <div class="main_panel">
        <h2 class="page__title black d-flex">{{ tab == 1 ? 'ステータス' : 'ステータス' }}
          <span class="stauts" :class="{ on: recipienter.status == 1 }">{{ recipienter.status == 1 ? '稼働中' : '停止中' }}</span>
          <button @click="toggleStatus" class="restore">{{ recipienter.status == 1 ? 'アカウントを停止する' : 'アカウントを有効化する' }}</button>
        </h2>
        <div class="table_cont" v-if="tab == 1">
          <vue-good-table
            :columns="columns"
            :rows="rows"
            :pagination-options="{
              enabled: false,
            }">
            <template slot="table-row" slot-scope="props">
              <div v-if="props.column.field == 'edit'">
                <button @click="editRecipienter(props.row)" class="detail_btn">編集</button>
              </div>
              <div v-else-if="props.column.field == 'user_type'">
                {{ !!props.row.parent_id ? '利用者アカウント' : 'マスターアカウント' }}
              </div>
              <div v-else-if="props.column.field == 'delete'">
                <a @click="removeRecipienter(props.row.id)" class="delete_btn"><img src="/asset/icons/delete.svg" /></a>
              </div>
              <div class="table_cell" v-else>
                {{ props.formattedRow[props.column.field] }}
              </div>
            </template>
          </vue-good-table>
        </div>
        <div class="table_cont" v-if="tab == 2">
          <vue-good-table
            :columns="columns1"
            :rows="rows1"
            :pagination-options="{
              enabled: false,
            }">
            <template slot="table-row" slot-scope="props">
              <div v-if="props.column.field == 'edit'">
                <button @click="editClient(props.row)" class="detail_btn">編集</button>
              </div>
              <div v-else-if="props.column.field == 'delete'">
                <a @click="removeUser(props.row.id)" class="delete_btn"><img src="/asset/icons/delete.svg" /></a>
              </div>
              <div class="table_cell" v-else>
                {{ props.formattedRow[props.column.field] }}
              </div>
            </template>
          </vue-good-table>
        </div>
      </div>
    </div>
    <Spinner v-if="isLoading" />
    <ConfirmModal
      v-if="isShowDeleteModal"
      :note="'本当に削除しますか？'"
      :cancelText="'キャンセル'"
      :okText="'削除する'"
      @cancel="cancelDeleteRecipienter"
      @ok="okDeleteRecipienter" />

    <ConfirmModal
      v-if="isShowDeleteModal1"
      :note="'本当に削除しますか？'"
      :cancelText="'キャンセル'"
      :okText="'削除する'"
      @cancel="cancelDeleteRecipienter1"
      @ok="okDeleteRecipienter1" />

    <div class="mask" v-if="isShowEditing || isShowEditing1"></div>
    <div class="edit_modal" v-if="isShowEditing">
      <h2 class="page__title">ユーザー情報</h2>
      <a @click="closeEditingModal" class="close_btn">
        <img src="/asset/icons/close.svg" />
      </a>
      <div class="form__part">
        <div class="form__row">
          <label>担当者名</label>
          <p>
            <input type="text" v-model="edit.name" />
          </p>
        </div>
        <div class="form__row">
          <label>パスワード</label>
          <p>
            <input type="password" v-model="edit.password" />
          </p>
        </div>
      </div>
      <div class="form__actions">
        <button @click="closeEditingModal">戻る</button>
        <button @click="saveEditing">保存</button>
      </div>
    </div>
    <div class="edit_modal" v-if="isShowEditing1">
      <h2 class="page__title">取引先情報</h2>
      <a @click="closeEditingModal1" class="close_btn">
        <img src="/asset/icons/close.svg" />
      </a>
      <div class="form__part">
        <div class="form__row">
          <label>会社名</label>
          <p>
            <input type="text" v-model="edit1.com_name" />
          </p>
        </div>
      </div>
      <div class="form__actions">
        <button @click="closeEditingModal1">戻る</button>
        <button @click="saveEditing1">保存</button>
      </div>
    </div>
  </div>
</template>
<script>
import Spinner from "~/components/common/Spinner"
import ConfirmModal from "~/components/common/ConfirmModal"
export default {
  layout: 'admin',
  middleware: 'admin',
  components: {
    Spinner,
    ConfirmModal
  },
  data() {
    return {
      tab: 1,
      columns: [
        {
          label: 'ユーザー種別',
          field: 'user_type',
          sortable: false
        },
        {
          label: '利用者名',
          field: 'tanto_name',
          sortable: false
        },
        {
          label: 'ユーザーID（アドレス）',
          field: 'email',
          sortable: false
        },
        {
          label: '編集',
          field: 'edit',
          sortable: false
        },
        {
          label: '削除',
          field: 'delete',
          sortable: false
        },
      ],
      columns1: [
        {
          label: '会社名',
          field: 'com_name',
          sortable: false
        },
        {
          label: 'ユーザーID（アドレス）',
          field: 'email',
          sortable: false
        },
        {
          label: '編集',
          field: 'edit',
          sortable: false
        },
        {
          label: '削除',
          field: 'delete',
          sortable: false
        },
      ],
      rows: [],
      rows1: [],
      recipienter: null,
      isLoading: false,
      tempRecipienterId: null,
      isShowDeleteModal: false,
      isShowEditing: false,

      edit: {
        id: null,
        name: "",
        password: ""
      },

      edit1: {
        id: null,
        com_name: ""
      },

      isShowDeleteModal1: false,
      tempUserId: null,

      isShowEditing1: false
    }
  },
  mounted() {
    this.init()
  },
  methods: {
    async init() {
      try {
        this.isLoading = true
        const { data } = await axios.post('/admin/get_recipienter_detail', {
          id: this.$route.query.id
        })
        this.recipienter = data.recipienter
        this.rows = data.recipienters || []
        this.rows1 = data.recipienter.users || []
        this.isLoading = false
      } catch (error) {
      }
    },

    removeRecipienter(recipienter_id) {
      this.tempRecipienterId = recipienter_id
      this.isShowDeleteModal = true
    },

    removeUser(user_id) {
      this.tempUserId = user_id
      this.isShowDeleteModal1 = true
    },

    cancelDeleteRecipienter() {
      this.isShowDeleteModal = false
    },

    cancelDeleteRecipienter1() {
      this.isShowDeleteModal1 = false
    },

    async okDeleteRecipienter() {
      try {
        const { data } = await axios.post('/admin/remove_recipienter', {
          id: this.tempRecipienterId
        })
        this.init()
      } catch (error) {
      }
    },

    async okDeleteRecipienter1() {
      try {
        const { data } = await axios.post('/admin/remove_client', {
          id: this.tempUserId
        })
        this.init()
      } catch (error) {
      }
    },

    closeEditingModal1() {
      this.isShowEditing1 = false
    },

    editRecipienter(recipienter) {
      this.edit.id = recipienter.id
      this.edit.name = recipienter.tanto_name
      this.isShowEditing = true
    },

    editClient(client) {
      this.edit1.id = client.id
      this.edit1.com_name = client.com_name
      this.isShowEditing1 = true
    },

    closeEditingModal() {
      this.isShowEditing = false
    },

    async saveEditing() {
      try {
        const { data } = await axios.post('/admin/save_editing_recipienter_info', this.edit)
        this.isShowEditing = false
        this.init()
      } catch (error) {
      }
    },

    async saveEditing1() {
      try {
        const { data } = await axios.post('/admin/save_editing_client_info', this.edit1)
        this.isShowEditing1 = false
        this.init()
      } catch(error) {
      }
    },

    async toggleStatus() {
      try {
        this.isLoading = true
        const { data } = await axios.post('/admin/block_user', {
          user_id: this.$route.query.id,
          status: (1 + this.recipienter.status) % 2
        })
        this.recipienter = {
          ...this.recipienter,
          status: (1 + this.recipienter.status) % 2
        }
        this.isLoading = false
      } catch (error) {
      }
    }
  }
}
</script>
<style lang="scss" scoped>
.setting__container {
  padding: 20px 30px;
}

.main__part {
  display: flex;
  margin-top: 40px;
  align-items: flex-start;

  .menu_part {
    width: 200px;
    border: 1px solid var(--border-color);
    
    h3 {
      font-size: 16px;
      text-align: center;
      height: 80px;
      width: 100%;
      line-height: 1.6;
      padding-top: 14px;
      border-bottom: 1px solid var(--border-color);
      position: relative;

      &:after {
        content: "";
        position: absolute;
        left: 0;
        width: 10px;
        background-color: var(--accent-color);
        top: 0;
        height: 100%
      }
    }

    a {
      display: flex;
      align-items: center;
      height: 40px;
      background-color: #fff;
      padding-left: 21px;
      padding-right: 10px;
      font-size: 14px;
      position: relative;
      border-bottom: 1px solid var(--border-color);
      color: var(--text-color);

      img {
        margin-left: auto;
      }

      &:before {
        content: "";
        position: absolute;
        left: 0;
        width: 10px;
        background-color: var(--accent-color);
        top: 0;
        height: 100%
      }

      &.current {
        &:before {
          content: "";
          position: absolute;
          left: 0;
          width: 10px;
          background-color: #48515D;
          top: 0;
          height: 100%
        }
      }

      span {
        margin-left: auto;
        border-top: 6px solid transparent;
        border-bottom: 6px solid transparent;
        border-left: 12px solid var(--accent-color);
      }
    }
  }

  .main_panel {
    padding: 14px 20px;
    background-color: #fff;
    border: 1px solid var(--border-color);
    width: 850px;
    position: relative;

    .page__title {
      font-size: 14px;
    }
  }
}

.table_cont {
  margin-top: 25px;
  box-shadow: 0 3px 6px rgba($color: #000000, $alpha: 0.16);
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

.seal_part {
  margin-top: 60px;
  display: flex;

  .label {
    border: 1px solid var(--border-color);
    height: 80px;
    width: 160px;
    display: flex;
    align-items: center;
    padding-left: 10px;
  }

  .value {
    width: 160px;
    height: 80px;
    border: 1px solid var(--border-color);
    display: flex;
    align-items: center;
    padding-left: 10px;
    
    .seal {
      width: 50px;
      height: 50px;
      border: 1px dotted var(--border-color);
      display: flex;
      align-items: center;
      justify-content: center;
      margin-right: auto;

      span {
        font-size: 8px;
      }
    }

    &>div {
      margin-right: 25px;
      display: flex;
      align-items: center;
      justify-content: center;
      flex-direction: column;

      button {
        font-size: 12px;
        background-color: var(--border-color);
        color: #fff;
        border: 0;
        width: 50px;
        height: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 8px;
      }

      a {
        width: 18px;
        height: 18px;
      }
    }
  }
}

.append_btn {
  position: absolute;
  right: 20px;
  top: 30px;
  display: flex;
  align-items: center;
  justify-content: center;
  width: 120px;
  height: 30px;
  background-color: #985298;
  color: #fff;
  font-size: 12px;
  border: 0;

  img {
    width: 20px;
    margin-right: 4px;
  }
}

.page__title {
  align-items: center;
}
.stauts {
  margin-left: 50px;
  width: 60px;
  height: 20px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #fff;
  background-color: var(--border-color);
  margin-right: 50px;
  font-size: 12px;

  &.on {
    background-color: var(--accent-color);
  }
}
.restore {
  width: 180px;
  height: 20px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #fff;
  font-size: 12px;
  background-color: #D34555;
  border: 0;
  font-weight: bold;  
}

.mask {
  position: fixed;
  left: 0;
  top: 0;
  width: 100%;
  height: 100%;
  z-index: 300;
  background-color: rgba($color: #000000, $alpha: 0.5);
}

.edit_modal {
  position: fixed;
  width: 500px;
  background-color: #fff;
  padding: 25px;
  left: 50%;
  z-index: 301;
  top: 50%;
  transform: translate(-50%, -50%);
  border: 1px solid var(--border-color);

  .close_btn {
    position: absolute;
    top: 20px;
    right: 20px;
    width: 20px;
    height: 20px;

    img {
      width: 100%;
    }
  }

  .form__part {
    margin-top: 30px;
    padding: 20px 10px;
    border: 1px solid var(--border-color);
    background-color: #F3F3F3;

    .form__row {
      display: flex;
      align-items: center;

      label {
        width: 170px;
      }

      p {
        flex: 1;
        input {
          width: 100%;
          border: 1px solid var(--border-color);
          border-radius: 4px;
          height: 30px;
        }
      }
    }
  }

  .form__actions {
    display: flex;
    align-items: center;
    justify-content: center;
    padding-top: 20px;

    button {
      width: 180px;
      height: 35px;
      display: flex;
      align-items: center;
      justify-content: center;

      &:first-of-type {
        background-color: #fff;
        border: 1px solid var(--border-color);
        box-shadow: 0 3px 6px rgba($color: #000000, $alpha: 0.16);
        margin-right: 50px;
      }

      &:last-of-type {
        background-color: var(--accent-color);
        border: 0;
        color: #fff;
      }
    }
  }
}
</style>