<template>
  <div class="order__detail_container">
    <div class="d-flex heading">
      <h2 class="page__title">見積書編集</h2>
    </div>

    <div class="client__info" v-if="step == 2">
      <div class="left">
        <h3 class="page__title">{{ com_name }} {{ !!department_name ? department_name : '' }}</h3>
        <p class="client_saki">{{ name }} <span>様</span></p>
        <p class="content">
          以下の通り見積もりいたします。
        </p>
        <p class="total_price">
          <span>合計金額</span>
          <span>￥{{ parseInt(calcSubTotalPrice() * 1.1) }}（税込）</span>
        </p>
      </div>
      <div class="right">
        <div class="info__row">
          <label>発行日：</label>
          <p>{{ publish_date }}</p>
        </div>
        <div class="com_name">株式会社{{ com_name }}</div>
        <div class="address">
          部署名　：{{ department_name }}
        </div>
        <div class="address">
          担当者：{{ name }}
        </div>
      </div>
    </div>

    <div class="detail__part">
      <div class="kizon_part" v-if="step == 1">
        <button @click="kizonModal">登録済み商品を選択</button>
      </div>
      <div class="detail__table">
        <vue-good-table
          :columns="columns"
          :rows="rows"
          :pagination-options="{
            enabled: false,
          }">
          <template slot="table-row" slot-scope="props">
            <div class="remark" v-if="props.column.field == 'remark'">
              <input type="text" @change="changeValue($event, props.index, 'remark')" :value="props.row.remark" :disabled="step == 2" />
            </div>
            <div v-else-if="props.column.field == 'name'">
              <input type="text" @change="changeValue($event, props.index, 'name')" :value="props.row.name" :disabled="step == 2" />
            </div>
            <div v-else-if="props.column.field == 'price'">
              <input type="number" @change="changeValue($event, props.index, 'price')" :value="props.row.price" :disabled="step == 2" />
            </div>
            <div v-else-if="props.column.field == 'amount'">
              <input type="number" @change="changeValue($event, props.index, 'amount')" :value="props.row.amount" :disabled="step == 2" />
            </div>
            <div v-else-if="props.column.field == 'total_price'">
              <input type="number" :value="parseInt(props.row.price) * parseInt(props.row.amount)" disabled />
            </div>
            <div v-else-if="props.column.field == 'delete'">
              <a v-if="step == 1" @click="deleteRow(props.index)" class="delete_btn"><img src="/asset/icons/delete.svg" /></a>
            </div>
            <div class="table_cell" v-else>
              {{ props.formattedRow[props.column.field] }}
            </div>
          </template>
        </vue-good-table>
      </div>
      <div class="append__part">
        <button @click="appendRow">行を追加</button>
      </div>
      <div class="total__part">
        <div class="message__part">
          <h4 v-if="step == 2">メッセージ</h4>
          <p v-if="step == 2">
            <textarea v-model="message"></textarea>
          </p>
        </div>
        <div class="calculator">
          <div class="calc_row">
            <label>小計（税抜）</label>
            <p>￥{{ calcSubTotalPrice() }}</p>
          </div>
          <div class="calc_row">
            <label>消費税（10％）</label>
            <p>￥{{ parseInt(calcSubTotalPrice() * 0.1) }}</p>
          </div>
          <div class="calc_row">
            <label>合計</label>
            <p>￥{{ parseInt(calcSubTotalPrice() * 1.1) }}</p>
          </div>
          <div class="calc__action">
            <button @click="toNextStep" class="on">{{ step == 1 ? '確認画面' : '保存' }}</button>
          </div>
          <div class="calc__action">
            <button @click="back" class="back">戻る</button>
          </div>
        </div>
      </div>
    </div>

    <KizonProductAppend v-if="isShowModal" @appendedProduct="appendedProduct" />
  </div>
