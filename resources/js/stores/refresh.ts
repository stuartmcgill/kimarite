import { acceptHMRUpdate, defineStore } from 'pinia'

export const useRefreshStore = defineStore('refresh', {
  state: () => ({ kimarites: [] as object[] }),
  getters: {},
  actions: {
    refresh() {
      console.log('Hello!')
    },
  },
})

if (import.meta.hot) {
  import.meta.hot.accept(acceptHMRUpdate(useRefreshStore, import.meta.hot))
}
