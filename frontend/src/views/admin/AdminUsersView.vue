<template>
  <div>
    <div class="flex justify-between items-center mb-6">
      <h2 class="text-2xl font-serif text-primary">Users</h2>
      <button
        @click="showCreateModal = true"
        class="px-4 py-2 bg-accent text-white rounded-lg hover:opacity-80 transition-cozy"
      >
        Create User
      </button>
    </div>

    <div v-if="loading" class="text-center py-12">
      <div class="inline-block animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-accent"></div>
    </div>

    <div v-else class="bg-secondary rounded-lg shadow overflow-hidden">
      <table class="min-w-full divide-y divide-color">
        <thead class="bg-accent-light">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-primary uppercase">Name</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-primary uppercase">Email</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-primary uppercase">Role</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-primary uppercase">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-color">
          <tr v-for="user in users" :key="user.id" class="hover:bg-accent-light">
            <td class="px-6 py-4 whitespace-nowrap text-primary">{{ user.name }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-secondary">{{ user.email }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-secondary">
              {{ user.roles?.[0]?.name || 'N/A' }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm">
              <button
                @click="editUser(user)"
                class="text-accent hover:opacity-80 mr-4"
              >
                Edit
              </button>
              <button
                @click="deleteUser(user.id)"
                class="text-red-500 hover:opacity-80"
                :disabled="user.id === authStore.user?.id"
              >
                Delete
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Create/Edit Modal -->
    <div
      v-if="showCreateModal || editingUser"
      class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
      @click.self="closeModal"
    >
      <div class="bg-secondary rounded-lg shadow-lg p-8 max-w-md w-full mx-4">
        <h3 class="text-2xl font-serif text-primary mb-6">
          {{ editingUser ? 'Edit User' : 'Create User' }}
        </h3>

        <form @submit.prevent="saveUser" class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-primary mb-2">Name</label>
            <input
              v-model="userForm.name"
              type="text"
              required
              class="w-full px-4 py-2 border border-color rounded-lg bg-primary text-primary"
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-primary mb-2">Email</label>
            <input
              v-model="userForm.email"
              type="email"
              required
              class="w-full px-4 py-2 border border-color rounded-lg bg-primary text-primary"
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-primary mb-2">Password</label>
            <input
              v-model="userForm.password"
              type="password"
              :required="!editingUser"
              class="w-full px-4 py-2 border border-color rounded-lg bg-primary text-primary"
            />
            <p v-if="editingUser" class="text-sm text-secondary mt-1">Leave blank to keep current password</p>
          </div>

          <div>
            <label class="block text-sm font-medium text-primary mb-2">Role</label>
            <select
              v-model="userForm.role"
              required
              class="w-full px-4 py-2 border border-color rounded-lg bg-primary text-primary"
            >
              <option value="admin">Admin</option>
              <option value="writer">Writer</option>
            </select>
          </div>

          <div class="flex gap-4 pt-4">
            <button
              type="submit"
              class="flex-1 px-4 py-2 bg-accent text-white rounded-lg hover:opacity-80"
            >
              {{ editingUser ? 'Update' : 'Create' }}
            </button>
            <button
              type="button"
              @click="closeModal"
              class="flex-1 px-4 py-2 border border-color text-primary rounded-lg hover:opacity-80"
            >
              Cancel
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useAuthStore } from '@/stores/auth'
import api from '@/utils/api'

const authStore = useAuthStore()

const users = ref([])
const loading = ref(false)
const showCreateModal = ref(false)
const editingUser = ref(null)

const userForm = ref({
  name: '',
  email: '',
  password: '',
  role: 'writer',
})

async function fetchUsers() {
  loading.value = true
  try {
    const response = await api.get('/users')
    users.value = response.data.data
  } catch (error) {
    console.error('Error fetching users:', error)
  } finally {
    loading.value = false
  }
}

function editUser(user) {
  editingUser.value = user
  userForm.value = {
    name: user.name,
    email: user.email,
    password: '',
    role: user.roles?.[0]?.name || 'writer',
  }
}

function closeModal() {
  showCreateModal.value = false
  editingUser.value = null
  userForm.value = {
    name: '',
    email: '',
    password: '',
    role: 'writer',
  }
}

async function saveUser() {
  try {
    if (editingUser.value) {
      const data = { ...userForm.value }
      if (!data.password) {
        delete data.password
      } else {
        data.password_confirmation = data.password
      }
      await api.put(`/users/${editingUser.value.id}`, data)
    } else {
      await api.post('/users', {
        ...userForm.value,
        password_confirmation: userForm.value.password,
      })
    }
    await fetchUsers()
    closeModal()
  } catch (error) {
    console.error('Error saving user:', error)
    alert(error.response?.data?.message || 'Error saving user')
  }
}

async function deleteUser(userId) {
  if (!confirm('Are you sure you want to delete this user?')) {
    return
  }

  try {
    await api.delete(`/users/${userId}`)
    await fetchUsers()
  } catch (error) {
    console.error('Error deleting user:', error)
    alert(error.response?.data?.message || 'Error deleting user')
  }
}

onMounted(() => {
  fetchUsers()
})
</script>

