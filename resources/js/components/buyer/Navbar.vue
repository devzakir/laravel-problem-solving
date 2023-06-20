<template>
	<div class="buyer__navbar">
		<router-link :to="{ name: 'top' }" class="logo">
			<h1>
				<img src="/asset/logo.png" alt="Plus Douga" />
			</h1>
		</router-link>

		<div class="top__search_panel">
			<p>
				<input type="text" v-model="keyword" placeholder="検索キーワードを入力してください" />
			</p>
			<a @click="search">
				<img src="/asset/icon_search.png" alt="検索ボタン" />
			</a>
		</div>

		<!-- profile icon -->
		<a v-if="!!$store.getters['auth/user'] && $store.getters['auth/user'].role_id == 1" class="profile__icon" @click="clickAvatarIcon">
			<img :src="$store.getters['auth/user'].avatar_url" />
		</a>

		<router-link v-else :to="{ name: 'buyer.login' }" class="signup__btn">
			ログイン
		</router-link>

		<div class="profile__dropdown" v-if="!!$store.getters['auth/user']" v-click-outside="hide" :class="{ dropped: isDropDownMenuShow }">
			<div class="profile__info">
				<p class="avatar">
					<img :src="$store.getters['auth/user'].avatar_url" />
				</p>
				<div class="profile_name">
					<p class="name">{{ $store.getters['auth/user'].name }}</p>
					<p class="email">{{ $store.getters['auth/user'].email }}</p>
				</div>
			</div>
			<ul class="link__part">
				<li>
					<router-link :to="{ name: 'buyer.mypage.movies' }" class="dougas">購入済みの動画</router-link>
				</li>
				<li>
					<router-link :to="{ name: 'buyer.mypage.creators' }" class="mypage">クリエイター</router-link>
				</li>
				<li>
					<router-link :to="{ name: 'buyer.account' }" class="account">プロフィール編集</router-link>
				</li>
				<li>
					<a @click="logout">ログアウト</a>
				</li>
			</ul>
		</div>
	</div>
</template>
<script>
import ClickOutside from 'vue-click-outside'
export default {
	name: 'Navbar',
	data() {
		return {
			isDropDownMenuShow: false,
			keyword: ''
		}
	},

	created() {
		console.log(this.$store.getters['auth/user'])
	},

	watch:{
		$route (to, from){
			console.log(to)
			this.isDropDownMenuShow = false
		}
	},

	methods: {
		clickAvatarIcon(event) {
			event.stopPropagation()
			this.isDropDownMenuShow = !this.isDropDownMenuShow
		},

		hide() {
			this.isDropDownMenuShow = false
		},

		async logout() {
			// Log out the user
			await this.$store.dispatch('auth/logout')

			this.hide()

			// Redirect to login.
      		this.$router.push({ name: 'buyer.login' })
		},

		search() {
			this.$router.push({ name: 'movies.search', query: { keyword: this.keyword } }).catch(()=>{});
		}
	},

	directives: {
		ClickOutside
	}
}
</script>