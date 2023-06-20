<template>
  <div class="confirm__modal">
    <div class="inner">
      <p class="note">{{ note }}</p>
      <p class="attension" v-if="isDelete == true">削除するとデータが完全に消去されます。</p>
      <div class="actions">
        <button @click="cancel">{{ cancelText }}</button>
        <button @click="ok">{{ okText }}</button>
      </div>
    </div>
  </div>
</template>
<script>
export default {
  name: 'ConfirmModal',
  props: ['note', 'cancelText', 'okText', 'isDelete'],
  methods: {
    cancel() {
      this.$emit('cancel')
    },
    ok() {
      this.$emit('ok')
    }
  }
}
</script>
<style lang="scss" scoped>
.confirm__modal {
  position: fixed;
  left: 0;
  top: 0;
  width: 100%;
  height: 100%;
  background-color: rgba($color: #000000, $alpha: 0.16);
  z-index: 200;

  .inner {
    position: fixed;
    z-index: 250;
    left: 50%;
    top: 50%;
    transform: translate(-50%, -50%);
    width: 400px;
    border-radius: 5px;
    border: 1px solid var(--border-color);
    background-color: #fff;
    padding: 50px 0 30px;
  }
}
.actions {
  display: flex;
  align-items: center;
  justify-content: center;
  padding-top: 30px;

  button {
    width: 180px;
    height: 35px;
    display: flex;
    align-items: center;
    justify-content: center;

    &:first-of-type {
      margin-right: 10px;
      border: 1px solid var(--border-color);
    }

    &:nth-of-type(2) {
      border: 0;
      background-color: var(--accent-color);
      color: #fff;
    }
  }
}

.note {
  text-align: center;
}

.attension {
  text-align: center;
  color: #ff0000;
  font-size: 12px;
  margin-top: 10px;
}
</style>