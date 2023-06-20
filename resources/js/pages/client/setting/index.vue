<template>
  <div class="setting__container">
    <h2 class="page__title1">アカウント設定</h2>
    
    <div class="main__part">
      <div class="menu_part">
        <h3>{{ $store.getters['auth/user'].com_name }}<br/>{{ $store.getters['auth/user'].name }}様</h3>
        <a :class="{ current: tab == 1 }" @click="() => { tab = 1 }">会社情報<span v-if="tab == 1"></span></a>
      </div>
      <div class="main_panel">
        <h2 class="page__title black">{{ tab == 1 ? '会社情報' : 'ユーザー一覧' }}</h2>
        <div class="table_cont" v-if="tab == 1">
          <vue-good-table
            :columns="columns"
            :rows="rows"
            :pagination-options="{
              enabled: false,
            }">
            <template slot="table-row" slot-scope="props">
              <div v-if="props.column.field == 'edit'">
                <button @click="editModal" class="detail_btn">編集</button>
              </div>
              <div class="table_cell" v-else>
                {{ props.formattedRow[props.column.field] }}
              </div>
            </template>
          </vue-good-table>
        </div>
      </div>
    </div>
    <EditModal v-if="isShowModal" @closeModal="closeModal" />
  </div>
</template>
<script>
import EditModal from '../../../components/client/EditModal.vue'
export default {
  layout: 'client',
  components: {
    EditModal
  },
  data() {
    return {
      isShowModal: false,
      tab: 1,
      columns: [
        {
          label: '会社名/屋号',
          field: 'com_name',
          sortable: false
        },
        {
          label: '住所',
          field: 'address',
          sortable: false
        },
        {
          label: '電話番号',
          field: 'phoneNumber',
          sortable: false
        },
        {
          label: '',
          field: 'edit',
          sortable: false
        },
      ],
      rows: [
        {
          id: 1,
          com_name: '株式会社〇〇〇〇',
          address: '〒500-0000 大阪府大阪市北区〇〇町1-4-4',
          phoneNumber: '06-1234-1234',
        },
      ],
    }
  },
  methods: {
    editModal() {
      this.isShowModal = true
    },
    closeModal() {
      this.isShowModal = false
      this.init()
    },
    init() {
      this.rows = []
      this.rows.push({
        id: this.$store.getters['auth/user'].id,
        com_name: this.$store.getters['auth/user'].com_name,
        address: (this.$store.getters['auth/user'].prefecture || '') + (this.$store.getters['auth/user'].city || '') + (this.$store.getters['auth/user'].building || ''),
        phoneNumber: this.$store.getters['auth/user'].telephone
      })
    }
  },
  mounted() {
    this.init()
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
      font-size: 14px;
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
        background-color: var(--accent-color2);
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
        background-color: var(--accent-color2);
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
        border-left: 12px solid var(--accent-color2);
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
    background-color: var(--accent-color2);
    color: #fff;
    font-size: 14px;
    border: 0;
  }
}
</style>