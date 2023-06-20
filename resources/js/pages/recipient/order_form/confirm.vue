<template>
  <div class="confirm__container">
    <h2 class="page__title" v-if="!!orderForm">注文フォーム（{{ orderForm.name }}）</h2>
    <div class="heading">
      <div class="search__form">
        <div class="d-flex mb-14">
          <div class="form__row d-flex">
            <input type="text" placeholder="キーワード" />
          </div>
          <div class="form__row d-flex">
            <label>マーク：</label>
            <select>
              <option>すべて</option>
              <option>すべて</option>
              <option>すべて</option>
            </select>
          </div>
          <div class="form__action">
            <button>検索</button>
          </div>
        </div>
      </div>
      <div class="actions">
        <button @click="() => { isShowModal = true }"><img src="/asset/icons/append.svg" />商品追加</button>
      </div>
    </div>

    <div class="table__part">
      <vue-good-table
        :columns="columns"
        :rows="rows"
        :pagination-options="{
          enabled: false,
        }">
        <template slot="table-row" slot-scope="props">
          <div v-if="props.column.field == 'mark'">
            <span class="mark">{{ props.row.mark }}</span>
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
          <div v-else-if="props.column.field == 'delete'">
            <a @click="cancelOrderedProduct(props.row.id)" class="delete_btn"><img src="/asset/icons/delete.svg" /></a>
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

    <ProductAppend @closeOnly="closeOnly" @close="closeModal" v-if="isShowModal" @onSelect="selectProduct" :order_form_id="$route.query.id" />
    <ConfirmModal 
      v-if="isShowModal1" 
      :note="'削除します。よろしいでしょうか'" 
      :cancelText="'キャンセル'" 
      :okText="'削除する'"
      @cancel="cancelDeleteConfirm"
      @ok="okDeleteConfirm"/>
  </div>
</template>
<script>
import ConfirmModal from '../../../components/common/ConfirmModal.vue'
import ProductAppend from "../../../components/recipient/ProductAppend.vue"
export default {
  layout: 'recipient',
  components: {
    ProductAppend,
    ConfirmModal
  },
  methods: {
    selectProduct() {
      this.isShowModal = false
    },
    closeOnly() {
      this.isShowModal = false
    },
    closeModal() {
      this.isShowModal = false
      this.init()
    },
    clickPage(params) {
      this.rows = this.tempRows.filter((item, index) => {
        return index >= (params - 1) * 10 && index < params * 10
      })
    },
    setPagination() {
      if (this.tempRows.length % 10 > 0) {
        this.pageCount = parseInt(this.tempRows.length / 10) + 1
      } else {
        this.pageCount = parseInt(this.tempRows.length / 10)
      }
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
    async cancelOrderedProduct(product_id) {
      this.temp_product_id = product_id
      this.isShowModal1 = true
    },
    cancelDeleteConfirm() {
      this.isShowModal1 = false
    },
    async okDeleteConfirm() {
      try {
        const { data } = await axios.post('/recipienter/cancel_ordered_product', {
          id: this.$route.query.id,
          product_id: this.temp_product_id
        })
        this.isShowModal1 = false
        this.init()
      } catch (error) {
      }
    },
    async init() {
      try {
        const { data } = await axios.post('/recipienter/get_order_form_detail', {
          id: this.$route.query.id
        })
        this.orderForm = data.orderForm
        this.tempRows = data.orderedProducts.filter(item => {
          return item.is_public == 1
        })
        this.rows = this.tempRows.filter((item, index) => {
          return index < 10
        })
        console.log(this.rows, "_____this.rows")
        this.setPagination()
        this.columns = []
        this.columns.push({
          label: 'マーク',
          field: 'mark',
          sortable: false,
        })
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
          label: '削除',
          field: 'delete',
          sortable: false,
        })
      } catch (error) {
      }
    }
  },
  data() {
    return {
      orderForm: null,
      isShowModal: false,
      isShowModal1: false,
      columns: [],
      rows: [],
      tempRows: [],
      pageCount: 1
    }
  },
  mounted() {
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

    .search__form {
      width: fit-content;
      display: flex;
      border: 1px solid var(--border-color);
      padding: 8px 10px 15px 10px;
      align-items: center;
      background-color: #fff;
      margin-top: 20px;
      margin-bottom: 50px;

      .form__row {
        align-items: center;

        input {
          width: 200px;
          height: 30px;
          border: 1px solid var(--border-color);
          padding-left: 5px;
          padding-right: 5px;
        }

        label {
          font-size: 12px;
          width: 80px;
        }

        &:first-of-type {
          margin-right: 14px;
        }

        &:nth-of-type(2) {
          margin-right: 50px;
        }

        select {
          width: 120px;
          height: 30px;
          border: 1px solid var(--border-color);
        }
      }

      .form__action {
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
      padding-left: 70px;
      padding-top: 20px;

      button {
        display: flex;
        align-items: center;
        padding-left: 10px;
        width: 120px;
        height: 30px;
        box-shadow: 0px 3px 6px rgba($color: #000000, $alpha: 0.16);
        border: 0;
        
        img {
          width: 20px;
          margin-right: 20px;
        }

        &:first-of-type {
          background-color: #985298;
          color: #fff;
          font-size: 12px;
          font-weight: bold;
          margin-bottom: 14px;
        }

        &:nth-of-type(2) {
          background-color: #fff;
          color: var(--text-color);
        }
      }
    }
  }
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