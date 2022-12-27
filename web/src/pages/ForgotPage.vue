<script setup>
  import { ref, inject } from 'vue'
  import { useToast } from '@/use/useToast'
  import { useRouter } from 'vue-router'    
  import Image from '@/components/Image.vue'
  import Heading from '@/components/Heading.vue'
  import Input from '@/components/Input.vue'
  import Button from '@/components/Button.vue'
  import Toast from '@/components/Toast.vue'

  const router = useRouter()
  const axios = inject('axios')

  const isLoading = ref(false)

  const formData = ref({
    desemail: ''
  })

  const validateEmail = (email) => {
    const re = /\S+@\S+\.\S+/;
    return re.test(email);
  }

  const handleRecovery = () => {
    isLoading.value = true

    axios
      .post('/forgot', formData.value)
      .then((response) => {
        if (response.data.status === 'success') {
          handleToast('success', response.data.data)
        } else {
          handleToast('error', response.data.data)                  
        }
      })
      .finally(() => {
        isLoading.value = false
      });
  }

  const handleValidate = () => {
    if (!formData.value.desemail) {
      return handleToast('error', 'Informe seu email')
    }

    if (!validateEmail(formData.value.desemail)) {
      return handleToast('error', 'Formato de e-mail inválido')
    }

    handleRecovery()
  }

  const { toast, toastData, handleToast } = useToast()
</script>

<template>
  <section>
    <Image />

    <div class="container">
      <div class="header">
        Já possui uma conta?
        <button type="button" @click="router.push('/login')">Entrar</button>
      </div>
      
      <div class="form">
        <Heading :text="'Recuperação de Conta'" />

        <div>
          <Input 
            v-model="formData.desemail" 
            :type="'email'" 
            :label="'Seu e-mail'"
            :placeholder="'johndoe@example.com'"
            @onKeyupEnter="handleValidate"
          />
        </div>

        <Button 
          :text="'Enviar'" 
          :is-loading="isLoading" 
          @onClickButton="handleValidate" 
        />
      </div>
    </div>
  </section>

  <Toast ref="toast" :toastData="toastData"/>
</template>

<style scoped>
section {
  @apply flex flex-row
}
.container {
  @apply flex flex-col h-screen max-w-none w-full md:w-2/5 lg:p-10 
}
.header {
  @apply self-center lg:self-end mt-10 lg:mt-0 px-2 text-dark-gray
}
.header button {
  @apply border border-solid rounded-full uppercase font-medium text-sm hover:bg-dark-gray hover:text-light transition-all ease-linear duration-100 px-6 py-1 ml-2
}
.form {
  @apply my-auto px-7 md:px-10
}
.form div {
  @apply my-5
}
</style>