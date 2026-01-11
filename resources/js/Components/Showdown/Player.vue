<script setup lang="ts">
import Card from '@/Components/Showdown/Card.vue'
import { Player as PlayerType } from '@/types/showdown'
import { computed } from 'vue'
import { useShowdownStore } from '@/stores/showdown'

const props = defineProps<{ player: PlayerType }>()

const store = useShowdownStore()

const topCard = computed(() => props.player.cards[0])

const isHuman = computed(() => props.player.type === 'human')
</script>

<template>
  <Card
    v-if="isHuman || !!store.selection"
    :card="topCard"
    @selected="$emit('selected', $event)"
  />
</template>
