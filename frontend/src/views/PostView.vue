<template>
  <div class="bg-primary min-h-screen">
    <AppHeader />
    <main class="container mx-auto px-4 py-12">
      <div
        v-if="postsStore.loading"
        class="py-12 text-center"
      >
        <div
          class="border-accent inline-block h-12 w-12 animate-spin rounded-full border-b-2 border-t-2"
        />
      </div>

      <div
        v-else-if="postsStore.currentPost"
        class="mx-auto max-w-4xl"
      >
        <article class="bg-secondary transition-cozy mb-8 rounded-lg p-8 shadow-lg">
          <div class="mb-6">
            <router-link
              :to="{ name: 'Home' }"
              class="text-accent transition-cozy mb-4 inline-flex items-center hover:opacity-80"
            >
              ← Back to posts
            </router-link>
          </div>

          <h1 class="text-primary mb-4 font-serif text-4xl">
            {{ postsStore.currentPost.title }}
          </h1>

          <div class="text-secondary mb-6 flex items-center gap-4">
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

        <div class="flex justify-center gap-4">
          <button
            class="bg-accent transition-cozy rounded-lg px-4 py-2 text-white hover:opacity-80"
            @click="toggleReaderMode"
          >
            {{ readerMode ? 'Exit' : 'Enter' }} Reader Mode
          </button>
        </div>
      </div>

      <div
        v-else
        class="py-12 text-center"
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
