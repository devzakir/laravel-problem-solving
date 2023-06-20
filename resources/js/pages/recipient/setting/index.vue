<template>
  <div class="setting__container">
    <h2 class="page__title">アカウント設定</h2>
    
    <div class="main__part">
      <div class="menu_part">
        <h3>{{ $store.getters['recipienter_auth/user'].name }}<br/>{{ $store.getters['recipienter_auth/user'].type == 1 ? 'マスターアカウント' : $store.getters['recipienter_auth/user'].tanto_name + '様' }}</h3>
        <a :class="{ current: tab == 1 }" @click="() => { tab = 1 }">会社情報<span v-if="tab == 1"></span></a>
        <a :class="{ current: tab == 2 }" @click="() => { tab = 2 }">ユーザー一覧<span v-if="tab == 2"></span></a>
        <a :class="{ current: tab == 3 }" @click="() => { tab = 3 }">ユーザー設定<span v-if="tab == 3"></span></a>
        <a :class="{ current: tab == 4 }" @click="() => { tab = 4 }">クレジットカード<span v-if="tab == 4"></span></a>
      </div>
      <div class="main_panel" v-if="!!recipienter">
        <h2 class="page__title black">{{ calcTitle() }}</h2>
        <button class="append_btn" v-if="tab == 2" @click="appendUser"><img src="/asset/icons/append.svg" />ユーザー追加</button>
        <div class="table_cont" v-if="tab == 1">
          <vue-good-table
            :columns="columns"
            :rows="rows"
            :pagination-options="{
              enabled: false,
            }">
            <template slot="table-row" slot-scope="props">
              <div v-if="props.column.field == 'edit'">
                <button @click="editProfile" class="detail_btn">編集</button>
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
                <button @click="editUserProc(props.row.id)" class="detail_btn">編集</button>
              </div>
              <div v-else-if="props.column.field == 'delete'">
                <a @click="deleteUserProc(props.row.id)" class="delete_btn"><img src="/asset/icons/delete.svg" /></a>
              </div>
              <div v-else-if="props.column.field == 'user_type'">
                利用者アカウント
              </div>
              <div class="table_cell" v-else>
                {{ props.formattedRow[props.column.field] }}
              </div>
            </template>
          </vue-good-table>
        </div>
        <div class="table_cont table_cont1" v-if="tab == 3">
          <vue-good-table
            :columns="columns2"
            :rows="rows2"
            :pagination-options="{
              enabled: false,
            }">
            <template slot="table-row" slot-scope="props">
              <div v-if="props.column.field == 'edit'">
                <input type="checkbox" :checked="props.row.show == 1" disabled />
              </div>
              <div v-else-if="props.column.field == 'show'">
                <input type="checkbox" :checked="props.row.control == 1" disabled />
              </div>
              <div class="table_cell" v-else>
                {{ props.formattedRow[props.column.field] }}
              </div>
            </template>
          </vue-good-table>
        </div>
        <p class="user_setting_edit_btn" v-if="tab == 3">
          <button @click="showUserPermissionModal">編集</button>
        </p>
        <div class="seal_part" v-if="tab == 1">
          <div class="label">
            会社印登録
          </div>
          <div class="value">
            <p class="seal">
              <span v-if="!stamp">NO IMAGE</span>
              <img :src="stamp" />
            </p>
            <div>
              <button @click="showStampRegisterModal">登録</button>
              <a @click="removeStamp" class="delete_icon">
                <img src="/asset/icons/delete.svg" />
              </a>
            </div>
          </div>
        </div>
        <div class="seal_part" v-if="tab == 1">
          <div class="label label1">
            銀行口座登録
          </div>
          <div class="value value1">
            <p>
              銀行名：{{ calcBankName() }}<br/>
              支店名：{{ calcBranchName() }}<br/>
              種類　：{{ calcAccountType() }}<br/>
              口座番号：{{ account_number }}<br/>
              口座名義：{{ account_name }}
            </p>
            <button @click="showBankEditModal">編集</button>
          </div>
        </div>

        <div class="credit_part" v-if="tab == 4">
          <p>基本情報</p>
          <div class="bank___info" v-if="!!tenant">
            <div class="info___row">
              <label>テナント名</label>
              <p class="value">{{ tenant.name }}</p>
            </div>
            <div class="info___row">
              <label>プラットフォーム利用料率</label>
              <p class="value">{{ tenant.platform_fee_rate }}％</p>
            </div>
            <div class="info___row">
              <label>入金最低額</label>
              <p class="value">{{ tenant.minimum_transfer_amount | moneyFormat }}円</p>
            </div>
            <div class="info___row">
              <label>売上金入金先銀行</label>
              <p class="value">{{ calcBankName() }}</p>
            </div>
            <div class="info___row">
              <label>支店</label>
              <p class="value">{{ calcBranchName() }}</p>
            </div>
            <div class="info___row">
              <label>口座種類</label>
              <p class="value">{{ calcAccountType() }}</p>
            </div>
            <div class="info___row">
              <label>口座番号</label>
              <p class="value">{{ account_number }}</p>
            </div>
            <div class="info___row">
              <label>口座名義</label>
              <p class="value">{{ account_name }}</p>
            </div>
          </div>

          <div class="links_group">
            <router-link :to="{ name: 'recipient.setting.income' }">入金一覧</router-link>
          </div>

          <p style="margin-top: 20px;">申請状況</p>
          <div class="credit_status">
            <div class="credit_row" v-for="(card, index) in CREDIT_CARD" :key="index">
              <div class="card_icon">
                <img :src="card.icon" />
              </div>
              <div class="value">
                {{ calcExist(card.brand) }}
              </div>
            </div>
          </div>

          <div>
            <button class="tenant_request_btn" @click="toReview">本番利用申請をする</button>
          </div>
        </div>
      </div>
    </div>
    <AccountEditModal @closeModal="closeEditModal" v-if="isShoEditModal"/>
    <StampRegisterModal @closeModal="closeModal" @savedStamp="savedStamp" v-if="isShowStampRegisterModal" />
    <BankEditModal v-if="isShowBankEditModal" @closeModal="closeBankEditModal" />
    <UserAppendModal v-if="isShowUserAppendModal" @closeModal="closeUserAppendModal" />
    <UserEditModal v-if="isShowUserEditModal" @closeModal="closeUserEditModal" :userId="editUserTempId" />
    <input class="dis-none" type="file" ref="stamp_selector" @change="changeStamp" accept="image/png, image/gif, image/jpeg" />
    <ConfirmModal 
      v-if="isShowStampRemoveModal" 
      :note="'削除します。よろしいでしょうか'" 
      :cancelText="'キャンセル'" 
      :okText="'削除する'"
      @cancel="cancelDeleteConfirm"
      @ok="okDeleteConfirm"/>
    <ConfirmModal 
      v-if="isShowUserDeleteModal" 
      :note="'削除します。よろしいでしょうか'" 
      :cancelText="'キャンセル'" 
      :okText="'削除する'"
      @cancel="cancelUserDeleteConfirm"
      @ok="okUserDeleteConfirm"/>
    <UserPermissionModal 
      v-if="isShowUserPermissionModal"
      :permissions="permissions"
      @closeModal="closeUserPermissionModal"
      @closeModalWithRefresh="closeUserPermissionModalWithRefresh"/>
  </div>
