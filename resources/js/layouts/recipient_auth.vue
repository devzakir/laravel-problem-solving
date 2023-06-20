<template>
  <div class="recipient_layout">
    <Navbar />
    <child />
  </div>
</template>

<script>
import Navbar from '../components/recipient/Navbar'

export default {
  name: 'RecipientAuth',
  middleware: 'recipient_auth',

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
