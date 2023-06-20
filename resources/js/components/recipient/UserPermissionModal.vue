<template>
  <div class="user_permission_modal">
    <div class="inner">
      <h4>ユーザー設定</h4>
      <a class="close_btn" @click="closeModal">
        <img src="/asset/icons/close.svg" />
      </a>
      <p class="note">利用者アカウント権限設定</p>
      <div class="table_cont">
        <vue-good-table
          :columns="columns"
          :rows="rows"
          :pagination-options="{
            enabled: false,
          }">
          <template slot="table-row" slot-scope="props">
            <div v-if="props.column.field == 'edit'">
              <input type="checkbox" :checked="props.row.show == 1" @change="changeShow($event, props.index)" />
            </div>
            <div v-else-if="props.column.field == 'show'">
              <input type="checkbox" :checked="props.row.control == 1" @change="changeControl($event, props.index)" />
            </div>
            <div class="table_cell" v-else>
              {{ props.formattedRow[props.column.field] }}
            </div>
          </template>
        </vue-good-table>
      </div>
      <div class="actions">
        <button @click="updatePermission">更新する</button>
        <button @click="closeModal">戻る</button>
      </div>
    </div>
  </div>
</template>
<script>
export default {
  props: ['permissions'],
  data() {
    return {
      columns: [
        {
          label: '利用者アカウント権限設定',
          field: 'screen',
          sortable: false
        },
        {
          label: '閲覧',
          field: 'show',
          sortable: false
        },
        {
          label: '操作',
          field: 'edit',
          sortable: false
        },
      ],
      rows: [
        {
          id: 1,
          screen: '受注管理',
        },
        {
          id: 2,
          screen: '出荷管理',
        },
        {
          id: 3,
          screen: '請求書管理',
        },
        {
          id: 4,
          screen: '注文フォーム',
        },
        {
          id: 5,
          screen: '商品管理',
        },
        {
          id: 6,
          screen: '項目設定',
        },
        {
          id: 7,
          screen: '取引先管理',
        },
        {
          id: 8,
          screen: '見積書管理',
        },
        {
          id: 9,
          screen: 'メール配信',
        },
        {
          id: 10,
          screen: 'アカウント設定（会社情報）',
        },
        {
          id: 11,
          screen: 'ー　ユーザー情報',
        },
      ],
    }
  },
  mounted() {
    this.init()
  },
  methods: {
    init() {
      let permissions = this.$props.permissions
      this.rows = this.rows.map((item, index) => {
        let temp = permissions.find(it => {
          return it.moduleNum == index + 1
        })
        if(!!temp) {
          return {
            ...item,
            show: temp.show,
            control: temp.control
          }
        } else {
          return item
        }
      })
    },

    changeShow(event, index) {
      let temp = this.rows.slice().map((item, idex) => {
        if (index == idex) {
          return {
            ...item,
            show: event.target.checked
          }
        } else {
          return item
        }
      })
      this.rows = temp
    },

    changeControl(event, index) {
      let temp = this.rows.slice().map((item, idex) => {
        if (index == idex) {
          return {
            ...item,
            control: event.target.checked
          }
        } else {
          return item
        }
      })
      this.rows = temp
    },

    closeModal() {
      this.$emit('closeModal')
    },

    async updatePermission() {
      try {
        await Promise.all(this.rows.map(async item => {
          await axios.post('/recipienter/update_permission', item)
        }))
        this.$swal('', '更新しました')
        this.$emit('closeModalWithRefresh')
      } catch (error) {
      }
    },
  }
}
</script>
<style lang="scss" scoped>
.user_permission_modal {
  position: fixed;
  left: 0;
  top: 0;
  width: 100%;
  height: 100%;
  z-index: 400;
  background-color: rgba($color: #000000, $alpha: 0.4);

  .inner {
    position: fixed;
    left: 50%;
    top: 50%;
    transform: translate(-50%, -50%);
    width: 370px;
    background-color: #fff;
    padding: 25px;
    z-index: 401;
    border: 1px solid #333;
    max-height: 80vh;
    overflow: auto;

    h4 {
      font-size: 14px;
      margin-bottom: 35px;
    }

    .note {
      font-size: 12px;
      margin-bottom: 10px;
    }
  }

  .close_btn {
    position: absolute;
    right: 10px;
    top: 10px;
    width: 24px;
    height: 24px;

    img {
      width: 100%;
    }
  }
}

.table_cont {
  margin-top: 25px;
  box-shadow: 0 3px 6px rgba($color: #000000, $alpha: 0.16);
}

.actions {
  padding-top: 30px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-direction: column;

  button {
    width: 180px;
    height: 35px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 15px;
    border: 0;

    &:first-of-type {
      background-color: var(--accent-color);
      color: #fff;
    }

    &:last-of-type {
      background-color: #fff;
      border: 1px solid #333;
      color: #333;
    }
  }
}
</style>