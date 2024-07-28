import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '@/stores/useAuthStore'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'AppLayout',
      component: () => import('@/layouts/AppLayout.vue'),
      children: [
        {
          path: '/inicio',
          name: 'inicio',
          component: () => import('@/views/InicioView.vue')
        },
        {
          path: 'reservas',
          name: 'reservas',
          component: () => import('@/views/ReservaView.vue'),
          meta: { requiresAuth: true, roles: ['admin', 'recep'] }
        },
        {
          path: 'recepcion',
          name: 'recepcion',
          component: () => import('@/views/RecepcionView.vue'),
          meta: { requiresAuth: true, roles: ['admin', 'recep'] }
        },
        {
          path: 'salidas',
          name: 'salidas',
          component: () => import('@/views/VerificarSalidaView.vue'),
          meta: { requiresAuth: true, roles: ['admin', 'recep'] }
        },
        {
          path: 'vender-productos',
          name: 'vender-productos',
          component: () => import('@/views/VenderProductoView.vue'),
          meta: { requiresAuth: true, roles: ['admin', 'recep'] }
        },
        {
          path: 'productos',
          name: 'productos',
          component: () => import('@/views/ProductoView.vue'),
          meta: { requiresAuth: true, roles: ['admin'] }
        },
        {
          path: 'habitaciones',
          name: 'habitaciones',
          component: () => import('@/views/HabitacionView.vue'),
          meta: { requiresAuth: true, roles: ['admin'] }
        },
        {
          path: 'pisos',
          name: 'pisos',
          component: () => import('@/views/PisoView.vue'),
          meta: { requiresAuth: true, roles: ['admin'] }
        },
        {
          path: 'sucursales',
          name: 'sucursales',
          component: () => import('@/views/SucursalView.vue'),
          meta: { requiresAuth: true, roles: ['admin'] }
        },
        {
          path: 'personales',
          name: 'personales',
          component: () => import('@/views/PersonalView.vue'),
          meta: { requiresAuth: true, roles: ['admin'] }
        },
        {
          path: 'usuario-reportes',
          name: 'usuario-reportes',
          component: () => import('@/views/report/UsuarioReporte.vue'),
          meta: { requiresAuth: true, roles: ['admin'] }
        },
        {
          path: 'tipo-habitaciones',
          name: 'tipo-habitaciones',
          component: () => import('@/views/TipoHabitacionView.vue'),
          meta: { requiresAuth: true, roles: ['admin'] }
        },
        {
          path: 'clientes',
          name: 'clientes',
          component: () => import('@/views/ClienteView.vue'),
          meta: { requiresAuth: true, roles: ['admin', 'recep'] }
        }

      ]
    },
    {
      path: '/login',
      name: 'login',
      component: () => import('@/views/auth/LoginView.vue')
    }
  ]
})

router.beforeEach((to, from, next) => {
  const authStore = useAuthStore();
  authStore.initializeAuth();

  const isAuthenticated = authStore.isAuthenticated;
  const requiredRoles = to.meta.roles;
  const userRole = authStore.userRole;

  if (to.meta.requiresAuth && !isAuthenticated) {
    next('/login');
  } else if (requiredRoles && !requiredRoles.includes(userRole)) {
    next('/');
  } else {
    next();
  }
});

export default router