<template>
  <div class="recipient_sidebar">
    <div class="current__date">{{ currentDate() }}</div>
    <ul>
      <li>
        <router-link :to="{ name: 'admin.user' }" :class="{ active: $route.name == 'admin.user' }"><img src="/asset/icons/toppage.svg" class="menu_icon" />トップページ</router-link>
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
      await this.$store.dispatch('admin_auth/logout')
      this.$router.push({ name: 'admin.auth.login' })
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