<template>
	<div class="column_setting" v-drag-and-drop:options="options">
		<h2 class="page__title">項目設定</h2>
		<div class="part1">
			<p class="note">注文フォームに表示する項目をドラッグ＆ドロップで設定できます。</p>
			<ul class="drag_part">
				<li :data-id="item.id" class="drag__item" v-for="(item, index) in available_columns" :key="index">
					<img src="/asset/icons/drag_icon.svg" />
					<span>{{ item.name }}</span>
				</li>
				<span class="arrow">
					<img src="/asset/icons/arrow_bg.svg" />
				</span>
			</ul>
		</div>
		<h2 class="page__title page__title3">現在の注文フォーム</h2>
		<div class="drop_part">
			<ul class="drop_part_heading"
				@added="added"
				@reordered="reordered">
				<li :data-id="item.id" class="drop__item" v-for="(item, index) in picked_columns" :key="index">
					<img src="/asset/icons/drag_icon.svg" />
					<span v-if="item.slug != 'free_space1' && item.slug != 'free_space2' && item.slug != 'free_space3'">{{ item.name }}</span>
					<span v-else>
						<input placeholder="フリースペース" type="text" :value="item.name" @change="changeFreeSpace($event, item.slug, index)" />
					</span>
					<span v-if="item.slug == 'price'">
						<button @click="settingTax(item.tax, item.tax_type)">税設定</button>
					</span>
					<a @click="removeItem(item, index)" v-if="item.needed == 0" class="remove_btn"><img src="/asset/icons/remove_icon.svg" /></a>
				</li>
			</ul>
		</div>
		<div class="actions">
			<button @click="toProductListScreen">商品一覧に戻る</button>
			<button @click="changeProductColumn">登録する</button>
		</div>
		<vue-modal-2 name="confirm_modal" @on-close="close" :headerOptions="headerOptions" :footerOptions="footerOptions">
			<p class="confirm_text">項目設定を変更します。よろしいでしょうか？<br/>※登録後３分間は変更ができません。</p>
		</vue-modal-2>

		<TaxSettingModal v-if="isShowModal" @onCloseModal="closeModal" :tempTax="tempTax" :tempTaxType="tempTaxType" @onSaveTax="changeTax"/>
	</div>
