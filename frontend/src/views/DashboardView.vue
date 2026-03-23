<template>
  <div class="dashboard">
    <div class="header">
      <h1>Dashboard</h1>
      <p>Bem-vindo, {{ authStore.user?.name }}!</p>
    </div>

    <div class="wallet-card">
      <h2>Seu Saldo</h2>
      <div v-if="authStore.isLoadingUser" class="balance-loading">
        Carregando...
      </div>
      <p v-else class="balance">
        R$ {{ formatCurrency(authStore.wallet?.balance || 0) }}
      </p>
      <router-link to="/transfer" class="btn btn-primary">
        Transferir Dinheiro
      </router-link>
    </div>

    <div class="transactions">
      <h2>Últimas Transações</h2>
      <div v-if="loading" class="loading">Carregando...</div>
      <div v-else-if="transactions.length === 0" class="empty">
        Nenhuma transação ainda
      </div>
      <div v-else class="transaction-list">
        <div v-for="tx in transactions" :key="tx.id" class="transaction-item">
          <div class="info">
            <p class="type" :class="tx.type">{{ tx.type === 'sent' ? '→ Enviado' : '← Recebido' }}</p>
            <p class="user">
              {{ tx.type === 'sent' ? tx.recipient.name : tx.sender.name }}
            </p>
            <p class="date">{{ formatDate(tx.created_at) }}</p>
          </div>
          <p class="amount" :class="tx.type">
            {{ tx.type === 'sent' ? '-' : '+' }} R$ {{ formatCurrency(tx.amount) }}
          </p>
        </div>
      </div>

      <router-link to="/history" class="link-more">Ver histórico completo</router-link>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useAuthStore } from '../stores/authStore'
import api from '../services/api'

const authStore = useAuthStore()
const transactions = ref([])
const loading = ref(true)

const formatCurrency = (value) => {
  return parseFloat(value).toLocaleString('pt-BR', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2,
  })
}

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('pt-BR')
}

const fetchUserData = async () => {
  try {
    await authStore.fetchUser()
  } catch (err) {
    console.error('Erro ao buscar dados do usuário:', err)
  }
}

const fetchTransactions = async () => {
  try {
    loading.value = true;
    const response = await api.get('/transactions/recent');
    transactions.value = response.data.data;
  } catch (err) {
    console.error('Erro ao buscar transações:', err);
  } finally {
    loading.value = false;
  }
}

onMounted(async () => {
  try {
    await fetchUserData()
    await fetchTransactions()
  } catch (err) {
    console.error('Erro ao carregar dashboard:', err)
  }
})
</script>

<style scoped>
.dashboard {
  padding: 2rem;
  max-width: 1200px;
  margin: 0 auto;
}

.header {
  margin-bottom: 2rem;
}

h1 {
  color: #333;
  margin-bottom: 0.5rem;
}

.header p {
  color: #666;
}

.balance-loading {
  font-size: 2.5rem;
  font-weight: bold;
  margin-bottom: 1.5rem;
  opacity: 0.7;
}

.wallet-card {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  padding: 2rem;
  border-radius: 8px;
  margin-bottom: 2rem;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.wallet-card h2 {
  margin-bottom: 1rem;
  font-size: 1rem;
  opacity: 0.9;
}

.balance {
  font-size: 2.5rem;
  font-weight: bold;
  margin-bottom: 1.5rem;
}

.btn {
  display: inline-block;
  padding: 0.75rem 1.5rem;
  border-radius: 4px;
  text-decoration: none;
  font-weight: bold;
  cursor: pointer;
}

.btn-primary {
  background: white;
  color: #667eea;
}

.btn-primary:hover {
  opacity: 0.9;
}

.transactions {
  background: white;
  padding: 2rem;
  border-radius: 8px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.transactions h2 {
  margin-bottom: 1.5rem;
  color: #333;
}

.loading,
.empty {
  text-align: center;
  color: #666;
  padding: 2rem;
}

.transaction-list {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.transaction-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1rem;
  border: 1px solid #eee;
  border-radius: 4px;
}

.info {
  flex: 1;
}

.type {
  font-weight: bold;
  margin-bottom: 0.25rem;
}

.type.sent {
  color: #dc3545;
}

.type.received {
  color: #28a745;
}

.user {
  color: #666;
  font-size: 0.9rem;
  margin-bottom: 0.25rem;
}

.date {
  color: #999;
  font-size: 0.85rem;
}

.amount {
  font-size: 1.1rem;
  font-weight: bold;
}

.amount.sent {
  color: #dc3545;
}

.amount.received {
  color: #28a745;
}

.link-more {
  display: block;
  margin-top: 1.5rem;
  color: #007bff;
  text-decoration: none;
  font-weight: bold;
}

.link-more:hover {
  text-decoration: underline;
}
</style>