<template>
  <div class="min-h-screen bg-primary">
    <AppHeader />
    <main class="container mx-auto px-4 py-12">
      <div class="max-w-4xl mx-auto text-center">
        <h1 class="text-6xl font-serif text-primary mb-4">404</h1>
        <h2 class="text-3xl font-serif text-primary mb-6">Page Not Found</h2>
        <p class="text-xl text-secondary mb-8">
          The page you're looking for doesn't exist or has been moved.
        </p>
        <router-link
          to="/"
          class="inline-block px-6 py-3 bg-accent text-primary rounded-lg hover:opacity-90 transition-opacity font-medium"
        >
          Go Home
        </router-link>
      </div>
    </main>
  </div>
</template>

<script setup>
import AppHeader from '@/components/AppHeader.vue'
</script>

