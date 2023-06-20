<template>
  <div class="recipient_sidebar">
    <div class="current__date">{{ currentDate() }}</div>
    <ul v-if="!!$route.name">
      <li>
        <router-link :to="{ name: 'recipient.top' }" :class="{ active: $route.name == 'recipient.top' }"><img src="/asset/icons/toppage.svg" class="menu_icon" />トップページ</router-link>
      </li>
      <li>
        <router-link :to="{ name: 'recipient.order1' }" :class="{ active: $route.name.includes('recipient.order1') }"><img src="/asset/icons/recepient.svg" class="menu_icon" />受注管理</router-link>
      </li>
      <li>
        <a @click="() => { submenu_opened1 = !submenu_opened1 }"><img src="/asset/icons/output.svg" class="menu_icon" />出荷<img src="/asset/icons/triangle.svg" class="caret_down" /></a>
      </li>
      <li v-if="submenu_opened1">
        <router-link :to="{ name: 'recipient.shipping' }" :class="{ active: $route.name.includes('recipient.shipping') }" class="sub_menu">出荷管理<img src="/asset/icons/triangle_right.svg" class="caret_down" /></router-link>
      </li>
      <li v-if="submenu_opened1">
        <router-link :to="{ name: 'recipient.invoice' }" :class="{ active: $route.name.includes('recipient.invoice') }" class="sub_menu">請求書管理<img src="/asset/icons/triangle_right.svg" class="caret_down" /></router-link>
      </li>
      <li>
        <router-link :to="{ name: 'recipient.order_form' }" :class="{ active: $route.name.includes('recipient.order_form') }"><img src="/asset/icons/order.svg" class="menu_icon" />注文フォーム</router-link>
      </li>
      <li>
        <a @click="() => { submenu_opened2 = !submenu_opened2 }"><img src="/asset/icons/product.svg" class="menu_icon" />商品<img src="/asset/icons/triangle.svg" class="caret_down" /></a>
      </li>
      <li v-if="submenu_opened2">
        <router-link :to="{ name: 'recipient.product' }" class="sub_menu" :class="{ active: $route.name.includes('recipient.product') }">商品管理<img src="/asset/icons/triangle_right.svg" class="caret_down" /></router-link>
      </li>
      <li v-if="submenu_opened2">
        <router-link :to="{ name: 'recipient.product.column_setting' }" class="sub_menu" :class="{ active: $route.name.includes('recipient.product.column_setting') }">項目設定<img src="/asset/icons/triangle_right.svg" class="caret_down" /></router-link>
      </li>
      <li>
        <a @click="() => { submenu_opened3 = !submenu_opened3 }"><img src="/asset/icons/trasaction.svg" class="menu_icon" />取引<img src="/asset/icons/triangle.svg" class="caret_down" /></a>
      </li>
      <li v-if="submenu_opened3">
        <router-link :to="{ name: 'recipient.customer' }" class="sub_menu" :class="{ active: $route.name.includes('recipient.customer') }">取引先管理<img src="/asset/icons/triangle_right.svg" class="caret_down" /></router-link>
      </li>
      <li v-if="submenu_opened3">
        <router-link :to="{ name: 'recipient.quote' }" class="sub_menu" :class="{ active: $route.name.includes('recipient.quote') }">見積書管理<img src="/asset/icons/triangle_right.svg" class="caret_down" /></router-link>
      </li>
      <li v-if="submenu_opened3">
        <router-link :to="{ name: 'recipient.message' }" :class="{ active: $route.name.includes('recipient.message') }" class="sub_menu">メール配信<img src="/asset/icons/triangle_right.svg" class="caret_down" /></router-link>
      </li>
      <li>
        <router-link :to="{ name: 'recipient.setting' }" :class="{ active: $route.name.includes('recipient.setting') }"><img src="/asset/icons/setting.svg" class="menu_icon" />設定</router-link>
      </li>
    </ul>

    <a class="logout_btn" @click="logoutProc">
      <img src="/asset/icons/logout.svg" class="menu_icon" />
      ログアウト
    </a>
  </div>
</template>
<script>
import moment from 'moment'
export default {
  data() {
    return {
      submenu_opened1: false,
      submenu_opened2: false,
      submenu_opened3: false,
    }
  },
  methods: {
    async logoutProc() {
      await this.$store.dispatch('recipienter_auth/logout')
      this.$router.push({ name: 'recipient.auth.login' })
    },

    currentDate() {
      return moment().format('YYYY年MM月DD日')
    }
  }
}
</script>
<style lang="scss" scoped>
.recipient_sidebar {
  width: 200px;
  min-height: calc(100vh - 50px);
  background-color: var(--main-color2);
  color: #fff;
  position: relative;
  padding-bottom: 65px;

  .current__date {
    padding-left: 15px;
    height: 65px;
    display: flex;
    align-items: center;
    color: #fff;
    font-size: 18px;
    background-color: #48515D;
  }

  ul {
    padding-left: 0;
    margin-bottom: 0;
    list-style: none;
    margin-top: 0;

    li {
      a {
        height: 65px;
        width: 100%;
        display: flex;
        align-items: center;
        padding-left: 15px;
        padding-right: 15px;
        font-size: 18px;
        border-bottom: 1px solid var(--main-color);
        color: #fff;

        &.sub_menu {
          padding-left: 55px;
          background-color: #48515D;
        }

        &.active {
          background-color: var(--accent-color);
        }

        &:hover {
          color: #fff;
          background-color: var(--accent-color);
        }

        .menu_icon {
          height: 30px;
          width: 30px;
          margin-right: 10px;
        }

        .caret_down {
          width: 12px;
          height: 12px;
          margin-left: auto;
        }
      }
    }
  }

  .logout_btn {
    position: absolute;
    bottom: 0;
    left: 0;
    width: 100%;
    height: 65px;
    display: flex;
    align-items: center;
    padding-left: 15px;
    padding-right: 15px;
    border-top: 1px solid var(--main-color);
    color: #fff;

    &:hover {
      color: #fff;
      background-color: var(--accent-color);
    }

    .menu_icon {
      height: 30px;
      width: 30px;
      margin-right: 10px;
    }
  }
}
</style>