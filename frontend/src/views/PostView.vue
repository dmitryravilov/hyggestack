<template>
  <div class="min-h-screen bg-primary">
    <AppHeader />
    <main class="container mx-auto px-4 py-12">
      <div
        v-if="postsStore.loading"
        class="text-center py-12"
      >
        <div class="inline-block animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-accent" />
      </div>

      <div
        v-else-if="postsStore.currentPost"
        class="max-w-4xl mx-auto"
      >
        <article class="bg-secondary rounded-lg shadow-lg p-8 mb-8 transition-cozy">
          <div class="mb-6">
            <router-link
              :to="{ name: 'Home' }"
              class="text-accent hover:opacity-80 transition-cozy inline-flex items-center mb-4"
            >
              ← Back to posts
            </router-link>
          </div>

          <h1 class="text-4xl font-serif text-primary mb-4">
            {{ postsStore.currentPost.title }}
          </h1>

          <div class="flex items-center gap-4 text-secondary mb-6">
            <span v-if="postsStore.currentPost.author">
              By {{ postsStore.currentPost.author.name }}
            </span>
            <span v-if="postsStore.currentPost.published_at">
              {{ formatDate(postsStore.currentPost.published_at) }}
            </span>
            <span v-if="postsStore.currentPost.views_count">
              {{ postsStore.currentPost.views_count }} views
            </span>
          </div>

          <div
            v-if="postsStore.currentPost.content"
            class="markdown-content text-primary"
            v-html="renderMarkdown(postsStore.currentPost.content)"
          />
        </article>

        <div class="flex gap-4 justify-center">
          <button
            class="px-4 py-2 bg-accent text-white rounded-lg hover:opacity-80 transition-cozy"
            @click="toggleReaderMode"
          >
            {{ readerMode ? 'Exit' : 'Enter' }} Reader Mode
          </button>
        </div>
      </div>

      <div
        v-else
        class="text-center py-12"
      >
        <p class="text-secondary">
          Post not found
        </p>
      </div>
    </main>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { usePostsStore } from '@/stores/posts'
import { renderMarkdown } from '@/utils/markdown'
import AppHeader from '@/components/AppHeader.vue'

const route = useRoute()
const postsStore = usePostsStore()
const readerMode = ref(false)

onMounted(async () => {
  await postsStore.fetchPostBySlug(route.params.slug)
})

function formatDate(dateString) {
  const date = new Date(dateString)
  return date.toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
  })
}

function toggleReaderMode() {
  readerMode.value = !readerMode.value
  if (readerMode.value) {
    document.body.classList.add('reader-mode')
  } else {
    document.body.classList.remove('reader-mode')
  }
}
</script>
