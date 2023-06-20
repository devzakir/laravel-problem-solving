<template>
  <div class="pagination" v-if="pages > 1">
    <div class="container">
      <div class="inner">
        <a class="prev" :class="{ disabled: (current == 1) }" @click="clickPrev"><img src="/asset/prev.png" /></a>
        <a class="num" :class="{ current: (current == i) }" @click="clickPage(i)" v-for="i in pages" :key="i">{{ i }}</a>
        <a class="next" :class="{ disabled: (current == pages) }" @click="clickNext"><img src="/asset/next.png" /></a>
      </div>
    </div>
  </div>
</template>
<script>
export default {
  props: ['total'],
  name: 'Pagination',
  data() {
    return {
      pages: 0,
      current: 1
    }
  },
  mounted() {
    this.pages = Math.floor(this.$props.total / 9 + 1)
    console.log(this.pages)
  },
  methods: {
    clickPrev() {
      if (this.current == 1) {
        return 
      } else {
        this.current = this.current - 1
      }
      this.$emit('changeCurrentPage', this.current)
    },
    clickNext() {
      if (this.current == this.pages) {
        return 
      } else {
        this.current = this.current + 1
      }
      this.$emit('changeCurrentPage', this.current)
    },
    clickPage(i) {
      this.current = i
      this.$emit('changeCurrentPage', this.current)
    }
  }
}
</script>