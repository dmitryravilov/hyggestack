<template>
  <div class="relative">
    <button
      class="text-primary hover:text-accent transition-cozy px-3 py-2 text-sm"
      @click="showDropdown = !showDropdown"
    >
      Theme
    </button>

    <div
      v-if="showDropdown"
      class="bg-secondary border-color absolute right-0 z-10 mt-2 w-64 rounded-lg border p-4 shadow-lg"
    >
      <div
        v-for="theme in themeStore.themes"
        :key="theme.id"
        class="hover:bg-accent-light transition-cozy mb-2 cursor-pointer rounded-lg p-3"
        :class="{ 'bg-accent-light': themeStore.currentTheme === theme.id }"
        @click="selectTheme(theme.id)"
      >
        <div class="text-primary font-medium">
          {{ theme.name }}
        </div>
        <div class="text-secondary text-sm">
          {{ theme.description }}
        </div>
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
