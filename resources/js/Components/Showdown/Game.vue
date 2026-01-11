<script setup lang="ts">
import { useShowdownStore } from '@/stores/showdown'
import { CategoryValue, Game as GameType } from '@/types/showdown'
import Button from 'primevue/button'
import Card from '@/Components/Showdown/Card.vue'

const props = defineProps<{ game: GameType }>()

const store = useShowdownStore()
store.init(props.game)

const handleCategorySelected = (categoryValue: CategoryValue) => {
  store.selection = categoryValue
}

const nextCard = () => {
  store.selection = null
}
</script>

<template>
  <div v-if="store.initialised" class="grid grid-cols-3 gap-4 items-center">
    <Card :card="game.cards[0]" @selected="handleCategorySelected" />
    <div class="flex justify-center">
      <Button
        label="Next"
        :disabled="!store.selection"
        class="w-fit"
        @click="nextCard"
      />
    </div>
    <Card v-if="!!store.selection" :card="game.cards[1]" />
  </div>
</template>
