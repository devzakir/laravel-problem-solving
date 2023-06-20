<template>
  <div class="order__detail_container">
    <div class="d-flex heading">
      <h2 class="page__title">出荷伝票の編集</h2>
    </div>
    <div class="status__part">
      <div class="panel">
        <h3 class="page__title">出荷情報</h3>
        <div class="panel__info">
          <div class="panel__row">
            <label>注文番号　　：</label>
            <p>XSAD-11101997-0001</p>
          </div>
          <div class="panel__row">
            <label>出荷日：</label>
            <p>
              <input type="date" />
            </p>
          </div>
        </div>
      </div>
    </div>

    <div class="client__info">
      <div class="message_part">
        この度はご注文いただきましてありがとうごいます。<br/>
        下記の通り納品させていただきます。
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
          <h4>メッセージ</h4>
          <p>
            <textarea></textarea>
          </p>
          <div class="d-flex mb-20">
            <label>希望納品時間：</label>
            <p>時間指定なし</p>
          </div>
          <div class="d-flex">
            <label>希望納品時間：</label>
            <p>日付指定なし</p>
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
            <button class="on">出荷伝票を作成</button>
          </div>
          <div class="calc__action">
            <button class="back">出荷伝票を作成</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
export default {
  layout: 'recipient',
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
    margin-left: 50px;
    display: flex;
    padding-top: 20px;
    border-bottom: 1px solid var(--border-color);
    padding-bottom: 20px;

    .panel {
      flex: 0.5;
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
            font-size: 10px;

            input {
              width: 124px;
              height: 20px;
              border: 1px solid var(--border-color);
            }
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

    .status__action {
      flex: 0.5;
      display: flex;
      align-items: flex-end;
      justify-content: flex-end;

      button {
        width: 70px;
        height: 25px;
        border-radius: 5px;
        border: 1px solid var(--border-color);
        color: var(--text-color);
        font-size: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 3px 6px rgba($color: #000000, $alpha: 0.16);

        img {
          margin-right: 4px;
        }

        &:first-of-type {
          margin-right: 17px;
        }
      }
    }
  }

  .client__info {
    margin-left: 50px;
    width: 860px;
    padding-top: 25px;
    display: flex;

    .message_part {
      width: 400px;
      padding: 8px;
      border: 1px solid var(--border-color);
      border-radius: 2px;
      font-size: 10px;
      min-height: 65px;
    }

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
</style>