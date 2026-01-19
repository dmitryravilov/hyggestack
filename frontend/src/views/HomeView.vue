<template>
  <div class="bg-primary min-h-screen">
    <AppHeader />
    <main class="container mx-auto px-4 py-12">
      <div class="mx-auto max-w-4xl">
        <h1 class="text-primary mb-8 text-center font-serif text-4xl">
          Welcome to HyggeStack
        </h1>
        <p class="text-secondary mb-12 text-center text-xl">
          A cozy space for thoughtful writing and peaceful reading
        </p>

        <div
          v-if="postsStore.loading"
          class="py-12 text-center"
        >
          <div
            class="border-accent inline-block h-12 w-12 animate-spin rounded-full border-b-2 border-t-2"
          />
        </div>

        <div
          v-else
          class="space-y-8"
        >
          <PostCard
            v-for="post in postsStore.posts"
            :key="post.id"
            :post="post"
          />
        </div>

        <div
          v-if="postsStore.posts.length === 0 && !postsStore.loading"
          class="py-12 text-center"
        >
          <p class="text-secondary">
            No posts yet. Check back soon!
          </p>
        </div>

        <!-- Loading more indicator -->
        <div
          v-if="postsStore.loadingMore"
          class="py-8 text-center"
        >
          <div
            class="border-accent inline-block h-8 w-8 animate-spin rounded-full border-b-2 border-t-2"
          />
        </div>

        <!-- End of posts indicator -->
        <div
          v-if="
            !postsStore.loading &&
              !postsStore.loadingMore &&
              postsStore.posts.length > 0 &&
              postsStore.pagination.current_page >= postsStore.pagination.last_page
          "
          class="py-8 text-center"
        >
          <p class="text-secondary">
            You've reached the end of all posts.
          </p>
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
    if (
      !postsStore.loadingMore &&
      postsStore.pagination.current_page < postsStore.pagination.last_page
    ) {
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

// eslint-disable-next-line no-unused-expressions
watch(
  () => route.query.category,
  () => {
    fetchPosts()
  },
)
</script>
