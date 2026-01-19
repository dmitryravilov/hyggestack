<template>
  <div class="bg-primary flex min-h-screen items-center justify-center px-4">
    <div class="bg-secondary w-full max-w-md rounded-lg p-8 shadow-lg">
      <h1 class="text-primary mb-6 text-center font-serif text-3xl">Join HyggeStack</h1>

      <form class="space-y-6" @submit.prevent="handleRegister">
        <div>
          <label for="name" class="text-primary mb-2 block text-sm font-medium"> Name </label>
          <input
            id="name"
            v-model="name"
            type="text"
            required
            class="border-color bg-primary text-primary focus:ring-accent w-full rounded-lg border px-4 py-2 focus:outline-none focus:ring-2"
          />
        </div>

        <div>
          <label for="email" class="text-primary mb-2 block text-sm font-medium"> Email </label>
          <input
            id="email"
            v-model="email"
            type="email"
            required
            class="border-color bg-primary text-primary focus:ring-accent w-full rounded-lg border px-4 py-2 focus:outline-none focus:ring-2"
          />
        </div>

        <div>
          <label for="password" class="text-primary mb-2 block text-sm font-medium">
            Password
          </label>
          <input
            id="password"
            v-model="password"
            type="password"
            required
            minlength="8"
            class="border-color bg-primary text-primary focus:ring-accent w-full rounded-lg border px-4 py-2 focus:outline-none focus:ring-2"
          />
        </div>

        <div>
          <label for="password_confirmation" class="text-primary mb-2 block text-sm font-medium">
            Confirm Password
          </label>
          <input
            id="password_confirmation"
            v-model="passwordConfirmation"
            type="password"
            required
            minlength="8"
            class="border-color bg-primary text-primary focus:ring-accent w-full rounded-lg border px-4 py-2 focus:outline-none focus:ring-2"
          />
        </div>

        <div v-if="error" class="text-sm text-red-500">
          {{ error }}
        </div>

        <button
          type="submit"
          :disabled="loading || password !== passwordConfirmation"
          class="bg-accent transition-cozy w-full rounded-lg px-4 py-2 text-white hover:opacity-80 disabled:opacity-50"
        >
          {{ loading ? 'Creating account...' : 'Register' }}
        </button>
      </form>

      <p class="text-secondary mt-6 text-center text-sm">
        Already have an account?
        <router-link to="/admin/login" class="text-accent hover:opacity-80">
          Login here
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

const name = ref('')
const email = ref('')
const password = ref('')
const passwordConfirmation = ref('')
const loading = ref(false)
const error = ref('')

async function handleRegister() {
  if (password.value !== passwordConfirmation.value) {
    error.value = 'Passwords do not match'
    return
  }

  loading.value = true
  error.value = ''

  const result = await authStore.register(name.value, email.value, password.value)

  if (result.success) {
    router.push('/')
  } else {
    error.value = result.error
  }

  loading.value = false
}
</script>
