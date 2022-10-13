import { defineStore } from 'pinia'

export const useUsersStore = defineStore('users', {
  state: () => {
    return { users: [] }
  },
  actions: {
    setUsers(users) {
      this.users = users
    },
  },
})