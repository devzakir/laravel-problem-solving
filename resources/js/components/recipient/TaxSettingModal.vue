<template>
  <div class="mask">
    <div class="inner">
      <h3>消費税の設定をします。</h3>
      <a class="close_btn" @click="closeModal">
        <img src="/asset/icons/close.svg" />
      </a>
      <div class="form__row">
        <label>税設定</label>
        <div class="group">
          <p>
            <label><input type="radio" name="tax_type" :checked="tax_type == 1" @click="selectTaxType(1)" />税抜</label>
          </p>
          <p>
            <label><input type="radio" name="tax_type" :checked="tax_type == 2" @click="selectTaxType(2)" />税込</label>
          </p>
          <p>
            <label><input type="radio" name="tax_type" :checked="tax_type == 3" @click="selectTaxType(3)" />非課税</label>
          </p>
        </div>
      </div>
      <div class="form__row">
        <label>軽減税率</label>
        <div class="group">
          <p>
            <label><input type="radio" name="tax" :checked="tax == 2" @click="selectTax(2)" :disabled="tax_type == 3" />設定する</label>
          </p>
          <p>
            <label><input type="radio" name="tax" :checked="tax == 1" @click="selectTax(1)" :disabled="tax_type == 3" />設定しない</label>
          </p>
        </div>
      </div>

      <div class="form__actions">
        <button @click="closeModal">キャンセル</button>
        <button @click="saveTax">決定</button>
      </div>
    </div>
  </div>
</template>
<script>
export default {
  props: ['tempTax', 'tempTaxType'],
  data() {
    return {
      tax_type: 1,
      tax: 1
    }
  },
  mounted() {
    this.tax_type = this.$props.tempTaxType
    this.tax = this.$props.tempTax
  },
  methods: {
    closeModal() {
      this.$emit('onCloseModal')
    },
    selectTaxType(flag) {
      this.tax_type = flag
    },
    selectTax(flag) {
      this.tax = flag
    },
    saveTax() {
      this.$emit('onSaveTax', this.tax, this.tax_type)
    }
  }
}
</script>
<style lang="scss" scoped>
.mask {
  position: fixed;
  left: 0;
  top: 0;
  width: 100%;
  height: 100%;
  background-color: rgba($color: #000000, $alpha: 0.5);
  z-index: 499;

  .inner {
    position: fixed;
    left: 50%;
    top: 50%;
    transform: translate(-50%, -50%);
    background-color: #fff;
    border-radius: 10px;
    z-index: 500;
    padding: 30px 20px;
    width: fit-content;
  }

  h3 {
    font-size: 16px;
    font-weight: normal;
    text-align: center;
    margin-bottom: 20px;
  }
}

.close_btn {
  position: absolute;
  right: 10px;
  top: 10px;
}

.form__row {
  display: flex;
  align-items: center;
  margin-bottom: 15px;

  &>label {
    width: 100px;
    font-weight: 600;
  }

  .group {
    display: flex;

    p {
      display: flex;
      align-items: center;
      width: 100px;
    }
  }
}

.form__actions {
  display: flex;
  padding-top: 20px;
  justify-content: center;
  align-items: center;

  button {
    width: 140px;
    height: 40px;
    border-radius: 5px;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 0;

    &:first-of-type {
      background-color: #fff;
      margin-right: 15px;
      border: 1px solid #ccc;
    }

    &:last-of-type {
      background-color: var(--accent-color);
      color: #fff;
    }
  }
}
</style>