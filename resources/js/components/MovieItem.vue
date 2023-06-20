<template>
	<div class="movie__item" v-hammer:tap="clickMovieItem">
		<div class="thumbnail">
			<img :src="'/storage/'+movie.thumbnail" />

			<span class="length">
				{{ movie.duration | movieDuration }}
			</span>

			<span class="newly" v-if="isNew()">
				<img src="/asset/icon_newly.png" alt="newly" />
			</span>
		</div>

		<div class="detail_cont">
			<h3>
				{{ movie.title | truncate(50) }}
			</h3>
			<p class="date">
				{{ movie.created_at | yearFormat }}<span>年</span>{{ movie.created_at | monthFormat }}<span>月</span>{{ movie.created_at | dayFormat }}<span>日</span>
			</p>
			<div class="detail__action">
				<div class="detail__status">
					<p>
						<img src="/asset/icon_comment.png" />
						<span>{{ comments.length }}</span>
					</p>
					<p>
						<img src="/asset/icon_payment.png" />
						<span>{{ donatePayments.length }}</span>
					</p>
				</div>
				<a class="buy" v-if="!buyFlag">
					購入画面へ
				</a>
				<a class="comment" v-else>
					コメント履歴
				</a>
			</div>
			<div class="creator__info">
				<p class="img">
					<img :src="movie.creator.avatar_url" />
				</p>
				<p>
					{{ movie.creator.name }}
				</p>
			</div>
		</div>
	</div>
</template>
<script>
export default {
	props: ['buyCheck', 'movie'],

	name: 'MovieItem',

	data() {
		return {
			comments: [],
			donatePayments: [],
			buyFlag: false
		}
	},

	watch: {
		movie: {
			immediate: true,
			handler(movie) {
				if (movie) {
					this.comments = movie.comments
					this.donatePayments = movie.donate_payments
				}
			}
		},
	},

	mounted() {
		// check buy status
		if (!!this.$store.getters['auth/check'])
		this.$props.movie.buy_payments.forEach(item => {
			if (item.buyer_id == this.$store.getters['auth/user'].id) {
				this.buyFlag = true
				return
			}
		})
	},

	methods: {
		clickMovieItem() {
			this.$router.push({ name: 'buyer.movie', params: { id: this.$props.movie.id } })
		},

		isNew() {
			var created_date = new Date(this.$props.movie.created_at)
			var today = new Date();
			created_date.setDate(created_date.getDate() + 7)
			
			return created_date > today
		},
	}
}
</script>