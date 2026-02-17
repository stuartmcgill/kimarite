<script setup lang="ts">
import { useShowdownStore } from '@/stores/showdown'
import { computed } from 'vue'

const store = useShowdownStore()

const imageUrl = computed(
  () =>
    new URL(`../../../images/showdown/facedown-card.png`, import.meta.url).href,
)
</script>

<template>
  <div
    v-if="store.game?.image"
    class="w-full sm:w-60 h-48.5 sm:h-104 sm:bg-coral-100 rounded-2xl border-4 border-coral-200 sm:[mask-image:none] overflow-hidden"
  >
    <img
      :src="store.game.image"
      alt="Game logo"
      class="w-full h-full object-cover sm:object-contain"
      :class="[
        store.thinking ? 'animate-pulse' : '',
        'sm:mask-[radial-gradient(ellipse_60%_80%_at_50%_50%,black_50%,transparent_85%)]',
      ]"
    />
  </div>
  <div
    v-else
    class="p-2 sm:p-8 w-full sm:w-60 h-48.5 sm:h-104 flex items-center justify-center bg-coral-100 rounded-2xl border-4 border-coral-200"
  >
    <img
      :src="imageUrl"
      alt="Dohyo logo"
      class="sm:w-auto sm:h-auto w-40 h-40 object-cover transition-transform"
      :class="store.thinking ? 'animate-pulse sm:animate-bounce' : ''"
    />
  </div>
</template>
