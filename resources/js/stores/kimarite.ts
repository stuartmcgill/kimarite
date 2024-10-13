import { acceptHMRUpdate, defineStore } from 'pinia'
import axios from 'axios'

interface KimariteCount {
  type: string
  basho_id: string
  division: string
  count: number
}

interface KimariteCountGroup {
  type: string
  groupedCounts: KimariteCount[]
}

export const useKimariteStore = defineStore('kimarite', {
  state: () => ({
    counts: [] as KimariteCountGroup[],
    bashoIds: [] as string[],
    loading: false as boolean,
  }),
  getters: {
    something: state => {
      const bashoIds = state.counts.map(
        (group: KimariteCountGroup) => group.groupedCounts,
      )

      return bashoIds
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
