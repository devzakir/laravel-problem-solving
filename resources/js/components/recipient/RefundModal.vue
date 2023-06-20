<template>
  <div class="refund_modal">
    <div class="inner">
      <h2 class="page__title">売上返金</h2>
      <div class="form__part">
        <div class="form__row">
          <p>
            <label><input type="radio" :checked="type == 1" @click="() => { type = 1 }" name="type" />全額</label>
            <label><input type="radio" :checked="type == 2" @click="() => { type = 2 }" name="type" />一部</label>
          </p>
        </div>
        <div class="form__row" v-if="type == 2">
          <label>返金する金額<span>必須</span></label>
          <p>
            <input type="number" v-model="price" />
          </p>
        </div>
        <div class="form__row">
          <label>理由</label>
          <p>
            <textarea v-model="refund_reason"></textarea>
          </p>
        </div>
        <div class="form__actions">
          <button @click="closeModal">キャンセル</button>
          <button @click="refundProc">返金</button>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
export default {
  data() {
    return {
      type: 1,
      price: 0,
      refund_reason: ""
    }
  },
  props: ['charge_id'],
  methods: {
    closeModal() {
      this.$emit('closeModal')
    },
    async refundProc() {
      try {
        const { data } = await axios.post('/recipienter/refund_proc', {
          id: this.$props.charge_id,
          type: this.type,
          price: this.price,
          refund_reason: this.refund_reason
        })
        this.$emit('refunded')
      } catch (error) {
      }
    }
  }
}
</script>
<style lang="scss" scoped>
.refund_modal {
  position: fixed;
  left: 0;
  top: 0;
  width: 100%;
  height: 100%;
  background-color: rgba($color: #000000, $alpha: 0.16);
  z-index: 100;

  .inner {
    position: fixed;
    left: 50%;
    top: 50%;
    width: 500px;
    transform: translate(-50%, -50%);
    background-color: #fff;
    border: 1px solid var(--border-color);
    box-shadow: 0 3px 6px rgba($color: #000000, $alpha: 0.16);
    padding: 28px 20px;
  }
}

.form__row {
  margin-bottom: 15px;

  label {
    font-size: 14px;
    display: flex;
    align-items: center;

    span {
      display: flex;
      align-items: center;
      justify-content: center;
      padding-left: 5px;
      padding-right: 5px;
      height: 20px;
      font-size: 12px;
      background-color: #ff0000;
      color: #fff;
      border-radius: 2px;
      margin-left: 4px;
    }
  }

  p {
    margin-top: 4px;
    input[type=number] {
      width: 100%;
      height: 40px;
      border: 1px solid #ccc;
      padding-left: 10px;
      padding-right: 10px;
      font-size: 14px;
    }

    textarea {
      width: 100%;
      height: 160px;
      border: 1px solid #ccc;
      padding: 10px;
      resize: none;
    }
  }
}

.form__part {
  margin-top: 20px;
}

.form__actions {
  display: flex;
  align-items: center;
  justify-content: center;

  button {
    width: 180px;
    height: 35px;
    display: flex;
    align-items: center;
    justify-content: center;

    &:first-of-type {
      margin-right: 50px;
      border: 1px solid var(--border-color);
    }

    &:last-of-type {
      background-color: var(--accent-color);
      color: #fff;
      border: 0;
    }
  }
}
</style>