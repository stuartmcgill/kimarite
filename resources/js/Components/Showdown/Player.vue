<script setup lang="ts">
import Card from '@/Components/Showdown/Card.vue'
import FaceDownCard from '@/Components/Showdown/FaceDownCard.vue'
import { Player as PlayerType, Card as CardType } from '@/types/showdown'
import { computed, Ref} from 'vue'
import { useShowdownStore } from '@/stores/showdown'

const props = defineProps<{ player: PlayerType }>()

const store = useShowdownStore()

const cardInPlay: Ref<CardType | null> = computed(() => props.player.cardInPlay)

const isHuman = computed(() => props.player.type === 'human')
</script>

<template>
  <div v-if="cardInPlay">
  <Card
    v-if="isHuman || !!store.selection"
    :card="cardInPlay"
    @selected="$emit('selected', $event)"
  />
  <FaceDownCard v-else />
  </div>
</template>
