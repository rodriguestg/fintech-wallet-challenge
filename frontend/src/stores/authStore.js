import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import api from '../services/api'

export const useAuthStore = defineStore('auth', () => {
  const user = ref(null);
  const token = ref(localStorage.getItem('auth_token') || null);
  const wallet = ref(null);
  const isLoadingUser = ref(false);
  const isLoggingOut = ref(false);

  const isAuthenticated = computed(() => !!token.value);

  const register = async (name, email, password, passwordConfirmation) => {
    const response = await api.post('/register', {
      name,
      email,
      password,
      password_confirmation: passwordConfirmation,
    });
    token.value = response.data.token
    user.value = response.data.user
    localStorage.setItem('auth_token', token.value)
    return response.data
  };

  const login = async (email, password) => {
    const response = await api.post('/login', { email, password })
    token.value = response.data.token
    user.value = response.data.user
    localStorage.setItem('auth_token', token.value)
    return response.data
  };

  const logout = async () => {
    isLoggingOut.value = true;
    try {
      await api.post('/logout');
      token.value = null;
      user.value = null;
      wallet.value = null;
      localStorage.removeItem('auth_token');
    } catch (error) {
      console.error('Erro ao fazer logout:', error);
      token.value = null
      user.value = null
      wallet.value = null
      localStorage.removeItem('auth_token')
    } finally {
      isLoggingOut.value = false;
    }
  };

  const fetchUser = async () => {
    isLoadingUser.value = true;
    try {
      const response = await api.get('/me');
      user.value = response.data.user;
      wallet.value = response.data.wallet;
      return response.data;
      
    } catch (error) {
      console.error('Erro ao buscar usuário:', error);
    } finally {
      isLoadingUser.value = false;
    }
    
  }

  return {
    user,
    token,
    wallet,
    isLoadingUser,
    isLoggingOut,
    isAuthenticated,
    register,
    login,
    logout,
    fetchUser,
  }
});