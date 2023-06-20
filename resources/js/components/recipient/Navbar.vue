<template>
  <div class="recipient_navbar">
    <router-link :to="{ name: 'recipient.top' }" class="logo">
      <img src="/asset/ukeru_logo.png" />
    </router-link>
  </div>
</template>
<script>
export default {
  
}
</script>
<style lang="scss" scoped>
.recipient_navbar {
  background-color: var(--accent-color);
  height: 50px;
  display: flex;
  padding-left: 15px;
  padding-right: 15px;
  align-items: center;

  .logo {
    width: 110px;
    display: flex;
    align-items: center;
    justify-content: center;

    img {
      width: 100%;
    }
  }
}
</style>