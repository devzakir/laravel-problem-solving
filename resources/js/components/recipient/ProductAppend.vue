<template>
  <div class="kizon_order_modal">
    <div class="mask" @click="close"></div>
    <div class="modal__inner">
      <div class="search_form">
        <input type="text" placeholder="キーワード" v-model="search.keyword" />
        <button @click="searchProc">検索</button>
      </div>
      <div class="count">
        {{ tempRows.length }}件
      </div>
      <div class="order_history_table">
        <vue-good-table
          :columns="columns"
          :rows="rows"
          :pagination-options="{
            enabled: false,
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
            <div class="action" v-else-if="props.column.field == 'append'">
              <button @click="append(props.row.id)" class="detail_btn">追加</button>
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
    </div>
  </div>
</template>
<script>
export default {
  props: ['order_form_id'],
  data() {
    return {
      columns: [],
      rows: [],
      tempRows: [],
      pageCount: 1,

      search: {
        keyword: ""
      }
    }
  },
  mounted() {
    this.init()
  },
  methods: {
    async append(id) {
      try {
        const { data } = await axios.post('/recipienter/append_ordered_form_produts', {
          id: id,
          order_form_id: this.$props.order_form_id
        })
        this.$emit('close')
      } catch (error) {
      }
    },
    close() {
      this.$emit('closeOnly')
    },
    async searchProc() {
      this.init()
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
    setPagination() {
      if (this.tempRows.length % 10 > 0) {
        this.pageCount = parseInt(this.tempRows.length / 10) + 1
      } else {
        this.pageCount = parseInt(this.tempRows.length / 10)
      }
    },
    async init() {
      try {
        const { data } = await axios.post('/recipienter/get_product_list', {
          ...this.search,
          order_form_id: this.$props.order_form_id
        })
        this.tempRows = data.products.filter(item => {
          return item.is_public == 1
        }).filter(item => {
          let find = data.orderedProducts.find(it => {
            return it.product_id == item.id
          })
          return !find
        })
        this.rows = this.tempRows.filter((item, index) => {
          return index < 10
        })
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
              order: column.order
            }
          } else {
            return item
          }
        })
        picked_columns.sort((a, b) => a.order - b.order)
        picked_columns.forEach(item => {
          this.columns.push({
            label: item.name,
            field: item.slug,
            sortable: item.type == 'number' ? true : false
          })
        })
        this.columns.push({
          label: '追加',
          field: 'append',
          sortable: false
        })
      } catch (error) {
      }
    },
    select() {
      this.$emit('onSelect')
    },
    clickPage(params) {
      this.rows = this.tempRows.filter((item, index) => {
        return index >= (params - 1) * 10 && index < params * 10
      })
    },
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
  max-height: 90vh;
  overflow: auto;
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