import { acceptHMRUpdate, defineStore } from 'pinia'
import axios from 'axios'

export const useRefreshStore = defineStore('refresh', {
  state: () => ({ kimarites: [] as object[], loading: false as boolean }),
  getters: {},
  actions: {
    async refresh() {
      this.loading = true
      try {
        await axios.post(route('rebuild'))
      } finally {
        this.loading = false
      }
    },
    async refreshBashoPercentages() {
      this.loading = true
      try {
        await axios.post(route('refresh-basho-percentages'))
      } finally {
        this.loading = false
      }
    },
  },
})

if (import.meta.hot) {
  import.meta.hot.accept(acceptHMRUpdate(useRefreshStore, import.meta.hot))
}
