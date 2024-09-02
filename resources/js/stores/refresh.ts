import { acceptHMRUpdate, defineStore } from 'pinia'
import axios from 'axios'

export const useRefreshStore = defineStore('refresh', {
  state: () => ({ kimarites: [] as object[], loading: false as boolean }),
  getters: {},
  actions: {
    async refresh() {
      this.loading = true
      try {
        const resp = await axios.get(
          'https://jsonplaceholder.typicode.com/todos/1',
        )
        console.log(resp)
      } finally {
        this.loading = false
      }
    },
  },
})

if (import.meta.hot) {
  import.meta.hot.accept(acceptHMRUpdate(useRefreshStore, import.meta.hot))
}
