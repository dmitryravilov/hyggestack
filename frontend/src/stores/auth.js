import { defineStore } from 'pinia'
import { ref } from 'vue'
import axios from 'axios'
import api from '@/utils/api'

export const useAuthStore = defineStore('auth', () => {
  const user = ref(null)
  const token = ref(localStorage.getItem('token'))

  if (token.value) {
    api.defaults.headers.common['Authorization'] = `Bearer ${token.value}`
  }

  async function login(email, password) {
    try {
      await axios.get(`${import.meta.env.VITE_API_URL?.replace('/api/v1', '') || 'http://localhost:8080'}/sanctum/csrf-cookie`, {
        withCredentials: true
      })
      
      const response = await api.post('/login', { email, password })
      token.value = response.data.token
      user.value = response.data.user
      localStorage.setItem('token', token.value)
      api.defaults.headers.common['Authorization'] = `Bearer ${token.value}`
      return { success: true }
    } catch (error) {
      return { success: false, error: error.response?.data?.message || 'Login failed' }
    }
  }

  async function register(name, email, password) {
    try {
      await axios.get(`${import.meta.env.VITE_API_URL?.replace('/api/v1', '') || 'http://localhost:8080'}/sanctum/csrf-cookie`, {
        withCredentials: true
      })
      
      const response = await api.post('/register', { name, email, password, password_confirmation: password })
      token.value = response.data.token
      user.value = response.data.user
      localStorage.setItem('token', token.value)
      api.defaults.headers.common['Authorization'] = `Bearer ${token.value}`
      return { success: true }
    } catch (error) {
      return { success: false, error: error.response?.data?.message || 'Registration failed' }
    }
  }

  async function logout() {
    try {
      await api.post('/logout')
    } catch (error) {
      // Log out locally even if the server call fails
    } finally {
      token.value = null
      user.value = null
      localStorage.removeItem('token')
      delete api.defaults.headers.common['Authorization']
    }
  }

  async function fetchUser() {
    if (!token.value) return

    try {
      const response = await api.get('/me')
      user.value = response.data.data
    } catch (error) {
      logout()
    }
  }

  async function changePassword(currentPassword, newPassword) {
    try {
      const payload = {
        current_password: currentPassword,
        password: newPassword,
      }
      console.log('Sending password change request:', payload)
      const response = await api.post('/change-password', payload)
      return { success: true, message: response.data.message }
    } catch (error) {
      console.error('Password change error:', error.response?.data)
      return { 
        success: false, 
        error: error.response?.data?.message || 'Failed to change password',
        errors: error.response?.data?.errors || {}
      }
    }
  }

  function isAdmin() {
    return user.value?.roles?.some(role => role.name === 'admin')
  }

  function isWriter() {
    return user.value?.roles?.some(role => ['admin', 'writer'].includes(role.name))
  }

  const isAuthenticated = () => !!token.value

  return {
    user,
    token,
    login,
    register,
    logout,
    fetchUser,
    changePassword,
    isAuthenticated,
    isAdmin,
    isWriter,
  }
})

