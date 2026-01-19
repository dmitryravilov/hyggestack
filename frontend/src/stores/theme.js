import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import { themes as themeConfigs, applyTheme } from '@/utils/themeLoader'

export const useThemeStore = defineStore('theme', () => {
  const currentTheme = ref(localStorage.getItem('theme') || 'soft-evening')

  const themes = computed(() => {
    return themeConfigs.map(theme => ({
      id: theme.id,
      name: theme.name,
      description: theme.description,
    }))
  })

  function setTheme(themeId) {
    currentTheme.value = themeId
    localStorage.setItem('theme', themeId)
    applyTheme(themeId)
  }

  return {
    currentTheme,
    themes,
    setTheme,
  }
})
