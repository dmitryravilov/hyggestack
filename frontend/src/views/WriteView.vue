<template>
  <div class="min-h-screen bg-primary">
    <AppHeader />
    <main class="container mx-auto px-4 py-12">
      <div class="max-w-6xl mx-auto">
        <h1 class="text-3xl font-serif text-primary mb-8">
          Write a New Post
        </h1>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
          <!-- Editor -->
          <div class="bg-secondary rounded-lg shadow-lg p-6">
            <div class="space-y-4 mb-6">
              <input
                v-model="title"
                type="text"
                placeholder="Post title..."
                class="w-full px-4 py-2 border border-color rounded-lg bg-primary text-primary focus:outline-none focus:ring-2 focus:ring-accent text-2xl font-serif"
              >
              <textarea
                v-model="content"
                placeholder="Write your post in Markdown..."
                rows="20"
                class="w-full px-4 py-2 border border-color rounded-lg bg-primary text-primary focus:outline-none focus:ring-2 focus:ring-accent font-mono text-sm"
              />
            </div>
            <button
              class="px-6 py-2 bg-accent text-white rounded-lg hover:opacity-80 transition-cozy"
              @click="savePost"
            >
              Save Draft
            </button>
          </div>

          <!-- Preview -->
          <div class="bg-secondary rounded-lg shadow-lg p-6">
            <h2 class="text-xl font-serif text-primary mb-4">
              Preview
            </h2>
            <div
              class="markdown-content text-primary"
              v-html="renderMarkdown(content || '*Start writing to see preview...*')"
            />
          </div>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { renderMarkdown } from '@/utils/markdown'
import AppHeader from '@/components/AppHeader.vue'

const title = ref('')
const content = ref('')

function savePost() {
  // TODO: Implement post saving
  console.log('Saving post:', { title: title.value, content: content.value })
}
</script>
