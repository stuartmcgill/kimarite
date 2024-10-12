import { acceptHMRUpdate, defineStore } from 'pinia'
import axios from 'axios'

export const useKimariteStore = defineStore('kimarite', {
  state: () => ({ kimarites: [] as object[], loading: false as boolean }),
  getters: {},
  actions: {
    async fetchStats(types: string[], from: string, to: string) {
      this.loading = true
      try {
        const resp = await axios.get(
          route(
            'kimarite.stats',
            {
              types: types,
              from: from,
              to: to
            }
          ),
        )
        console.log(resp.data.stats)
      } finally {
        this.loading = false
      }
    },
  },
})

if (import.meta.hot) {
  import.meta.hot.accept(acceptHMRUpdate(useKimariteStore, import.meta.hot))
}
