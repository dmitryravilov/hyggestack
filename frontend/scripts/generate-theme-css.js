#!/usr/bin/env node

/**
 * Generate CSS from theme configuration files
 *
 * This script reads theme JSON files and generates the corresponding
 * CSS classes to keep them in sync.
 */

import fs from 'fs'
import path from 'path'
import { fileURLToPath } from 'url'

const __filename = fileURLToPath(import.meta.url)
const __dirname = path.dirname(__filename)

const themesDir = path.resolve(__dirname, '../../themes')
const outputFile = path.resolve(__dirname, '../src/assets/css/themes.css')

function generateThemeCSS(theme) {
  const cssVars = Object.entries(theme.colors)
    .map(([key, value]) => `  --${key}: ${value};`)
    .join('\n')

  return `/* Theme: ${theme.name} */
.theme-${theme.id} {
${cssVars}
}

.theme-${theme.id},
.theme-${theme.id} #app {
  background-color: var(--bg-primary);
  color: var(--text-primary);
}

`
}

function generateAllThemesCSS() {
  const themeFiles = fs.readdirSync(themesDir)
    .filter(file => file.endsWith('.json'))
    .map(file => path.join(themesDir, file))

  const themes = themeFiles.map(file => {
    const content = fs.readFileSync(file, 'utf-8')
    return JSON.parse(content)
  })

  const css = themes.map(theme => generateThemeCSS(theme)).join('\n')

  const header = `/*
 * Auto-generated theme CSS
 * Generated from theme configuration files in /themes directory
 * DO NOT EDIT MANUALLY - Run 'npm run generate:themes' to regenerate
 * 
 * Generated: ${new Date().toISOString()}
 */

`

  return header + css
}

try {
  const css = generateAllThemesCSS()
  fs.writeFileSync(outputFile, css, 'utf-8')
  console.log('✅ Theme CSS generated successfully:', outputFile)
} catch (error) {
  console.error('❌ Error generating theme CSS:', error)
  process.exit(1)
}
