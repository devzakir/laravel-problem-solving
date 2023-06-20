<template>
  <div class="container">
    <div class="block">
      <h1>レースデータをインポート</h1>
      <div class="import_form">
        <div class="form__row">
          <label>日付</label>
          <p>
            <input type="date" v-model="form.date" />
          </p>
        </div>
        <div class="form__row">
          <label>JSONファイル</label>
          <input type="file" @change="changeFile" />
        </div>
        <div class="form__action">
          <button @click="importJson">インポートする</button>
        </div>
      </div>
    </div>

    <div class="block">
      <h1>馬データーをインポート</h1>
      <div class="import_form">
        <div class="form__row">
          <label>Txtファイル</label>
          <input type="file" @change="changeFile1" />
        </div>
        <div class="form__action">
          <button @click="importTxt">インポートする</button>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
export default {
  layout: 'default',
  data() {
    return {
      form: {
        date: undefined,
        file: undefined,
        name: '',
        file1: undefined,
      }
    }
  },
  mounted() {
  },
  methods: {
    changeFile(event) {
      const file = event.target.files[0]
      if (!!file) {
        this.form.file = file
        this.form.name = file.name
      }
    },
    changeFile1(event) {
      const file = event.target.files[0]
      if (!!file) {
        this.form.file1 = file
      }
    },
    async importJson() {
      if (!this.form.date || !this.form.file) {
        return
      }

      let formData = new FormData();
      formData.append('date', this.form.date)
      formData.append('file', this.form.file)
      formData.append('name', this.form.name)

      try {
        const { data } = await axios.post('/api/import_json_data', formData, { headers: { 'Content-Type': 'multipart/form-data' } })
      } catch (error) {
        throw(error)
      }
    },
    async importTxt() {
      if (!this.form.file1) {
        return
      }

      let formData = new FormData();
      formData.append('file', this.form.file1)

      try {
        const { data } = await axios.post('/api/import_txt_file', formData, { headers: { 'Content-Type': 'multipart/form-data' } })
        console.log(data, '______123123123')
      } catch (error) {
        throw(error)
      }
    }
  }
}
</script>
<style lang="scss" scoped>
.container {
  width: 1000px;
  margin-left: auto;
  margin-right: auto;
  padding-top: 30px;
}
h1 {
  font-size: 16px;
  font-weight: bold;
  padding-bottom: 10px;
  border-bottom: 2px solid #f09415;
}
.form__row {
  margin-bottom: 20px;
  display: flex;
  align-items: center;

  label {
    font-size: 14px;
    width: 140px;
  }

  p {
    input[type=text] {
      height: 40px;
      border: 1px solid #ddd;
    }
  }
}

.block {
  border: 1px solid #ccc;
  box-shadow: 0 2px 3px 0 rgba($color: #000000, $alpha: 0.25);
  padding: 20px;
  border-radius: 10px;
  margin-bottom: 15px;
}
</style>