<template>
  <div class="bg-primary min-h-screen">
    <div class="bg-secondary border-color border-b shadow-sm">
      <div class="container mx-auto px-4 py-4">
        <div class="flex items-center justify-between">
          <h1 class="text-primary font-serif text-2xl">Admin Dashboard</h1>
          <div class="flex items-center gap-4">
            <span class="text-secondary">{{ authStore.user?.name }}</span>
            <router-link to="/" class="text-primary hover:text-accent transition-cozy">
              View Site
            </router-link>
            <button
              @click="handleLogout"
              class="bg-accent transition-cozy rounded-lg px-4 py-2 text-sm text-white hover:opacity-80"
            >
              Logout
            </button>
          </div>
        </div>
      </div>
    </div>

    <div class="container mx-auto px-4 py-8">
      <nav class="border-color mb-8 flex gap-4 border-b">
        <button
          v-for="tab in tabs"
          :key="tab.id"
          @click="activeTab = tab.id"
          class="text-primary hover:text-accent transition-cozy border-b-2 px-4 py-2"
          :class="activeTab === tab.id ? 'border-accent text-accent' : 'border-transparent'"
        >
          {{ tab.label }}
        </button>
      </nav>

      <div v-if="activeTab === 'users' && isAdmin">
        <AdminUsersView />
      </div>
      <div v-else-if="activeTab === 'posts'">
        <AdminPostsView />
      </div>
      <div v-else-if="activeTab === 'categories'">
        <AdminCategoriesView />
      </div>
      <div v-else-if="activeTab === 'settings'">
        <div class="bg-secondary max-w-md rounded-lg p-8 shadow">
          <h2 class="text-primary mb-6 font-serif text-2xl">Change Password</h2>

          <form class="space-y-4" @submit.prevent="handleChangePassword">
            <div>
              <label for="current_password" class="text-primary mb-2 block text-sm font-medium">
                Current Password
              </label>
              <input
                id="current_password"
                v-model="passwordForm.currentPassword"
                type="password"
                required
                class="border-color bg-primary text-primary focus:ring-accent w-full rounded-lg border px-4 py-2 focus:outline-none focus:ring-2"
              />
            </div>

            <div>
              <label for="new_password" class="text-primary mb-2 block text-sm font-medium">
                New Password
              </label>
              <input
                id="new_password"
                v-model="passwordForm.newPassword"
                type="password"
                required
                minlength="8"
                class="border-color bg-primary text-primary focus:ring-accent w-full rounded-lg border px-4 py-2 focus:outline-none focus:ring-2"
              />
            </div>

            <div v-if="passwordError" class="text-sm text-red-500">
              {{ passwordError }}
            </div>
            <div v-if="passwordSuccess" class="text-sm text-green-500">
              {{ passwordSuccess }}
            </div>

            <button
              type="submit"
              :disabled="passwordLoading"
              class="bg-accent transition-cozy w-full rounded-lg px-4 py-2 text-white hover:opacity-80 disabled:opacity-50"
            >
              {{ passwordLoading ? 'Changing password...' : 'Change Password' }}
            </button>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import AdminUsersView from './AdminUsersView.vue'
import AdminPostsView from './AdminPostsView.vue'
import AdminCategoriesView from './AdminCategoriesView.vue'

const router = useRouter()
const authStore = useAuthStore()

const activeTab = ref('posts')

const passwordForm = ref({
  currentPassword: '',
  newPassword: '',
})

const passwordLoading = ref(false)
const passwordError = ref('')
const passwordSuccess = ref('')

const isAdmin = computed(() => {
  return authStore.user?.roles?.some(role => role.name === 'admin')
})

const tabs = computed(() => {
  const allTabs = [
    { id: 'posts', label: 'Posts' },
    { id: 'categories', label: 'Categories' },
    { id: 'settings', label: 'Settings' },
  ]

  if (isAdmin.value) {
    allTabs.unshift({ id: 'users', label: 'Users' })
  }

  return allTabs
})

onMounted(() => {
  if (!authStore.isAuthenticated()) {
    router.push('/admin/login')
  } else {
    const user = authStore.user
    if (!user?.roles?.some(role => ['admin', 'writer'].includes(role.name))) {
      router.push('/')
    }
  }
})

async function handleLogout() {
  await authStore.logout()
  router.push('/admin/login')
}

async function handleChangePassword() {
  passwordLoading.value = true
  passwordError.value = ''
  passwordSuccess.value = ''

  const result = await authStore.changePassword(
    passwordForm.value.currentPassword,
    passwordForm.value.newPassword,
  )

  if (result.success) {
    passwordSuccess.value = result.message || 'Password changed successfully'
    passwordForm.value = {
      currentPassword: '',
      newPassword: '',
    }
  } else {
    passwordError.value = result.error || 'Failed to change password'
    if (result.errors) {
      const errorMessages = Object.values(result.errors).flat()
      passwordError.value = errorMessages.join(', ')
    }
  }

  passwordLoading.value = false
}
</script>
