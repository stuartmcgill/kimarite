import { acceptHMRUpdate, defineStore } from 'pinia'
import { CategoryValue, Game } from '@/types/showdown'

export const useShowdownStore = defineStore('showdown', {
  state: () => ({
    initialised: false as boolean,
    game: null as Game | null,
    currentChooser: 0 as number,
    selection: null as CategoryValue | null,
  }),
  actions: {
    init(game: Game) {
      this.game = game

      this.shuffle()
      this.initialised = true
    },
    shuffle() {
      let cards = this.game!.cards

      for (let i = cards.length - 1; i > 0; i--) {
        const j = Math.floor(Math.random() * (i + 1))

        const temp = cards[i]
        cards[i] = cards[j]
        cards[j] = temp
      }

      return cards
    },
  },
  getters: {},
})

if (import.meta.hot) {
  import.meta.hot.accept(acceptHMRUpdate(useShowdownStore, import.meta.hot))
}
