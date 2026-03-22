<template>
  <div id="app">
    <nav v-if="authStore.isAuthenticated">
      <router-link to="/dashboard">Dashboard</router-link>
      <router-link to="/transfer">Transferir</router-link>
      <router-link to="/history">Histórico</router-link>
      <button @click="logout">Logout</button>
    </nav>
    
    <router-view />
  </div>
</template>

<script setup>
import { useAuthStore } from './stores/authStore'

const authStore = useAuthStore()

const logout = async () => {
  await authStore.logout()
  window.location.href = '/login'
}
</script>

<style scoped>
nav {
  display: flex;
  gap: 1rem;
  padding: 1rem;
  background: #f0f0f0;
  border-bottom: 1px solid #ccc;
}

nav a {
  text-decoration: none;
  color: #333;
  cursor: pointer;
}

nav button {
  margin-left: auto;
  padding: 0.5rem 1rem;
  background: #dc3545;
  color: white;
  border: none;
  cursor: pointer;
}
</style>