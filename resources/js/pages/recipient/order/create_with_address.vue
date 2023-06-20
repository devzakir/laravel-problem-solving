<template>
  <div class="create__container">
    <h2 class="page__title">受注登録</h2>
    <div class="main__part">
      <div class="flow__part">
        <p class="step__item">
          <span class="circle"></span>
          <span class="step_text">取引先</span>
        </p>
        <p class="step__item">
          <span class="circle active"></span>
          <span class="step_text">注文フォーム選択</span>
        </p>
        <p class="step__item">
          <span class="circle"></span>
          <span class="step_text">注文情報</span>
        </p>
        <p class="step__item">
          <span class="circle"></span>
          <span class="step_text">確認</span>
        </p>
        <p class="step__item">
          <span class="circle"></span>
          <span class="step_text">完了</span>
        </p>
      </div>
      <div class="create__form">
        <div class="form__part">
          <h4 class="page__title">取引先情報</h4>
          <p class="content">{{ com_name }} {{ department_name }}</p>
          <h4 class="page__title">注文フォーム選択</h4>
          <select class="form__select" v-model="order_form">
            <option :value="null">注文フォーム選択</option>
            <option v-for="(item, index) in orderForms" :key="index" :value="item.id">{{ item.name }}</option>
          </select>
          <div class="form__action">
            <button @click="back" class="back">戻る</button>
            <button @click="next">注文フォーム選択</button>
          </div>
        </div>
      </div>
    </div>

    <KizonOrderSelector v-if="isShowKizonModal" @onSelect="selectKizonOrder" />
  </div>
</template>
<script>
import KizonOrderSelector from '../../../components/recipient/KizonOrderSelector.vue'
export default {
  layout: 'recipient',
  data() {
    return {
      isShowKizonModal: false,
      orderForms: [],
      order_form: null,
      com_name: null,
      department_name: null
    }
  },
  mounted() {
    this.com_name = localStorage.getItem('com_name')
    this.department_name = localStorage.getItem('department_name')
    this.init()
  },
  components: {
    KizonOrderSelector
  },
  methods: {
    async init() {
      try {
        const { data } = await axios.post('/recipienter/get_order_form_list')
        this.orderForms = data.orderForms
      } catch (error) {
      }
    },
    selectKizonOrder() {
      this.isShowKizonModal = false
    },
    selectFromKizon() {
      this.isShowKizonModal = true
    },
    back() {
      this.$router.back()
    },
    next() {
      if (!this.order_form) {
        this.$swal('', '注文フォームを選択して下さい')
        return
      }

      localStorage.setItem('order_form', this.order_form)
      this.$router.push({ name: 'recipient.order1.create_with_detail' })
    }
  }
}
</script>
<style lang="scss" scoped>
.create__container {
  padding: 20px 30px;

  .main__part {
    width: 500px;
    margin-left: 200px;
    margin-top: 20px;

    .flow__part {
      display: flex;
      position: relative;
      width: 450px;
      margin-left: auto;
      margin-right: auto;
      justify-content: space-between;
      margin-bottom: 60px;

      &:after {
        content: '';
        position: absolute;
        left: 15px;
        width: 420px;
        top: 7px;
        height: 3px;
        background-color: #8D8B8B;
      }

      .step__item {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        position: relative;
        z-index: 20;

        .circle {
          width: 15px;
          height: 15px;
          background-color: #8D8B8B;
          margin-bottom: 2px;
          border-radius: 50%;

          &.active {
            background-color: var(--accent-color);
          }
        }

        .step_text {
          font-size: 10px;
          text-align: center;
          position: absolute;
          top: 20px;
          left: 50%;
          transform: translate(-50%, 0);
        }

        &:nth-of-type(1) {
          .step_text {
            width: 34px;
          }
        }
        &:nth-of-type(2) {
          .step_text {
            width: 86px;
          }
        }
        &:nth-of-type(3) {
          .step_text {
            width: 43px;
          }
        }
        &:nth-of-type(4) {
          .step_text {
            width: 43px;
          }
        }
        &:nth-of-type(5) {
          .step_text {
            width: 43px;
          }
        }
      }
    }   
    
    .create__form {
      width: 100%;
      border: 1px solid var(--border-color);
      padding: 30px 20px;
      box-shadow: 0 3px 6px rgba($color: #000000, $alpha: 0.16);
      background-color: #fff;

      h3 {
        text-align: center;
        font-size: 16px;
        color: var(--text-color);
        margin-bottom: 30px;
      }

      .kizon_select_btn {
        width: 100%;
        height: 50px;
        background-color: var(--accent-color);
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
        margin-bottom: 50px;
        border: 0;
        box-shadow: 0 3px 6px rgba($color: #000000, $alpha: 0.16);
      }

      .form__part {
        h4 {
          margin-bottom: 10px;
        }

        .content {
          padding-left: 15px;
          margin-bottom: 40px;
        }

        .form__inner {
          border: 1px solid var(--border-color);
          padding: 25px 15px;
          background-color: var(--main-color);

          .form__row {
            display: flex;
            align-items: center;
            margin-bottom: 10px;

            label {
              width: 165px;
              display: flex;
              align-items: center;
              font-size: 14px;

              .must {
                width: 30px;
                height: 20px;
                background-color: #D34555;
                color: #fff;
                font-size: 10px;
                display: flex;
                align-items: center;
                justify-content: center;
                margin-left: 5px;
              }
            }

            p {
              flex: 1;
              align-items: center;
              display: flex;
              
              input {
                flex: 1;
                height: 30px;
                border-radius: 5px;
                border: 1px solid var(--border-color);
                padding-left: 10px;
                padding-right: 10px;
              }

              select {
                width: 120px;
                height: 30px;
                border-radius: 5px;
                border: 1px solid var(--border-color);
                padding-left: 10px;
                padding-right: 10px;
              }

              span {
                font-size: 12px;
              }

              &.group {
                input {
                  width: 120px;
                  
                  &:first-of-type {
                    margin-right: 10px;
                  }
                }
              }

              &.triple {
                input {
                  width: 70px;
                }
              }
            }
          }
        }
      }
    }
  }
}

.form__select {
  width: 100%;
  height: 35px;
  text-align: center;
  font-size: 12px;
  border: 1px solid var(--border-color);
  margin-bottom: 25px;
}

.form__action {
  display: flex;
  justify-content: center;
  padding-top: 10px;

  button {
    height: 35px;
    width: 180px;
    background-color: var(--accent-color);
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
    border: 0;
    box-shadow: 0 3px 6px rgba($color: #000000, $alpha: 0.16);

    &.back {
      background-color: #fff;
      border: 1px solid var(--border-color);
      margin-right: 50px;
      color: var(--text-color);
    }
  }
}
</style>