<template>
  <div
    id="app"
    :class="themeClass"
  >
    <router-view />
  </div>
</template>

<script setup>
import { computed, watch, onMounted } from 'vue'
import { useThemeStore } from '@/stores/theme'
import { useAuthStore } from '@/stores/auth'
import { applyTheme } from '@/utils/themeLoader'

const themeStore = useThemeStore()
const authStore = useAuthStore()

const themeClass = computed(() => {
  return `theme-${themeStore.currentTheme}`
})

// eslint-disable-next-line no-unused-expressions
watch(
  () => themeStore.currentTheme,
  newTheme => {
    applyTheme(newTheme)
  },
)

onMounted(async () => {
  applyTheme(themeStore.currentTheme)
  if (authStore.isAuthenticated() && !authStore.user) {
    await authStore.fetchUser()
  }
})
</script>

<style>
#app {
  min-height: 100vh;
  transition:
    background-color 0.6s ease,
    color 0.6s ease;
}
</style>
