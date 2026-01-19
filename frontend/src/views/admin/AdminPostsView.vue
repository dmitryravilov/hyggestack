<template>
  <div>
    <div class="mb-6 flex items-center justify-between">
      <h2 class="text-primary font-serif text-2xl">Posts</h2>
      <button
        @click="showCreateModal = true"
        class="bg-accent transition-cozy rounded-lg px-4 py-2 text-white hover:opacity-80"
      >
        Create Post
      </button>
    </div>

    <div v-if="loading" class="py-12 text-center">
      <div
        class="border-accent inline-block h-12 w-12 animate-spin rounded-full border-b-2 border-t-2"
      ></div>
    </div>

    <div v-else class="space-y-4">
      <div
        v-for="post in posts"
        :key="post.id"
        class="bg-secondary transition-cozy rounded-lg p-6 shadow hover:shadow-lg"
      >
        <div class="flex items-start justify-between">
          <div class="flex-1">
            <h3 class="text-primary mb-2 font-serif text-xl">{{ post.title }}</h3>
            <p class="text-secondary mb-2">{{ post.excerpt }}</p>
            <div class="text-secondary flex gap-4 text-sm">
              <span>Status: {{ post.status }}</span>
              <span v-if="post.category">Category: {{ post.category.name }}</span>
              <span v-if="post.author">Author: {{ post.author.name }}</span>
            </div>
          </div>
          <div class="ml-4 flex gap-2">
            <button
              @click="editPost(post)"
              class="bg-accent transition-cozy rounded-lg px-4 py-2 text-white hover:opacity-80"
            >
              Edit
            </button>
            <button
              @click="deletePost(post.id)"
              class="transition-cozy rounded-lg bg-red-500 px-4 py-2 text-white hover:opacity-80"
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
      class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 p-4"
      @click.self="closeModal"
    >
      <div
        class="bg-secondary mx-4 max-h-[90vh] w-full max-w-4xl overflow-y-auto rounded-lg p-8 shadow-lg"
      >
        <h3 class="text-primary mb-6 font-serif text-2xl">
          {{ editingPost ? 'Edit Post' : 'Create Post' }}
        </h3>

        <form @submit.prevent="savePost" class="space-y-4">
          <div>
            <label class="text-primary mb-2 block text-sm font-medium">Title</label>
            <input
              v-model="postForm.title"
              type="text"
              required
              class="border-color bg-primary text-primary w-full rounded-lg border px-4 py-2"
            />
          </div>

          <div>
            <label class="text-primary mb-2 block text-sm font-medium">Excerpt</label>
            <textarea
              v-model="postForm.excerpt"
              rows="3"
              class="border-color bg-primary text-primary w-full rounded-lg border px-4 py-2"
            ></textarea>
          </div>

          <div>
            <label class="text-primary mb-2 block text-sm font-medium">Content</label>
            <textarea
              v-model="postForm.content"
              rows="10"
              required
              class="border-color bg-primary text-primary w-full rounded-lg border px-4 py-2 font-mono"
            ></textarea>
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="text-primary mb-2 block text-sm font-medium">Category</label>
              <select
                v-model="postForm.category_id"
                class="border-color bg-primary text-primary w-full rounded-lg border px-4 py-2"
              >
                <option value="">None</option>
                <option v-for="cat in categories" :key="cat.id" :value="cat.id">
                  {{ cat.name }}
                </option>
              </select>
            </div>

            <div>
              <label class="text-primary mb-2 block text-sm font-medium">Status</label>
              <select
                v-model="postForm.status"
                required
                class="border-color bg-primary text-primary w-full rounded-lg border px-4 py-2"
              >
                <option value="draft">Draft</option>
                <option value="published">Published</option>
              </select>
            </div>
          </div>

          <div class="flex gap-4 pt-4">
            <button
              type="submit"
              class="bg-accent flex-1 rounded-lg px-4 py-2 text-white hover:opacity-80"
            >
              {{ editingPost ? 'Update' : 'Create' }}
            </button>
            <button
              type="button"
              @click="closeModal"
              class="border-color text-primary flex-1 rounded-lg border px-4 py-2 hover:opacity-80"
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
    category_id: post.category_id || post.category?.id || null,
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
