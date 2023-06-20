<template>
  <div class="admin__layout 2">
    <div>
      <child />
    </div>

  </div>
</template>

<script>
import Navbar from '../components/admin/Navbar'
import Sidebar from '../components/admin/Sidebar'

export default {
  name: 'Manager',

  components: {
    Navbar,
    Sidebar
  }
}
</script>
<style scoped>
.admin__layout {
  background-color: #e8ebee;
  min-height: 100vh;
  padding-top: 50px;
}
</style>