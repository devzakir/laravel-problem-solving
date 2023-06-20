<template>
  <div class="custom-spinner" :class="{ full: isFullScreen }">
    <Spinner />
  </div>
</template>

<script>
import Spinner from 'vue-simple-spinner'
export default {
  name: 'CustomSpinner',
  
  props: {
    isFull: {
      type: Boolean,
      default: false
    }
  },

  computed: {
    isFullScreen() {
      return this.isFull
    }
  },

  components: {
    Spinner
  }
}
</script>

<style lang="scss" scoped>
.custom-spinner {
  position: fixed;
  z-index: 9004;
  display: flex;
  justify-content: center;
  align-items: center;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: rgba($color: #000000, $alpha: 0.4);
  &.full {
    position: fixed;
  }
}
</style>