<template>
  <div class="bg-primary min-h-screen">
    <AppHeader />
    <main class="container mx-auto px-4 py-12">
      <div class="mx-auto max-w-4xl text-center">
        <h1 class="text-primary mb-4 font-serif text-6xl">
          404
        </h1>
        <h2 class="text-primary mb-6 font-serif text-3xl">
          Page Not Found
        </h2>
        <p class="text-secondary mb-8 text-xl">
          The page you're looking for doesn't exist or has been moved.
        </p>
        <router-link
          to="/"
          class="bg-accent text-primary inline-block rounded-lg px-6 py-3 font-medium transition-opacity hover:opacity-90"
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
