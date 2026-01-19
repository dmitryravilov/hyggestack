<template>
  <div class="min-h-screen bg-primary">
    <AppHeader />
    <main class="container mx-auto px-4 py-12">
      <div class="max-w-4xl mx-auto">
        <h1 class="text-4xl font-serif text-primary mb-8 text-center">
          Welcome to HyggeStack
        </h1>
        <p class="text-xl text-secondary text-center mb-12">
          A cozy space for thoughtful writing and peaceful reading
        </p>

        <div v-if="postsStore.loading" class="text-center py-12">
          <div class="inline-block animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-accent"></div>
        </div>

        <div v-else class="space-y-8">
          <PostCard
            v-for="post in postsStore.posts"
            :key="post.id"
            :post="post"
          />
        </div>

        <div v-if="postsStore.posts.length === 0 && !postsStore.loading" class="text-center py-12">
          <p class="text-secondary">No posts yet. Check back soon!</p>
        </div>

        <!-- Loading more indicator -->
        <div v-if="postsStore.loadingMore" class="text-center py-8">
          <div class="inline-block animate-spin rounded-full h-8 w-8 border-t-2 border-b-2 border-accent"></div>
        </div>

        <!-- End of posts indicator -->
        <div v-if="!postsStore.loading && !postsStore.loadingMore && postsStore.posts.length > 0 && postsStore.pagination.current_page >= postsStore.pagination.last_page" class="text-center py-8">
          <p class="text-secondary">You've reached the end of all posts.</p>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup>
import { onMounted, onUnmounted, watch } from 'vue'
import { useRoute } from 'vue-router'
import { usePostsStore } from '@/stores/posts'
import AppHeader from '@/components/AppHeader.vue'
import PostCard from '@/components/PostCard.vue'

const route = useRoute()
const postsStore = usePostsStore()

function fetchPosts() {
  const categorySlug = route.query.category || null
  postsStore.fetchPosts(1, categorySlug, 5)
}

function handleScroll() {
  const scrollTop = window.pageYOffset || document.documentElement.scrollTop
  const windowHeight = window.innerHeight
  const documentHeight = document.documentElement.scrollHeight
  
  if (scrollTop + windowHeight >= documentHeight - 200) {
    const categorySlug = route.query.category || null
    if (!postsStore.loadingMore && postsStore.pagination.current_page < postsStore.pagination.last_page) {
      postsStore.loadMorePosts(categorySlug)
    }
  }
}

onMounted(() => {
  fetchPosts()
  window.addEventListener('scroll', handleScroll)
})

onUnmounted(() => {
  window.removeEventListener('scroll', handleScroll)
})

watch(() => route.query.category, () => {
  fetchPosts()
})
</script>

