<script setup>
  import { ref, onMounted } from 'vue';
  import { RouterView } from 'vue-router' 
  import { useSessionStore } from '@/stores/session'

  const session = ref({})

  const getSession = () => {
    const json = sessionStorage.getItem('session')
    const obj = JSON.parse(json) || {}
    session.value = typeof obj === 'object' ? obj : {}
  }

  const storeSession = useSessionStore()

  onMounted(() => {
    getSession()

    storeSession.setSession(session.value)
  })
</script>

<template>
  <RouterView />
</template>
