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
import Navbar from '../components/admin/Navbar'
import Sidebar from '../components/admin/Sidebar'

export default {
  name: 'Recipient',

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
