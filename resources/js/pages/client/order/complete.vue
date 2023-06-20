<template>
  <div class="complete__container">
    <h2 class="page__title1">注文フォーム名　：株式会社〇〇　特設販売フォーム</h2>

    <h3>発注が完了しました。</h3>

    <div class="d-flex link_group">
      <a>＞続けて発注する</a>
      <a>＞発注履歴</a>
    </div>

    <div class="bank__info">
      <p>銀行振込の方は、以下にお振込手続きをお願い致します。</p><br/>
      <p>
        銀行名：〇〇銀行<br/>
        支店名：〇〇支店<br/>
        種類　：〇〇口座<br/>
        口座番号：0001234<br/>
        口座名義：カブシキガイシャ〇〇〇〇
      </p>
    </div>
  </div>
</template>
<script>
export default {
  layout: 'client'
}
</script>
<style lang="scss" scoped>
.complete__container {
  padding: 20px 30px;

  h3 {
    font-size: 16px;
    width: 450px;
    height: 50px;
    background-color: #529598;
    color: #fff;
    display: flex;
    align-items: center;
    padding-left: 10px;
    margin-top: 20px;
    margin-bottom: 30px;
    border-radius: 4px;
    font-weight: normal;
  }
}

.link_group {
  align-items: center;
  margin-bottom: 40px;

  a {
    height: 40px;
    display: flex;
    align-items: center;
    color: #337DFD;
    width: 200px;

    &:first-of-type {
      margin-right: 40px;
    }
  }
}

.bank__info {
  width: fit-content;
  border: 1px solid var(--border-color);
  padding: 10px;
  border-radius: 5px;
}
</style>