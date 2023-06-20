<template>
  <div class="recipient_layout">
    <Navbar />
    <child />
  </div>
</template>

<script>
import Navbar from '../components/client/Navbar'

export default {
  name: 'RecipientAuth',

  components: {
    Navbar,
  }
}
</script>
<style scoped>
.recipient_layout {
  width: 100%;
  min-height: 100vh;
  background-color: var(--main-color);
}
</style>
