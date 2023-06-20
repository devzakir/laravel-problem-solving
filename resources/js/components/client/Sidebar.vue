<template>
  <div class="recipient_sidebar">
    <div class="current__date">{{ currentDate() }}</div>
    <ul>
      <li>
        <router-link :to="{ name: 'client.top' }" :class="{ active: $route.name == 'client.top' }"><img src="/asset/icons/toppage.svg" class="menu_icon" />トップページ</router-link>
      </li>
      <li>
        <router-link :to="{ name: 'client.order' }" :class="{ active: $route.name.includes('client.order') }"><img src="/asset/icons/order2.svg" class="menu_icon" />発注</router-link>
      </li>
      <li>
        <router-link :to="{ name: 'client.history' }" :class="{ active: $route.name.includes('client.history') }"><img src="/asset/icons/order.svg" class="menu_icon" />発注履歴</router-link>
      </li>
      <li>
        <router-link :to="{ name: 'client.shipping' }" :class="{ active: $route.name.includes('client.shipping') }"><img src="/asset/icons/product.svg" class="menu_icon" />入荷処理</router-link>
      </li>
      <li>
        <router-link :to="{ name: 'client.setting' }" :class="{ active: $route.name.includes('client.setting') }"><img src="/asset/icons/setting.svg" class="menu_icon" />設定</router-link>
      </li>
    </ul>

    <a class="logout_btn" @click="logout">
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
    async logout() {
      await this.$store.dispatch('auth/logout')
      this.$router.push({ name: 'client.auth.login' })
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
          background-color: var(--accent-color2);
        }

        &:hover {
          color: #fff;
          background-color: var(--accent-color2);
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
      background-color: var(--accent-color2);
    }

    .menu_icon {
      height: 30px;
      width: 30px;
      margin-right: 10px;
    }
  }
}
</style>