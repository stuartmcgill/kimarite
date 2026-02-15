<script setup lang="ts">
import { computed, ref } from 'vue'
import { useShowdownStore } from '@/stores/showdown'

const imageUrl = computed(
  () =>
    new URL(`../../../images/showdown/facedown-card.png`, import.meta.url).href,
)

const store = useShowdownStore()
</script>

<template>
  <div
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
