<template>
  <div class="min-h-screen bg-primary">
    <div class="bg-secondary border-b border-color shadow-sm">
      <div class="container mx-auto px-4 py-4">
        <div class="flex items-center justify-between">
          <h1 class="text-2xl font-serif text-primary">Admin Dashboard</h1>
          <div class="flex items-center gap-4">
            <span class="text-secondary">{{ authStore.user?.name }}</span>
            <router-link
              to="/"
              class="text-primary hover:text-accent transition-cozy"
            >
              View Site
            </router-link>
            <button
              @click="handleLogout"
              class="px-4 py-2 text-sm bg-accent text-white rounded-lg hover:opacity-80 transition-cozy"
            >
              Logout
            </button>
          </div>
        </div>
      </div>
    </div>

    <div class="container mx-auto px-4 py-8">
      <nav class="flex gap-4 mb-8 border-b border-color">
        <button
          v-for="tab in tabs"
          :key="tab.id"
          @click="activeTab = tab.id"
          class="px-4 py-2 text-primary hover:text-accent transition-cozy border-b-2"
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
        <div class="bg-secondary rounded-lg shadow p-8 max-w-md">
          <h2 class="text-2xl font-serif text-primary mb-6">Change Password</h2>
          
          <form @submit.prevent="handleChangePassword" class="space-y-4">
            <div>
              <label for="current_password" class="block text-sm font-medium text-primary mb-2">
                Current Password
              </label>
              <input
                id="current_password"
                v-model="passwordForm.currentPassword"
                type="password"
                required
                class="w-full px-4 py-2 border border-color rounded-lg bg-primary text-primary focus:outline-none focus:ring-2 focus:ring-accent"
              />
            </div>

            <div>
              <label for="new_password" class="block text-sm font-medium text-primary mb-2">
                New Password
              </label>
              <input
                id="new_password"
                v-model="passwordForm.newPassword"
                type="password"
                required
                minlength="8"
                class="w-full px-4 py-2 border border-color rounded-lg bg-primary text-primary focus:outline-none focus:ring-2 focus:ring-accent"
              />
            </div>

            <div v-if="passwordError" class="text-red-500 text-sm">{{ passwordError }}</div>
            <div v-if="passwordSuccess" class="text-green-500 text-sm">{{ passwordSuccess }}</div>

            <button
              type="submit"
              :disabled="passwordLoading"
              class="w-full px-4 py-2 bg-accent text-white rounded-lg hover:opacity-80 transition-cozy disabled:opacity-50"
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
    passwordForm.value.newPassword
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