</template>
<script>
import AccountEditModal from "../../../components/recipient/AccountEditModal.vue"
import StampRegisterModal from "../../../components/recipient/StampRegisterModal.vue"
import ConfirmModal from '../../../components/common/ConfirmModal.vue'
import BankEditModal from '../../../components/recipient/BankEditModal.vue'
import UserAppendModal from '../../../components/recipient/UserAppendModal.vue'
import UserEditModal from '../../../components/recipient/UserEditModal.vue'
import UserPermissionModal from '../../../components/recipient/UserPermissionModal.vue'
import { CREDIT_CARD } from '../../../const'
var zenginCode = require('zengin-code')
export default {
  layout: 'recipient',
  components: {
    AccountEditModal,
    StampRegisterModal,
    ConfirmModal,
    BankEditModal,
    UserAppendModal,
    UserEditModal,
    UserPermissionModal
  },
  data() {
    return {
      tab: 1,
      isShoEditModal: false,
      isShowUserPermissionModal: false,
      bankList: [],
      branchList: [],
      tenant: null,
      CREDIT_CARD: CREDIT_CARD,
      columns: [
        {
          label: '会社名/屋号',
          field: 'name',
          sortable: false
        },
        {
          label: '住所',
          field: 'address',
          sortable: false
        },
        {
          label: '電話番号',
          field: 'phone',
          sortable: false
        },
        {
          label: '',
          field: 'edit',
          sortable: false
        },
      ],
      columns1: [
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
          label: '',
          field: 'edit',
          sortable: false
        },
        {
          label: '',
          field: 'delete',
          sortable: false
        },
      ],
      columns2: [
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
      rows2: [
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
      rows: [],
      rows1: [],
      recipienter: null,
      stamp: null,
      stamp_file: null,
      isShowStampRegisterModal: false,
      isShowStampRemoveModal: false,
      bank_code: null,
      branch_code: null,
      account_type: null,
      account_number: null,
      account_name: null,
      isShowBankEditModal: false,
      childs: [],
      isShowUserAppendModal: false,
      isShowUserDeleteModal: false,
      isShowUserEditModal: false,
      review_url: null,

      permissions: []
    }
  },
  async mounted() {
    await this.loadBankList()
    this.init()
  },
  methods: {
    calcTitle() {
      if (this.tab == 1) {
        return '会社情報'
      } else if (this.tab == 2) {
        return 'ユーザー一覧'
      } else if (this.tab == 3) {
        return 'ユーザー設定'
      } else if (this.tab == 4) {
        return 'クレジットカード'
      }
    },
    async loadBankList() {
      this.bankList = Object.values(zenginCode)
    },
    calcBankName() {
      var temp = this.bankList.find(item => {
        return item.code == this.bank_code
      })
      
      return !!temp ? temp.name : ""
    },
    showBankEditModal() {
      this.isShowBankEditModal = true
    },
    closeBankEditModal() {
      this.isShowBankEditModal = false
      this.init()
    },
    calcExist(brand) {
      let temp = this.tenant.reviewed_brands.find(item => {
        return item.brand == brand
      })

      if (!!temp) {
        return temp.status == 'passed' ? '通過' : '審査中'
      } else {
        return '未申請'
      }
    },
    calcAccountType() {
      let res = ""
      switch (this.account_type) {
        case '1':
          res = '普通預金'
          break;
        case '2':
          res = '当座預金'
          break;
        case '3':
          res = '貯蓄預金'
          break;
      }

      return res
    },
    availableReview() {
      return this.tenant.reviewed_brands.filter(item => {
        return item.status == 'passed'
      }).length < 6
    },
    showUserPermissionModal() {
      this.isShowUserPermissionModal = true
    },
    closeUserPermissionModal() {
      this.isShowUserPermissionModal = false
    },
    toReview() {
      window.open(this.review_url+'?return_to=https://ukerundesu.com/recipient/setting', '_blank').focus()
    },
    closeUserPermissionModalWithRefresh() {
      this.isShowUserPermissionModal = false
      this.init()
    },
    calcBranchName() {
      var temp = this.bankList.find(item => {
        return item.code == this.bank_code
      })

      if (!!temp) {
        var branches = Object.values(temp.branches)

        var temp1 = branches.find(item => {
          return item.code == this.branch_code
        })

        return !!temp1 ? temp1.name : ""
      } else {
        return ""
      }
    },
    async deleteUserProc(userId) {
      this.tempUserId = userId
      this.isShowUserDeleteModal = true
    },
    async init() {
      try {
        const { data } = await axios.post('/recipienter/get_account_info')
        this.recipienter = data.recipienter
        this.tenant = data.tenant
        this.review_url = data.review_url
        this.childs = data.childs
        this.rows1 = this.childs
        this.rows = []
        this.rows.push(data.recipienter)
        this.stamp = data.recipienter.stamp_url
        this.bank_code = data.recipienter.bank_code
        this.branch_code = data.recipienter.branch_code
        this.account_type = data.recipienter.account_type
        this.account_number = data.recipienter.account_number
        this.account_name = data.recipienter.account_name

        // permissions
        this.permissions = data.permissions
        this.rows2 = this.rows2.map((item, index) => {
          let temp = data.permissions.find(it => {
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
      } catch (error) {
      }
    },
    editProfile() {
      this.isShoEditModal = true
    },
    appendUser() {
      this.isShowUserAppendModal = true
    },
    closeEditModal() {
      this.isShoEditModal = false
      this.init()
    },
    changeStamp(event) {
      const file = event.target.files[0]
      if (!!file) {
        this.stamp_file = file
        this.stamp = URL.createObjectURL(file)
      }
    },
    registerStamp() {
      this.$refs.stamp_selector.click()
    },
    removeStamp() {
      this.isShowStampRemoveModal = true
    },
    showStampRegisterModal() {
      this.isShowStampRegisterModal = true
    },
    savedStamp() {
      this.$swal('', '登録しました')
      this.init()
      this.isShowStampRegisterModal = false
    },
    closeModal() {
      this.isShowStampRegisterModal = false
    },
    cancelDeleteConfirm() {
      this.isShowStampRemoveModal = false
    },
    async okDeleteConfirm() {
      this.isShowStampRemoveModal = false
      try {
        const { data } = await axios.post('/recipienter/delete_stamp')
        this.$swal('', '削除しました')
        this.init()
      } catch (error) {
      }
    },
    closeUserAppendModal() {
      this.isShowUserAppendModal = false
      this.init()
    },
    cancelUserDeleteConfirm() {
      this.isShowUserDeleteModal = false
    },
    async okUserDeleteConfirm() {
      this.isShowUserDeleteModal = false
      try {
        const { data } = await axios.post('/recipienter/delete_child_proc', {
          id: this.tempUserId
        })
        this.$swal('', '削除しました')
        this.init()
      } catch (error) {
      }
    },
    editUserProc(id) {
      this.editUserTempId = id
      this.isShowUserEditModal = true
    },
    closeUserEditModal() {
      this.isShowUserEditModal = false
      this.init()
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
  margin-top: 20px;
  align-items: flex-start;

  .menu_part {
    width: 200px;
    border: 1px solid var(--border-color);
    
    h3 {
      font-size: 13px;
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

  &.table_cont1 {
    width: 300px;
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

    &.label1 {
      height: auto;
    }
  }

  .value {
    width: 160px;
    height: 80px;
    border: 1px solid var(--border-color);
    display: flex;
    align-items: center;
    padding-left: 10px;

    &.value1 {
      height: auto;
      width: 532px;
      padding-right: 18px;
      padding-top: 12px;
      padding-bottom: 12px;

      button {
        margin-left: auto;
        font-size: 12px;
        background-color: var(--border-color);
        color: #fff;
        border: 0;
        width: 50px;
        height: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
      }
    }
    
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

.delete_btn {
  display: flex;
}

.user_setting_edit_btn {
  padding-top: 48px;
  display: flex;
  justify-content: center;
  width: 300px;

  button {
    width: 180px;
    height: 35px;
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: var(--accent-color);
    color: #fff;
    font-size: 14px;
    border: 0;
  }
}

.credit_part {
  padding-top: 20px;

  .bank___info {
    margin-top: 10px;

    .info___row {
      display: flex;
      align-items: center;

      label {
        font-size: 14px;
        border: 1px solid #ccc;
        width: 200px;
        height: 35px;
        display: flex;
        align-items: center;
        padding-left: 10px;
      }

      .value {
        border: 1px solid #ccc;
        flex: 1;
        height: 35px;
        display: flex;
        align-items: center;
        padding-left: 10px;
      }
    }
  }
}

.tenant_request_btn {
  width: 400px;
  height: 35px;
  border-radius: 5px;
  background-color: #1484C2;
  color: #fff;
  font-size: 14px;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-top: 20px;
  border: 0;
}

.credit_status {
  margin-top: 10px;

  .credit_row {
    display: flex;
    align-items: center;

    .card_icon {
      border: 1px solid #ccc;
      height: 40px;
      width: 200px;
      display: flex;
      align-items: center;
      padding-left: 10px;
    }

    .value {
      border: 1px solid #ccc;
      height: 40px;
      width: 200px;
      display: flex;
      align-items: center;
      padding-left: 10px;
    }
  }
}

.links_group {
  display: flex;
  align-items: center;
  padding-top: 20px;

  a {
    width: 180px;
    height: 35px;
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: var(--accent-color);
    color: #fff;
    text-decoration: none;

    &:first-of-type {
      margin-right: 20px;
    }

    &:hover {
      color: #fff;
    }
  }
}
</style>