import { acceptHMRUpdate, defineStore } from 'pinia'
import axios from 'axios'
import { formatBashoId } from '@/Composables/utils'

interface KimariteCount {
  type: string
  basho_id: string
  total: number
  percentage: number
}

interface GroupedKimariteTotal {
  type: string
  groupedCounts: KimariteCount[]
}

export const useKimariteStore = defineStore('kimarite', {
  state: () => ({
    counts: [] as GroupedKimariteTotal[],
    bashoIds: [] as string[],
    displayAsPercent: true as boolean,
    loading: false as boolean,
  }),
  getters: {
    datasets: state => {
      if (!state.counts) {
        return []
      }

      // There is one dataset per kimarite type
      const datasets = state.counts.map(
        (groupedTotal: GroupedKimariteTotal) => {
          // For each type we want one data point for each basho (whether there's data or not)
          const data = state.bashoIds.map((bashoId: string) => {
            // The counts are grouped by type and basho ID
            const groupCount = groupedTotal.groupedCounts.find(
              (count: KimariteCount) =>
                count.type === groupedTotal.type.toLowerCase() &&
                formatBashoId(count.basho_id) === bashoId,
            )

            if (!groupCount) {
              return 0
            }

            return state.displayAsPercent
              ? groupCount.percentage
              : groupCount.total
          })

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
