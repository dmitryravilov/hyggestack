<template>
  <header class="bg-secondary border-color border-b shadow-sm">
    <div class="container mx-auto px-4 py-4">
      <div class="flex items-center justify-between">
        <router-link
          to="/"
          class="text-primary transition-cozy font-serif text-2xl hover:opacity-80"
        >
          HyggeStack
        </router-link>

        <nav class="flex items-center gap-6">
          <CategorySelector />
          <ThemeSelector />
        </nav>
      </div>
    </div>
  </header>
</template>

<script setup>
import ThemeSelector from './ThemeSelector.vue'
import CategorySelector from './CategorySelector.vue'
</script>
