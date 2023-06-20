<template>
  <div class="spinner">
    <clip-loader :loading="true" class="spinner_inner" color="#529598"></clip-loader>
  </div>
</template>
<script>
import ClipLoader from 'vue-spinner/src/ClipLoader.vue'
export default {
  components: {
    ClipLoader
  },
  data() {
    return {
      size: '100'
    }
  }
}
</script>
<style lang="scss" scoped>
.spinner {
  position: fixed;
  left: 0;
  top: 0;
  width: 100%;
  height: 100%;
  z-index: 1000;
  background: rgba($color: #000, $alpha: 0.2);

  .spinner_inner {
    position: fixed;
    left: 50%;
    top: 50%;
    transform: translate(-50%, -50%);
    z-index: 1001;
  }
}
</style>