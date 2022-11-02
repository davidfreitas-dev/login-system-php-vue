import { createApp } from 'vue'
import { createPinia } from 'pinia'

import App from './App.vue'
import router from './router'

import axios from 'axios'
import VueAxios from 'vue-axios'

axios.defaults.baseURL = 'http://localhost:8000'

import './use/useInterceptor'

import './assets/css/index.css'

const pinia = createPinia()

const app = createApp(App)

app.config.globalProperties.$filters = {
  filterName(value) {
    return 'Regra do filtro'
  }
}

app.use(pinia)
app.use(router)

app.use(VueAxios, axios)
app.provide('axios', app.config.globalProperties.axios)

app.mount('#app')
