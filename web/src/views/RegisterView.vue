<script setup>
    import { ref, inject } from 'vue'
    import { useToast } from '@/use/useToast'
    import { useRouter } from 'vue-router'
    
    import ImageForm from '../components/form/ImageForm.vue'
    import HeadingForm from '../components/form/HeadingForm.vue'
    import InputForm from '../components/form/InputForm.vue'
    import ButtonForm from '../components/form/ButtonForm.vue'
    import ToastMessage from '../components/ToastMessage.vue'

    const router = useRouter()
    const axios = inject('axios')

    const isLoading = ref(false)

    const formData = ref({
        desperson: '',
        deslogin: '',
        desemail: '',
        nrphone: '',
        nrcpf: '',
        despassword: '',
        inadmin: 0
    })

    const handleRegister = () => {
        isLoading.value = true

        axios
            .post('/register', formData.value)
            .then((response) => {
                if (response.data.status === 'success') {
                    handleToast(response.data.status, response.data.data)
                    
                    setTimeout(() => {
                        router.push('/login')
                    }, 2000);
                } else {
                    handleToast(response.data.status, response.data.data)                   
                }                
            })
            .finally(() => {
                isLoading.value = false
            });
    }

    const validateEmail = (email) => {
        const re = /\S+@\S+\.\S+/;
        return re.test(email);
    }

    const handleValidate = () => {
        const numbers = /([0-9])/;
        const letters = /([a-zA-Z])/;
        const specialChar = /([~,!,@,#,$,%,^,&,*,-,_,+,=,?,>,<])/;

        if (!formData.value.desperson || !formData.value.desemail || !formData.value.despassword) {
            return handleToast('error', 'Preencha todos os campos!') 
        }
        
        if (!validateEmail(formData.value.desemail)) {
            return handleToast('error', 'Formato de e-mail inválido')
        }

        if (formData.value.despassword.length < 8) {
            return handleToast('error', 'Sua senha deve ter no mínimo 8 caracteres')
        }

        if (!formData.value.despassword.match(numbers) || !formData.value.despassword.match(letters) || !formData.value.despassword.match(specialChar)) {
            return handleToast('error', 'Sua senha deve conter um mínimo de uma letra, um número e um caractere especial')
        }

        handleRegister()
    }

    const { toast, toastData, handleToast } = useToast()
</script>

<template>
    <section>
        <ImageForm />

        <div class="container">
            <span class="header">
                Já possui uma conta?
                <button type="button" @click="router.push('/login')">Entrar</button>
            </span>
            
            <div class="form">
                <HeadingForm :text="'Bem-vindo!'" />

                <div>
                    <InputForm 
                        v-model="formData.desperson" 
                        :type="'text'" 
                        :label="'Nome e sobrenome'"
                        :placeholder="'John Doe'"
                    />
                </div>

                <div>
                    <InputForm 
                        v-model="formData.desemail" 
                        :type="'email'" 
                        :label="'Seu melhor e-mail'"
                        :placeholder="'johndoe@example.com'"
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
                    :text="'Cadastrar'" 
                    :is-loading="isLoading" 
                    @onClickButton="handleValidate" 
                />
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