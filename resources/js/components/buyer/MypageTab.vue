<template>
	<div class="mypage__tab">
		<div class="main_container">
			<router-link :to="{ name: 'buyer.mypage.movies' }">購入済みの動画</router-link>
			<router-link :to="{ name: 'buyer.mypage.creators' }">クリエイター</router-link>
			<router-link :to="{ name: 'buyer.mypage.news' }">お知らせ</router-link>
		</div>
	</div>
</template>
<script>
export default {
	name: 'MypageTab'
}
</script>