</template>
<script>
import TaxSettingModal from '../../../components/recipient/TaxSettingModal.vue'
import moment from 'moment'
export default {
	layout: 'recipient',
	middleware: 'recipient',
	components: {
		TaxSettingModal
	},
	data() {
		return {
			available_columns: [],
			picked_columns: [],
			options: {
				dropzoneSelector: 'ul',
				draggableSelector: 'li',
				handlerSelector: null,
				reactivityEnabled: true,
				multipleDropzonesItemsDraggingEnabled: true,
				showDropzoneAreas: true,
				onDrop: function(event) {},
				onDragstart: function(event) {},
				onDragenter: function(event) {},
				onDragover: function(event) {},
				onDragend: function(event) {}
			},
			headerOptions: {
				title: "項目設定"
			},
			footerOptions: {
				btn1: "キャンセル",
				btn2: "同意する",
				btn1OnClick: () => {
					this.$vm2.close('confirm_modal')
				},
				btn2OnClick: () => {
					this.changeProductColumnConfirm()
					this.$vm2.close('confirm_modal')
				}
			},
			isShowModal: false,
			tempTax: null,
			tempTaxType: null,
			last_updated: null
		}
	},
	mounted() {
		this.init()
	},
	methods: {
		closeModal() {
			this.isShowModal = false
		},
		settingTax(tax, tax_type) {
			this.tempTax = tax
			this.tempTaxType = tax_type
			this.isShowModal = true
		},
		async init() {
			const { data } = await axios.post('/recipienter/get_column_info')
			let available_columns = data.columns.filter(item => {
				if(data.userColumns.length > 0) {
					let column = data.userColumns.find(it => {
						return it.product_column_id == item.id
					})
					return column.picked == 0
				} else {
					return item.picked == 0
				}
			}).map(item => {
				if(data.userColumns.length > 0) {
					let column = data.userColumns.find(it => {
						return it.product_column_id == item.id
					})
					return {
						...item,
						order: column.order,
						name: !!column.nickname ? column.nickname : item.name,
						tax: column.tax,
						tax_type: column.tax_type
					}
				} else {
					return item
				}
			})
			available_columns.sort((a, b) => a.order - b.order)
			this.available_columns = available_columns
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
					return {
						...item,
						order: column.order,
						name: !!column.nickname ? column.nickname : item.name,
						tax: column.tax,
						tax_type: column.tax_type
					}
				} else {
					return item
				}
			})
			picked_columns.sort((a, b) => a.order - b.order)
			this.picked_columns = picked_columns
		},
		changeFreeSpace(event, slug, index) {
			let temp = this.picked_columns.slice()
			temp = temp.map(item => {
				if (item.slug == slug) {
					return {
						...item,
						name: event.target.value
					}
				} else {
					return item
				}
			})
			this.picked_columns = temp
		},
		changePriceTax(event) {
			let temp = this.picked_columns.slice()
			temp = temp.map(item => {
				if (item.slug == 'price') {
					return {
						...item,
						tax: event.target.value,
					}
				} else {
					return item
				}
			})
			this.picked_columns = temp
		},
		changeTax(tax, tax_type) {
			let temp = this.picked_columns.slice()
			temp = temp.map(item => {
				if (item.slug == 'price') {
					return {
						...item,
						tax: tax,
						tax_type: tax_type
					}
				} else {
					return item
				}
			})
			this.picked_columns = temp
			this.isShowModal = false
		},
		added(event) {
			const itemId = event.detail.ids[0]
			const index = event.detail.index
			let temp_available = this.available_columns
			let fromIndex = 0
			let fromItem = null
			temp_available.forEach((item, idex) => {
				if (item.id == itemId) {
					fromIndex = idex
					fromItem = item
				}
			})
			temp_available.splice(fromIndex, 1)
			this.available_columns = temp_available
			this.picked_columns.splice(index, 0, fromItem)
		},
		reordered(event) {
			const itemId = event.detail.ids[0]
			const index = event.detail.index
			let temp = this.picked_columns
			let fromIndex = 0
			let fromItem = null
			temp.forEach((item, idex) => {
				if (item.id == itemId) {
					fromIndex = idex
					fromItem = item
				}
			})
			temp.splice(fromIndex, 1)
			this.picked_columns = temp
			this.picked_columns.splice(index, 0, fromItem)
		},
		removeItem(item, index) {
			this.available_columns.push(item)
			this.picked_columns.splice(index, 1)
		},
		toProductListScreen() {
			this.$router.push({ name: 'recipient.product' })
		},
		changeProductColumn() {
			this.$vm2.open('confirm_modal')
		},
		async changeProductColumnConfirm() {
			if (!!this.last_updated) {
				let diff = moment().diff(this.last_updated, 'minutes')
				if (diff < 3) {
					this.$swal('','最後登録した後３分間登録できません。')
					return
				}
			}

			this.picked_columns = this.picked_columns.map((item, index) => {
				return {
					...item,	
					order: index,
					picked: 1,
					tax: item.tax || 1,
					tax_type: item.tax_type || 1
				}
			})
			this.available_columns = this.available_columns.map((item, index) => {
				return {
					...item,
					order: index + this.picked_columns.length,
					picked: 0,
					tax: 1,
					tax_type: 1
				}
			})
			let merged_arr = this.picked_columns.concat(this.available_columns)
			try {
				await Promise.all(merged_arr.map(async item => {
					await axios.post('/recipienter/change_product_column', item)
				}))
				this.last_updated = moment()
				this.$swal('', '項目設定しました')
			} catch (error) {
			}
		},
		close() {
			this.$vm2.close('confirm_modal')
		}
	}
}
</script>
<style lang="scss" scoped>
.column_setting {
	padding: 20px 30px;
}
.note {
	font-size: 14px;
	margin-top: 12px;
	margin-bottom: 20px;
	color: #4B4B4B;
}
.part1 {
	position: relative;
}
.drag_part {
	width: 1050px;
	border: 1px solid var(--border-color);
	border-radius: 5px;
	background: #fff;
	padding: 25px 40px 0;
	display: flex;
	flex-wrap: wrap;
	position: relative;
	margin-bottom: 60px;
	.drag__item {
		height: 50px;
		padding-left: 27px;
		padding-right: 55px;
		background-color: #8D8B8B;
		color: #fff;
		font-size: 14px;
		display: flex;
		align-items: center;
		position: relative;
		margin-right: 30px;
		margin-bottom: 35px;
		border-radius: 5px;
		cursor: pointer;
		img {
			position: absolute;
			left: 5px;
			top: 50%;
			transform: translate(0, -50%);
		}
	}
}
.arrow {
	position: absolute;
	left: 50%;
	bottom: 0;
	transform: translate(0, 100%);
	width: 80px;
}
.drop_part {
	background-color: #fff;
	border: 1px solid var(--border-color);
	border-radius: 5px;
	padding: 40px 45px;
	margin-top: 10px;
	width: 1050px;
	overflow: auto;
	.drop_part_heading {
		display: flex;
		align-items: center;
		width: fit-content;
		padding-right: 160px;
		.drop__item {
			height: 50px;
			padding-left: 27px;
			padding-right: 55px;
			background-color: #8D8B8B;
			color: #fff;
			font-size: 14px;
			display: flex;
			align-items: center;
			position: relative;
			margin-right: 10px;
			border-radius: 5px;
			word-break: keep-all;
			width: fit-content;
			cursor: pointer;
			position: relative;
			img {
				position: absolute;
				left: 5px;
				top: 50%;
				transform: translate(0, -50%);
			}
			input {
				height: 30px;
			}
			select {
				height: 30px;
				margin-left: 5px;
			}

			button {
				height: 30px;
				padding-left: 8px;
				padding-right: 8px;
				border-radius: 5px;
				display: flex;
				align-items: center;
				justify-content: center;
				background-color: #fff;
				border: 0;
				margin-left: 10px;
			}
			.remove_btn {
				position: absolute;
				right: -10px;
				top: -10px;

				img {
					width: 35px;
					position: relative;
					left: 0;
					top: 0;
					transform: none;
				}
			}
		}
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
.confirm_text {
	text-align: center;
}
</style>