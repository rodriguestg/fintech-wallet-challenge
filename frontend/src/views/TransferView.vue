<template>
  <div class="container">
    <div class="card">
      <h1>Transferir Dinheiro</h1>

      <form @submit.prevent="handleTransfer">
        <div class="form-group">
          <label>E-mail do Destinatário</label>
          <input v-model="recipientEmail" type="email" required />
        </div>

        <div class="form-group">
          <label>Valor (R$)</label>
          <input v-model.number="amount" type="number" step="0.01" required />
        </div>

        <button type="submit" :disabled="loading">
          {{ loading ? 'Transferindo...' : 'Transferir' }}
        </button>

        <p v-if="error" class="error">{{ error }}</p>
        <p v-if="success" class="success">
          {{ success }}
          <router-link to="/dashboard">Voltar ao Dashboard</router-link>
        </p>
      </form>

      <div class="info">
        <p><strong>Seu Saldo:</strong> R$ {{ formatCurrency(authStore.wallet?.balance || 0) }}</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useAuthStore } from '../stores/authStore'
import api from '../services/api'

const authStore = useAuthStore()

const recipientEmail = ref('')
const amount = ref(null)
const loading = ref(false)
const error = ref('')
const success = ref('')

const formatCurrency = (value) => {
  return parseFloat(value).toLocaleString('pt-BR', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2,
  })
}

const handleTransfer = async () => {
  error.value = ''
  success.value = ''

  if (!recipientEmail.value || !amount.value) {
    error.value = 'Preencha todos os campos'
    return
  }

  if (amount.value <= 0) {
    error.value = 'O valor deve ser maior que zero'
    return
  }

  loading.value = true

  try {
    const response = await api.post('/transfers', {
      recipient_email: recipientEmail.value,
      amount: amount.value,
    })

    success.value = response.data.message
    recipientEmail.value = ''
    amount.value = null

    // Atualiza saldo
    await authStore.fetchUser()
  } catch (err) {
    error.value = err.response?.data?.message || 'Erro ao transferir'
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
  padding: 1rem;
}

.card {
  background: white;
  padding: 2rem;
  border-radius: 8px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  width: 100%;
  max-width: 500px;
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

.success {
  color: #155724;
  margin-top: 1rem;
  padding: 0.75rem;
  background: #d4edda;
  border: 1px solid #c3e6cb;
  border-radius: 4px;
}

.success a {
  display: block;
  margin-top: 0.5rem;
  color: #155724;
  font-weight: bold;
  text-decoration: none;
}

.success a:hover {
  text-decoration: underline;
}

.info {
  margin-top: 1.5rem;
  padding: 1rem;
  background: #f9f9f9;
  border-radius: 4px;
  color: #666;
}
</style>