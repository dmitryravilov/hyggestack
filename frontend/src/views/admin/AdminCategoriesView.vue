<template>
  <div>
    <div class="mb-6 flex items-center justify-between">
      <h2 class="text-primary font-serif text-2xl">
        Categories
      </h2>
      <button
        class="bg-accent transition-cozy rounded-lg px-4 py-2 text-white hover:opacity-80"
        @click="showCreateModal = true"
      >
        Create Category
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
      class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3"
    >
      <div
        v-for="category in categories"
        :key="category.id"
        class="bg-secondary transition-cozy rounded-lg p-6 shadow hover:shadow-lg"
      >
        <div class="flex items-start justify-between">
          <div class="flex-1">
            <h3 class="text-primary mb-2 font-serif text-xl">
              {{ category.name }}
            </h3>
            <p
              v-if="category.description"
              class="text-secondary mb-2"
            >
              {{ category.description }}
            </p>
            <p class="text-secondary text-sm">
              {{ category.posts_count || 0 }} {{ category.posts_count === 1 ? 'post' : 'posts' }}
            </p>
          </div>
          <div class="ml-4 flex gap-2">
            <button
              class="bg-accent transition-cozy rounded-lg px-4 py-2 text-white hover:opacity-80"
              @click="editCategory(category)"
            >
              Edit
            </button>
            <button
              class="transition-cozy rounded-lg bg-red-500 px-4 py-2 text-white hover:opacity-80"
              @click="deleteCategory(category.id)"
            >
              Delete
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Create/Edit Modal -->
    <div
      v-if="showCreateModal || editingCategory"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50"
      @click.self="closeModal"
    >
      <div class="bg-secondary mx-4 w-full max-w-md rounded-lg p-8 shadow-lg">
        <h3 class="text-primary mb-6 font-serif text-2xl">
          {{ editingCategory ? 'Edit Category' : 'Create Category' }}
        </h3>

        <form
          class="space-y-4"
          @submit.prevent="saveCategory"
        >
          <div>
            <label class="text-primary mb-2 block text-sm font-medium">Name</label>
            <input
              v-model="categoryForm.name"
              type="text"
              required
              class="border-color bg-primary text-primary w-full rounded-lg border px-4 py-2"
            >
          </div>

          <div>
            <label class="text-primary mb-2 block text-sm font-medium">Description</label>
            <textarea
              v-model="categoryForm.description"
              rows="3"
              class="border-color bg-primary text-primary w-full rounded-lg border px-4 py-2"
            />
          </div>

          <div>
            <label class="text-primary mb-2 block text-sm font-medium">Color (hex)</label>
            <input
              v-model="categoryForm.color"
              type="text"
              placeholder="#FF5733"
              class="border-color bg-primary text-primary w-full rounded-lg border px-4 py-2"
            >
          </div>

          <div class="flex gap-4 pt-4">
            <button
              type="submit"
              class="bg-accent flex-1 rounded-lg px-4 py-2 text-white hover:opacity-80"
            >
              {{ editingCategory ? 'Update' : 'Create' }}
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
import api from '@/utils/api'

const categories = ref([])
const loading = ref(false)
const showCreateModal = ref(false)
const editingCategory = ref(null)

const categoryForm = ref({
  name: '',
  description: '',
  color: '',
})

async function fetchCategories() {
  loading.value = true
  try {
    const response = await api.get('/categories')
    categories.value = response.data.data
  } catch (error) {
    console.error('Error fetching categories:', error)
  } finally {
    loading.value = false
  }
}

function editCategory(category) {
  editingCategory.value = category
  categoryForm.value = {
    name: category.name,
    description: category.description || '',
    color: category.color || '',
  }
}

function closeModal() {
  showCreateModal.value = false
  editingCategory.value = null
  categoryForm.value = {
    name: '',
    description: '',
    color: '',
  }
}

async function saveCategory() {
  try {
    if (editingCategory.value) {
      await api.put(`/categories/${editingCategory.value.id}`, categoryForm.value)
    } else {
      await api.post('/categories', categoryForm.value)
    }
    await fetchCategories()
    closeModal()
  } catch (error) {
    console.error('Error saving category:', error)
    alert(error.response?.data?.message || 'Error saving category')
  }
}

async function deleteCategory(categoryId) {
  if (!confirm('Are you sure you want to delete this category?')) {
    return
  }

  try {
    await api.delete(`/categories/${categoryId}`)
    await fetchCategories()
  } catch (error) {
    console.error('Error deleting category:', error)
    alert(error.response?.data?.message || 'Error deleting category')
  }
}

onMounted(() => {
  fetchCategories()
})
</script>
