<template>
  <div class="container">
    <h1>Histórico de Transações</h1>

    <div class="filters">
      <div class="filter-group">
        <label>Tipo</label>
        <select v-model="filterType">
          <option value="">Todos</option>
          <option value="sent">Enviados</option>
          <option value="received">Recebidos</option>
        </select>
      </div>

      <div class="filter-group">
        <label>Data Inicial</label>
        <input v-model="filterStartDate" type="date" />
      </div>

      <div class="filter-group">
        <label>Data Final</label>
        <input v-model="filterEndDate" type="date" />
      </div>

      <button @click="applyFilters" class="btn-filter">Filtrar</button>
      <button @click="resetFilters" class="btn-reset">Limpar</button>
    </div>

    <div v-if="loading" class="loading">Carregando...</div>
    <div v-else-if="transactions.length === 0" class="empty">
      Nenhuma transação encontrada
    </div>

    <div v-else class="table-responsive">
      <table>
        <thead>
          <tr>
            <th>Data</th>
            <th>Tipo</th>
            <th>Usuário</th>
            <th>Valor</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="tx in transactions" :key="tx.id">
            <td>{{ formatDate(tx.created_at) }}</td>
            <td :class="`type type-${tx.type}`">
              {{ tx.type === 'sent' ? 'Enviado' : 'Recebido' }}
            </td>
            <td>{{ tx.type === 'sent' ? tx.recipient.name : tx.sender.name }}</td>
            <td :class="`amount amount-${tx.type}`">
              {{ tx.type === 'sent' ? '-' : '+' }} R$ {{ formatCurrency(tx.amount) }}
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <div v-if="meta" class="pagination">
      <button
        v-if="meta.current_page > 1"
        @click="goToPage(meta.current_page - 1)"
        class="btn-page"
      >
        ← Anterior
      </button>

      <span class="page-info">
        Página {{ meta.current_page }} de {{ meta.last_page }}
      </span>

      <button
        v-if="meta.current_page < meta.last_page"
        @click="goToPage(meta.current_page + 1)"
        class="btn-page"
      >
        Próxima →
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import api from '../services/api'

const transactions = ref([])
const meta = ref(null)
const loading = ref(true)
const currentPage = ref(1)

const filterType = ref('')
const filterStartDate = ref('')
const filterEndDate = ref('')

const formatCurrency = (value) => {
  return parseFloat(value).toLocaleString('pt-BR', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2,
  })
}

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('pt-BR', {
    year: 'numeric',
    month: '2-digit',
    day: '2-digit',
    hour: '2-digit',
    minute: '2-digit',
  })
}

const buildParams = () => {
  const params = new URLSearchParams()
  params.append('page', currentPage.value)

  if (filterType.value) params.append('type', filterType.value)
  if (filterStartDate.value) params.append('start_date', filterStartDate.value)
  if (filterEndDate.value) params.append('end_date', filterEndDate.value)

  return params.toString()
}

const fetchTransactions = async () => {
  loading.value = true
  try {
    const response = await api.get(`/transactions?${buildParams()}`)
    transactions.value = response.data.data
    meta.value = response.data.meta
  } catch (err) {
    console.error('Erro ao buscar transações:', err)
  } finally {
    loading.value = false
  }
}

const applyFilters = () => {
  currentPage.value = 1
  fetchTransactions()
}

const resetFilters = () => {
  filterType.value = ''
  filterStartDate.value = ''
  filterEndDate.value = ''
  currentPage.value = 1
  fetchTransactions()
}

const goToPage = (page) => {
  currentPage.value = page
  fetchTransactions()
}

onMounted(() => {
  fetchTransactions()
})
</script>

<style scoped>
.container {
  padding: 2rem;
  max-width: 1200px;
  margin: 0 auto;
}

h1 {
  color: #333;
  margin-bottom: 1.5rem;
}

.filters {
  display: flex;
  gap: 1rem;
  margin-bottom: 2rem;
  flex-wrap: wrap;
  background: #f9f9f9;
  padding: 1rem;
  border-radius: 8px;
}

.filter-group {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.filter-group label {
  font-weight: bold;
  color: #555;
  font-size: 0.9rem;
}

.filter-group select,
.filter-group input {
  padding: 0.5rem;
  border: 1px solid #ddd;
  border-radius: 4px;
  font-size: 0.9rem;
}

.btn-filter,
.btn-reset {
  padding: 0.5rem 1rem;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-weight: bold;
  align-self: flex-end;
}

.btn-filter {
  background: #007bff;
  color: white;
}

.btn-filter:hover {
  background: #0056b3;
}

.btn-reset {
  background: #6c757d;
  color: white;
}

.btn-reset:hover {
  background: #5a6268;
}

.loading,
.empty {
  text-align: center;
  padding: 2rem;
  color: #666;
}

.table-responsive {
  overflow-x: auto;
  background: white;
  border-radius: 8px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

table {
  width: 100%;
  border-collapse: collapse;
}

th {
  padding: 1rem;
  text-align: left;
  background: #f9f9f9;
  border-bottom: 2px solid #ddd;
  font-weight: bold;
  color: #333;
}

td {
  padding: 1rem;
  border-bottom: 1px solid #ddd;
}

tr:hover {
  background: #f9f9f9;
}

.type {
  font-weight: bold;
}

.type-sent {
  color: #dc3545;
}

.type-received {
  color: #28a745;
}

.amount {
  font-weight: bold;
}

.amount-sent {
  color: #dc3545;
}

.amount-received {
  color: #28a745;
}

.pagination {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 1rem;
  margin-top: 2rem;
}

.btn-page {
  padding: 0.5rem 1rem;
  background: #007bff;
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-weight: bold;
}

.btn-page:hover {
  background: #0056b3;
}

.page-info {
  color: #666;
  font-weight: bold;
}
</style>