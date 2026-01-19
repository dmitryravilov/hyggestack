<template>
  <div class="relative">
    <button
      @click="showDropdown = !showDropdown"
      class="px-3 py-2 text-sm text-primary hover:text-accent transition-cozy"
    >
      Theme
    </button>

    <div
      v-if="showDropdown"
      class="absolute right-0 mt-2 w-64 bg-secondary border border-color rounded-lg shadow-lg p-4 z-10"
    >
      <div
        v-for="theme in themeStore.themes"
        :key="theme.id"
        @click="selectTheme(theme.id)"
        class="p-3 rounded-lg cursor-pointer hover:bg-accent-light transition-cozy mb-2"
        :class="{ 'bg-accent-light': themeStore.currentTheme === theme.id }"
      >
        <div class="font-medium text-primary">{{ theme.name }}</div>
        <div class="text-sm text-secondary">{{ theme.description }}</div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import { useThemeStore } from '@/stores/theme'

const themeStore = useThemeStore()
const showDropdown = ref(false)

function selectTheme(themeId) {
  themeStore.setTheme(themeId)
  showDropdown.value = false
}

function handleClickOutside(event) {
  if (!event.target.closest('.relative')) {
    showDropdown.value = false
  }
}

onMounted(() => {
  document.addEventListener('click', handleClickOutside)
})

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
})
</script>

