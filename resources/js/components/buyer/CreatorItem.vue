<template>
	<div class="creator__item" v-if="!!creator">
		<div class="avatar">
			<p class="img">
				<img src="/asset/dummy03.jpg" />
			</p>
		</div>

		<div class="creator__info">
			<p class="nickname">
				{{ creator.name }}
			</p>

			<div class="count">
				<div class="count__row" v-if="!!creator.movies">
					<p>投稿数</p>
					<p>{{ creator.movies.length }}件</p>
				</div>
				<div class="count__row" v-if="!!creator.payments">
					<p>販売実績</p>
					<p>{{ creator.payments.length }}件</p>
				</div>
				<div class="count__row" v-if="!!creator.comments">
					<p>コメント数</p>
					<p>{{ creator.comments.length }}件</p>
				</div>
			</div>

			<router-link :to="{ name: 'movies.search', query: { creator_id: creator.id } }" class="donate__btn">動画一覧へ</router-link>
		</div>
	</div>
</template>
<script>
export default {
	name: 'CreatorItem',
	props: ['creator'],

	data() {
		return {
			donate: {
				price: 0,
				movie_id: this.$route.params.id,
				creator_id: undefined,
				type: 'donate',
			},
		}
	},

	mounted() {
		console.log(this.$props.creator)
	},

	methods: {
	}
}
</script>
<style lang="scss" scoped>
.donate__btn {
	&:hover {
		text-decoration: none !important;
	}
}
</style>