<template>
  <div class="container">
    <div class="card">
      <h1>Criar Conta</h1>
      
      <form @submit.prevent="handleRegister">
        <div class="form-group">
          <label>Nome</label>
          <input v-model="name" type="text" required />
        </div>

        <div class="form-group">
          <label>E-mail</label>
          <input v-model="email" type="email" required />
        </div>

        <div class="form-group">
          <label>Senha</label>
          <input v-model="password" type="password" required />
        </div>

        <div class="form-group">
          <label>Confirmar Senha</label>
          <input v-model="passwordConfirmation" type="password" required />
        </div>

        <button type="submit" :disabled="loading">
          {{ loading ? 'Criando...' : 'Criar Conta' }}
        </button>

        <p v-if="error" class="error">{{ error }}</p>
      </form>

      <p>
        Já tem conta?
        <router-link to="/login">Entrar</router-link>
      </p>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/authStore'

const router = useRouter()
const authStore = useAuthStore()

const name = ref('')
const email = ref('')
const password = ref('')
const passwordConfirmation = ref('')
const loading = ref(false)
const error = ref('')

const handleRegister = async () => {
  if (password.value !== passwordConfirmation.value) {
    error.value = 'Senhas não conferem'
    return
  }

  loading.value = true
  error.value = ''

  try {
    await authStore.register(name.value, email.value, password.value, passwordConfirmation.value)
    router.push('/dashboard')
  } catch (err) {
    error.value = err.response?.data?.message || 'Erro ao criar conta'
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
.container {
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 100vh;
  background: #f5f5f5;
}

.card {
  background: white;
  padding: 2rem;
  border-radius: 8px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  width: 100%;
  max-width: 400px;
}

h1 {
  margin-bottom: 1.5rem;
  color: #333;
}

.form-group {
  margin-bottom: 1rem;
}

label {
  display: block;
  margin-bottom: 0.5rem;
  font-weight: bold;
  color: #555;
}

input {
  width: 100%;
  padding: 0.75rem;
  border: 1px solid #ddd;
  border-radius: 4px;
  font-size: 1rem;
}

input:focus {
  outline: none;
  border-color: #007bff;
  box-shadow: 0 0 4px rgba(0, 123, 255, 0.3);
}

button {
  width: 100%;
  padding: 0.75rem;
  margin-top: 1rem;
  background: #007bff;
  color: white;
  border: none;
  border-radius: 4px;
  font-size: 1rem;
  cursor: pointer;
  font-weight: bold;
}

button:hover:not(:disabled) {
  background: #0056b3;
}

button:disabled {
  background: #ccc;
  cursor: not-allowed;
}

.error {
  color: #dc3545;
  margin-top: 1rem;
  padding: 0.75rem;
  background: #f8d7da;
  border: 1px solid #f5c6cb;
  border-radius: 4px;
}

p {
  margin-top: 1rem;
  text-align: center;
}

a {
  color: #007bff;
  text-decoration: none;
}

a:hover {
  text-decoration: underline;
}
</style>