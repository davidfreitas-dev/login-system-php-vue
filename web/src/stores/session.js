import { defineStore } from 'pinia'

export const useSessionStore = defineStore('session', {
  state: () => {
    return { session: {} }
  },
  actions: {
    setSession(session) {
      this.session = session
    },
    destroySession() {
      this.session = {}
    },
  },
})