</template>
<script>
import KizonProductAppend from "../../../components/recipient/KizonProductAppend.vue"
export default {
  layout: 'recipient',
  components: {
    KizonProductAppend
  },
  mounted() {
    this.init()
  },
  data() {
    return {
      subject_title: null,
      publish_date: null,
      email: null,
      com_name: null,
      department_name: null,
      name: null,
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
        {
          label: '',
          field: 'delete',
          sortable: false
        },
      ],
      rows: [],
      isShowModal: false,
      step: 1,
      message: ""
    }
  },
  methods: {
    async init() {
      try {
        const { data } = await axios.post('/recipienter/get_quote_edit_init', {
          id: this.$route.query.id
        })
        this.subject_title = data.quote.subject_title
        this.publish_date = data.quote.publish_date
        this.email = data.quote.email
        this.com_name = data.quote.com_name
        this.department_name = data.quote.department_name
        this.name = data.quote.name
        this.rows = data.quote_products
      } catch (error) {
      }
    },
    changeValue(event, index, slug) {
      this.rows[index][slug] = event.target.value
      console.log(this.rows, "______this.rows")
    },
    appendRow() {
      this.rows.push({
        name: null,
        price: null,
        amount: null,
        total_price: null,
        remark: null
      })
    },
    back() {
      if (this.step == 1) {
        this.$router.back()
      } else {
        this.step = 1
      }
    },
    deleteRow(index) {
      let temp = this.rows.slice()
      temp.splice(index, 1)
      this.rows = temp
    },
    calcSubTotalPrice() {
      let price = 0
      this.rows.forEach(item => {
        if (!!item.price && !!item.amount) {
          price += parseInt(item.price) * parseInt(item.amount)
        }
      })
      return price
    },
    appendedProduct(...args) {
      console.log(args, "_____args, 123")
      const [name, price, amount] = args
      this.rows.push({
        name: name,
        price: price,
        amount: amount,
        total_price: parseInt(price) * parseInt(amount),
        remark: null
      })
      this.isShowModal = false
    },
    kizonModal() {
      this.isShowModal = true
    },
    toNextStep() {
      if (this.step == 1) {
        this.step = 2;
      } else {
        this.saveProc()
      }
    },
    async saveProc() {
      let flag = false
      this.rows.forEach(row => {
        if (!row.price || !row.amount || !row.name) {
          flag = true
        }
      })
      if (flag) {
        this.$swal('', '必須項目を入力してください')
        return
      }

      try {
        const { data } = await axios.post('/recipienter/update_quote', {
          id: this.$route.query.id
        })
        this.saveQuoteProduct()
      } catch (error) {
      }
    },
    async saveQuoteProduct() {
      await Promise.all(this.rows.map(async item => {
        await axios.post('/recipienter/save_quote_product', {
          name: item.name,
          price: item.price,
          amount: item.amount,
          total_price: parseInt(item.price) * parseInt(item.amount),
          remark: item.remark,
          quoteId: this.$route.query.id
        })
      }))

      this.$swal('', '保存しました')
      this.$router.push({ name: 'recipient.quote' })
    }
  }
}
</script>
<style lang="scss" scoped>
.mb-20 {
  margin-bottom: 20px;
}
.order__detail_container {
  padding: 20px 30px;

  .heading {
    width: 910px;
    align-items: center;
  }

  .download_btn {
    border-radius: 5px;
    background-color: #fff;
    padding-left: 6px;
    padding-right: 6px;
    height: 25px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    margin-left: auto;
    margin-right: 20px;
    color: var(--text-color);
    border: 1px solid var(--border-color);

    img {
      margin-right: 6px;
    }
  }

  .status__part {
    padding-left: 50px;
    display: flex;
    padding-top: 20px;

    .panel {
      flex: 1;
      border: 1px solid var(--border-color);
      padding: 12px 16px;
      border-radius: 5px;
      background-color: #fff;
      &:first-of-type {
        margin-right: 30px;
      }

      .sub_title {
        margin-left: 30px;
        font-size: 14px;
      }

      .panel__info {
        margin-top: 12px;
        width: 100%;
        .panel__row {
          width: 100%;
          display: flex;
          align-items: center;
          margin-bottom: 4px;
          label {
            font-size: 12px;
            margin-right: 10px;
          }

          p {
            flex: 1;
          }
        }

        .contactClient {
          display: flex;
          align-items: center;
          font-size: 12px;
          color: #337DFD;
          margin-left: auto;
          width: fit-content;

          img {
            margin-right: 5px;
            font-size: 12px;
            color: 15px;
            margin-right: 5px;
          }
        }
      }
    }
  }

  .detail__part {
    .detail__table {
      padding-left: 50px;
      padding-top: 25px;

      .remark input {
        height: 30px;
        border: 1px solid var(--border-color);
      }
    }

    .total__part {
      display: flex;
      padding-left: 50px;

      .message__part {
        width: 500px;
        border: 1px solid var(--border-color);
        padding: 15px;
        background-color: #fff;

        h4 {
          font-size: 14px;
        }

        textarea {
          width: 100%;
          display: block;
          height: 78px;
          border: 1px solid var(--border-color);
          border-radius: 5px;
          padding: 10px;
          margin-top: 4px;
          resize: none;
          margin-bottom: 16px;
        }
      }

      .calculator {
        margin-bottom: 15px;
        flex: 1;

        .calc_row {
          display: flex;
          align-items: center;

          label {
            flex: 1;
            height: 50px;
            border: 1px solid var(--border-color);
            line-height: 50px;
            padding-left: 10px;
          }

          p {
            text-align: right;
            flex: 1;
            height: 50px;
            border: 1px solid var(--border-color);
            line-height: 50px;
            padding-right: 10px;
          }
        }

        .calc__action {
          width: 100%;
          display: flex;
          justify-content: center;
          padding-top: 15px;

          button {
            width: 180px;
            height: 35px;
            display: flex;
            align-items: center;
            justify-content: center;

            &.on {
              background-color: var(--accent-color);
              color: #fff;
              border: 0;
            }

            &.back {
              background-color: #fff;
              border: 1px solid var(--border-color);
            }
          }
        }
      }
    }
  }
}

