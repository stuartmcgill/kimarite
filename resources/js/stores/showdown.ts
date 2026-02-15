import { acceptHMRUpdate, defineStore } from 'pinia'
import {
  Card,
  Category,
  CategoryValue,
  ComputerPlayer,
  DifficultyLabelsMap,
  GameSettings,
  GameType,
  HumanPlayer,
  Player,
} from '@/types/showdown'
import { useSettings } from '@/Composables/showdown/useSettings'

export const useShowdownStore = defineStore('showdown', {
  state: () => ({
    initialised: false as boolean,
    game: null as GameType | null,
    difficultyLabelsMap: new Map<number, string>(),
    chooser: null as Player | null,
    selection: null as CategoryValue | null,
    players: [] as Player[],
    tiedCards: [] as Card[],
    thinking: true as boolean,
  }),
  actions: {
    newGame(game: GameType, settings: GameSettings) {
      const { difficultyRank } = useSettings(this.difficultyLabelsMap)
      this.game = game

      this.shuffleCards()

      this.players = [
        {
          type: 'human',
          name: settings.playerName,
          cards: this.game.cards.slice(0, this.game.cards.length / 2),
          cardInPlay: null,
        } as HumanPlayer,
        {
          type: 'computer',
          name: difficultyRank.value,
          cards: this.game.cards.slice(
            this.game.cards.length / 2,
            this.game.cards.length,
          ),
          cardInPlay: null,
          level: settings.difficultyLevel,
        } as ComputerPlayer,
      ]

      this.drawCards()
      this.chooser = this.human

      this.initialised = true
    },

    drawCards() {
      this.human.cardInPlay = this.human.cards[0]
      this.computer.cardInPlay = this.computer.cards[0]
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
    selectedCategory(state): Category {
      const selectedCode = state.selection.code

      return state.game.categories.find(
        (c: Category) => c.code === selectedCode,
      )
    },
    winner(state): Player | null {
      const humanCards = state.players[0].cards
      const computerCards = state.players[1].cards

      if (humanCards.length === 0) {
        return state.computer
      }

      if (computerCards.length === 0) {
        return state.human
      }

      return null
    },
    gameOver(state): boolean {
      return !!state.winner
    },
  },
})

if (import.meta.hot) {
  import.meta.hot.accept(acceptHMRUpdate(useShowdownStore, import.meta.hot))
}
