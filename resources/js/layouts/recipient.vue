<template>
  <div class="recipient_layout">
    <Navbar />

    <div class="d-flex">
      <Sidebar />
      <child />
    </div>

  </div>
</template>

<script>
import Navbar from '../components/recipient/Navbar'
import Sidebar from '../components/recipient/Sidebar'

export default {
  name: 'Recipient',
  middleware: 'recipient',

  components: {
    Navbar,
    Sidebar
  }
}
</script>
<style scoped>
.recipient_layout {
  width: 100%;
  min-height: 100vh;
  background-color: var(--main-color);
}

.d-flex {
  display: flex;
}
</style>
