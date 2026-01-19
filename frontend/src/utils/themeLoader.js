import nordicMinimal from '@themes/nordic-minimal.json'
import warmCoffeehouse from '@themes/warm-coffeehouse.json'
import softEvening from '@themes/soft-evening.json'

export const themes = [softEvening, nordicMinimal, warmCoffeehouse]

export function getTheme(themeId) {
  return themes.find(theme => theme.id === themeId) || null
}

export function applyTheme(themeId) {
  const theme = getTheme(themeId)
  if (!theme) {
    console.warn(`Theme "${themeId}" not found`)
    return
  }

  const appElement = document.getElementById('app')
  if (appElement) {
    Object.entries(theme.colors).forEach(([key, value]) => {
      appElement.style.setProperty(`--${key}`, value)
    })
  }
}

export function generateThemeCSS(theme) {
  const cssVars = Object.entries(theme.colors)
    .map(([key, value]) => `  --${key}: ${value};`)
    .join('\n')

  return `.theme-${theme.id} {\n${cssVars}\n}\n\n.theme-${theme.id} body {\n  background-color: var(--bg-primary);\n  color: var(--text-primary);\n}\n`
}

export function generateAllThemesCSS() {
  return themes.map(theme => generateThemeCSS(theme)).join('\n')
}
