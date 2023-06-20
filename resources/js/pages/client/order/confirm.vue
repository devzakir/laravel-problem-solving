<template>
  <div class="order__detail_container">
    <div class="d-flex heading">
      <h2 class="page__title1">注文フォーム名　：株式会社〇〇　特設販売フォーム</h2>
    </div>

    <div class="search__form">
      <div class="d-flex align-center">
        発注先：   株式会社〇〇　〇〇
      </div>
    </div>

    <div class="detail__part">
      <div class="detail__table">
        <vue-good-table
          :columns="columns"
          :rows="rows"
          :pagination-options="{
            enabled: false,
          }">
          <template slot="table-row" slot-scope="props">
            <div class="remark" v-if="props.column.field == 'remark'">
              <input type="text" />
            </div>
            <div class="table_cell" v-else>
              {{ props.formattedRow[props.column.field] }}
            </div>
          </template>
        </vue-good-table>
      </div>
      <div class="total__part">
        <div class="message__part">
          <h4 class="mb-20">メッセージ</h4>
          <p class="mb-20">
            お世話になります。<br/>
            よろしくお願いいたします。
          </p>
          <div class="d-flex mb-20 align-center">
            <label>希望納品時間：</label>
            <p>
              時間指定なし
            </p>
          </div>
          <div class="d-flex mb-20 align-center">
            <label>希望納品時間：</label>
            <p>
              日付指定なし
            </p>
          </div>
          <div class="d-flex">
            <label>支払方法<span>必須</span></label>
            <p>
              クレジットカード
            </p>
          </div>
        </div>
        <div class="calculator">
          <div class="calc_row">
            <label>小計（税抜）</label>
            <p>￥600</p>
          </div>
          <div class="calc_row">
            <label>消費税（○％）</label>
            <p>￥60</p>
          </div>
          <div class="calc_row">
            <label>合計</label>
            <p>￥660</p>
          </div>
          <div class="calc__action">
            <button class="on">発注</button>
          </div>
          <div class="calc__action">
            <button class="back">戻る</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
export default {
  layout: 'client',
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
          label: '数量',
          field: 'amount',
          sortable: false
        },
        {
          label: '発注金額',
          field: 'real_price',
          sortable: false
        },
        {
          label: '備考',
          field: 'remark',
          sortable: false
        },
      ],
      rows: [
        {
          id: 1, 
          name: 'テスト商品',
          price: '￥100',
          amount: 1,
          real_price: '￥100',
          remark: ''
        },
        {
          id: 2, 
          name: 'テスト商品',
          price: '￥100',
          amount: 1,
          real_price: '￥100',
          remark: ''
        },
        {
          id: 3, 
          name: 'テスト商品',
          price: '￥100',
          amount: 1,
          real_price: '￥100',
          remark: ''
        },
        {
          id: 4, 
          name: 'テスト商品',
          price: '￥100',
          amount: 1,
          real_price: '￥100',
          remark: ''
        },
        {
          id: 5, 
          name: 'テスト商品',
          price: '￥100',
          amount: 1,
          real_price: '￥100',
          remark: ''
        },
        {
          id: 6, 
          name: 'テスト商品',
          price: '￥100',
          amount: 1,
          real_price: '￥100',
          remark: ''
        },
      ]
    }
  }
}
</script>
<style lang="scss" scoped>
.align-center {
  align-items: center;
}
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
      padding-top: 25px;

      .remark input {
        height: 30px;
        border: 1px solid var(--border-color);
      }
    }

    .total__part {
      display: flex;

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

      p {
        input, select {
          width: 200px;
          height: 30px;
          border: 1px solid var(--border-color);
          padding-left: 5px;
          padding-right: 5px;
        }
      }

      .payment_method {
        &>label {
          width: 100px;
          display: flex;
          align-items: center;
          span {
            margin-left: auto;
            width: 30px;
            height: 20px;
            background-color: #D34555;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
          }
        }
        p {
          display: flex;
          align-items: center;
          label {
            display: flex;
            margin-right: 5px;
            font-size: 12px;
            input {
              margin-right: 4px;
              width: 15px;
              height: 15px;
            }
          }
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
              background-color: var(--accent-color2);
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

.search__form {
  width: 350px;
  border: 1px solid var(--border-color);
  background-color: #fff;
  border-radius: 5px;
  margin-bottom: 30px;
  margin-top: 20px;
  height: 50px;
  padding-left: 10px;

  .d-flex {
    height: 50px;
  }

  .form__row {
    margin-right: 15px;
    width: 200px;

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
      background-color: var(--accent-color2);
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

  .mark {
    align-items: center;
    label {
      width: 80px;
    }

    p {
      select {
        width: 120px;
        height: 30px;
        border: 1px solid var(--border-color);
        padding-left: 6px;
        padding-right: 6px;
        font-size: 14px;
      }
    }
  }
}
</style>