import { acceptHMRUpdate, defineStore } from 'pinia'
import { Game } from '@/types/showdown'

export const useShowdownStore = defineStore('showdown', {
  state: () => ({
    game: null as Game | null,
  }),
  actions: {
    init(game: Game) {
      this.game = game
    },
  },
  getters: {},
})

if (import.meta.hot) {
  import.meta.hot.accept(acceptHMRUpdate(useShowdownStore, import.meta.hot))
}
