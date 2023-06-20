<template>
  <div class="confirm__container">
    <h2 class="page__title">商品管理</h2>
    <div class="heading">
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
        </div>
        <div class="d-flex">
          <div class="form__row d-flex mb0">
            <label>ステータス：</label>
            <select v-model="search.status">
              <option>すべて</option>
              <option :value="1">公開</option>
              <option :value="0">非公開</option>
            </select>
          </div>
          <div class="form__row d-flex mb0">
            <label>マーク：</label>
            <select v-model="search.mark">
              <option :value="null">マーク選択</option>
              <option>SALE</option>
              <option>NEW</option>
              <option>予約販売</option>
              <option>再入荷</option>
              <option>売れ筋</option>
              <option>残りわずか</option>
              <option>完売</option>
              <option>マークなし</option>
            </select>
          </div>
          <div class="form__row d-flex mb0">
            <label>在庫数：</label>
            <div class="d-flex">
              <input type="number" class="amount" v-model="search.fromAmount" />
              <span>~</span>
              <input type="number" class="amount" v-model="search.toAmount" />
            </div>
          </div>
          <div class="form__action">
            <button @click="init">検索</button>
          </div>
        </div>
      </div>
      <div class="actions">
        <button @click="toProductAppend"><img src="/asset/icons/append.svg" />商品追加</button>
        <!-- <button><img src="/asset/icons/csv.svg" />CSV更新</button> -->
        <button @click="toColumnSettingScreen"><img src="/asset/icons/setting1.svg" />項目設定</button>
      </div>
    </div>

    <div class="table__part">
      <vue-good-table
        :columns="columns"
        :rows="rows"
        :pagination-options="{
          enabled: false,
          perPage: 1000
        }">
        <template slot="table-row" slot-scope="props">
          <div v-if="props.column.field == 'mark'">
            <span class="mark" :class="calcMarkColorClass(props.row.mark)">{{ props.row.mark }}</span>
          </div>
          <div class="toggle" v-else-if="props.column.field == 'is_public'">
            <toggle-button :width="88" :value="props.row.is_public == 0 ? false : true" :sync="true" :disabled="true" :color="{unchecked: '#707070', checked: '#337DFD'}" :labels="{unchecked: '非公開', checked: '公開'}"/>
          </div>
          <div v-else-if="props.column.field == 'name'">
            <span>{{ getValue('name', props.row.id) }}</span>
          </div>
          <div v-else-if="props.column.field == 'price'">
            <span>{{ getValue('price', props.row.id) | moneyFormat }}円</span>
          </div>
          <div v-else-if="props.column.field == 'tax'">
            <span>{{ props.row.tax == 2 ? '対象' : '' }}</span>
          </div>
          <div v-else-if="props.column.field == 'amount'">
            <span>{{ getValue('amount', props.row.id) | moneyFormat }}</span>
          </div>
          <div v-else-if="props.column.field == 'total_price'">
            <span>{{ getValue('total_price', props.row.id) | moneyFormat }}円</span>
          </div>
          <div v-else-if="props.column.field == 'remark'">
            <span>{{ getValue('remark', props.row.id) }}</span>
          </div>
          <div v-else-if="props.column.field == 'maker'">
            <span>{{ getValue('maker', props.row.id) }}</span>
          </div>
          <div v-else-if="props.column.field == 'color'">
            <span>{{ getValue('color', props.row.id) }}</span>
          </div>
          <div v-else-if="props.column.field == 'size'">
            <span>{{ getValue('size', props.row.id) }}</span>
          </div>
          <div v-else-if="props.column.field == 'unit'">
            <span>{{ getValue('unit', props.row.id) }}</span>
          </div>
          <div v-else-if="props.column.field == 'image'">
            <span class="img"><img :src="getImageUrl(props.row.id)" /></span>
          </div>
          <div v-else-if="props.column.field == 'jan'">
            <span>{{ getValue('jan', props.row.id) }}</span>
          </div>
          <div v-else-if="props.column.field == 'order'">
            <span>{{ getValue('order', props.row.id) }}</span>
          </div>
          <div v-else-if="props.column.field == 'free_space1'">
            <span>{{ getValue('free_space1', props.row.id) }}</span>
          </div>
          <div v-else-if="props.column.field == 'free_space2'">
            <span>{{ getValue('free_space2', props.row.id) }}</span>
          </div>
          <div v-else-if="props.column.field == 'free_space3'">
            <span>{{ getValue('free_space3', props.row.id) }}</span>
          </div>
          <div v-else-if="props.column.field == 'created_at'">
            <span>{{ props.row.created_at | dateFormatFull }}</span>
          </div>
          <div v-else-if="props.column.field == 'edit'">
            <button class="detail_btn" @click="toEditScreen(props.row.id)">編集</button>
          </div>
          <div v-else-if="props.column.field == 'delete'">
            <a class="delete_btn" @click="deleteProc(props.row.id)"><img src="/asset/icons/delete.svg" /></a>
          </div>
          <div class="table_cell" v-else>
            {{ props.formattedRow[props.column.field] }}
          </div>
        </template>
        <div slot="table-actions-bottom">
          <paginate
            :page-count="pageCount"
            :click-handler="clickPage"
            :prev-text="'前'"
            :next-text="'次'"
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
  methods: {
    selectProduct() {
      // this.isShowModal = false
    },
    deleteProc(product_id) {
      this.isShowModal = true
      this.temp_product_id = product_id
    },
    clickPage(params) {
      this.rows = this.tempRows.filter((item, index) => {
        return index >= (params - 1) * 10 && index < params * 10
      })
    },
    toColumnSettingScreen() {
      this.$router.push({ name: 'recipient.product.column_setting' })
    },
    cancelDeleteConfirm() {
      this.isShowModal = false
    },
    toEditScreen(id) {
      this.$router.push({ name: 'recipient.product.edit', query: { id: id } })
    },
    async okDeleteConfirm() {
      try {
        const { data } = await axios.post('/recipienter/delete_product', {
          id: this.temp_product_id
        })
        this.init()
        this.isShowModal = false
      } catch (error) {
      }
    },
    toProductAppend() {
      this.$router.push({ name: 'recipient.product.create' })
    },
    getValue(slug, id) {
      let temp = this.rows.find(item => {
        return item.id == id
      })
      let temp1 = null
      temp.product_values.forEach(item => {
        if (item.product_user_column.product_column.slug == slug) {
          if (item.product_user_column.product_column.type == 'number') {
            temp1 = parseInt(item.value)
          } else {
            temp1 = item.value
          }
        }
      })
      return temp1
    },
    calcMarkColorClass(mark) {
      if (mark == 'SALE' || mark == 'NEW') {
        return 'bg_red white'
      } else if (mark == '予約販売' || mark == '再入荷' || mark == '売れ筋') {
        return 'bg_yel white'
      } else if (mark == '残りわずか') {
        return 'bg_white red border_red'
      } else if (mark == '完売') {
        return 'bg_grey white'
      } else if (mark == 'マークなし') {
        return 'bg_white grey border_grey'
      }
    },
    getImageUrl(id) {
      let temp = this.rows.find(item => {
        return item.id == id
      })
      let temp1 = null
      temp.product_values.forEach(item => {
        if (item.product_user_column.product_column.slug == 'image') {
          temp1 = item.value_url
        }
      })

      return !!temp1 ? temp1 : '/asset/no_image.svg'
    },
    setPagination() {
      if (this.tempRows.length % 10 > 0) {
        this.pageCount = parseInt(this.tempRows.length / 10) + 1
      } else {
        this.pageCount = parseInt(this.tempRows.length / 10)
      }
    },
    async prepare() {
      try {
        const { data } = await axios.post('/recipienter/column_prepare')
      } catch (error) {
      }
    },
    async init() {
      try {
        const { data } = await axios.post('/recipienter/get_product_list', this.search)
        this.tempRows = data.products
        this.rows = this.tempRows.filter((item, index) => {
          return index < 10
        })
        this.setPagination()
        let picked_columns = data.columns.filter(item => {
          if(data.userColumns.length > 0) {
            let column = data.userColumns.find(it => {
              return it.product_column_id == item.id
            })
            return column.picked == 1
          } else {
            return item.picked == 1
          }
        }).map(item => {
          if(data.userColumns.length > 0) {
            let column = data.userColumns.find(it => {
              return it.product_column_id == item.id
            })
            return {
              ...item,
              order: column.order,
              tax: column.tax,
              tax_type: column.tax_type,
              name: column.nickname
            }
          } else {
            return item
          }
        })
        this.columns = [
          {
            label: 'ステータス',
            field: 'is_public',
            sortable: false,
          },
          {
            label: 'マーク',
            field: 'mark',
            sortable: false,
          },
        ]
        picked_columns.sort((a, b) => a.order - b.order)
        picked_columns.forEach(item => {
          if (item.slug != 'total_price') {
            if (item.slug == 'price') {
              this.columns.push({
                label: item.name + (item.tax_type == 1 ? '（税抜）' : ( item.tax_type == 2 ? '（税込）' : '（非課税）' )),
                field: item.slug,
                sortable: item.type == 'number' ? true : false
              })
              if (item.tax_type != 3 && item.tax == 2) {
                this.columns.push({
                  label: '軽減税率',
                  field: 'tax',
                  sortable: false,
                  width: '60px'
                })
              }
            } else {
              this.columns.push({
                label: item.name,
                field: item.slug,
                sortable: item.type == 'number' ? true : false
              })
            }
          }
        })
        this.columns.push({
          label: '登録日時',
          field: 'created_at',
          sortable: false
        })
        this.columns.push({
          label: '',
          field: 'edit',
          sortable: false
        })
        this.columns.push({
          label: '削除',
          field: 'delete',
          sortable: false
        })
      } catch (error) {
      }
    }
  },
  data() {
    return {
      isShowModal: false,
      columns: [],
      rows: [],
      tempRows: [],
      temp_product_id: null,
      pageCount: 1,

      search: {
        keyword: "",
        fromDate: null,
        toDate: null,
        status: null,
        fromAmount: null,
        toAmount: null,
        mark: null
      }
    }
  },
  async mounted() {
    await this.prepare()
    this.init()
  }
}
</script>
<style lang="scss" scoped>
.confirm__container {
  padding: 20px 30px;

  .heading {
    width: 840px;
    display: flex;
    margin-top: 20px;
    margin-bottom: 24px;

    .search__form {
      width: fit-content;
      border: 1px solid var(--border-color);
      padding: 8px 50px 8px 10px;
      background-color: #fff;
      border-radius: 5px;

      .form__row {
        margin-right: 15px;
        width: 200px;
        margin-bottom: 14px;

        &.mb0 {
          margin-bottom: 0;
        }

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

          &.amount {
            width: 60px;
            flex: none;
          }
        }

        span {
          width: 20px;
          text-align: center;
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

    .actions {
      padding-left: 40px;

      button {
        display: flex;
        align-items: center;
        padding-left: 10px;
        width: 120px;
        height: 30px;
        box-shadow: 0px 3px 6px rgba($color: #000000, $alpha: 0.16);
        border: 0;
        margin-bottom: 10px;
        
        img {
          width: 20px;
          margin-right: 20px;
        }

        &:first-of-type {
          background-color: #985298;
          color: #fff;
          font-size: 12px;
          font-weight: bold;
        }

        &:nth-of-type(2) {
          background-color: #fff;
          color: var(--text-color);
        }
        &:nth-of-type(3) {
          margin-bottom: 0;
        }
      }
    }
  }
}

.table__part {
  width: 1012px;
}

.mark {
  width: 70px;
  height: 20px;
  display: flex;
  align-items: center;
  justify-content: center;
  background-color: #D04F4F;
  color: #fff;
  font-size: 12px;

  &.bg_red {
    background-color: #D04F4F;
  }

  &.bg_yel {
    background-color: #D0A34F;
  }
  &.white {
    color: #fff;
  }

  &.bg_white {
    background-color: #fff;
  }

  &.red {
    color: #D04F4F;
  }

  &.bg_grey {
    background-color: #707070;
  }

  &.border_red {
    border: 1px solid #D04F4F;
  }

  &.border_grey {
    border: 1px solid #707070;
  }

  &.grey {
    color: #707070;
  }
}

.img {
  width: 50px;
  height: 50px;
  position: relative;
  overflow: hidden;
  display: flex;

  img {
    position: absolute;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
  }
}
</style>