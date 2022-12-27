<script setup>
  import { ref, onMounted } from 'vue'
  import { useRouter } from 'vue-router'
  import { useSessionStore } from '@/stores/session'
  import Logout from '@/components/Logout.vue'

  const router = useRouter()
  const storeSession = useSessionStore()

  const verifySession = () => {
    if (!storeSession.session.hasOwnProperty('token')) {
      router.push('/login')
    }        
  }

  onMounted(() => {
    verifySession()
  })

  const modal = ref(null)
  
  const toggleModal = () => {
    modal.value?.toggleModal()
  } 

  const logout = (response) => {
    toggleModal()

    if (response) {
      storeSession.destroySession()
      verifySession()
    }
  }
</script>

<template>
  <main>
    <div class="welcome">
      <h1 v-if="storeSession.session.hasOwnProperty('desperson')">
        Olá, {{ storeSession.session.desperson }}!
      </h1>

      <img 
        src="../assets/img/Welcome-rafiki.svg" 
        alt="Pessoa com uma faixa escrita Bem-vindo"
        class="h-4/6"
      >

      <div class="actions flex flex-col gap-2">
        <button>Continuar</button>
        <button @click="toggleModal">
          Sair
        </button>
      </div>
    </div>
  </main>
    
  <Logout ref="modal" @onModal="logout" />
</template>

<style scoped>
.welcome {
  @apply w-full h-screen flex flex-col justify-center items-center
}
.welcome h1 {
  @apply text-3xl font-semibold
}
.actions button:first-child {
  @apply py-3 p-10 rounded font-medium text-white bg-brand hover:bg-success hover:shadow-lg transition-all ease-linear duration-100
}
.actions button:last-child {
  @apply py-3 p-10 rounded font-medium hover:bg-dark-gray hover:text-white hover:shadow-lg transition-all ease-linear duration-100
}
</style>