<template>
  <div class="order_detail_products">
    <vue-good-table
      :columns="columns"
      :rows="rows"
      :pagination-options="{
        enabled: false,
      }">
      <template slot="table-row" slot-scope="props">
        <div class="remark" v-if="props.column.field == 'remark'">
          <input type="text" disabled />
        </div>
        <div v-else-if="props.column.field == 'tax'">
          <input class="price" :checked="props.row.tax == 2" type="checkbox" disabled />
        </div>
        <div v-else-if="props.column.field == 'price'">
          ￥{{ props.row.price | moneyFormat }}
        </div>
        <div v-else-if="props.column.field == 'amount'">
          ￥{{ props.row.amount | moneyFormat }}
        </div>
        <div v-else-if="props.column.field == 'total_price'">
          ￥{{ props.row.total_price | moneyFormat }}
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
      columns: [
        {
          label: '商品名',
          field: 'name',
          sortable: false
        },
        {
          label: '単価',
          field: 'price',
          sortable: false
        },
        {
          label: '軽減税率',
          field: 'tax',
          sortable: false,
          width: '60px'
        },
        {
          label: '数量',
          field: 'amount',
          sortable: false
        },
        {
          label: '発注金額',
          field: 'total_price',
          sortable: false
        },
        {
          label: '備考',
          field: 'remark',
          sortable: false
        },
      ],
      rows: [],
      quote: null
    }
  },
  mounted() {
    this.init()
  },
  methods: {
    async init() {
      try {
        const { data } = await axios.post('/recipienter/get_quote_detail_info', {
          id: this.$props.id
        })
        this.rows = data.quote.products
        this.quote = data.quote
      } catch (error) {
      }
    },
    calcTotalPrice() {
      let price = 0
      this.rows.forEach(item => {
        if (!!item.price && !!item.amount) {
          price += parseInt(parseInt(item.price) * parseInt(item.amount))
        }
      })

      return price
    },
  }
}
</script>
<style lang="scss" scoped>

</style>