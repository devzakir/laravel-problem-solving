<template>
  <div class="stamp_modal">
    <div class="inner" @dragover.prevent @drop.prevent @drop="dragFile">
      <a class="close_btn" @click="closeModal">
        <img src="/asset/icons/close.svg" />
      </a>
      <h2 class="ttl">見積書と納品書に会社印を表示できます。</h2>
      <p class="note">※既に作成済みの書類には反映されません。</p>

      <div class="image_upload">
        <p class="icon" v-if="!stamp">
          <img src="/asset/icons/albumn.svg" />
        </p>
        <p class="text" v-if="!stamp">画像をドロップ</p>
        <button @click="selectFile" v-if="!stamp">ファイルを選択</button>
        <p class="stamp" v-if="!!stamp">
          <img :src="stamp" />
        </p>
      </div>

      <div class="form__actions">
        <button @click="closeModal">戻る</button>
        <button @click="save">保存</button>
      </div>
    </div>

    <input class="dis-none" type="file" ref="stamp_selector" @change="changeStamp" accept="image/png, image/gif, image/jpeg" />
  </div>
</template>
<script>
export default {
  data() {
    return {
      stamp: null,
      stamp_file: null
    }
  },
  methods: {
    selectFile() {
      this.$refs.stamp_selector.click()
    },
    changeStamp(event) {
      const file = event.target.files[0]
      if (!!file) {
        this.stamp_file = file
        this.stamp = URL.createObjectURL(file)
      }
    },
    dragFile(e) {
      const file = e.dataTransfer.files[0]
      if (!!file) {
        this.stamp_file = file
        this.stamp = URL.createObjectURL(file)
      }
    },
    async save() {
      if (!this.stamp_file) {
        return
      }

      try {
        let formData = new FormData()
        formData.append('stamp', this.stamp_file)
        const { data } = await axios.post('/recipienter/save_stamp', formData, { headers: { 'Content-Type': 'multipart/form-data' }})
        this.$emit('savedStamp')
      } catch (error) {
      }
    },
    closeModal() {
      this.$emit('closeModal')
    }
  }
}
</script>
<style lang="scss" scoped>
.stamp_modal {
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
    width: 400px;
    transform: translate(-50%, -50%);
    background-color: #fff;
    border: 1px solid var(--border-color);
    box-shadow: 0 3px 6px rgba($color: #000000, $alpha: 0.16);
    padding: 50px 15px;
    border-radius: 5px;
  }
}

.close_btn {
  position: absolute;
  right: 10px;
  top: 10px;
}

.ttl {
  font-size: 14px;
  text-align: center;
}

.note {
  font-size: 10px;
  text-align: center;
  margin-bottom: 45px;
}

.form__actions {
  display: flex;
  align-items: center;
  justify-content: center;
  padding-top: 50px;

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

    &:last-of-type {
      background-color: var(--accent-color);
      color: #fff;
      border: 0;
    }
  }
}

.image_upload {
  width: 80px;
  height: 80px;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-left: auto;
  margin-right: auto;
  flex-direction: column;
  border:1px dotted #ccc;
  border-radius: 4px;
  position: relative;

  .icon {
    position: relative;
    width: 30px;
    height: 30px;
    overflow: hidden;
    margin-bottom: 4px;

    img {
      position: absolute;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      object-fit: cover;
    }
  }
  .text {
    font-size: 10px;
    color: #777;
  }
  button {
    width: 70px;
    height: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 8px;
    background-color: #aaa;
    color: #fff;
    border: 0;
    border-radius: 4px;
  }
  .stamp {
    position: absolute;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
  }
}
</style>