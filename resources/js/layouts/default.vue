<template>
  <div class="main-layout">
    <div>
      <child />
    </div>
  </div>
</template>

<script>
import Navbar from '~/components/buyer/Navbar'
import Footer from '~/components/buyer/Footer'

export default {
  name: 'MainLayout',

  async mounted() {
  },

  components: {
    Navbar,
    Footer
  }
}
</script>
<style lang="scss" scoped>
.header_bar {
  height: 60px;
  background-color: #dcf4ff;

  ul {
    margin: 0;
    display: flex;
    align-items: center;
    height: 60px;
    list-style: none;
    li {
      border-right: 1px solid #707070;

      a {
        font-size: 16px;
        color: #333;
        padding-left: 30px;
        padding-right: 30px;

        &.current {
          color: #a1a025;
          font-weight: bold;
        }
      }
    }
  }
}
.main-layout {
  background-color: #F4F8FA;
  min-height: 100vh;
}
</style>
