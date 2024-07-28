<script setup>
import { RouterView } from 'vue-router'
import { onBeforeMount } from 'vue';
import { useAuthStore } from '@/stores/useAuthStore';
import router from './router';

const authStore = useAuthStore();
onBeforeMount(() => {
  authStore.initializeAuth();
  if (!authStore.isAuthenticated) {
    router.push({ name: 'login' });
  }
});

</script>

<template>

  <RouterView />
</template>

<style scoped></style>
