import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const routes = [
  {
    path: '/',
    name: 'Home',
    component: () => import('@/views/HomeView.vue'),
  },
  {
    path: '/posts/:slug',
    name: 'Post',
    component: () => import('@/views/PostView.vue'),
  },
  {
    path: '/admin/login',
    name: 'AdminLogin',
    component: () => import('@/views/admin/AdminLoginView.vue'),
    meta: { guest: true },
  },
  {
    path: '/admin',
    name: 'AdminDashboard',
    component: () => import('@/views/admin/AdminDashboardView.vue'),
    meta: { requiresAuth: true, requiresAdmin: true },
  },
  {
    path: '/:pathMatch(.*)*',
    name: 'NotFound',
    component: () => import('@/views/NotFoundView.vue'),
  },
]

const router = createRouter({
  history: createWebHistory(),
  routes,
})

router.beforeEach(async (to, from, next) => {
  const authStore = useAuthStore()

  if (authStore.isAuthenticated() && !authStore.user) {
    await authStore.fetchUser()
  }

  if (to.meta.requiresAuth && !authStore.isAuthenticated()) {
    if (to.path.startsWith('/admin')) {
      next({ name: 'AdminLogin' })
    } else {
      next({ name: 'Home' })
    }
  } else if (to.meta.requiresAdmin) {
    const user = authStore.user
    if (!user || !user.roles?.some(role => ['admin', 'writer'].includes(role.name))) {
      next({ name: 'AdminLogin' })
    } else {
      next()
    }
  } else if (to.meta.guest && authStore.isAuthenticated()) {
    if (to.path.startsWith('/admin')) {
      next({ name: 'AdminDashboard' })
    } else {
      next({ name: 'Home' })
    }
  } else {
    next()
  }
})

export default router
