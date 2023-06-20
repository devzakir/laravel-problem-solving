<template>
  <transition name="page" mode="out-in">
    <slot>
      <router-view :key="$route.fullPath" />
    </slot>
  </transition>
</template>

<script>
export default {
  name: 'Child'
}
</script>
