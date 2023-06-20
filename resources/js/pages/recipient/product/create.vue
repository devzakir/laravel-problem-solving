<template>
  <div class="create__container">
    <h2 class="page__title">商品登録</h2>

    <div class="main__part">
      <p class="note" v-if="step == 2">{{ rows.length }}件の商品を登録します。よろしいですか？</p>
      <div class="heading" v-if="step == 1">
        <button @click="csvUpload"><img src="/asset/icons/csv.svg" />CSV一括登録</button>
        <button @click="toColumnSettingScreen"><img src="/asset/icons/setting1.svg" />項目設定</button>
      </div>
      <div class="table_cont" v-if="rows.length > 0">
        <vue-good-table
          :columns="columns"
          :rows="rows"
          :pagination-options="{
            enabled: false,
          }">
          <template slot="table-row" slot-scope="props">
            <div class="mark" v-if="props.column.field == 'mark'">
              <select :value="props.row.mark" @change="changedField($event, 'mark', props.row.index)" :disabled="step == 2">
                <option>SALE</option>
                <option>NEW</option>
                <option>予約販売</option>
                <option>再入荷</option>
                <option>売れ筋</option>
                <option>残りわずか</option>
                <option>完売</option>
                <option>マークなし</option>
              </select>
              <!-- <input type="text" placeholder="マークなし" @change="changedField($event, 'mark', props.row.index)" :disabled="step == 2" /> -->
            </div>
            <div class="toggle" v-else-if="props.column.field == 'is_public'">
              <toggle-button @change="changedToggleBtn($event, props.row.index)" :width="88" :value="props.row.is_public == 1" :color="{unchecked: '#707070', checked: '#337DFD'}" :labels="{unchecked: '非公開', checked: '公開'}" :disabled="step == 2"/>
            </div>
            <div v-else-if="props.column.field == 'order'">
              <input class="order" type="number" :value="props.row.order" @change="changedField($event, 'order', props.row.index)" :disabled="step == 2" />
            </div>
            <div v-else-if="props.column.field == 'name'">
              <input class="name" type="text" :value="props.row.name" @change="changedField($event, 'name', props.row.index)" :disabled="step == 2" />
            </div>
            <div v-else-if="props.column.field == 'price'">
              <input class="price" type="number" :value="props.row.price" @change="changedField($event, 'price', props.row.index)" :disabled="step == 2" />
            </div>
            <div v-else-if="props.column.field == 'tax'">
              <input class="price" :checked="props.row.tax == 2" type="checkbox" @change="changeTax($event, props.row.index)" :disabled="step == 2" />
            </div>
            <div v-else-if="props.column.field == 'amount'">
              <input class="amount" type="number" :value="props.row.amount" @change="changedField($event, 'amount', props.row.index)"  :disabled="step == 2"/>
            </div>
            <div v-else-if="props.column.field == 'total_price'">
              <input class="total_price" type="number" :value="props.row.total_price" @change="changedField($event, 'total_price', props.row.index)" :disabled="step == 2" />
            </div>
            <div v-else-if="props.column.field == 'remark'">
              <input class="remark" type="text" :value="props.row.remark" @change="changedField($event, 'remark', props.row.index)" :disabled="step == 2" />
            </div>
            <div v-else-if="props.column.field == 'maker'">
              <input class="maker" type="text" :value="props.row.maker" @change="changedField($event, 'maker', props.row.index)" :disabled="step == 2"/>
            </div>
            <div v-else-if="props.column.field == 'color'">
              <input class="color" type="text" :value="props.row.color" @change="changedField($event, 'color', props.row.index)" :disabled="step == 2" />
            </div>
            <div v-else-if="props.column.field == 'size'">
              <input class="size" type="text" :value="props.row.size" @change="changedField($event, 'size', props.row.index)" :disabled="step == 2" />
            </div>
            <div v-else-if="props.column.field == 'unit'">
              <input class="unit" type="text" :value="props.row.unit" @change="changedField($event, 'unit', props.row.index)" :disabled="step == 2" />
            </div>
            <div v-else-if="props.column.field == 'image'">
              <a class="image__btn" @click="selectImage(props.row.index)">
                <img :src="calcImageUrl(props.row.image)" />
              </a>
            </div>
            <div v-else-if="props.column.field == 'jan'">
              <input class="jan" type="text" :value="props.row.jan" @change="changedField($event, 'jan', props.row.index)" :disabled="step == 2" />
            </div>
            <div v-else-if="props.column.field == 'free_space1'">
              <input class="free_space1" type="text" :value="props.row.free_space1" @change="changedField($event, 'free_space1', props.row.index)" :disabled="step == 2" />
            </div>
            <div v-else-if="props.column.field == 'free_space2'">
              <input class="free_space2" type="text" :value="props.row.free_space2" @change="changedField($event, 'free_space2', props.row.index)" :disabled="step == 2" />
            </div>
            <div v-else-if="props.column.field == 'free_space3'">
              <input class="free_space3" type="text" :value="props.row.free_space3" @change="changedField($event, 'free_space3', props.row.index)" :disabled="step == 2" />
            </div>
            <div v-else-if="props.column.field == 'edit'">
              <button class="detail_btn">編集</button>
            </div>
            <div v-else-if="props.column.field == 'delete'">
              <a class="delete_btn" @click="deleteItem(props.row.index)"><img src="/asset/icons/delete.svg" /></a>
            </div>
            <div class="table_cell" v-else>
              {{ props.formattedRow[props.column.field] }}
            </div>
          </template>
        </vue-good-table>
      </div>
      <div class="append_row" v-if="step == 1">
        <button @click="appendRow">行を追加</button>
      </div>

      <div class="actions">
        <button @click="back">戻る</button>
        <button @click="toConfirmScreen">{{ step == 1 ? '確認画面へ' : '登録' }}</button>
      </div>
    </div>

    <input type="file" class="image_selector dis-none" @change="changeImage" ref="image_selector" />
    <input type="file" class="csv_selector dis-none" @change="changeCSV" ref="csv_selector" accept=".csv" />
  </div>
