<template>
  <article class="bg-secondary transition-cozy rounded-lg p-6 shadow-lg hover:shadow-xl">
    <router-link :to="{ name: 'Post', params: { slug: post.slug } }">
      <h2 class="text-primary hover:text-accent transition-cozy mb-3 font-serif text-2xl">
        {{ post.title }}
      </h2>
    </router-link>

    <p class="text-secondary mb-4">
      {{ post.excerpt }}
    </p>

    <div class="text-secondary flex items-center gap-4 text-sm">
      <span v-if="post.author"> By {{ post.author.name }} </span>
      <span v-if="post.published_at">
        {{ formatDate(post.published_at) }}
      </span>
      <span
        v-if="post.category"
        class="rounded px-2 py-1 text-xs"
        :style="{ backgroundColor: post.category.color + '20', color: post.category.color }"
      >
        {{ post.category.name }}
      </span>
    </div>

    <div
      v-if="post.tags && post.tags.length > 0"
      class="mt-4 flex flex-wrap gap-2"
    >
      <span
        v-for="tag in post.tags"
        :key="tag.id"
        class="bg-accent-light text-secondary rounded px-2 py-1 text-xs"
      >
        #{{ tag.name }}
      </span>
    </div>
  </article>
</template>

<script setup>
defineProps({
  post: {
    type: Object,
    required: true,
  },
})

function formatDate(dateString) {
  const date = new Date(dateString)
  return date.toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
  })
}
</script>
