<script setup lang="ts">
import { useShowdownStore } from '@/stores/showdown'
import { CategoryValue, Game as GameType } from '@/types/showdown'
import Button from 'primevue/button'
import MeterGroup from 'primevue/metergroup'
import Player from '@/Components/Showdown/Player.vue'
import { computed } from 'vue'

const props = defineProps<{ game: GameType }>()

const store = useShowdownStore()
store.init(props.game)

const handleCategorySelected = (categoryValue: CategoryValue) => {
  store.selection = categoryValue
}

const nextCard = () => {
  store.selection = null
}

const score = computed(() => {
  return [
    {
      label: store.human.name,
      value: store.human.cards.length,
      color: 'white',
    },
    {
      label: 'Ties pile',
      value: store.tiedCards.length,
      color: 'var(--p-primary-color)',
    },
    {
      label: store.computer.name,
      value: store.computer.cards.length,
      color: 'black',
    },
  ]
})
</script>

<template>
  <div v-if="store.initialised">
    <div class="flex gap-4 items-center justify-between">
      <Player :player="store.human" @selected="handleCategorySelected" />
      <div class="flex justify-center">
        <Button
          label="Next"
          :disabled="!store.selection"
          class="w-fit"
          @click="nextCard"
        />
      </div>
      <Player :player="store.computer" @selected="handleCategorySelected" />
    </div>
    <div class="mt-8 p-4 bg-coral-100 rounded">
      <MeterGroup :value="score" :min="0" :max="store.numCards" />
    </div>
  </div>
</template>
