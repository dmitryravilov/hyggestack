<template>
  <div>
    <div class="mb-6 flex items-center justify-between">
      <h2 class="text-primary font-serif text-2xl">
        Users
      </h2>
      <button
        class="bg-accent transition-cozy rounded-lg px-4 py-2 text-white hover:opacity-80"
        @click="showCreateModal = true"
      >
        Create User
      </button>
    </div>

    <div
      v-if="loading"
      class="py-12 text-center"
    >
      <div
        class="border-accent inline-block h-12 w-12 animate-spin rounded-full border-b-2 border-t-2"
      />
    </div>

    <div
      v-else
      class="bg-secondary overflow-hidden rounded-lg shadow"
    >
      <table class="divide-color min-w-full divide-y">
        <thead class="bg-accent-light">
          <tr>
            <th class="text-primary px-6 py-3 text-left text-xs font-medium uppercase">
              Name
            </th>
            <th class="text-primary px-6 py-3 text-left text-xs font-medium uppercase">
              Email
            </th>
            <th class="text-primary px-6 py-3 text-left text-xs font-medium uppercase">
              Role
            </th>
            <th class="text-primary px-6 py-3 text-left text-xs font-medium uppercase">
              Actions
            </th>
          </tr>
        </thead>
        <tbody class="divide-color divide-y">
          <tr
            v-for="user in users"
            :key="user.id"
            class="hover:bg-accent-light"
          >
            <td class="text-primary whitespace-nowrap px-6 py-4">
              {{ user.name }}
            </td>
            <td class="text-secondary whitespace-nowrap px-6 py-4">
              {{ user.email }}
            </td>
            <td class="text-secondary whitespace-nowrap px-6 py-4">
              {{ user.roles?.[0]?.name || 'N/A' }}
            </td>
            <td class="whitespace-nowrap px-6 py-4 text-sm">
              <button
                class="text-accent mr-4 hover:opacity-80"
                @click="editUser(user)"
              >
                Edit
              </button>
              <button
                class="text-red-500 hover:opacity-80"
                :disabled="user.id === authStore.user?.id"
                @click="deleteUser(user.id)"
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
      class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50"
      @click.self="closeModal"
    >
      <div class="bg-secondary mx-4 w-full max-w-md rounded-lg p-8 shadow-lg">
        <h3 class="text-primary mb-6 font-serif text-2xl">
          {{ editingUser ? 'Edit User' : 'Create User' }}
        </h3>

        <form
          class="space-y-4"
          @submit.prevent="saveUser"
        >
          <div>
            <label class="text-primary mb-2 block text-sm font-medium">Name</label>
            <input
              v-model="userForm.name"
              type="text"
              required
              class="border-color bg-primary text-primary w-full rounded-lg border px-4 py-2"
            >
          </div>

          <div>
            <label class="text-primary mb-2 block text-sm font-medium">Email</label>
            <input
              v-model="userForm.email"
              type="email"
              required
              class="border-color bg-primary text-primary w-full rounded-lg border px-4 py-2"
            >
          </div>

          <div>
            <label class="text-primary mb-2 block text-sm font-medium">Password</label>
            <input
              v-model="userForm.password"
              type="password"
              :required="!editingUser"
              class="border-color bg-primary text-primary w-full rounded-lg border px-4 py-2"
            >
            <p
              v-if="editingUser"
              class="text-secondary mt-1 text-sm"
            >
              Leave blank to keep current password
            </p>
          </div>

          <div>
            <label class="text-primary mb-2 block text-sm font-medium">Role</label>
            <select
              v-model="userForm.role"
              required
              class="border-color bg-primary text-primary w-full rounded-lg border px-4 py-2"
            >
              <option value="admin">
                Admin
              </option>
              <option value="writer">
                Writer
              </option>
            </select>
          </div>

          <div class="flex gap-4 pt-4">
            <button
              type="submit"
              class="bg-accent flex-1 rounded-lg px-4 py-2 text-white hover:opacity-80"
            >
              {{ editingUser ? 'Update' : 'Create' }}
            </button>
            <button
              type="button"
              class="border-color text-primary flex-1 rounded-lg border px-4 py-2 hover:opacity-80"
              @click="closeModal"
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
