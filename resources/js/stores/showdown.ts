import { acceptHMRUpdate, defineStore } from 'pinia'
import { Card, CategoryValue, Game, Player } from '@/types/showdown'

export const useShowdownStore = defineStore('showdown', {
  state: () => ({
    initialised: false as boolean,
    game: null as Game | null,
    currentChooser: 0 as number,
    selection: null as CategoryValue | null,
    players: [] as Player[],
    tiedCards: [] as Card[],
  }),
  actions: {
    init(game: Game) {
      this.game = game

      this.shuffleCards()

      this.players = [
        {
          type: 'human',
          name: 'You',
          cards: this.game.cards.slice(0, this.game.cards.length / 2 - 1),
        },
        {
          type: 'computer',
          name: 'Yokozuna',
          cards: this.game.cards.slice(
            this.game.cards.length / 2,
            this.game.cards.length,
          ),
          level: 10,
        },
      ]

      this.initialised = true
    },

    shuffleCards() {
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
  getters: {
    human: state => state.players[0],
    computer: state => state.players[1],
    numCards(state) {
      const humanCards = state.players[0].cards
      const computerCards = state.players[1].cards

      return humanCards.length + computerCards.length + state.tiedCards.length
    },
  },
})

if (import.meta.hot) {
  import.meta.hot.accept(acceptHMRUpdate(useShowdownStore, import.meta.hot))
}
