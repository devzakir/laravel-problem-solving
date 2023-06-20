<template>
	<div class="news__item">
		<p v-if="!news.user_id" class="news__icon">
			<img src="/asset/icon_news1.png" />
		</p>
		<p v-else class="news__icon">
			<img src="/asset/icon_news2.png" />
		</p>
		<div class="news__content">
			{{ news.content }}
			<p class="news__date">
				{{ news.created_at | dateFormat }}
			</p>
		</div>
	</div>
</template>
<script>
export default {
	name: 'NewsItem',
	props: ['news']
}
</script>