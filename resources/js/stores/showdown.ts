import { acceptHMRUpdate, defineStore } from 'pinia'
import { CategoryValue, Game } from '@/types/showdown'

export const useShowdownStore = defineStore('showdown', {
  state: () => ({
    game: null as Game | null,
    currentChooser: 0 as number,
    selection: null as CategoryValue | null,
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