.flow__part {
  display: flex;
  position: relative;
  width: 355px;
  margin-left: auto;
  margin-right: auto;
  justify-content: space-between;
  margin-bottom: 60px;
  margin-top: 20px;

  &:after {
    content: '';
    position: absolute;
    left: 15px;
    width: 325px;
    top: 7px;
    height: 3px;
    background-color: #8D8B8B;
  }

  .step__item {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    position: relative;
    z-index: 20;

    .circle {
      width: 15px;
      height: 15px;
      background-color: #8D8B8B;
      margin-bottom: 2px;
      border-radius: 50%;

      &.active {
        background-color: var(--accent-color);
      }
    }

    .step_text {
      font-size: 10px;
      text-align: center;
      position: absolute;
      top: 20px;
      left: 50%;
      transform: translate(-50%, 0);
    }

    &:nth-of-type(1) {
      .step_text {
        width: 47px;
      }
    }
    &:nth-of-type(2) {
      .step_text {
        width: 43px;
      }
    }
    &:nth-of-type(3) {
      .step_text {
        width: 43px;
      }
    }
    &:nth-of-type(4) {
      .step_text {
        width: 43px;
      }
    }
  
  }
}

.delete_btn {
  display: flex;
}

.append__part {
  height: 50px;
  display: flex;
  align-items: center;
  justify-content: flex-end;
  background-color: #fff;
  border: 1px solid var(--border-color);
  padding-right: 12px;
  margin-left: 50px;

  button {
    width: 70px;
    height: 25px;
    border-radius: 4px;
    border: 1px solid var(--border-color);
    color: var(--text-color);
    font-size: 12px;
    box-shadow: 0 3px 6px rgba($color: #000000, $alpha: 0.6);
  }
}

.kizon_part {
  background-color: var(--accent-color);
  border-radius: 5px;
  width: 200px;
  height: 54px;
  position: relative;
  display: flex;
  align-items: center;
  justify-content: center;

  button {
    width: 132px;
    height: 25px;
    font-size: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 5px;
    box-shadow: 0 3px 6px rgba($color: #000000, $alpha: 0.16);
    border: 0;
  }

  &:after {
    content: "";
    position: absolute;
    top: 54px;
    left: 77px;
    border-left: 10px solid transparent;
    border-right: 10px solid transparent;
    border-top: 20px solid var(--accent-color);
  }
}

.client__info {
    margin-left: 50px;
    width: 860px;
    padding-top: 25px;
    display: flex;
    border-top: 1px solid var(--border-color);

    .left {
      width: 300px;

      .client_saki {
        font-size: 12px;
        color: var(--text-color);
        width: 100%;
        border-bottom: 1px solid var(--border-color);
        position: relative;
        padding-right: 35px;
        margin-top: 6px;
        line-height: 25px;

        span {
          font-size: 18px;
          position: absolute;
          right: 15px;
        }
      }

      .content {
        font-size: 10px;
        margin-left: 20px;
        margin-bottom: 44px;
        padding-top: 18px;
      }

      .total_price {
        display: flex;
        border-bottom: 1px solid var(--border-color);
        line-height: 25px;

        span {
          font-size: 12px;

          &:first-of-type {
            margin-right: auto;
          }
        }
      }
    }

    .right {
      width: 270px;
      margin-left: auto;

      .info__row {
        display: flex;
        align-items: center;

        label {
          font-size: 10px;
          width: 120px;
        }

        p {
          flex: 1;
          font-size: 10px;
        }
      }

      .com_name {
        margin-top: 14px;
        font-size: 18px;
        color: var(--text-color);
        margin-bottom: 8px;
      }

      .address {
        margin-bottom: 5px;
        font-size: 10px;
      }

      .contact_btn {
        display: flex;
        align-items: center;
        font-size: 12px;
        color: #337DFD;

        img {
          width: 15px;
          margin-right: 20px;
        }
      }
    }
  }
</style>