<template>
	<div class="footer__container">
		<div class="page__top">
			<a @click="clickPageTop">
				<p>
					<i class="fas fa-arrow-alt-circle-up"></i>
				</p>
				<p>
					ページトップへ戻る
				</p>
			</a>
		</div>
		<div class="footer_cont">
			<ul class="footer_link" v-if="!!$store.getters['auth/check'] && $store.getters['auth/user'].role_id == 1">
				<li>
					<router-link :to="{ name: 'buyer.mypage.movies' }">マイページ</router-link>
				</li>
				<li>
					<router-link :to="{ name: 'buyer.mypage.news' }">お知らせ</router-link>
				</li>
				<li>
					<router-link :to="{ name: 'buyer.mypage.movies' }">購入履歴</router-link>
				</li>
				<li>
					<router-link :to="{ name: 'term' }">利用規約</router-link>
				</li>
				<li>
					<router-link :to="{ name: 'commercial' }">特商法</router-link>
				</li>
			</ul>
			<ul class="footer_link" v-if="!$store.getters['auth/check']">
				<li>
					<router-link :to="{ name: 'term' }">利用規約</router-link>
				</li>
				<li>
					<router-link :to="{ name: 'commercial' }">特商法</router-link>
				</li>
			</ul>
			<p>
				Copyright@ plus douga ALL RIGHT Reserved.
			</p>
		</div>
	</div>
</template>
<script>
export default {
	name: 'Footer',

	methods: {
		clickPageTop() {
			window.scrollTo(0, 0)
		}
	}
}
</script>
<style lang="scss" scoped>
.footer_link {
	li {
		a {
			&:hover {
				text-decoration: none !important;
				color: white !important;
			}
		}
	}
}
</style>