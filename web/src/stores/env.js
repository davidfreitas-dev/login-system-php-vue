import { defineStore } from 'pinia'

export const useEnvStore = defineStore('env', {
  state: () => {
    return { 
      apiURL: 'http://localhost:8000'
    }
  }
})