<template>
  <div class="main__container">
    <div class="panel" v-if="!!platform">
      <h2>プラットフォーム設定</h2>
      <div class="info_row">
        <label>プラットフォーム利用料率</label>
        <p class="value">{{ platform.platform_fee }}%</p>
      </div>
      <p class="note">
        ＊商品代金の決済金額から、プラットフォーム利用料を徴収することができます。<br/>
        上記とは別にカード会社に決済手数料が必要となります。
      </p>
      <div class="info_row">
        <label>最低入金額</label>
        <p class="value">{{ platform.min_price | moneyFormat }}円</p>
      </div>
      <p class="note">
        ＊テナントへの入金下限額は1,000円以上となります。
      </p>

      <div class="actions">
        <button @click="back">戻る</button>
        <button @click="edit">編集</button>
      </div>
    </div>

    <PlatformEdit v-if="isShowModal" :fee="platform.platform_fee" :price="platform.min_price" @closeModal="() => {
      isShowModal = false
      init()
    }" />
  </div>
</template>
<script>
import PlatformEdit from '../../../components/admin/modal/PlatformEdit.vue'
export default {
  layout: 'admin',
  middleware: 'admin',
  components: {
    PlatformEdit
  },
  data() {
    return {
      isShowModal: false,
      platform: null
    }
  },
  mounted() {
    this.init()
  },
  methods: {
    async init() {
      try {
        const { data } = await axios.post('/admin/get_platform_info')
        this.platform = data.platform
      } catch (error) {
      }
    },
    back() {
      this.$router.back()
    },
    edit() {
      this.isShowModal = true
    }
  }
}
</script>
<style lang="scss" scoped>
.main__container {
  padding-top: 66px;
  padding-left: 30px;
}
.panel {
  width: 850px;
  background-color: #fff;
  border: 1px solid #707070;
  padding: 15px;

  h2 {
    font-size: 16px;
    padding-left: 6px;
    border-left: 10px solid #48515D;
    line-height: 40px;
    margin-bottom: 20px;
  }

  .info_row {
    display: flex;
    align-items: center;
    margin-bottom: 20px;

    label {
      font-size: 14px;
      font-weight: bold;
      width: 180px;
      color: #2A3039;
    }

    .value {
      font-size: 14px;
      font-weight: bold;
      color: #2A3039;
    }
  }

  .note {
    font-size: 10px;
    color: #C93D4D;
    margin-bottom: 30px;
  }

  .actions {
    display: flex;
    align-items: center;
    justify-content: center;

    button {
      width: 180px;
      height: 35px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 14px;

      &:first-of-type {
        border: 1px solid #707070;
        margin-right: 10px;
        background-color: #fff;
        color: #2A3039;
      }

      &:nth-of-type(2) {
        border: 1px solid var(--accent-color);
        background-color: var(--accent-color);
        color: #fff;
      }
    }
  }
}
</style>