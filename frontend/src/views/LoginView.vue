<template>
  <div class="min-h-screen bg-primary flex items-center justify-center px-4">
    <div class="max-w-md w-full bg-secondary rounded-lg shadow-lg p-8">
      <h1 class="text-3xl font-serif text-primary mb-6 text-center">
        Welcome Back
      </h1>

      <form
        class="space-y-6"
        @submit.prevent="handleLogin"
      >
        <div>
          <label
            for="email"
            class="block text-sm font-medium text-primary mb-2"
          >
            Email
          </label>
          <input
            id="email"
            v-model="email"
            type="email"
            required
            class="w-full px-4 py-2 border border-color rounded-lg bg-primary text-primary focus:outline-none focus:ring-2 focus:ring-accent"
          >
        </div>

        <div>
          <label
            for="password"
            class="block text-sm font-medium text-primary mb-2"
          >
            Password
          </label>
          <input
            id="password"
            v-model="password"
            type="password"
            required
            class="w-full px-4 py-2 border border-color rounded-lg bg-primary text-primary focus:outline-none focus:ring-2 focus:ring-accent"
          >
        </div>

        <div
          v-if="error"
          class="text-red-500 text-sm"
        >
          {{ error }}
        </div>

        <button
          type="submit"
          :disabled="loading"
          class="w-full px-4 py-2 bg-accent text-white rounded-lg hover:opacity-80 transition-cozy disabled:opacity-50"
        >
          {{ loading ? 'Logging in...' : 'Login' }}
        </button>
      </form>

      <p class="mt-6 text-center text-secondary text-sm">
        Don't have an account?
        <router-link to="/register" class="text-accent hover:opacity-80">
          Register here
        </router-link>
      </p>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const router = useRouter()
const authStore = useAuthStore()

const email = ref('')
const password = ref('')
const loading = ref(false)
const error = ref('')

async function handleLogin() {
  loading.value = true
  error.value = ''

  const result = await authStore.login(email.value, password.value)

  if (result.success) {
    router.push('/')
  } else {
    error.value = result.error
  }

  loading.value = false
}
</script>
