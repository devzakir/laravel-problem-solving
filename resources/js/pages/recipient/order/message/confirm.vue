<template>
  <div class="create__container">
    <h2 class="page__title">発注者に連絡</h2>
    <div class="contact__info">
      <div class="d-flex contact_heading">
        <h2 class="page__title">取引先情報</h2>
        <span class="company_name">株式会社〇〇〇〇</span>
      </div>
      <div class="contact__form">
        <div class="form__row">
          <label>差出人名</label>
          <p>
            株式会社〇〇　マスターアカウント
          </p>
        </div>
        <div class="form__row">
          <label>宛先</label>
          <p>株式会社〇〇〇〇　　　info@aaa.com</p>
        </div>
        <div class="form__row">
          <label>件名<span class="must">必須</span></label>
          <p>
            【ご連絡】株式会社〇〇（注文番号：0000000-111）
          </p>
        </div>
      </div>
    </div>
    <div class="message__part">
      <p class="to_client">
        株式会社〇〇〇〇<br/>
        〇〇　〇〇様
      </p>
      <p class="message_text">
        ▼希望納品日時<br/>
        —————————————————————————<br/>
        指定なし<br/>
        —————————————————————————<br/>
<br/>
        ▼ご注文内容<br/>
        —————————————————————————<br/>
        品番 : 10001<br/>
        商品名 : テスト商品１<br/>
        発注単価 : 1,000円<br/>
        数量 : 1<br/>
        発注金額 : 1,000円<br/>
        備考 : <br/>
        —————————————————————————<br/>
        小計（税抜） : 1,000円<br/>
        消費税（10%） : 100円<br/>
        合計 : 1,100円<br/>
        —————————————————————————<br/>
        株式会社〇〇<br/>
        〇〇 テスト<br/>
        info@conecle.com<br/>
<br/>
        〒520-1111<br/>
        大阪府大阪市北区〇〇町1-1<br/>
        TEL:06-7777-0000<br/>
        —————————————————————————
      </p>
    </div>

    <div class="actions">
      <button>戻る</button>
      <button>送信</button>
    </div>
  </div>
</template>
<script>
export default {
  layout: 'recipient'
}
</script>
<style lang="scss" scoped>
.create__container {
  padding: 20px 30px;

  .contact__info {
    width: fit-content;
    padding: 12px 14px;
    border: 1px solid var(--border-color);
    border-radius: 5px;
    background-color: #fff;
    margin-left: 50px;
    margin-top: 20px;
  }
  .contact_heading {
    align-items: center;
    margin-bottom: 14px;

    .company_name {
      margin-left: 30px;
      font-size: 12px;
    }
  }

  .contact__form {
    padding-left: 20px;

    .form__row {
      margin-bottom: 4px;
      display: flex;
      align-items: center;
      
      label {
        font-size: 12px;
        align-items: center;
        width: 65px;
        display: flex;

        .must {
          width: 26px;
          height: 12px;
          font-size: 10px;
          display: flex;
          align-items: center;
          justify-content: center;
          background-color: #D34555;
          color: #fff;
          margin-left: 3px;
        }
      }

      p {
        font-size: 12px;
        flex: 1;
        input, select {
          width: 100%;
          height: 30px;
          border: 1px solid var(--border-color);
          padding-left: 10px;
          padding-right: 10px;
          font-size: 12px;
          width: 316px;
        }
      }
    }
  }

  .message__part {
    width: 800px;
    border: 1px solid var(--border-color);
    margin-left: 50px;
    margin-top: 24px;
    padding: 25px 32px;
    background-color: #fff;

    .to_client {
      font-size: 12px;
      margin-bottom: 20px;
    }

    .message_input {
      display: block;
      width: 100%;
      height: 100px;
      border: 1px solid var(--border-color);
      padding: 4px 8px;
      font-size: 12px;
      margin-bottom: 26px;
      resize: none;
    }

    .condition_input {
      display: block;
      width: 100%;
      height: 240px;
      border: 1px solid var(--border-color);
      padding: 4px 8px;
      font-size: 12px;
      margin-bottom: 26px;
      resize: none;
    }

    .message_text {
      padding-left: 16px;
      margin-top: 25px;
      font-size: 12px;
    }
  }

  .actions {
    display: flex;
    align-items: center;
    justify-content: center;
    padding-top: 30px;

    button {
      width: 180px;
      height: 35px;
      box-shadow: 0 3px 6px rgba($color: #000000, $alpha: 0.16);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 14px;

      &:first-of-type {
        margin-right: 50px;
        background-color: #fff;
        border: 1px solid var(--border-color);
      }

      &:last-of-type {
        background-color: var(--accent-color);
        color: #fff;
        border: 0;
      }
    }
  }
}
</style>