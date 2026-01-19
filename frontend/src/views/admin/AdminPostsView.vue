<template>
  <div>
    <div class="flex justify-between items-center mb-6">
      <h2 class="text-2xl font-serif text-primary">Posts</h2>
      <button
        @click="showCreateModal = true"
        class="px-4 py-2 bg-accent text-white rounded-lg hover:opacity-80 transition-cozy"
      >
        Create Post
      </button>
    </div>

    <div v-if="loading" class="text-center py-12">
      <div class="inline-block animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-accent"></div>
    </div>

    <div v-else class="space-y-4">
      <div
        v-for="post in posts"
        :key="post.id"
        class="bg-secondary rounded-lg shadow p-6 hover:shadow-lg transition-cozy"
      >
        <div class="flex justify-between items-start">
          <div class="flex-1">
            <h3 class="text-xl font-serif text-primary mb-2">{{ post.title }}</h3>
            <p class="text-secondary mb-2">{{ post.excerpt }}</p>
            <div class="flex gap-4 text-sm text-secondary">
              <span>Status: {{ post.status }}</span>
              <span v-if="post.category">Category: {{ post.category.name }}</span>
              <span v-if="post.author">Author: {{ post.author.name }}</span>
            </div>
          </div>
          <div class="flex gap-2 ml-4">
            <button
              @click="editPost(post)"
              class="px-4 py-2 bg-accent text-white rounded-lg hover:opacity-80 transition-cozy"
            >
              Edit
            </button>
            <button
              @click="deletePost(post.id)"
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
      v-if="showCreateModal || editingPost"
      class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4"
      @click.self="closeModal"
    >
      <div class="bg-secondary rounded-lg shadow-lg p-8 max-w-4xl w-full mx-4 max-h-[90vh] overflow-y-auto">
        <h3 class="text-2xl font-serif text-primary mb-6">
          {{ editingPost ? 'Edit Post' : 'Create Post' }}
        </h3>

        <form @submit.prevent="savePost" class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-primary mb-2">Title</label>
            <input
              v-model="postForm.title"
              type="text"
              required
              class="w-full px-4 py-2 border border-color rounded-lg bg-primary text-primary"
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-primary mb-2">Excerpt</label>
            <textarea
              v-model="postForm.excerpt"
              rows="3"
              class="w-full px-4 py-2 border border-color rounded-lg bg-primary text-primary"
            ></textarea>
          </div>

          <div>
            <label class="block text-sm font-medium text-primary mb-2">Content</label>
            <textarea
              v-model="postForm.content"
              rows="10"
              required
              class="w-full px-4 py-2 border border-color rounded-lg bg-primary text-primary font-mono"
            ></textarea>
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-primary mb-2">Category</label>
              <select
                v-model="postForm.category_id"
                class="w-full px-4 py-2 border border-color rounded-lg bg-primary text-primary"
              >
                <option value="">None</option>
                <option v-for="cat in categories" :key="cat.id" :value="cat.id">
                  {{ cat.name }}
                </option>
              </select>
            </div>

            <div>
              <label class="block text-sm font-medium text-primary mb-2">Status</label>
              <select
                v-model="postForm.status"
                required
                class="w-full px-4 py-2 border border-color rounded-lg bg-primary text-primary"
              >
                <option value="draft">Draft</option>
                <option value="published">Published</option>
              </select>
            </div>
          </div>

          <div class="flex gap-4 pt-4">
            <button
              type="submit"
              class="flex-1 px-4 py-2 bg-accent text-white rounded-lg hover:opacity-80"
            >
              {{ editingPost ? 'Update' : 'Create' }}
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

const posts = ref([])
const categories = ref([])
const loading = ref(false)
const showCreateModal = ref(false)
const editingPost = ref(null)

const postForm = ref({
  title: '',
  excerpt: '',
  content: '',
  category_id: null,
  status: 'draft',
})

async function fetchPosts() {
  loading.value = true
  try {
    const response = await api.get('/admin/posts')
    posts.value = response.data.data || []
  } catch (error) {
    console.error('Error fetching posts:', error)
  } finally {
    loading.value = false
  }
}

async function fetchCategories() {
  try {
    const response = await api.get('/categories')
    categories.value = response.data.data
  } catch (error) {
    console.error('Error fetching categories:', error)
  }
}

function editPost(post) {
  editingPost.value = post
  postForm.value = {
    title: post.title,
    excerpt: post.excerpt || '',
    content: post.content || '',
    category_id: post.category_id || (post.category?.id || null),
    status: post.status || 'draft',
  }
}

function closeModal() {
  showCreateModal.value = false
  editingPost.value = null
  postForm.value = {
    title: '',
    excerpt: '',
    content: '',
    category_id: null,
    status: 'draft',
  }
}

async function savePost() {
  try {
    if (editingPost.value) {
      await api.put(`/posts/${editingPost.value.id}`, postForm.value)
    } else {
      await api.post('/posts', postForm.value)
    }
    await fetchPosts()
    closeModal()
  } catch (error) {
    console.error('Error saving post:', error)
    alert(error.response?.data?.message || 'Error saving post')
  }
}

async function deletePost(postId) {
  if (!confirm('Are you sure you want to delete this post?')) {
    return
  }

  try {
    await api.delete(`/posts/${postId}`)
    await fetchPosts()
  } catch (error) {
    console.error('Error deleting post:', error)
    alert(error.response?.data?.message || 'Error deleting post')
  }
}

onMounted(() => {
  fetchPosts()
  fetchCategories()
})
</script>

