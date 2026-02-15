<script setup lang="ts">
import { useShowdownStore } from '@/stores/showdown'
import {
  Card,
  Category,
  CategoryValue,
  GameResult,
  GameSettings,
  GameType as GameType,
} from '@/types/showdown'
import Badge from 'primevue/badge'
import Button from 'primevue/button'
import MeterGroup from 'primevue/metergroup'
import Player from '@/Components/Showdown/Player.vue'
import { computed } from 'vue'
import { router } from '@inertiajs/vue3'

const props = defineProps<{ game: GameType; settings: GameSettings }>()

const store = useShowdownStore()
store.newGame(props.game, props.settings)

const calcResult = () => {
  const humanCard = store.human.cards[0] as Card
  const computerCard = store.computer.cards[0] as Card

  const humanValue = humanCard.categories.find(
    (cv: CategoryValue) => cv.code === store.selection.code,
  )?.value
  const computerValue = computerCard.categories.find(
    (cv: CategoryValue) => cv.code === store.selection.code,
  )?.value

  if (humanValue === computerValue) {
    return GameResult.Tie
  }

  if (store.selectedCategory.inverse) {
    return humanValue > computerValue ? GameResult.Computer : GameResult.Human
  } else {
    return humanValue > computerValue ? GameResult.Human : GameResult.Computer
  }
}

const switchChooser = () => {
  const otherPlayer =
    store.players.find((p: Player) => p.type !== store.chooser!.type) || null
  if (!otherPlayer) {
    console.error('Unable to find other player')

    return
  }

  store.chooser = otherPlayer
}

const processResult = () => {
  const result = calcResult()
  if (result === GameResult.Tie) {
    const humanCard = store.human.cards.shift()
    const computerCard = store.computer.cards.shift()

    store.tiedCards.push(humanCard, computerCard)
    switchChooser()

    return
  }

  const { winner, loser } =
    result === GameResult.Computer
      ? { winner: store.computer, loser: store.human }
      : { winner: store.human, loser: store.computer }

  // The winner puts their own card and the loser's card to
  // the pack of their back
  winner.cards.push(winner.cards.shift())
  const losingCard = loser.cards.shift()
  winner.cards.push(losingCard)

  // The winner also gets the pile of tied cards
  if (store.tiedCards.length > 0) {
    const tiedPile = store.tiedCards.splice(0)
    winner.cards = winner.cards.concat(tiedPile)
  }

  store.chooser = winner
}

const handleCategorySelected = (categoryValue: CategoryValue) => {
  store.selection = categoryValue
}

// Higher skill levels make the choice based on a knowledge of more cards
const memorisationMap = new Map([
  [100, 100],
  [80, 10],
  [60, 6],
  [40, 4],
  [20, 2],
])

const numCardsMemorised = computed(() => {
  const percentageKnown = memorisationMap.get(store.computer.level)

  return Math.round((percentageKnown * store.numCards) / 100)
})

const doComputerSelection = () => {
  let selectedCategory = null
  if (store.computer.level === 0) {
    // Just pick randomly
    const selectedIndex = Math.floor(Math.random() * 6)
    console.log(selectedIndex)
    selectedCategory = store.game.categories[selectedIndex]
  } else {
    // For each category, sort the known cards according to that category and see where the top card comes.
    // Choose the category where the top card comes closest to the top
    const memorisedCards: Array<Card> = store.game.cards.slice(
      0,
      numCardsMemorised.value,
    )
    const computerCard = store.computer.cardInPlay
    memorisedCards.push(computerCard)

    const rankingMap = new Map()

    store.game.categories.forEach((category: Category) => {
      const rankedCards = memorisedCards.sort((a: Card, b: Card) => {
        const aCategory = a.categories.find(c => c.code === category.code)
        const bCategory = b.categories.find(c => c.code === category.code)

        if (!aCategory || !bCategory) {
          throw Error('Could not find card categories')
        }

        if (aCategory.value === bCategory.value) {
          return 0
        }

        if (category.inverse) {
          return aCategory.value > bCategory.value ? 1 : -1
        } else {
          return bCategory.value > aCategory.value ? 1 : -1
        }
      })

      const computerCardIndex = rankedCards.indexOf(computerCard)
      rankingMap.set(category.code, computerCardIndex)
    })

    const bestCategoryCode = [...rankingMap.entries()].reduce(
      (best, current) => (current[1] < best[1] ? current : best),
    )[0]

    selectedCategory = store.game.categories.find(
      (c: Category) => c.code === bestCategoryCode,
    )
  }

  store.selection = store.computer.cardInPlay.categories.find(
    (c: CategoryValue) => c.code === selectedCategory.code,
  )
  store.thinking = false
}

const nextCard = () => {
  processResult()

  store.drawCards()
  store.selection = null

  if (store.chooser === store.computer) {
    store.thinking = true
    setTimeout(() => doComputerSelection(), 2000)
  }
}

const newGame = () => {
  router.visit(route('showdown.view'))
}

const score = computed(() => {
  return [
    {
      label: store.human.name,
      value: store.human.cards.length,
      color: 'white',
      severity: 'secondary',
      class: '!bg-white !text-grey-800',
    },
    {
      label: 'Ties',
      value: store.tiedCards.length,
      color: 'var(--p-primary-color)',
    },
    {
      label: store.computer.name,
      value: store.computer.cards.length,
      color: 'black',
      severity: 'contrast',
    },
  ]
})
</script>

<template>
  <div v-if="store.initialised">
    <div
      class="flex flex-col sm:flex-row gap-2 sm:gap-4 items-center justify-between"
    >
      <Player :player="store.human" @selected="handleCategorySelected" />
      <div class="flex flex-col">
        <div class="flex justify-center">
          <Button
            v-if="store.winner"
            label="New game"
            class="w-fit"
            @click="newGame"
          />
          <Button
            v-else
            label="Next"
            :disabled="!store.selection"
            class="w-fit"
            @click="nextCard"
          />
        </div>
        <!--        <div class="mt-2 flex-flex-col">-->
        <!--          <div> Human {{ store.human.cards.length }}</div>-->
        <!--          <div> Computer {{ store.computer.cards.length }}</div>-->
        <!--          <div> Ties {{ store.tiedCards.length }}</div>-->
        <!--        </div>-->
        <div v-if="store.winner" class="mt-12 text-3xl font-bold">
          {{ store.winner.name }} kachikoshi!
        </div>
      </div>
      <Player :player="store.computer" />
    </div>
    <div class="mt-8 p-4 bg-coral-100 rounded">
      <MeterGroup :value="score" :min="0" :max="store.numCards">
        <template #label="{ value }">
          <div class="flex justify-between w-full">
            <span v-for="(item, index) in value" :key="index">
              <div class="flex items-center gap-2">
                <span>{{ item.label }}</span>
                <Badge :class="item.class" :severity="item.severity">{{
                  item.value
                }}</Badge>
              </div>
            </span>
          </div>
        </template>
      </MeterGroup>
    </div>
  </div>
</template>
