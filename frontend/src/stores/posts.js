import { defineStore } from 'pinia'
import { ref } from 'vue'
import api from '@/utils/api'

export const usePostsStore = defineStore('posts', () => {
  const posts = ref([])
  const currentPost = ref(null)
  const loading = ref(false)
  const loadingMore = ref(false)
  const pagination = ref({
    current_page: 1,
    last_page: 1,
    per_page: 15,
    total: 0,
  })

  async function fetchPosts(page = 1, categorySlug = null, perPage = 5) {
    loading.value = true
    try {
      const params = { page, per_page: perPage }
      if (categorySlug) {
        params.category = categorySlug
      }
      const response = await api.get('/posts', { params })
      if (page === 1) {
        posts.value = response.data.data || []
      } else {
        posts.value = [...posts.value, ...(response.data.data || [])]
      }
      const meta = response.data.meta || {}
      pagination.value = {
        current_page: meta.current_page || 1,
        last_page: meta.last_page || 1,
        per_page: meta.per_page || 15,
        total: meta.total || 0,
      }
    } catch (error) {
      if (page === 1) {
        posts.value = []
      }
      throw error
    } finally {
      loading.value = false
    }
  }

  async function loadMorePosts(categorySlug = null) {
    if (loadingMore.value || pagination.value.current_page >= pagination.value.last_page) {
      return
    }
    
    loadingMore.value = true
    try {
      const nextPage = pagination.value.current_page + 1
      const params = { page: nextPage, per_page: 5 }
      if (categorySlug) {
        params.category = categorySlug
      }
      const response = await api.get('/posts', { params })
      const newPosts = response.data.data || []
      posts.value = [...posts.value, ...newPosts]
      
      const meta = response.data.meta || {}
        pagination.value = {
          current_page: meta.current_page || pagination.value.current_page + 1,
          last_page: meta.last_page || pagination.value.last_page,
          per_page: meta.per_page || 5,
          total: meta.total || pagination.value.total,
        }
    } catch (error) {
      throw error
    } finally {
      loadingMore.value = false
    }
  }

  async function fetchPostBySlug(slug) {
    loading.value = true
    try {
      const response = await api.get(`/posts/${slug}`)
      currentPost.value = response.data.data
      return response.data.data
    } catch (error) {
      currentPost.value = null
      throw error
    } finally {
      loading.value = false
    }
  }

  return {
    posts,
    currentPost,
    loading,
    loadingMore,
    pagination,
    fetchPosts,
    fetchPostBySlug,
    loadMorePosts,
  }
})

