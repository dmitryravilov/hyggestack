<template>
  <div class="relative">
    <button
      class="text-primary hover:text-accent transition-cozy px-3 py-2 text-sm"
      @click="showDropdown = !showDropdown"
    >
      {{ selectedCategory ? selectedCategory.name : 'Categories' }}
    </button>

    <div
      v-if="showDropdown"
      class="bg-secondary border-color absolute right-0 z-10 mt-2 max-h-96 w-64 overflow-y-auto rounded-lg border p-4 shadow-lg"
    >
      <div
        class="hover:bg-accent-light transition-cozy mb-2 cursor-pointer rounded-lg p-3"
        :class="{ 'bg-accent-light': !selectedCategory }"
        @click="selectCategory(null)"
      >
        <div class="text-primary font-medium">All Categories</div>
      </div>
      <div
        v-for="category in categories"
        :key="category.id"
        class="hover:bg-accent-light transition-cozy mb-2 cursor-pointer rounded-lg p-3"
        :class="{ 'bg-accent-light': selectedCategory?.id === category.id }"
        @click="selectCategory(category)"
      >
        <div class="text-primary font-medium">
          {{ category.name }}
        </div>
        <div v-if="category.posts_count" class="text-secondary text-sm">
          {{ category.posts_count }} {{ category.posts_count === 1 ? 'post' : 'posts' }}
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted, watch } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import api from '@/utils/api'

const router = useRouter()
const route = useRoute()

const categories = ref([])
const selectedCategory = ref(null)
const showDropdown = ref(false)

async function fetchCategories() {
  try {
    const response = await api.get('/categories')
    categories.value = response.data.data
  } catch (error) {
    console.error('Error fetching categories:', error)
  }
}

function selectCategory(category) {
  selectedCategory.value = category
  showDropdown.value = false

  if (category) {
    router.push({ query: { category: category.slug } })
  } else {
    router.push({ query: {} })
  }
}

function handleClickOutside(event) {
  if (!event.target.closest('.relative')) {
    showDropdown.value = false
  }
}

onMounted(() => {
  document.addEventListener('click', handleClickOutside)

  fetchCategories().then(() => {
    if (route.query.category) {
      const categorySlug = route.query.category
      selectedCategory.value = categories.value.find(c => c.slug === categorySlug) || null
    }
  })
})

watch(
  () => route.query.category,
  newCategory => {
    if (newCategory && categories.value.length > 0) {
      selectedCategory.value = categories.value.find(c => c.slug === newCategory) || null
    } else {
      selectedCategory.value = null
    }
  }
)

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
})
</script>
