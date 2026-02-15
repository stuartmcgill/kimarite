<script setup lang="ts">
import Card from '@/Components/Showdown/Card.vue'
import FaceDownCard from '@/Components/Showdown/FaceDownCard.vue'
import { Player as PlayerType, Card as CardType } from '@/types/showdown'
import { computed, Ref } from 'vue'
import { useShowdownStore } from '@/stores/showdown'
import LosingMessage from '@/Components/Showdown/LosingMessage.vue'

const props = defineProps<{ player: PlayerType }>()

const store = useShowdownStore()

const cardInPlay: Ref<CardType | null> = computed(() => props.player.cardInPlay)

const isHuman = computed(() => props.player.type === 'human')
</script>

<template>
  <div v-if="cardInPlay" class="w-full sm:w-auto">
    <!--    The human player always sees their card-->
    <Card
      v-if="!store.gameOver && (isHuman || !!store.selection)"
      :card="cardInPlay"
      @selected="$emit('selected', $event)"
    />
    <FaceDownCard v-else />
  </div>
  <LosingMessage v-else />
</template>
