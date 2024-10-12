import { acceptHMRUpdate, defineStore } from 'pinia'
import axios from 'axios'

interface KimariteCount {}

export const useKimariteStore = defineStore('kimarite', {
  state: () => ({ counts: [] as object[], loading: false as boolean }),
  getters: {},
  actions: {
    async fetchCounts(
      types: string[],
      divisions: string[],
      from: string,
      to: string,
    ) {
      this.loading = true
      try {
        const resp = await axios.get(
          route('kimarite.stats', {
            types: types,
            divisions: divisions,
            from: from,
            to: to,
          }),
        )
        console.log(resp.data.counts)
        this.counts = resp.data.counts
      } finally {
        this.loading = false
      }
    },
  },
})

if (import.meta.hot) {
  import.meta.hot.accept(acceptHMRUpdate(useKimariteStore, import.meta.hot))
}
