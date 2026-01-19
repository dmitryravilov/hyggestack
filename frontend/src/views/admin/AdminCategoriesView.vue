<template>
  <div>
    <div class="flex justify-between items-center mb-6">
      <h2 class="text-2xl font-serif text-primary">Categories</h2>
      <button
        @click="showCreateModal = true"
        class="px-4 py-2 bg-accent text-white rounded-lg hover:opacity-80 transition-cozy"
      >
        Create Category
      </button>
    </div>

    <div v-if="loading" class="text-center py-12">
      <div class="inline-block animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-accent"></div>
    </div>

    <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
      <div
        v-for="category in categories"
        :key="category.id"
        class="bg-secondary rounded-lg shadow p-6 hover:shadow-lg transition-cozy"
      >
        <div class="flex justify-between items-start">
          <div class="flex-1">
            <h3 class="text-xl font-serif text-primary mb-2">{{ category.name }}</h3>
            <p v-if="category.description" class="text-secondary mb-2">{{ category.description }}</p>
            <p class="text-sm text-secondary">
              {{ category.posts_count || 0 }} {{ category.posts_count === 1 ? 'post' : 'posts' }}
            </p>
          </div>
          <div class="flex gap-2 ml-4">
            <button
              @click="editCategory(category)"
              class="px-4 py-2 bg-accent text-white rounded-lg hover:opacity-80 transition-cozy"
            >
              Edit
            </button>
            <button
              @click="deleteCategory(category.id)"
              class="px-4 py-2 bg-red-500 text-white rounded-lg hover:opacity-80 transition-cozy"
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
      class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
      @click.self="closeModal"
    >
      <div class="bg-secondary rounded-lg shadow-lg p-8 max-w-md w-full mx-4">
        <h3 class="text-2xl font-serif text-primary mb-6">
          {{ editingCategory ? 'Edit Category' : 'Create Category' }}
        </h3>

        <form @submit.prevent="saveCategory" class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-primary mb-2">Name</label>
            <input
              v-model="categoryForm.name"
              type="text"
              required
              class="w-full px-4 py-2 border border-color rounded-lg bg-primary text-primary"
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-primary mb-2">Description</label>
            <textarea
              v-model="categoryForm.description"
              rows="3"
              class="w-full px-4 py-2 border border-color rounded-lg bg-primary text-primary"
            ></textarea>
          </div>

          <div>
            <label class="block text-sm font-medium text-primary mb-2">Color (hex)</label>
            <input
              v-model="categoryForm.color"
              type="text"
              placeholder="#FF5733"
              class="w-full px-4 py-2 border border-color rounded-lg bg-primary text-primary"
            />
          </div>

          <div class="flex gap-4 pt-4">
            <button
              type="submit"
              class="flex-1 px-4 py-2 bg-accent text-white rounded-lg hover:opacity-80"
            >
              {{ editingCategory ? 'Update' : 'Create' }}
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

