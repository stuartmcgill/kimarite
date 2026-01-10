<script setup lang="ts">
import { useShowdownStore } from '@/stores/showdown'
import { CategoryValue, Game as GameType } from '@/types/showdown'
import Card from '@/Components/Showdown/Card.vue'

const props = defineProps<{ game: GameType }>()

const store = useShowdownStore()
store.init(props.game)

const handleCategorySelected = (categoryValue: CategoryValue) => {
  store.selection = categoryValue
}

const endTurn = () => {
  store.selection = null
}
</script>

<template>
  <div class="flex gap-4 justify-between">
    <Card :card="game.cards[0]" @selected="handleCategorySelected" />
    <Card :card="game.cards[1]" />
  </div>
</template>
