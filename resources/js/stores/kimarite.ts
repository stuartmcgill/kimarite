import { acceptHMRUpdate, defineStore } from 'pinia'
import axios from 'axios'

interface KimariteCount {
  type: string
  basho_id: string
  division: string
  count: number
}

export const useKimariteStore = defineStore('kimarite', {
  state: () => ({
    counts: [] as KimariteCount[],
    bashoIds: [] as string[],
    loading: false as boolean,
  }),
  getters: {
    datasets: state => {
      if (!state.counts) {
        return []
      }

      return state.counts
    },
  },
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
          route('kimarite.counts', {
            types: types,
            divisions: divisions,
            from: from,
            to: to,
          }),
        )
        this.counts = resp.data.counts
        this.bashoIds = resp.data.bashoIds
      } finally {
        this.loading = false
      }
    },
  },
})

if (import.meta.hot) {
  import.meta.hot.accept(acceptHMRUpdate(useKimariteStore, import.meta.hot))
}
