import { createRouter, createWebHistory } from 'vue-router';
import { useAuthStore } from '../stores/authStore';
import LoginView from '../views/LoginView.vue';
import RegisterView from '../views/RegisterView.vue';
import TransferView from '../views/TransferView.vue';

const routes = [
    { path: '/login', component: LoginView, meta: { requiresAuth: false } },
    { path: '/register', component: RegisterView, meta: { requiresAuth: false } },
    { path: '/transfer', component: TransferView, meta: { requiresAuth: true } }
]

const router = createRouter({
  history: createWebHistory(),
  routes,
})

router.beforeEach((to, from, next) => {
  const authStore = useAuthStore()
  
  if (to.meta.requiresAuth && !authStore.isAuthenticated) {
    next('/login')
  } else if ((to.path === '/login' || to.path === '/register') && authStore.isAuthenticated) {
    next('/dashboard')
  } else {
    next()
  }
})

export default router;