</template>
<script>
export default {
  layout: 'recipient',
  middleware: 'recipient',
  data() {
    return {
      columns: [
        {
          label: 'ステータス',
          field: 'is_public',
          sortable: false,
        },
        {
          label: 'マーク',
          field: 'mark',
          sortable: false,
        }
      ],
      rows: [],
      step: 1,
      tempIndex: null
    }
  },
  mounted() {
    this.init()
  },
  methods: {
    selectImage(index) {
      this.tempIndex = index
      this.$refs.image_selector.click()
    },
    csvUpload() {
      this.$refs.csv_selector.click()
    },
    changeCSV(event) {
      const file = event.target.files[0]
      if (!!file) {
        let url = URL.createObjectURL(file)
        let req = new XMLHttpRequest()
        req.open('get', url, true)
        req.send(null)
        let self = this
        req.onload = function() {
          self.convertCSVtoArray(req.responseText)
        }
      }
    },
    convertCSVtoArray(str) {
      let result = [];
      let tmp = str.split('\n')
      for(var i=0; i<tmp.length; ++i) {
        result[i] = tmp[i].split(',')
        result[i].pop()
      }
      result = result.filter((item, index) => {
        return index > 0
      })
      result.forEach(item => {
        this.rows.push({
          name: item[0],
          price: parseInt(item[1]),
          is_public: 1,
          mark: null,
          amount: parseInt(item[2]),
          total_price: parseInt(item[3]),
          remark: item[4],
          color: item[5],
          size: item[6],
          unit: item[7],
          image: null,
          jan: item[9],
          order: item[10],
          free_space1: item[11],
          free_space2: item[12],
          free_space3: item[13],
          index: this.rows.length,
          temp_tax_type: null
        })
      })
    },
    changeImage(event) {
      const file = event.target.files[0]
      if (!!file) {
        this.rows[this.tempIndex].image = file
      }
    },
    back() {
      if (this.step == 1) {
        this.$router.back()
      } else if (this.step == 2) {
        this.step = 1
      }
    },
    calcImageUrl(image) {
      if (!!image) {
        return URL.createObjectURL(image)
      } else {
        return '/asset/no_image.svg'
      }
    },
    toConfirmScreen() {
      let flag = false
      this.rows.forEach(row => {
        this.columns.forEach(column => {
          if (column.field != 'delete' && column.field != 'image') {
            if(row[column.field] == null) {
              flag = true
            }
          }
        })
      })
      if (flag) {
        this.$swal('', '必須項目を入力してください')
        return
      }

      if (this.step == 1) {
        this.step = 2
      } else {
        this.registerProduct()
      }
    },
    async registerProduct() {
      try {
        await Promise.all(this.rows.map(async item => {
          let formData = new FormData()
          Object.entries(item).forEach(entry => {
            const [key, value] = entry;
            formData.append(key, value)
          })
          await axios.post('/recipienter/save_products', formData, { headers: { 'Content-Type': 'multipart/form-data' }})
        }))
        this.$swal('', '商品追加しました')
        this.$router.push({ name: 'recipient.product' })
      } catch (error) {}
    },
    toColumnSettingScreen() {
      this.$router.push({ name: 'recipient.product.column_setting' })
    },
    changedField(event, slug, index) {
      this.rows[index][slug] = event.target.value
    },
    changeTax(event, index) {
      let temp = this.rows.slice()
      temp[index]['tax'] = event.target.checked ? 2 : 1
      this.rows = temp
    },
    deleteItem(index) {
      let temp = [...this.rows]
      temp.splice(index, 1)
      this.rows = temp
    },
    changedToggleBtn(event, index) {
      let temp = this.rows.slice().map((item, idex) => {
        if (idex == index) {
          return {
            ...item,
            is_public: event.value == true ? 1: 0
          }
        } else {
          return item
        }
      })
      this.rows = temp
    },
    async init() {
      try {
        const { data } = await axios.post('/recipienter/get_column_info')
        if (!data.userColumns[0].nickname) {
          this.$swal('', 'まずは項目設定から行なってください。')
          this.$router.push({ name: 'recipient.product.column_setting' })
          return
        }
        let picked_columns = data.columns.filter(item => {
          if(data.userColumns.length > 0) {
            let column = data.userColumns.find(it => {
              return it.product_column_id == item.id
            })
            return column.picked == 1
          } else {
            return item.picked == 1
          }
        }).map(item => {
          if(data.userColumns.length > 0) {
            let column = data.userColumns.find(it => {
              return it.product_column_id == item.id
            })
            if (column.nickname == '発注単価') {
              this.temp_tax_type = column.tax_type
            }
            return {
              ...item,
              order: column.order,
              tax: column.tax,
              tax_type: column.tax_type,
              name: column.nickname
            }
          } else {
            return item
          }
        })
        picked_columns.sort((a, b) => a.order - b.order)
        picked_columns.forEach(item => {
          if (item.slug != 'total_price') {
            if (item.slug == 'price') {
              this.columns.push({
                label: item.name + (item.tax_type == 1 ? '（税抜）' : ( item.tax_type == 2 ? '（税込）' : '（非課税）' )),
                field: item.slug,
                sortable: item.type == 'number' ? true : false
              })
              if (item.tax_type != 3 && item.tax == 2) {
                this.columns.push({
                  label: '軽減税率',
                  field: 'tax',
                  sortable: false,
                  width: '60px'
                })
              }
            } else {
              this.columns.push({
                label: item.name,
                field: item.slug,
                sortable: item.type == 'number' ? true : false
              })
            }
          }
        })
        this.columns.push({
          label: '削除',
          field: 'delete',
          sortable: false
        })
        this.rows.push({
          name: null,
          price: null,
          tax: 1,
          tax_type: this.temp_tax_type,
          is_public: 1,
          mark: null,
          amount: null,
          total_price: null,
          remark: null,
          color: null,
          size: null,
          unit: null,
          image: null,
          jan: null,
          order: null,
          free_space1: null,
          free_space2: null,
          free_space3: null,
          index: 0,
        })
      } catch (error) {
      }
    },

    appendRow() {
      this.rows.push({
        name: null,
        price: null,
        tax: 1,
        tax_type: this.temp_tax_type,
        is_public: 1,
        mark: null,
        amount: null,
        total_price: null,
        remark: null,
        color: null,
        size: null,
        unit: null,
        image: null,
        jan: null,
        order: null,
        free_space1: null,
        free_space2: null,
        free_space3: null,
        index: this.rows.length
      })
    }
  }
}
</script>
<style lang="scss" scoped>
.create__container {
  padding: 20px 30px;

  .main__part {
    margin-top: 60px;
    width: 1200px;

    .heading {
      display: flex;
      align-items: center;
      justify-content: flex-end;
      margin-bottom: 20px;
      width: 100%;

      button {
        width: 120px;
        height: 30px;
        background-color: #fff;
        box-shadow: 0 3px 6px rgba($color: #000000, $alpha: 0.16);
        margin-right: 16px;
        display: flex;
        align-items: center;
        border: 0;
        justify-content: center;

        img {
          width: 16px;
          margin-right: 5px;
        }

        &:last-of-type {
          margin-right: 0;
        }
      }
    }
  }
}

// .mark {
//   width: 70px;
//   height: 20px;
//   display: flex;
//   align-items: center;
//   justify-content: center;
//   background-color: #D04F4F;
//   color: #fff;
//   font-size: 12px;
// }

.table_cont {
  box-shadow: 0 3px 6px rgba($color: #000000, $alpha: 0.16);
  
  input {
    &.input1 {
      width: 50px;
    }

    &.input2 {
      width: 150px;
    }

    &.input3 {
      width: 120px;
    }

    &.input4 {
      width: 50px;
    }
  }

  select {
    height: 30px;
  }
}

.append_row {
  padding-top: 25px;
  display: flex;
  align-items: center;
  justify-content: flex-end;

  button {
    width: 120px;
    height: 30px;
    background-color: #fff;
    box-shadow: 0 3px 6px rgba($color: #000000, $alpha: 0.16);
    border: 0; 
  }
}
.actions {
	display: flex;
	align-items: center;
	justify-content: center;
	padding-top: 40px;
	button {
		width: 120px;
		height: 30px;
		display: flex;
		align-items: center;
		justify-content: center;
		box-shadow: 0 3px 6px rgba($color: #000000, $alpha: 0.16);
		color: #fff;
		border: 0;
		&:first-of-type {
			background-color: var(--border-color);
			margin-right: 60px;
		}
		&:last-of-type {
			background-color: var(--accent-color);
		}
	}
}
// .mark {
//   width: 70px;
//   height: 20px;
//   border: 1px solid var(--border-color);
// }

.note {
  margin-bottom: 50px;
}

.image__btn {
  width: 50px;
  height: 50px;
  position: relative;
  overflow: hidden;
  display: flex;

  img {
    position: absolute;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
  }
}
</style>