<template>
  <div class="bg-primary min-h-screen">
    <AppHeader />
    <main class="container mx-auto px-4 py-12">
      <div class="mx-auto max-w-6xl">
        <h1 class="text-primary mb-8 font-serif text-3xl">
          Write a New Post
        </h1>

        <div class="grid grid-cols-1 gap-8 lg:grid-cols-2">
          <!-- Editor -->
          <div class="bg-secondary rounded-lg p-6 shadow-lg">
            <div class="mb-6 space-y-4">
              <input
                v-model="title"
                type="text"
                placeholder="Post title..."
                class="border-color bg-primary text-primary focus:ring-accent w-full rounded-lg border px-4 py-2 font-serif text-2xl focus:outline-none focus:ring-2"
              >
              <textarea
                v-model="content"
                placeholder="Write your post in Markdown..."
                rows="20"
                class="border-color bg-primary text-primary focus:ring-accent w-full rounded-lg border px-4 py-2 font-mono text-sm focus:outline-none focus:ring-2"
              />
            </div>
            <button
              class="bg-accent transition-cozy rounded-lg px-6 py-2 text-white hover:opacity-80"
              @click="savePost"
            >
              Save Draft
            </button>
          </div>

          <!-- Preview -->
          <div class="bg-secondary rounded-lg p-6 shadow-lg">
            <h2 class="text-primary mb-4 font-serif text-xl">
              Preview
            </h2>
            <!-- eslint-disable-next-line vue/no-v-html -->
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
/* eslint-disable vue/no-v-html */
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
