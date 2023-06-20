<template>
  <div class="order_detail_products">
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
          <span>{{ props.row.price | moneyFormat }}円</span>
        </div>
        <div v-else-if="props.column.field == 'tax'">
          <span>{{ props.row.tax == 2 ? '対象' : '' }}</span>
        </div>
        <div v-else-if="props.column.field == 'amount'">
          <span>{{ props.row.amount | moneyFormat }}</span>
        </div>
        <div v-else-if="props.column.field == 'total_price'">
          <span>{{ (props.row.price * props.row.amount) | moneyFormat }}円</span>
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
        <div class="table_cell" v-else>
          {{ props.formattedRow[props.column.field] }}
        </div>
      </template>
    </vue-good-table>
  </div>
</template>
<script>
export default {
  props: ['id'],
  data() {
    return {
      order_form: null,
      rows: [],
      columns: []
    }
  },
  mounted() {
    this.init()
  },
  methods: {
    async init() {
      try {
        const { data } = await axios.post('/recipienter/get_order_detail', {
          id: this.$props.id
        })
        this.order_form = data.order_form
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
              name: column.nickname,
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
        this.rows = data.order_form_products.map(item => {
          return {
            ...item.product,
            price: item.price,
            amount: item.amount,
            tax: item.tax,
            tax_type: item.tax_type
          }
        })
      } catch (error) {
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
  }
}
</script>
<style lang="scss" scoped>
.img {
  display: flex;
  width: 100px;

  img {
    width: 100%;
  }
}
</style>