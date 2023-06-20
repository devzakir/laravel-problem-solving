<template>
	<div class="bread_crumb">
		<template v-for="(item, index) in items">
			<router-link v-if="index != items.length-1" :to="{ name: item.link }" :key="index">{{ item.name }}</router-link>
			<span v-else>{{ item.name }}</span>
			<span v-if="index != items.length-1">></span>
		</template>
	</div>
</template>
<script>
export default {
	props: ['items'],
	name: 'BreadCrumb'
}
</script>