<script setup>
    import { ref, inject } from 'vue'
    import { useToast } from '@/use/useToast'
    import { useRouter } from 'vue-router'
    import { useSessionStore } from '@/stores/session'
    
    import ImageForm from '../components/form/ImageForm.vue'
    import HeadingForm from '../components/form/HeadingForm.vue'
    import InputForm from '../components/form/InputForm.vue'
    import ButtonForm from '../components/form/ButtonForm.vue'
    import ToastMessage from '../components/ToastMessage.vue'

    const router = useRouter()
    const axios = inject('axios')
    const storeSession = useSessionStore()

    const isLoading = ref(false)

    const formData = ref({
        deslogin: '',
        despassword: ''
    })

    const handleLogin = () => {
        isLoading.value = true

        axios
            .post('/login', formData.value)
            .then((response) => {
                if (response.data.status === 'success') {
                    storeSession.setSession(response.data.data)
                    router.push('/')
                } else {
                    handleToast(response.data.status, response.data.data)
                }                
            })
            .finally(() => {
                isLoading.value = false
            });
    }

    const handleValidate = () => {
        if (!formData.value.deslogin) {
            return handleToast('error', 'Informe seu login')
        }

        if (!formData.value.despassword) {
            return handleToast('error', 'Informe sua senha')
        }

        handleLogin()
    }

    const { toast, toastData, handleToast } = useToast()
</script>

<template>
    <section>
        <ImageForm />

        <div class="container">
            <div class="header">
                Ainda não possui uma conta?
                <button type="button" @click="router.push('/register')">Cadastre-se</button>
            </div>
            
            <div class="form">
                <HeadingForm :text="'Bem-vindo de volta!'" />

                <div>
                    <InputForm 
                        v-model="formData.deslogin" 
                        :type="'text'" 
                        :label="'Nome de usuário ou E-mail'"
                        :placeholder="'johndoe@email.com'"
                    />
                </div>

                <div>
                    <InputForm 
                        v-model="formData.despassword" 
                        :type="'password'" 
                        :label="'Sua senha'"
                        :placeholder="'**********'"
                        @onKeyupEnter="handleValidate"
                    />
                </div>

                <ButtonForm 
                    :text="'Entrar'" 
                    :is-loading="isLoading" 
                    @onClickButton="handleValidate" 
                />
            </div>

            <div class="footer" @click="router.push('/forgot')">
                Esqueceu sua seha?
                <span>Clique aqui</span>
            </div>
        </div>
    </section>

    <ToastMessage ref="toast" :toastData="toastData"/>
</template>

<style scoped>
section {
    @apply flex flex-row
}
.container {
    @apply flex flex-col h-screen max-w-none w-full md:w-2/5 lg:p-10 
}
.header {
    @apply flex flex-col self-center xl:block xl:self-end mt-10 lg:mt-0 px-2 text-dark-gray
}
.header button {
    @apply border border-solid rounded-full uppercase font-medium text-sm hover:bg-dark-gray hover:text-light transition-all ease-linear duration-100 px-6 py-1 ml-2 mt-2 xl:mt-0
}
.form {
    @apply my-auto px-7 md:px-10
}
.form div {
    @apply my-5
}
.footer {
    @apply text-dark-gray text-center mb-10 cursor-pointer
}
.footer span {
    @apply text-success hover:text-brand
}
</style>