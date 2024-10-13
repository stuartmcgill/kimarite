import { acceptHMRUpdate, defineStore } from 'pinia'
import axios from 'axios'
import { formatBashoId } from '@/Composables/utils'

interface KimariteCount {
  type: string
  basho_id: string
  division: string
  total: number
}

interface GroupedKimariteTotal {
  type: string
  groupedCounts: KimariteCount[]
}

export const useKimariteStore = defineStore('kimarite', {
  state: () => ({
    counts: [] as GroupedKimariteTotal[],
    bashoIds: [] as string[],
    loading: false as boolean,
  }),
  getters: {
    datasets: state => {
      if (!state.counts) {
        return []
      }

      const datasets = state.counts.map(
        (groupedTotal: GroupedKimariteTotal) => {
          const data = groupedTotal.groupedCounts.map(
            (count: KimariteCount) => count.total,
          )

          return {
            label: groupedTotal.type,
            data: data,
          }
        },
      )

      return datasets
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
        this.bashoIds = resp.data.bashoIds.map((bashoId: string) =>
          formatBashoId(bashoId),
        )
      } finally {
        this.loading = false
      }
    },
  },
})

if (import.meta.hot) {
  import.meta.hot.accept(acceptHMRUpdate(useKimariteStore, import.meta.hot))
}
